<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    /**
     * Solo Super Admin puede listar todas las empresas de la plataforma.
     */
    public function viewAny(User $user): bool
    {
        return $user->status === 'active' && $user->isSuperAdmin();
    }

    /**
     * Ver datos de la empresa si el usuario es miembro activo o Super Admin.
     */
    public function view(User $user, Business $business): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->status === 'active' && $user->belongsToBusiness($business->id);
    }

    /**
     * Solo Super Admin puede crear empresas adicionales a nivel de plataforma.
     */
    public function create(User $user): bool
    {
        return $user->status === 'active' && $user->isSuperAdmin();
    }

    /**
     * Solo el Propietario del negocio (owner) o el Super Admin pueden modificar la configuración del negocio.
     * Un administrador regular o cajero no puede alterar la configuración fiscal/propietario.
     */
    public function update(User $user, Business $business): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->status === 'active'
            && $user->belongsToBusiness($business->id)
            && $user->isOwner($business->id);
    }

    /**
     * Solo el Super Admin puede suspender o eliminar empresas de la plataforma.
     */
    public function delete(User $user, Business $business): bool
    {
        return $user->status === 'active' && $user->isSuperAdmin();
    }
}
