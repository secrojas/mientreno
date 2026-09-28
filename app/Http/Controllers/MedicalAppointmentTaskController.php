<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalAppointmentTaskRequest;
use App\Models\MedicalAppointment;
use App\Models\MedicalAppointmentTask;
use Illuminate\Http\RedirectResponse;

class MedicalAppointmentTaskController extends Controller
{
    public function store(StoreMedicalAppointmentTaskRequest $request, MedicalAppointment $appointment): RedirectResponse
    {
        $appointment->tasks()->create($request->validated());

        return back()->with('status', 'task-created');
    }

    public function toggle(MedicalAppointmentTask $task): RedirectResponse
    {
        abort_if($task->appointment->user_id !== auth()->id(), 403);

        $task->update([
            'completed_at' => $task->isCompleted() ? null : now(),
        ]);

        return back();
    }

    public function destroy(MedicalAppointmentTask $task): RedirectResponse
    {
        abort_if($task->appointment->user_id !== auth()->id(), 403);

        $task->delete();

        return back()->with('status', 'task-deleted');
    }
}
