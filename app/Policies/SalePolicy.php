<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Cualquier operador activo puede acceder al módulo POS / ventas.
     */
    public function viewAny(User $user): bool
    {
        return $user->status === 'active';
    }

    /**
     * Solo se permite ver comprobantes que pertenezcan a la misma empresa (o Super Admin).
     */
    public function view(User $user, Sale $sale): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->status === 'active' && $user->belongsToBusiness($sale->business_id);
    }

    /**
     * Cualquier operador activo (Cajero, Admin, Propietario, Super Admin) puede emitir ventas.
     */
    public function create(User $user): bool
    {
        return $user->status === 'active';
    }

    /**
     * Solo Super Admin, Propietario o Administrador pueden acceder a reportes contables.
     */
    public function viewReports(User $user): bool
    {
        return $user->status === 'active' && $user->isAdmin();
    }

    /**
     * Solo Super Admin, Propietario o Administrador pueden exportar reportes financieros.
     */
    public function exportReports(User $user): bool
    {
        return $user->status === 'active' && $user->isAdmin();
    }
}
