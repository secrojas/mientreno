<?php

namespace App\Http\Controllers;

use App\Enums\MedicalDocumentType;
use App\Http\Requests\StoreMedicalDocumentRequest;
use App\Http\Requests\UpdateMedicalDocumentRequest;
use App\Models\MedicalDocument;
use App\Models\ReportShare;
use App\Services\MetricsService;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalController extends Controller
{
    public function __construct(
        private MetricsService $metricsService,
        private ReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $documents = $user->medicalDocuments()
            ->with('doctor')
            ->orderByDesc('issued_at')
            ->orderByDesc('created_at')
            ->get();

        $fitnessCertificate = $documents
            ->where('type', MedicalDocumentType::FitnessCertificate)
            ->first();

        $doctors = $user->doctors()->orderBy('name')->get();

        $documentGroups = $user->medicalDocumentGroups()
            ->with('doctor', 'documents')
            ->latest()
            ->get();

        $totalMetrics = $this->metricsService->getTotalMetrics($user);
        $yearlyMetrics = $this->metricsService->getYearlyMetrics($user);
        $firstWorkout = $user->workouts()->completed()->oldest('date')->first();

        return view('medical.index', compact(
            'user',
            'documents',
            'fitnessCertificate',
            'doctors',
            'documentGroups',
            'totalMetrics',
            'yearlyMetrics',
            'firstWorkout'
        ));
    }

    public function store(StoreMedicalDocumentRequest $request): RedirectResponse
    {
        $file = $request->file('document');
        $path = $file->store('medical/'.$request->user()->id, 'local');

        $request->user()->medicalDocuments()->create([
            'doctor_id' => $request->doctor_id ?: null,
            'type' => $request->type,
            'title' => $request->title,
            'notes' => $request->notes,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'images_url' => $request->images_url ?: null,
            'issued_at' => $request->issued_at ?: null,
            'expires_at' => $request->expires_at ?: null,
        ]);

        return back()->with('status', 'document-uploaded');
    }

    public function update(UpdateMedicalDocumentRequest $request, MedicalDocument $document): RedirectResponse
    {
        $data = [
            'doctor_id' => $request->doctor_id ?: null,
            'type' => $request->type,
            'title' => $request->title,
            'notes' => $request->notes,
            'images_url' => $request->images_url ?: null,
            'issued_at' => $request->issued_at ?: null,
            'expires_at' => $request->expires_at ?: null,
        ];

        if ($request->hasFile('document')) {
            Storage::disk('local')->delete($document->file_path);

            $file = $request->file('document');
            $data['file_path'] = $file->store('medical/'.$document->user_id, 'local');
            $data['original_name'] = $file->getClientOriginalName();
        }

        $document->update($data);

        return back()->with('status', 'document-updated');
    }

    public function download(MedicalDocument $document): StreamedResponse
    {
        abort_if($document->user_id !== auth()->id(), 403);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function preview(MedicalDocument $document): \Symfony\Component\HttpFoundation\Response
    {
        abort_if($document->user_id !== auth()->id(), 403);

        return Storage::disk('local')->response($document->file_path, $document->original_name, [
            'Content-Disposition' => 'inline; filename="'.$document->original_name.'"',
        ]);
    }

    public function destroy(MedicalDocument $document): RedirectResponse
    {
        abort_if($document->user_id !== auth()->id(), 403);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('status', 'document-deleted');
    }

    public function report(Request $request): View
    {
        $user = $request->user();
        $report = $this->reportService->getMedicalReport($user);
        $fitnessCertificate = $user->medicalDocuments()
            ->where('type', MedicalDocumentType::FitnessCertificate)
            ->orderByDesc('issued_at')
            ->first();

        return view('medical.report', compact('report', 'fitnessCertificate'));
    }

    public function exportReportPDF(Request $request): \Illuminate\Http\Response
    {
        $user = $request->user();
        $report = $this->reportService->getMedicalReport($user);
        $fitnessCertificate = $user->medicalDocuments()
            ->where('type', MedicalDocumentType::FitnessCertificate)
            ->orderByDesc('issued_at')
            ->first();

        $pdf = Pdf::loadView('medical.pdf.report', compact('report', 'user', 'fitnessCertificate'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'reporte-medico-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    public function shareReport(Request $request): JsonResponse
    {
        $user = $request->user();

        $share = ReportShare::createShare(
            userId: $user->id,
            reportType: 'medical',
            year: 0,
            period: 0,
            hoursValid: 168,
        );

        return response()->json([
            'success' => true,
            'url' => $share->getShareUrl(),
            'expires_at' => $share->expires_at->format('d/m/Y H:i'),
        ]);
    }
}
