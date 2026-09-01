<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\MedicalDocument;
use App\Models\MedicalDocumentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalDocumentGroupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_group_with_selected_documents(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create();
        $documents = MedicalDocument::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('medical.groups.store'), [
            'title' => 'Estudios pre-consulta',
            'doctor_id' => $doctor->id,
            'document_ids' => $documents->pluck('id')->toArray(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'group-created');

        $this->assertDatabaseHas('medical_document_groups', [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'title' => 'Estudios pre-consulta',
        ]);

        $group = MedicalDocumentGroup::first();
        $this->assertCount(2, $group->documents);
    }

    public function test_store_requires_at_least_one_document(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.groups.store'), [
            'title' => 'Sin estudios',
            'document_ids' => [],
        ]);

        $response->assertSessionHasErrors(['document_ids']);
    }

    public function test_store_rejects_documents_from_other_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $foreignDocument = MedicalDocument::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->post(route('medical.groups.store'), [
            'title' => 'Estudios',
            'document_ids' => [$foreignDocument->id],
        ]);

        $response->assertSessionHasErrors(['document_ids.0']);
    }

    public function test_share_generates_valid_link(): void
    {
        $user = User::factory()->create();
        $group = MedicalDocumentGroup::factory()->for($user)->create();

        $response = $this->actingAs($user)->postJson(route('medical.groups.share', $group));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('report_shares', [
            'user_id' => $user->id,
            'report_type' => 'medical_documents_group',
            'period' => $group->id,
        ]);
    }

    public function test_share_is_forbidden_for_other_users(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $group = MedicalDocumentGroup::factory()->for($owner)->create();

        $response = $this->actingAs($other)->postJson(route('medical.groups.share', $group));

        $response->assertForbidden();
    }

    public function test_shared_group_is_accessible_without_auth(): void
    {
        $user = User::factory()->create();
        $group = MedicalDocumentGroup::factory()->for($user)->create();
        $document = MedicalDocument::factory()->create(['user_id' => $user->id]);
        $group->documents()->attach($document);

        $share = \App\Models\ReportShare::createShare(
            userId: $user->id,
            reportType: 'medical_documents_group',
            year: 0,
            period: $group->id,
            hoursValid: 168,
        );

        $response = $this->get(route('reports.shared', $share->token));

        $response->assertOk();
        $response->assertViewIs('medical.public.documents-group');
    }

    public function test_download_zip_contains_group_documents(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $group = MedicalDocumentGroup::factory()->for($user)->create();
        $file = UploadedFile::fake()->create('estudio.pdf', 100, 'application/pdf');
        $path = $file->store('medical/'.$user->id, 'local');
        $document = MedicalDocument::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'original_name' => 'estudio.pdf',
        ]);
        $group->documents()->attach($document);

        $response = $this->actingAs($user)->get(route('medical.groups.zip', $group));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/zip');
    }

    public function test_destroy_deletes_group_without_deleting_documents(): void
    {
        $user = User::factory()->create();
        $group = MedicalDocumentGroup::factory()->for($user)->create();
        $document = MedicalDocument::factory()->create(['user_id' => $user->id]);
        $group->documents()->attach($document);

        $response = $this->actingAs($user)->delete(route('medical.groups.destroy', $group));

        $response->assertRedirect();
        $this->assertModelMissing($group);
        $this->assertModelExists($document);
    }
}
