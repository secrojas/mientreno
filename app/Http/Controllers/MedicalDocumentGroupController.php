<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalDocumentGroupRequest;
use App\Models\MedicalDocument;
use App\Models\MedicalDocumentGroup;
use App\Models\ReportShare;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class MedicalDocumentGroupController extends Controller
{
    public function store(StoreMedicalDocumentGroupRequest $request): RedirectResponse
    {
        $group = $request->user()->medicalDocumentGroups()->create([
            'doctor_id' => $request->doctor_id ?: null,
            'title' => $request->title,
            'notes' => $request->notes,
        ]);

        $group->documents()->sync($request->document_ids);

        return back()->with('status', 'group-created');
    }

    public function share(MedicalDocumentGroup $group): JsonResponse
    {
        abort_if($group->user_id !== auth()->id(), 403);

        $share = ReportShare::createShare(
            userId: $group->user_id,
            reportType: 'medical_documents_group',
            year: 0,
            period: $group->id,
            hoursValid: 168,
        );

        return response()->json([
            'success' => true,
            'url' => $share->getShareUrl(),
            'expires_at' => $share->expires_at->format('d/m/Y H:i'),
        ]);
    }

    public function downloadZip(MedicalDocumentGroup $group): Response
    {
        abort_if($group->user_id !== auth()->id(), 403);

        return $this->buildZipResponse($group);
    }

    public function downloadSharedZip(string $token): Response
    {
        $share = ReportShare::findValidByToken($token);

        abort_if(! $share || $share->report_type !== 'medical_documents_group', 404);

        $group = MedicalDocumentGroup::findOrFail($share->period);

        return $this->buildZipResponse($group);
    }

    public function previewSharedDocument(string $token, MedicalDocument $document): Response
    {
        $share = ReportShare::findValidByToken($token);

        abort_if(! $share || $share->report_type !== 'medical_documents_group', 404);

        $group = MedicalDocumentGroup::findOrFail($share->period);

        abort_unless($group->documents()->where('medical_documents.id', $document->id)->exists(), 404);

        return Storage::disk('local')->response($document->file_path, $document->original_name, [
            'Content-Disposition' => 'inline; filename="'.$document->original_name.'"',
        ]);
    }

    public function destroy(MedicalDocumentGroup $group): RedirectResponse
    {
        abort_if($group->user_id !== auth()->id(), 403);

        $group->delete();

        return back()->with('status', 'group-deleted');
    }

    private function buildZipResponse(MedicalDocumentGroup $group): Response
    {
        $documents = $group->documents()->get();

        $zipPath = Storage::disk('local')->path('tmp/estudios-'.$group->id.'-'.uniqid().'.zip');

        if (! is_dir(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($documents as $index => $document) {
            $localPath = Storage::disk('local')->path($document->file_path);

            if (is_file($localPath)) {
                $zip->addFile($localPath, ($index + 1).' - '.$document->original_name);
            }
        }

        $zip->close();

        $filename = 'estudios-'.str($group->title)->slug().'.zip';

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }
}
