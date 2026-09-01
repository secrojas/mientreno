<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;

class DoctorController extends Controller
{
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $request->user()->doctors()->create($request->validated());

        return back()->with('status', 'doctor-created');
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update($request->validated());

        return back()->with('status', 'doctor-updated');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        abort_if($doctor->user_id !== auth()->id(), 403);

        $doctor->delete();

        return back()->with('status', 'doctor-deleted');
    }
}
