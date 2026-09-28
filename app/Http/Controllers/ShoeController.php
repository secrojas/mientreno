<?php

namespace App\Http\Controllers;

use App\Enums\ShoeCondition;
use App\Http\Requests\StoreShoeRequest;
use App\Http\Requests\UpdateShoeRequest;
use App\Models\Shoe;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ShoeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $shoes = $user->shoes()
            ->withUsageStats()
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        [$retiredShoes, $activeShoes] = $shoes->partition(fn (Shoe $shoe) => $shoe->isRetired());

        $shoesNeedingAttention = $activeShoes->filter(
            fn (Shoe $shoe) => in_array($shoe->condition(), [ShoeCondition::NearLimit, ShoeCondition::Worn], true)
        );

        $rotation = $this->rotationByShoe($user);
        $catalog = collect(config('running_shoes.models'))->sortBy(['brand', 'model'])->values();
        $colors = config('running_shoes.colors');

        return view('shoes.index', compact(
            'activeShoes',
            'retiredShoes',
            'shoesNeedingAttention',
            'rotation',
            'catalog',
            'colors',
        ));
    }

    public function store(StoreShoeRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $this->shoeData($request);
        $data['is_default'] = $request->boolean('is_default') || ! $user->shoes()->active()->exists();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('shoes/'.$user->id, 'local');
        }

        $shoe = $user->shoes()->create($data);

        if ($shoe->is_default) {
            $this->makeOnlyDefault($shoe);
        }

        return redirect()->route('shoes.index')->with('status', 'shoe-created');
    }

    public function update(UpdateShoeRequest $request, Shoe $shoe): RedirectResponse
    {
        $data = $this->shoeData($request);

        if ($request->boolean('remove_photo') || $request->hasFile('photo')) {
            if ($shoe->photo_path) {
                Storage::disk('local')->delete($shoe->photo_path);
            }
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('shoes/'.$shoe->user_id, 'local');
        }

        $shoe->update($data);

        if ($request->boolean('is_default') && ! $shoe->isRetired()) {
            $this->makeOnlyDefault($shoe);
        }

        return back()->with('status', 'shoe-updated');
    }

    public function makeDefault(Shoe $shoe): RedirectResponse
    {
        abort_if($shoe->user_id !== auth()->id(), 403);
        abort_if($shoe->isRetired(), 422, 'Una zapatilla retirada no puede ser la predeterminada.');

        $this->makeOnlyDefault($shoe);

        return back()->with('status', 'shoe-default');
    }

    public function toggleRetired(Shoe $shoe): RedirectResponse
    {
        abort_if($shoe->user_id !== auth()->id(), 403);

        if ($shoe->isRetired()) {
            $shoe->update(['retired_at' => null]);

            return back()->with('status', 'shoe-reactivated');
        }

        $shoe->update(['retired_at' => now(), 'is_default' => false]);

        return back()->with('status', 'shoe-retired');
    }

    public function photo(Shoe $shoe): Response
    {
        abort_if($shoe->user_id !== auth()->id(), 403);
        abort_unless($shoe->photo_path && Storage::disk('local')->exists($shoe->photo_path), 404);

        return Storage::disk('local')->response($shoe->photo_path);
    }

    public function destroy(Shoe $shoe): RedirectResponse
    {
        abort_if($shoe->user_id !== auth()->id(), 403);

        if ($shoe->photo_path) {
            Storage::disk('local')->delete($shoe->photo_path);
        }

        $shoe->delete();

        return back()->with('status', 'shoe-deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function shoeData(StoreShoeRequest $request): array
    {
        return [
            'brand' => $request->brand,
            'model' => $request->model,
            'nickname' => $request->nickname,
            'color' => strtoupper($request->color),
            'usage' => $request->usage ?: null,
            'purchased_at' => $request->purchased_at ?: null,
            'price' => $request->price ?: null,
            'initial_km' => $request->initial_km ?: 0,
            'max_km' => $request->max_km,
            'notes' => $request->notes,
        ];
    }

    private function makeOnlyDefault(Shoe $shoe): void
    {
        $shoe->user->shoes()->whereKeyNot($shoe->id)->update(['is_default' => false]);
        $shoe->update(['is_default' => true]);
    }

    /**
     * Km por tipo de entrenamiento para cada zapatilla, para ver cómo se reparte el desgaste.
     *
     * @return \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, array{type: string, label: string, km: float, percentage: float}>>
     */
    private function rotationByShoe(User $user): \Illuminate\Support\Collection
    {
        $typeLabels = Workout::typeLabels();

        return $user->workouts()
            ->completed()
            ->whereNotNull('shoe_id')
            ->selectRaw('shoe_id, type, SUM(distance) as km')
            ->groupBy('shoe_id', 'type')
            ->toBase()
            ->get()
            ->groupBy('shoe_id')
            ->map(function ($rows) use ($typeLabels) {
                $total = $rows->sum('km');

                return $rows
                    ->sortByDesc('km')
                    ->map(fn ($row) => [
                        'type' => $row->type,
                        'label' => $typeLabels[$row->type] ?? $row->type,
                        'km' => round((float) $row->km, 1),
                        'percentage' => $total > 0 ? round($row->km / $total * 100) : 0,
                    ])
                    ->values();
            });
    }
}
