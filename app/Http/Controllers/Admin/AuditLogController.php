<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    /**
     * Bitácora de auditoría protegida de solo lectura.
     * Solo accesible por el Owner del negocio (aislado a su empresa) o Super Admin.
     */
    public function index(Request $request): View
    {
        Gate::authorize('view-audit-logs');

        $actor = Auth::user();
        $businessId = $this->tenantManager->getTenantId() ?: $actor->current_business_id;

        $query = AuditLog::with(['user', 'business']);

        if (! $actor->isSuperAdmin() || ! $request->boolean('global')) {
            $query->where('business_id', $businessId);
        }

        if ($action = $request->input('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($search = $request->input('search')) {
            $query->where('description', 'like', "%{$search}%");
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.audit', compact('logs'));
    }
}
