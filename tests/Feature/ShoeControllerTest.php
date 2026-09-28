<?php

namespace Tests\Feature;

use App\Enums\ShoeCondition;
use App\Enums\ShoeUsage;
use App\Models\Shoe;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShoeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_authentication(): void
    {
        $this->get(route('shoes.index'))->assertRedirect(route('login'));
    }

    public function test_index_separates_active_and_retired_shoes_of_the_user(): void
    {
        $user = User::factory()->create();
        $active = Shoe::factory()->for($user)->create();
        $retired = Shoe::factory()->for($user)->retired()->create();
        Shoe::factory()->create();

        $response = $this->actingAs($user)->get(route('shoes.index'));

        $response->assertOk();
        $response->assertViewIs('shoes.index');
        $response->assertViewHas('activeShoes', fn ($shoes) => $shoes->pluck('id')->values()->all() === [$active->id]);
        $response->assertViewHas('retiredShoes', fn ($shoes) => $shoes->pluck('id')->values()->all() === [$retired->id]);
    }

    public function test_index_warns_about_worn_shoes(): void
    {
        $user = User::factory()->create();
        $shoe = Shoe::factory()->for($user)->create(['brand' => 'Brooks', 'model' => 'Ghost 16', 'max_km' => 500, 'initial_km' => 480]);
        Workout::factory()->completed()->for($user)->create(['shoe_id' => $shoe->id, 'distance' => 30]);

        $response = $this->actingAs($user)->get(route('shoes.index'));

        $response->assertViewHas('shoesNeedingAttention', fn ($shoes) => $shoes->pluck('id')->all() === [$shoe->id]);
        $response->assertSee('Tus Brooks Ghost 16 superaron su vida útil');
    }

    public function test_store_creates_shoe_and_makes_first_one_default(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shoes.store'), [
            'brand' => 'Nike',
            'model' => 'Pegasus 41',
            'nickname' => 'Las rojas',
            'color' => '#ff3b5c',
            'usage' => ShoeUsage::Daily->value,
            'purchased_at' => '2026-05-10',
            'price' => '180000',
            'initial_km' => '25.5',
            'max_km' => 800,
        ]);

        $response->assertRedirect(route('shoes.index'));
        $response->assertSessionHas('status', 'shoe-created');

        $shoe = $user->shoes()->first();
        $this->assertSame('Pegasus 41', $shoe->model);
        $this->assertSame('#FF3B5C', $shoe->color);
        $this->assertSame(ShoeUsage::Daily, $shoe->usage);
        $this->assertSame(800, $shoe->max_km);
        $this->assertEquals(25.5, $shoe->totalKm());
        $this->assertTrue($shoe->is_default);
    }

    public function test_store_with_default_flag_unsets_previous_default(): void
    {
        $user = User::factory()->create();
        $previous = Shoe::factory()->for($user)->default()->create();

        $this->actingAs($user)->post(route('shoes.store'), [
            'brand' => 'ASICS',
            'model' => 'Novablast 5',
            'color' => '#2DE38E',
            'max_km' => 700,
            'is_default' => '1',
        ]);

        $this->assertFalse($previous->fresh()->is_default);
        $this->assertTrue($user->shoes()->where('model', 'Novablast 5')->first()->is_default);
    }

    public function test_store_second_shoe_without_flag_keeps_existing_default(): void
    {
        $user = User::factory()->create();
        $previous = Shoe::factory()->for($user)->default()->create();

        $this->actingAs($user)->post(route('shoes.store'), [
            'brand' => 'Hoka',
            'model' => 'Clifton 10',
            'color' => '#60A5FA',
            'max_km' => 700,
        ]);

        $this->assertTrue($previous->fresh()->is_default);
        $this->assertFalse($user->shoes()->where('model', 'Clifton 10')->first()->is_default);
    }

    public function test_store_validates_input(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shoes.store'), [
            'brand' => '',
            'model' => '',
            'color' => 'red',
            'max_km' => 50,
            'purchased_at' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors([
            'brand' => 'Indicá la marca.',
            'model' => 'Indicá el modelo.',
            'color' => 'Elegí un color válido.',
            'max_km' => 'La vida útil debe ser de al menos 100 km.',
            'purchased_at' => 'La fecha de compra no puede ser futura.',
        ]);
    }

    public function test_store_saves_photo_privately_and_serves_it_only_to_the_owner(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('shoes.store'), [
            'brand' => 'Saucony',
            'model' => 'Endorphin Speed 4',
            'color' => '#F59E0B',
            'max_km' => 600,
            'photo' => UploadedFile::fake()->image('zapas.jpg'),
        ]);

        $shoe = $user->shoes()->first();
        Storage::disk('local')->assertExists($shoe->photo_path);

        $this->actingAs($user)->get(route('shoes.photo', $shoe))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('shoes.photo', $shoe))->assertForbidden();
    }

    public function test_update_changes_data_and_can_remove_photo(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('vieja.jpg')->store('shoes/'.$user->id, 'local');
        $shoe = Shoe::factory()->for($user)->create(['photo_path' => $path, 'max_km' => 700]);

        $response = $this->actingAs($user)->put(route('shoes.update', $shoe), [
            'brand' => $shoe->brand,
            'model' => $shoe->model,
            'color' => '#A78BFA',
            'max_km' => 650,
            'remove_photo' => '1',
        ]);

        $response->assertSessionHas('status', 'shoe-updated');
        $shoe->refresh();
        $this->assertSame(650, $shoe->max_km);
        $this->assertSame('#A78BFA', $shoe->color);
        $this->assertNull($shoe->photo_path);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_update_is_forbidden_for_other_users(): void
    {
        $shoe = Shoe::factory()->create();

        $response = $this->actingAs(User::factory()->create())->put(route('shoes.update', $shoe), [
            'brand' => 'X',
            'model' => 'Y',
            'color' => '#FFFFFF',
            'max_km' => 700,
        ]);

        $response->assertForbidden();
    }

    public function test_make_default_switches_default_shoe(): void
    {
        $user = User::factory()->create();
        $previous = Shoe::factory()->for($user)->default()->create();
        $shoe = Shoe::factory()->for($user)->create();

        $this->actingAs($user)->patch(route('shoes.default', $shoe))->assertRedirect();

        $this->assertTrue($shoe->fresh()->is_default);
        $this->assertFalse($previous->fresh()->is_default);
    }

    public function test_retiring_removes_default_and_reactivating_restores_it_to_active(): void
    {
        $user = User::factory()->create();
        $shoe = Shoe::factory()->for($user)->default()->create();

        $this->actingAs($user)->patch(route('shoes.retire', $shoe))->assertSessionHas('status', 'shoe-retired');
        $shoe->refresh();
        $this->assertTrue($shoe->isRetired());
        $this->assertFalse($shoe->is_default);
        $this->assertSame(ShoeCondition::Retired, $shoe->condition());

        $this->actingAs($user)->patch(route('shoes.retire', $shoe))->assertSessionHas('status', 'shoe-reactivated');
        $this->assertFalse($shoe->fresh()->isRetired());
    }

    public function test_retire_is_forbidden_for_other_users(): void
    {
        $shoe = Shoe::factory()->create();

        $this->actingAs(User::factory()->create())->patch(route('shoes.retire', $shoe))->assertForbidden();
        $this->assertFalse($shoe->fresh()->isRetired());
    }

    public function test_destroy_deletes_shoe_and_keeps_workouts(): void
    {
        $user = User::factory()->create();
        $shoe = Shoe::factory()->for($user)->create();
        $workout = Workout::factory()->completed()->for($user)->create(['shoe_id' => $shoe->id]);

        $this->actingAs($user)->delete(route('shoes.destroy', $shoe))->assertRedirect();

        $this->assertModelMissing($shoe);
        $this->assertNull($workout->fresh()->shoe_id);
    }

    public function test_destroy_is_forbidden_for_other_users(): void
    {
        $shoe = Shoe::factory()->create();

        $this->actingAs(User::factory()->create())->delete(route('shoes.destroy', $shoe))->assertForbidden();
        $this->assertModelExists($shoe);
    }

    public function test_km_condition_and_cost_per_km_are_calculated_from_completed_workouts(): void
    {
        $user = User::factory()->create();
        $shoe = Shoe::factory()->for($user)->create(['initial_km' => 100, 'max_km' => 500, 'price' => 100000]);
        Workout::factory()->completed()->for($user)->create(['shoe_id' => $shoe->id, 'distance' => 250]);
        Workout::factory()->completed()->for($user)->create(['shoe_id' => $shoe->id, 'distance' => 50]);
        Workout::factory()->planned()->for($user)->create(['shoe_id' => $shoe->id, 'distance' => 200]);

        $shoe = Shoe::withUsageStats()->find($shoe->id);

        $this->assertEquals(400, $shoe->totalKm());
        $this->assertEquals(100, $shoe->remainingKm());
        $this->assertSame(2, $shoe->completed_sessions);
        $this->assertSame(ShoeCondition::NearLimit, $shoe->condition());
        $this->assertEquals(250, $shoe->costPerKm());
    }

    public function test_dashboard_shows_alert_for_shoes_near_the_limit(): void
    {
        $user = User::factory()->create();
        Shoe::factory()->for($user)->create(['brand' => 'Nike', 'model' => 'Pegasus 41', 'initial_km' => 700, 'max_km' => 800]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Tus Nike Pegasus 41 se acercan al límite');
    }
}
