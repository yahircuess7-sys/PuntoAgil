<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Cualquier operador activo de la empresa puede consultar el catálogo de inventario.
     */
    public function viewAny(User $user): bool
    {
        return $user->status === 'active';
    }

    /**
     * Consultar detalle de un producto de la misma empresa.
     */
    public function view(User $user, Product $product): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->status === 'active' && $user->belongsToBusiness($product->business_id);
    }

    /**
     * Solo Super Admin, Propietario (owner) o Administrador (admin) pueden registrar productos.
     * Los cajeros tienen acceso de solo lectura.
     */
    public function create(User $user): bool
    {
        return $user->status === 'active' && $user->isAdmin();
    }

    /**
     * Solo Super Admin, Propietario o Administrador del negocio pueden modificar productos.
     */
    public function update(User $user, Product $product): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->status === 'active'
            && $user->belongsToBusiness($product->business_id)
            && $user->isAdmin($product->business_id);
    }

    /**
     * Solo Super Admin, Propietario o Administrador del negocio pueden eliminar productos.
     */
    public function delete(User $user, Product $product): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->status === 'active'
            && $user->belongsToBusiness($product->business_id)
            && $user->isAdmin($product->business_id);
    }

    /**
     * Exportar catálogo completo en PDF o CSV.
     */
    public function export(User $user): bool
    {
        return $user->status === 'active';
    }
}
