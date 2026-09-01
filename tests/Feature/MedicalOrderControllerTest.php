<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\MedicalOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicalOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $response = $this->get(route('medical.orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_index_shows_only_user_orders(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $ownOrder = MedicalOrder::factory()->create(['user_id' => $user->id]);
        MedicalOrder::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->get(route('medical.orders.index'));

        $response->assertOk();
        $response->assertViewIs('medical.orders');
        $response->assertViewHas('orders', function ($orders) use ($ownOrder, $other) {
            return $orders->contains($ownOrder)
                && $orders->doesntContain(fn ($o) => $o->user_id === $other->id);
        });
    }

    public function test_store_uploads_photo_order_successfully(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create();
        $file = UploadedFile::fake()->image('orden.jpg');

        $response = $this->actingAs($user)->post(route('medical.orders.store'), [
            'title' => 'Orden análisis de sangre',
            'file' => $file,
            'doctor_id' => $doctor->id,
            'issued_at' => '2026-08-01',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'order-uploaded');

        $this->assertDatabaseHas('medical_orders', [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'title' => 'Orden análisis de sangre',
        ]);
    }

    public function test_store_accepts_pdf_orders(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('orden.pdf', 200, 'application/pdf');

        $response = $this->actingAs($user)->post(route('medical.orders.store'), [
            'title' => 'Orden doppler',
            'file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('medical_orders', [
            'user_id' => $user->id,
            'title' => 'Orden doppler',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('medical.orders.store'), []);

        $response->assertSessionHasErrors(['title', 'file']);
    }

    public function test_store_rejects_unsupported_file_types(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('orden.txt', 10, 'text/plain');

        $response = $this->actingAs($user)->post(route('medical.orders.store'), [
            'title' => 'Orden',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_update_modifies_order_fields(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $doctor = Doctor::factory()->for($user)->create();
        $file = UploadedFile::fake()->image('orden.jpg');
        $path = $file->store('medical-orders/'.$user->id, 'local');

        $order = MedicalOrder::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'title' => 'Título viejo',
        ]);

        $response = $this->actingAs($user)->put(route('medical.orders.update', $order), [
            'title' => 'Orden actualizada',
            'doctor_id' => $doctor->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'order-updated');
        $this->assertDatabaseHas('medical_orders', [
            'id' => $order->id,
            'title' => 'Orden actualizada',
            'doctor_id' => $doctor->id,
        ]);
    }

    public function test_update_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->image('orden.jpg');
        $path = $file->store('medical-orders/'.$owner->id, 'local');

        $order = MedicalOrder::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->put(route('medical.orders.update', $order), [
            'title' => 'Hackeado',
        ]);

        $response->assertForbidden();
        $this->assertNotSame('Hackeado', $order->fresh()->title);
    }

    public function test_preview_serves_file_inline_to_owner(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('orden.jpg');
        $path = $file->store('medical-orders/'.$user->id, 'local');

        $order = MedicalOrder::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'original_name' => 'orden.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('medical.orders.preview', $order));

        $response->assertOk();
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_preview_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->image('orden.jpg');
        $path = $file->store('medical-orders/'.$owner->id, 'local');

        $order = MedicalOrder::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->get(route('medical.orders.preview', $order));

        $response->assertForbidden();
    }

    public function test_destroy_deletes_order(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('orden.jpg');
        $path = $file->store('medical-orders/'.$user->id, 'local');

        $order = MedicalOrder::factory()->create([
            'user_id' => $user->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($user)->delete(route('medical.orders.destroy', $order));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'order-deleted');
        $this->assertModelMissing($order);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_destroy_is_forbidden_for_other_users(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = UploadedFile::fake()->image('orden.jpg');
        $path = $file->store('medical-orders/'.$owner->id, 'local');

        $order = MedicalOrder::factory()->create([
            'user_id' => $owner->id,
            'file_path' => $path,
        ]);

        $response = $this->actingAs($other)->delete(route('medical.orders.destroy', $order));

        $response->assertForbidden();
        $this->assertModelExists($order);
    }
}
