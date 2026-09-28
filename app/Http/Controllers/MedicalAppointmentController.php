<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\StoreMedicalAppointmentRequest;
use App\Http\Requests\UpdateMedicalAppointmentRequest;
use App\Models\MedicalAppointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MedicalAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $relations = ['doctor', 'referredFrom.doctor', 'referrals.doctor', 'tasks.followUpAppointment', 'documents', 'orders'];

        $upcomingAppointments = $user->medicalAppointments()
            ->upcoming()
            ->with($relations)
            ->orderBy('scheduled_at')
            ->get();

        $awaitingFollowUp = $user->medicalAppointments()
            ->awaitingFollowUp()
            ->with($relations)
            ->orderByDesc('scheduled_at')
            ->get();

        $historyQuery = $user->medicalAppointments()
            ->where('status', '!=', AppointmentStatus::Scheduled)
            ->with($relations)
            ->orderByDesc('scheduled_at');

        if ($request->filled('doctor')) {
            $historyQuery->where('doctor_id', $request->integer('doctor'));
        }

        if ($request->filled('specialty')) {
            $historyQuery->whereHas('doctor', fn ($query) => $query->where('specialty', $request->string('specialty')));
        }

        $history = $historyQuery->get()
            ->groupBy(fn (MedicalAppointment $appointment) => ucfirst($appointment->scheduled_at->locale('es')->isoFormat('MMMM YYYY')));

        $openTasks = $user->medicalAppointmentTasks()
            ->whereNull('completed_at')
            ->with(['appointment.doctor', 'followUpAppointment'])
            ->orderByRaw('due_date IS NULL, due_date')
            ->orderBy('medical_appointment_tasks.created_at')
            ->get();

        $doctors = $user->doctors()->orderBy('name')->get();
        $specialties = $doctors->pluck('specialty')->filter()->unique()->sort()->values();
        $documents = $user->medicalDocuments()->orderByDesc('issued_at')->get();
        $orders = $user->medicalOrders()->orderByDesc('issued_at')->orderByDesc('created_at')->get();

        return view('medical.appointments', compact(
            'upcomingAppointments',
            'awaitingFollowUp',
            'history',
            'openTasks',
            'doctors',
            'specialties',
            'documents',
            'orders',
        ));
    }

    public function store(StoreMedicalAppointmentRequest $request): RedirectResponse
    {
        $user = $request->user();
        $doctorId = $request->doctor_id ?: null;

        if (! $doctorId && $request->filled('new_doctor_name')) {
            $doctorId = $user->doctors()->create([
                'name' => $request->new_doctor_name,
                'specialty' => $request->new_doctor_specialty,
            ])->id;
        }

        $location = $request->location;

        if (! $location && $doctorId) {
            $location = $user->doctors()->find($doctorId)?->address;
        }

        $appointment = $user->medicalAppointments()->create([
            'doctor_id' => $doctorId,
            'referred_from_id' => $request->referred_from_id ?: null,
            'scheduled_at' => $request->scheduled_at,
            'location' => $location,
            'reason' => $request->reason,
            'status' => AppointmentStatus::Scheduled,
        ]);

        if ($request->filled('referral_task_id')) {
            $user->medicalAppointmentTasks()
                ->whereKey($request->integer('referral_task_id'))
                ->first()
                ?->update([
                    'follow_up_appointment_id' => $appointment->id,
                    'completed_at' => now(),
                ]);
        }

        return redirect()->route('medical.appointments.index')->with('status', 'appointment-created');
    }

    public function update(UpdateMedicalAppointmentRequest $request, MedicalAppointment $appointment): RedirectResponse
    {
        $user = $request->user();

        $appointment->update([
            'doctor_id' => $request->doctor_id ?: null,
            'scheduled_at' => $request->scheduled_at,
            'location' => $request->location,
            'reason' => $request->reason,
            'status' => $request->status,
            'observations' => $request->observations,
        ]);

        $appointment->documents()->sync($request->input('document_ids', []));

        $orderIds = $request->input('order_ids', []);
        $user->medicalOrders()
            ->where('medical_appointment_id', $appointment->id)
            ->whereNotIn('id', $orderIds)
            ->update(['medical_appointment_id' => null]);
        $user->medicalOrders()
            ->whereIn('id', $orderIds)
            ->update(['medical_appointment_id' => $appointment->id]);

        $appointment->tasks()->createMany($request->newTasks());

        return back()->with('status', 'appointment-updated');
    }

    public function destroy(MedicalAppointment $appointment): RedirectResponse
    {
        abort_if($appointment->user_id !== auth()->id(), 403);

        $appointment->delete();

        return back()->with('status', 'appointment-deleted');
    }

    public function calendar(MedicalAppointment $appointment): Response
    {
        abort_if($appointment->user_id !== auth()->id(), 403);

        $appointment->loadMissing('doctor');

        $description = collect([
            $appointment->reason ? 'Motivo: '.$appointment->reason : null,
            $appointment->doctor?->phone ? 'Tel.: '.$appointment->doctor->phone : null,
            route('medical.appointments.index'),
        ])->filter()->implode("\n");

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//MiEntreno//Turnos Medicos//ES',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:medical-appointment-'.$appointment->id.'@'.parse_url(config('app.url'), PHP_URL_HOST),
            'DTSTAMP:'.now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:'.$appointment->scheduled_at->format('Ymd\THis'),
            'DTEND:'.$appointment->scheduled_at->copy()->addHour()->format('Ymd\THis'),
            'SUMMARY:'.$this->escapeIcsText('Turno médico: '.$appointment->title()),
            'DESCRIPTION:'.$this->escapeIcsText($description),
        ];

        if ($appointment->location) {
            $lines[] = 'LOCATION:'.$this->escapeIcsText($appointment->location);
        }

        foreach (['-P1D', '-PT2H'] as $trigger) {
            array_push(
                $lines,
                'BEGIN:VALARM',
                'ACTION:DISPLAY',
                'DESCRIPTION:'.$this->escapeIcsText('Turno médico: '.$appointment->title()),
                'TRIGGER:'.$trigger,
                'END:VALARM',
            );
        }

        array_push($lines, 'END:VEVENT', 'END:VCALENDAR');

        $content = collect($lines)->map(fn (string $line) => $this->foldIcsLine($line))->implode("\r\n")."\r\n";

        $filename = 'turno-'.$appointment->scheduled_at->format('Y-m-d').'-'.Str::slug($appointment->title()).'.ics';

        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function escapeIcsText(string $text): string
    {
        return str_replace(
            ['\\', ';', ',', "\r\n", "\n"],
            ['\\\\', '\;', '\\,', '\\n', '\\n'],
            $text,
        );
    }

    /**
     * Parte las líneas a 75 octetos como pide RFC 5545, sin cortar caracteres multibyte.
     */
    private function foldIcsLine(string $line): string
    {
        $folded = '';
        $current = '';

        foreach (mb_str_split($line) as $character) {
            if (strlen($current.$character) > 75) {
                $folded .= $current."\r\n ";
                $current = '';
            }

            $current .= $character;
        }

        return $folded.$current;
    }
}
