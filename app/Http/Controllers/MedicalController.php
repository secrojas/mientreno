<?php

namespace App\Http\Controllers;

use App\Enums\MedicalDocumentType;
use App\Http\Requests\StoreMedicalDocumentRequest;
use App\Models\MedicalDocument;
use App\Services\MetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalController extends Controller
{
    public function __construct(private MetricsService $metricsService) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $documents = $user->medicalDocuments()
            ->orderByDesc('issued_at')
            ->orderByDesc('created_at')
            ->get();

        $fitnessCertificate = $documents
            ->where('type', MedicalDocumentType::FitnessCertificate)
            ->first();

        $totalMetrics = $this->metricsService->getTotalMetrics($user);
        $yearlyMetrics = $this->metricsService->getYearlyMetrics($user);
        $firstWorkout = $user->workouts()->completed()->oldest('date')->first();

        return view('medical.index', compact(
            'user',
            'documents',
            'fitnessCertificate',
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
            'type' => $request->type,
            'title' => $request->title,
            'notes' => $request->notes,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'issued_at' => $request->issued_at ?: null,
            'expires_at' => $request->expires_at ?: null,
        ]);

        return back()->with('status', 'document-uploaded');
    }

    public function download(MedicalDocument $document): StreamedResponse
    {
        abort_if($document->user_id !== auth()->id(), 403);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(MedicalDocument $document): RedirectResponse
    {
        abort_if($document->user_id !== auth()->id(), 403);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('status', 'document-deleted');
    }
}
