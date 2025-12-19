<?php

namespace App\Policies;

use App\Models\TrainingGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TrainingGroupPolicy
{
    /**
     * Determine whether the user can view any models.
     * Solo coaches y admins pueden ver grupos
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['coach', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     * Solo pueden ver grupos de su propio business
     */
    public function view(User $user, TrainingGroup $trainingGroup): bool
    {
        // Admins pueden ver cualquier grupo
        if ($user->role === 'admin') {
            return true;
        }

        // Coaches solo pueden ver grupos de su business
        return $user->business_id === $trainingGroup->business_id;
    }

    /**
     * Determine whether the user can create models.
     * Solo coaches con business pueden crear grupos
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['coach', 'admin']) && $user->business_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     * Solo el coach dueño del business puede actualizar
     */
    public function update(User $user, TrainingGroup $trainingGroup): bool
    {
        // Admins pueden actualizar cualquier grupo
        if ($user->role === 'admin') {
            return true;
        }

        // Coaches solo pueden actualizar grupos de su business
        return $user->business_id === $trainingGroup->business_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Solo el coach dueño del business puede eliminar
     */
    public function delete(User $user, TrainingGroup $trainingGroup): bool
    {
        // Admins pueden eliminar cualquier grupo
        if ($user->role === 'admin') {
            return true;
        }

        // Coaches solo pueden eliminar grupos de su business
        return $user->business_id === $trainingGroup->business_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrainingGroup $trainingGroup): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Solo admins pueden hacer force delete
     */
    public function forceDelete(User $user, TrainingGroup $trainingGroup): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can manage members.
     * Solo el coach del business puede gestionar miembros
     */
    public function manageMembers(User $user, TrainingGroup $trainingGroup): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->business_id === $trainingGroup->business_id;
    }
}
