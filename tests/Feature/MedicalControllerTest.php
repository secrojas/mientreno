<?php

namespace Tests\Feature;

use App\Enums\MedicalDocumentType;
use App\Models\Doctor;
use App\Models\MedicalDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('medical.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_is_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('medical.index'));

        $response->assertOk();
        $response->assertViewIs('medical.index');
    }

    public function test_index_shows_only_user_documents(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Storage::fake('local');
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$user->id, 'local');

        $ownDoc = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
        ]);

        MedicalDocument::factory()->create([
            'user_id' => $other->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($user)->get(route('medical.index'));

        $response->assertOk();
        $response->assertViewHas('documents', function ($documents) use ($ownDoc, $other) {
            return $documents->contains($ownDoc)
                && $documents->doesntContain(fn ($d) => $d->user_id === $other->id);
        });
    }

    public function test_store_uploads_document_successfully(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('resultado.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('medical.documents.store'), [
            'type' => MedicalDocumentType::BloodTest->value,
            'title' => 'Análisis de sangre junio 2026',
            'document' => $file,
            'issued_at' => '2026-06-01',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'document-uploaded');

        $this->assertDatabaseHas('medical_documents', [
            'user_id' => $user->id,
            'type' => MedicalDocumentType::BloodTest->value,
            'title' => 'Análisis de sangre junio 2026',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.documents.store'), []);

        $response->assertSessionHasErrors(['type', 'title', 'document']);
    }

    public function test_store_rejects_non_pdf_files(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($user)->post(route('medical.documents.store'), [
            'type' => MedicalDocumentType::BloodTest->value,
            'title' => 'Test',
            'document' => $file,
        ]);

        $response->assertSessionHasErrors('document');
    }

    public function test_download_serves_file_to_owner(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$user->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'original_name' => 'test.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('medical.documents.download', $document));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_download_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$owner->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->get(route('medical.documents.download', $document));

        $response->assertForbidden();
    }

    public function test_destroy_deletes_document(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$user->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($user)->delete(route('medical.documents.destroy', $document));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'document-deleted');
        $this->assertModelMissing($document);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_destroy_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$owner->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->delete(route('medical.documents.destroy', $document));

        $response->assertForbidden();
        $this->assertModelExists($document);
    }

    public function test_preview_serves_file_inline_to_owner(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$user->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'original_name' => 'test.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('medical.documents.preview', $document));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_preview_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$owner->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->get(route('medical.documents.preview', $document));

        $response->assertForbidden();
    }

    public function test_update_modifies_document_fields(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create();
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$user->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'title' => 'Título viejo',
        ]);

        $response = $this->actingAs($user)->put(route('medical.documents.update', $document), [
            'type' => MedicalDocumentType::ChestXray->value,
            'title' => 'Placa de tórax 2026',
            'doctor_id' => $doctor->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'document-updated');

        $this->assertDatabaseHas('medical_documents', [
            'id' => $document->id,
            'type' => MedicalDocumentType::ChestXray->value,
            'title' => 'Placa de tórax 2026',
            'doctor_id' => $doctor->id,
        ]);
    }

    public function test_update_replaces_file_when_new_document_uploaded(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $oldFile = UploadedFile::fake()->create('old.pdf', 100, 'application/pdf');
        $oldPath = $oldFile->store('medical/'.$user->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $oldPath,
        ]);

        $newFile = UploadedFile::fake()->create('new.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->put(route('medical.documents.update', $document), [
            'type' => $document->type->value,
            'title' => $document->title,
            'document' => $newFile,
        ]);

        $response->assertRedirect();
        Storage::disk('local')->assertMissing($oldPath);
        $this->assertNotSame($oldPath, $document->fresh()->file_path);
    }

    public function test_update_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$owner->id, 'local');

        $document = MedicalDocument::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->put(route('medical.documents.update', $document), [
            'type' => $document->type->value,
            'title' => 'Hackeado',
        ]);

        $response->assertForbidden();
        $this->assertNotSame('Hackeado', $document->fresh()->title);
    }

    public function test_store_accepts_optional_doctor_id(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create();
        $file = UploadedFile::fake()->create('resultado.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post(route('medical.documents.store'), [
            'type' => MedicalDocumentType::AbdominalUltrasound->value,
            'title' => 'Ecografía abdominal',
            'document' => $file,
            'doctor_id' => $doctor->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('medical_documents', [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'type' => MedicalDocumentType::AbdominalUltrasound->value,
        ]);
    }
}
