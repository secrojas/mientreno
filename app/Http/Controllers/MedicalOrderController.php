<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalOrderRequest;
use App\Http\Requests\UpdateMedicalOrderRequest;
use App\Models\MedicalOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MedicalOrderController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = $user->medicalOrders()
            ->with('doctor')
            ->orderByDesc('issued_at')
            ->orderByDesc('created_at')
            ->get();

        $doctors = $user->doctors()->orderBy('name')->get();

        return view('medical.orders', compact('orders', 'doctors'));
    }

    public function store(StoreMedicalOrderRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $path = $file->store('medical-orders/'.$request->user()->id, 'local');

        $request->user()->medicalOrders()->create([
            'doctor_id' => $request->doctor_id ?: null,
            'title' => $request->title,
            'notes' => $request->notes,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'issued_at' => $request->issued_at ?: null,
        ]);

        return back()->with('status', 'order-uploaded');
    }

    public function update(UpdateMedicalOrderRequest $request, MedicalOrder $order): RedirectResponse
    {
        $data = [
            'doctor_id' => $request->doctor_id ?: null,
            'title' => $request->title,
            'notes' => $request->notes,
            'issued_at' => $request->issued_at ?: null,
        ];

        if ($request->hasFile('file')) {
            Storage::disk('local')->delete($order->file_path);

            $file = $request->file('file');
            $data['file_path'] = $file->store('medical-orders/'.$order->user_id, 'local');
            $data['original_name'] = $file->getClientOriginalName();
        }

        $order->update($data);

        return back()->with('status', 'order-updated');
    }

    public function download(MedicalOrder $order): StreamedResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return Storage::disk('local')->download($order->file_path, $order->original_name);
    }

    public function preview(MedicalOrder $order): Response
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return Storage::disk('local')->response($order->file_path, $order->original_name, [
            'Content-Disposition' => 'inline; filename="'.$order->original_name.'"',
        ]);
    }

    public function destroy(MedicalOrder $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        Storage::disk('local')->delete($order->file_path);
        $order->delete();

        return back()->with('status', 'order-deleted');
    }
}
