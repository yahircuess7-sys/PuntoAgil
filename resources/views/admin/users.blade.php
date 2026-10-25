@extends('layouts.app')

@section('title', 'Gestión de Usuarios y Membresías - PuntoÁgil')

@section('content')
<div class="space-y-6" x-data="{
    deleteModalOpen: false,
    deleteActionUrl: '',
    deleteUserName: '',
    toggleModalOpen: false,
    toggleActionUrl: '',
    toggleUserName: '',
    toggleTargetState: '',
    inviteModalOpen: false,
    confirmDelete(url, name) {
        this.deleteActionUrl = url;
        this.deleteUserName = name;
        this.deleteModalOpen = true;
    },
    confirmToggle(url, name, stateLabel) {
        this.toggleActionUrl = url;
        this.toggleUserName = name;
        this.toggleTargetState = stateLabel;
        this.toggleModalOpen = true;
    }
}">
    <!-- Top Header -->
    <div class="animate-fade-up flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <span>Administración</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-emerald-600 font-semibold">Miembros y Accesos</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Usuarios y Membresías</h1>
            <p class="text-xs text-slate-500 mt-0.5">Administra operadores, invitaciones, estados de cuenta y permisos dentro de <strong>{{ $currentBusiness->name ?? 'la empresa' }}</strong>.</p>
        </div>

        @can('create', \App\Models\User::class)
        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button"
                    @click="inviteModalOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition-all active:scale-95">
                <i class="bi bi-envelope-plus text-emerald-600 text-sm"></i>
                <span>Invitar por Correo</span>
            </button>

            <a href="{{ route('admin.users.create') }}"
               class="btn-shine inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm hover:shadow transition-all active:scale-95 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <i class="bi bi-person-plus text-sm"></i>
                <span>Nuevo Usuario</span>
            </a>
        </div>
        @endcan
    </div>

    @if(session('invitation_link'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start gap-2.5">
                <i class="bi bi-link-45deg text-lg text-emerald-600 shrink-0"></i>
                <div>
                    <p class="font-bold">Enlace de Invitación Generado</p>
                    <p class="text-emerald-700 font-mono text-[11px] break-all mt-0.5">{{ session('invitation_link') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="animate-fade-up stagger-1 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="relative sm:col-span-5">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Buscar por nombre o correo electrónico..."
                       class="w-full pl-9 pr-8 py-2 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
            </div>

            <div class="sm:col-span-3">
                <select name="role" class="w-full px-3 py-2 text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">Todos los roles</option>
                    <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Propietario (Owner)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador (Admin)</option>
                    <option value="cajero" {{ request('role') === 'cajero' ? 'selected' : '' }}>Cajero POS</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <select name="status" class="w-full px-3 py-2 text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">Cualquier estado</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex justify-end gap-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-all">
                    <i class="bi bi-funnel text-emerald-400"></i>
                    <span>Filtrar</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="animate-fade-up stagger-2 bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Usuario</th>
                        <th class="py-3.5 px-6">Rol en Empresa</th>
                        <th class="py-3.5 px-6">Estado de Cuenta</th>
                        <th class="py-3.5 px-6">Último Acceso</th>
                        <th class="py-3.5 px-6">Registro</th>
                        <th class="py-3.5 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs uppercase shrink-0 border border-slate-200/60">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if(auth()->id() === $user->id)
                                                <span class="px-1.5 py-0.5 text-[10px] rounded bg-emerald-50 text-emerald-700 font-medium">Tú</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-6">
                                @php $effectiveRole = $user->roleInBusiness(auth()->user()->current_business_id); @endphp
                                @if($effectiveRole === 'super_admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-stars text-[10px]"></i> Super Admin
                                    </span>
                                @elseif($effectiveRole === 'owner')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="bi bi-award-fill text-[10px]"></i> Owner (Propietario)
                                    </span>
                                @elseif($effectiveRole === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i class="bi bi-shield-lock-fill text-[10px]"></i> Administrador
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="bi bi-cash-coin text-[10px]"></i> Cajero POS
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                @if($user->last_login_at)
                                    <div class="font-mono text-slate-700 text-[11px]">{{ $user->last_login_at->format('d/m/Y H:i') }}</div>
                                    @if($user->last_login_ip)
                                        <div class="text-[10px] text-slate-400 font-mono">IP: {{ $user->last_login_ip }}</div>
                                    @endif
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Sin accesos registrados</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 font-mono text-slate-500 text-[11px]">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @can('update', $user)
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           title="Editar Usuario y Rol"
                                           class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                            <i class="bi bi-pencil-square text-sm"></i>
                                        </a>

                                        @if(auth()->id() !== $user->id)
                                            <button type="button"
                                                    @click="confirmToggle('{{ route('admin.users.toggle-status', $user) }}', '{{ addslashes($user->name) }}', '{{ $user->status === 'active' ? 'desactivar' : 'activar' }}')"
                                                    title="{{ $user->status === 'active' ? 'Desactivar Cuenta' : 'Activar Cuenta' }}"
                                                    class="p-2 {{ $user->status === 'active' ? 'text-amber-500 hover:text-amber-700 hover:bg-amber-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' }} rounded-lg transition-colors">
                                                <i class="bi {{ $user->status === 'active' ? 'bi-pause-circle' : 'bi-play-circle' }} text-sm"></i>
                                            </button>
                                        @endif
                                    @endcan

                                    @can('delete', $user)
                                        <button type="button"
                                                @click="confirmDelete('{{ route('admin.users.destroy', $user) }}', '{{ addslashes($user->name) }}')"
                                                title="Revocar Acceso / Eliminar"
                                                class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                            <i class="bi bi-person-x text-sm"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="bi bi-people text-xl"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700">No se encontraron usuarios</h3>
                                <p class="text-xs text-slate-400 mt-1">Prueba con otro término de búsqueda o registra un nuevo operador.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($users, 'links') && $users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Pending Invitations Section -->
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Invitaciones Pendientes de Aceptación</h2>
                    <p class="text-xs text-slate-500">Enlaces enviados que aún no han sido activados por el destinatario.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $pendingInvitations->count() }} pendientes
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase">
                            <th class="py-3 px-6">Destinatario</th>
                            <th class="py-3 px-6">Rol Asignado</th>
                            <th class="py-3 px-6">Invitado Por</th>
                            <th class="py-3 px-6">Expira</th>
                            <th class="py-3 px-6 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingInvitations as $inv)
                            <tr>
                                <td class="py-3 px-6">
                                    <div class="font-semibold text-slate-800">{{ $inv->name ?: 'Invitado' }}</div>
                                    <div class="font-mono text-[11px] text-slate-500">{{ $inv->email }}</div>
                                </td>
                                <td class="py-3 px-6 uppercase font-mono text-[11px] font-semibold text-slate-700">
                                    {{ $inv->role }}
                                </td>
                                <td class="py-3 px-6 text-slate-500">
                                    {{ $inv->inviter->name ?? 'Administrador' }}
                                </td>
                                <td class="py-3 px-6 font-mono text-[11px] text-slate-500">
                                    {{ $inv->expires_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('invitations.show', $inv->token) }}"
                                           target="_blank"
                                           class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                                            Ver Enlace
                                        </a>
                                        <form action="{{ route('admin.invitations.revoke', $inv) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100">
                                                Revocar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Invite Modal -->
    <div x-show="inviteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="inviteModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 z-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Invitar o Asociar Miembro</h3>
                    <button type="button" @click="inviteModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Envía una invitación para asociar un usuario nuevo o existente a <strong>{{ $currentBusiness->name ?? 'tu negocio' }}</strong>.
                </p>

                <form action="{{ route('admin.users.invite') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nombre (Opcional)</label>
                        <input type="text" name="name" placeholder="Ej. María Gómez"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="colaborador@empresa.com"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rol Asignado <span class="text-rose-500">*</span></label>
                        <select name="role" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none">
                            @can('assignRole', [\App\Models\User::class, 'owner'])
                                <option value="owner">Propietario (Owner)</option>
                            @endcan
                            @can('assignRole', [\App\Models\User::class, 'admin'])
                                <option value="admin">Administrador (Admin)</option>
                            @endcan
                            <option value="cajero" selected>Cajero POS</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="inviteModalOpen = false"
                                class="px-4 py-2 rounded-xl font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-xl font-semibold text-white bg-emerald-600 hover:bg-emerald-700">
                            Enviar Invitación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toggle Status Confirmation Modal -->
    <div x-show="toggleModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="toggleModalOpen = false"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 z-10 text-center">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-shield-exclamation text-xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">Confirmar cambio de estado</h3>
                <p class="text-xs text-slate-500 mt-2">
                    ¿Deseas <strong class="text-slate-800" x-text="toggleTargetState"></strong> la cuenta de <span class="font-semibold text-slate-800" x-text="toggleUserName"></span>?
                </p>
                <div class="mt-6 flex justify-end gap-2.5">
                    <button type="button" @click="toggleModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200">
                        Cancelar
                    </button>
                    <form :action="toggleActionUrl" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-700">
                            Confirmar Acción
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Confirmation Modal for Revoke/Delete -->
    <div x-show="deleteModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="deleteModalOpen = false"></div>

            <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl max-w-md w-full border border-slate-200 z-10">
                <div class="p-6">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="text-center">
                        <h3 class="text-base font-bold text-slate-900" id="modal-title">¿Revocar acceso del usuario?</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Estás a punto de revocar el acceso de <span class="font-semibold text-slate-800" x-text="deleteUserName"></span> en esta empresa. Esta operación quedará registrada en la auditoría.
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button"
                            @click="deleteModalOpen = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                        Cancelar
                    </button>
                    <form :action="deleteActionUrl" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 shadow-sm transition-all">
                            Sí, Revocar Acceso
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
