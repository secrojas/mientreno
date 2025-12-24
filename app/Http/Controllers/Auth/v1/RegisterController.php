<?php

namespace App\Http\Controllers\Auth\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v1\RegisterRequest;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        // Verificar si hay token de invitación
        $invitationToken = $request->query('invitation');
        $businessName = null;

        if ($invitationToken) {
            $businessId = $this->decodeInvitationToken($invitationToken);
            if ($businessId) {
                $business = Business::find($businessId);
                $businessName = $business?->name;
            }
        }

        return view('auth.register', [
            'invitationToken' => $invitationToken,
            'businessName' => $businessName,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // Determinar business_id
        $businessId = null;
        if ($request->input('invitation_token')) {
            $businessId = $this->decodeInvitationToken($request->input('invitation_token'));
        }

        // VALIDACIÓN DE LÍMITES: Verificar si el business puede agregar más estudiantes
        if ($businessId) {
            $business = Business::find($businessId);

            if ($business && !$business->canAddStudents(1)) {
                return back()->withErrors([
                    'invitation_token' => 'Este negocio ' . lcfirst(subscriptionLimitMessage('students', $business)) .
                                         ' El coach debe actualizar su plan para poder agregar más alumnos.'
                ])->withInput();
            }
        }

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'business_id' => $businessId,
            'role'        => $data['role'], // Ya no usamos fallback porque role es required
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Decodifica un token de invitación y retorna el business_id
     */
    private function decodeInvitationToken(?string $token): ?int
    {
        if (!$token) {
            return null;
        }

        try {
            // Decodificar token (base64 + business_id)
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);

            if (count($parts) === 2 && $parts[0] === 'business') {
                return (int) $parts[1];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Genera un token de invitación para un business
     * Uso: RegisterController::generateInvitationToken($businessId)
     */
    public static function generateInvitationToken(int $businessId): string
    {
        return base64_encode("business:{$businessId}");
    }
}
