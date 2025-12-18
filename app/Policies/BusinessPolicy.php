<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Solo coaches y admins pueden ver businesses
        return in_array($user->role, ['coach', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Business $business): bool
    {
        // Solo el dueño o admin puede ver el business
        return $user->id === $business->owner_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo coaches sin business pueden crear
        return in_array($user->role, ['coach', 'admin']) && !$user->business_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): bool
    {
        // Solo el dueño puede actualizar
        return $user->id === $business->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Business $business): bool
    {
        // Solo el dueño puede eliminar
        return $user->id === $business->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Business $business): bool
    {
        // Solo el dueño puede restaurar
        return $user->id === $business->owner_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Business $business): bool
    {
        // Solo admins pueden eliminar permanentemente
        return $user->role === 'admin';
    }
}
