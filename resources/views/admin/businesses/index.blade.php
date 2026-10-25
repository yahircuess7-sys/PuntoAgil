@extends('layouts.app')

@section('title', 'Empresas Registradas (Super Admin) - PuntoÁgil')
@section('page-title', 'Administración Global de Empresas')
@section('page-subtitle', 'Gestión centralizada de negocios (tenants), propietarios y estado operativo en la plataforma')

@section('content')
<div class="space-y-6" x-data="{
    confirmStatusModalOpen: false,
    targetBusinessId: null,
    targetBusinessName: '',
    targetBusinessStatus: '',
    openStatusConfirm(id, name, status) {
        this.targetBusinessId = id;
        this.targetBusinessName = name;
        this.targetBusinessStatus = status;
        this.confirmStatusModalOpen = true;
    }
}">
    {{-- Header Actions & Search --}}
    <div class="pa-card p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('admin.businesses.index') }}" method="GET" class="flex-1 max-w-md flex items-center gap-2">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, RFC/NIT, slug o correo..." class="pa-input pl-9 text-xs">
            </div>
            <button type="submit" class="pa-btn-secondary py-2.5 px-3.5 text-xs">Filtrar</button>
            @if(request('search'))
                <a href="{{ route('admin.businesses.index') }}" class="pa-btn-ghost py-2.5 px-3 text-xs text-slate-500">Limpiar</a>
            @endif
        </form>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.businesses.create') }}" class="pa-btn-primary text-xs">
                <i class="fas fa-plus-circle"></i> Registrar Nuevo Negocio
            </a>
        </div>
    </div>

    {{-- Businesses Table --}}
    <div class="pa-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Negocios en la Plataforma ({{ $businesses->total() }})</h3>
                <p class="text-xs text-slate-500 mt-0.5">Como Super Admin puedes inspeccionar cualquier negocio, suspender su operación o registrar nuevas empresas.</p>
            </div>
            <span class="pa-badge-amber text-[11px]">
                <i class="fas fa-shield-halved mr-1"></i> Vista Super Admin
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="pa-table">
                <thead>
                    <tr>
                        <th>Empresa / Slug</th>
                        <th>Identificación Fiscal & Contacto</th>
                        <th class="text-center">Miembros</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Ventas</th>
                        <th class="text-center">Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($businesses as $biz)
                        @php
                            $isCurrentTenant = (int) auth()->user()->currentBusinessId() === (int) $biz->id;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $isCurrentTenant ? 'bg-emerald-50/30' : '' }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ $biz->status === 'active' ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border-rose-500/20' }} border flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($biz->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 text-sm">{{ $biz->name }}</span>
                                            @if($isCurrentTenant)
                                                <span class="pa-badge-emerald text-[10px] py-0.5 px-2">Contexto Activo</span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] font-mono text-slate-400 mt-0.5">ID: #{{ $biz->id }} · {{ $biz->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-xs text-slate-700 font-medium">
                                    {{ $biz->tax_id ?: 'Sin RFC/NIT' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $biz->email ?: 'Sin correo' }} {{ $biz->phone ? '· ' . $biz->phone : '' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="pa-badge-slate font-mono text-xs">{{ $biz->users_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="pa-badge-slate font-mono text-xs">{{ $biz->products_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="pa-badge-slate font-mono text-xs">{{ $biz->sales_count }}</span>
                            </td>
                            <td class="text-center">
                                @if($biz->status === 'active')
                                    <span class="pa-badge-emerald">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span> Activo
                                    </span>
                                @else
                                    <span class="pa-badge-red">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1"></span> Suspendido
                                    </span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    @if(!$isCurrentTenant)
                                        <form action="{{ route('business.switch') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="business_id" value="{{ $biz->id }}">
                                            <button type="submit" class="pa-btn-secondary py-1.5 px-2.5 text-xs" title="Cambiar contexto a esta empresa">
                                                <i class="fas fa-arrow-right-to-bracket text-emerald-600"></i> Operar
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button"
                                        @click="openStatusConfirm({{ $biz->id }}, '{{ addslashes($biz->name) }}', '{{ $biz->status }}')"
                                        class="pa-btn-ghost py-1.5 px-2.5 text-xs {{ $biz->status === 'active' ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                                        title="{{ $biz->status === 'active' ? 'Suspender empresa' : 'Reactivar empresa' }}">
                                        <i class="fas {{ $biz->status === 'active' ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                        {{ $biz->status === 'active' ? 'Suspender' : 'Activar' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-building text-3xl mb-2 opacity-40 block"></i>
                                No se encontraron empresas registradas con ese criterio.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($businesses->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $businesses->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Confirmar Suspensión/Activación de Empresa --}}
    <div x-show="confirmStatusModalOpen" x-cloak class="pa-modal-backdrop" @keydown.escape.window="confirmStatusModalOpen = false">
        <div class="pa-modal max-w-md" @click.away="confirmStatusModalOpen = false">
            <div class="p-6 text-center">
                <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center mb-4"
                     :class="targetBusinessStatus === 'active' ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200'">
                    <i class="fas text-xl" :class="targetBusinessStatus === 'active' ? 'fa-triangle-exclamation' : 'fa-building-circle-check'"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900" x-text="targetBusinessStatus === 'active' ? '¿Suspender operación del negocio?' : '¿Reactivar operación del negocio?'"></h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Estás a punto de cambiar el estado del negocio <strong class="text-slate-800" x-text="targetBusinessName"></strong>.
                    <span x-show="targetBusinessStatus === 'active'">Los miembros asociados no podrán operar en este tenant mientras permanezca suspendido.</span>
                </p>

                <form :action="`/admin/businesses/${targetBusinessId}/toggle-status`" method="POST" class="mt-6 flex items-center justify-center gap-3">
                    @csrf
                    @method('PATCH')
                    <button type="button" @click="confirmStatusModalOpen = false" class="pa-btn-secondary text-xs flex-1">
                        Cancelar
                    </button>
                    <button type="submit" class="text-xs flex-1"
                            :class="targetBusinessStatus === 'active' ? 'pa-btn-danger' : 'pa-btn-primary'">
                        <span x-text="targetBusinessStatus === 'active' ? 'Sí, Suspender' : 'Sí, Reactivar'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
