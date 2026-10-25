<?php

namespace App\Policies;

use App\Models\User;
use App\Services\TenantManager;

class UserPolicy
{
    /**
     * Determina el ID del negocio activo para evaluar permisos contextuales.
     */
    protected function resolveBusinessId(User $actor): ?int
    {
        return app(TenantManager::class)->getTenantId() ?: $actor->current_business_id;
    }

    /**
     * Ver listado de usuarios (Super Admin, Propietario o Administrador).
     */
    public function viewAny(User $actor): bool
    {
        return $actor->status === 'active' && $actor->isAdmin();
    }

    /**
     * Ver un usuario específico dentro de la misma empresa.
     */
    public function view(User $actor, User $target): bool
    {
        if ($actor->isSuperAdmin()) {
            return true;
        }

        $businessId = $this->resolveBusinessId($actor);

        return $actor->status === 'active'
            && $actor->isAdmin($businessId)
            && $target->belongsToBusiness($businessId);
    }

    /**
     * Crear nuevos usuarios en la empresa (Super Admin, Propietario o Administrador).
     */
    public function create(User $actor): bool
    {
        return $actor->status === 'active' && $actor->isAdmin();
    }

    /**
     * Valida si el actor puede asignar un rol determinado:
     * - Super Admin: puede asignar super_admin, owner, admin, cajero.
     * - Propietario (owner): puede asignar owner, admin, cajero.
     * - Administrador (admin): solo puede asignar cajero (no puede escalar privilegios a owner/admin).
     */
    public function assignRole(User $actor, string $roleToAssign): bool
    {
        if ($actor->status !== 'active') {
            return false;
        }

        if ($actor->isSuperAdmin()) {
            return in_array($roleToAssign, [
                User::ROLE_SUPER_ADMIN,
                User::ROLE_OWNER,
                User::ROLE_ADMIN,
                User::ROLE_CASHIER,
            ], true);
        }

        $businessId = $this->resolveBusinessId($actor);

        if ($actor->isOwner($businessId)) {
            return in_array($roleToAssign, [
                User::ROLE_OWNER,
                User::ROLE_ADMIN,
                User::ROLE_CASHIER,
            ], true);
        }

        if ($actor->isAdmin($businessId)) {
            return $roleToAssign === User::ROLE_CASHIER;
        }

        return false;
    }

    /**
     * Actualizar un usuario:
     * - Super Admin puede editar cualquier cuenta.
     * - Propietario puede editar administradores y cajeros de su negocio (no a un Super Admin).
     * - Administrador regular solo puede editar cajeros de su negocio (o su propio perfil básico), nunca al Propietario ni a otro Admin.
     */
    public function update(User $actor, User $target): bool
    {
        if ($actor->status !== 'active') {
            return false;
        }

        if ($actor->isSuperAdmin()) {
            return true;
        }

        if ($target->isSuperAdmin()) {
            return false;
        }

        $businessId = $this->resolveBusinessId($actor);
        if (! $target->belongsToBusiness($businessId)) {
            return false;
        }

        if ($actor->isOwner($businessId)) {
            if ($target->roleInBusiness($businessId) === User::ROLE_OWNER && $actor->id !== $target->id) {
                return false;
            }

            return true;
        }

        if ($actor->isAdmin($businessId)) {
            if ($actor->id === $target->id) {
                return true;
            }

            return $target->roleInBusiness($businessId) === User::ROLE_CASHIER;
        }

        return false;
    }

    /**
     * Eliminar / desvincular un usuario:
     * - Nadie puede auto-eliminarse en sesión activa.
     * - Nadie puede eliminar a un Super Admin.
     * - Un Propietario puede eliminar Administradores y Cajeros de su empresa, pero no a otro Propietario.
     * - Un Administrador regular solo puede eliminar Cajeros de su empresa (no puede eliminar Propietarios ni Administradores).
     */
    public function delete(User $actor, User $target): bool
    {
        if ($actor->status !== 'active' || $actor->id === $target->id) {
            return false;
        }

        if ($target->isSuperAdmin()) {
            return false;
        }

        if ($actor->isSuperAdmin()) {
            return true;
        }

        $businessId = $this->resolveBusinessId($actor);
        if (! $target->belongsToBusiness($businessId)) {
            return false;
        }

        $targetRole = $target->roleInBusiness($businessId);

        // El propietario del negocio no puede ser eliminado por administradores ni otros propietarios
        if ($targetRole === User::ROLE_OWNER) {
            return false;
        }

        if ($actor->isOwner($businessId)) {
            return in_array($targetRole, [User::ROLE_ADMIN, User::ROLE_CASHIER], true);
        }

        if ($actor->isAdmin($businessId)) {
            return $targetRole === User::ROLE_CASHIER;
        }

        return false;
    }
}
