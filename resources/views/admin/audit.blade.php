@extends('layouts.app')

@section('title', 'Auditoría de Seguridad - PuntoÁgil')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <span>Administración</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-emerald-600 font-semibold">Auditoría Protegida</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Bitácora de Auditoría y Seguridad</h1>
            <p class="text-xs text-slate-500 mt-0.5">Registro inmutable de operaciones sensibles sobre usuarios, roles, invitaciones y negocios.</p>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-900 text-emerald-400">
            <i class="bi bi-shield-lock-fill"></i>
            Registro Protegido (Solo Lectura)
        </span>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm">
        <form action="{{ route('admin.audit.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
            <div class="sm:col-span-5">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar en la descripción del evento..."
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
            </div>
            <div class="sm:col-span-4">
                <input type="text" name="action" value="{{ request('action') }}"
                       placeholder="Filtrar por código (ej. user., invitation., business.)"
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 font-mono focus:bg-white focus:outline-none">
            </div>
            <div class="sm:col-span-3 flex justify-end">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl font-semibold text-white bg-slate-900 hover:bg-slate-800">
                    <i class="bi bi-funnel text-emerald-400"></i>
                    <span>Filtrar Bitácora</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Fecha y Hora</th>
                        <th class="py-3.5 px-6">Acción</th>
                        <th class="py-3.5 px-6">Ejecutado Por</th>
                        <th class="py-3.5 px-6">Descripción</th>
                        <th class="py-3.5 px-6">Dirección IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3.5 px-6 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 font-mono text-[11px] font-semibold text-slate-700">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-800">{{ $log->user->name ?? 'Sistema' }}</div>
                                <div class="font-mono text-[10px] text-slate-400">{{ $log->user->email ?? '' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-slate-700">
                                <div>{{ $log->description }}</div>
                                @if(!empty($log->metadata))
                                    <div class="mt-1 font-mono text-[10px] text-slate-400">
                                        {{ json_encode($log->metadata, JSON_UNESCAPED_UNICODE) }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 font-mono text-[11px] text-slate-500">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                No existen registros de auditoría que coincidan con la consulta.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($logs, 'links') && $logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
