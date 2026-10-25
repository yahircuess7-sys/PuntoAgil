@extends('layouts.app')

@section('title', 'Configuración del Negocio - PuntoÁgil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-1">
                <span>Administración</span>
                <i class="bi bi-chevron-right text-[10px]"></i>
                <span class="text-emerald-600 font-semibold">Configuración de Empresa</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $business->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Parámetros fiscales, datos de contacto e impuestos configurables por el Propietario (Owner).</p>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-mono font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Slug: {{ $business->slug }}
        </span>
    </div>

    <!-- Summary KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Miembros Autorizados</span>
            <p class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $business->users_count ?? 0 }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Productos en Catálogo</span>
            <p class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $business->products_count ?? 0 }}</p>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Ventas Emitidas</span>
            <p class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $business->sales_count ?? 0 }}</p>
        </div>
    </div>

    <!-- Configuration Form -->
    <form action="{{ route('admin.business.update') }}" method="POST"
          class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden text-xs">
        @csrf
        @method('PUT')

        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900">Datos Comerciales y Fiscales</h2>
            <p class="text-xs text-slate-500">Estos datos se utilizan en comprobantes, reportes y encabezados de la empresa.</p>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Razón Social / Nombre Comercial <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $business->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">RUC / NIT / Identificación Fiscal</label>
                <input type="text" name="tax_id" value="{{ old('tax_id', $business->tax_id) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 font-mono focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Correo Corporativo</label>
                <input type="email" name="email" value="{{ old('email', $business->email) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Teléfono de Contacto</label>
                <input type="text" name="phone" value="{{ old('phone', $business->phone) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1.5">Dirección de Sucursal / Matriz</label>
                <input type="text" name="address" value="{{ old('address', $business->address) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Moneda Base <span class="text-rose-500">*</span></label>
                <input type="text" name="currency" value="{{ old('currency', $business->settings['currency'] ?? 'USD') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 font-mono focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Porcentaje IVA (%) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" name="tax_percentage" value="{{ old('tax_percentage', $business->settings['tax_percentage'] ?? 12) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 font-mono focus:bg-white focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block font-semibold text-slate-700 mb-1.5">Zona Horaria <span class="text-rose-500">*</span></label>
                <input type="text" name="timezone" value="{{ old('timezone', $business->settings['timezone'] ?? 'America/Guayaquil') }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 font-mono focus:bg-white focus:outline-none">
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all">
                <i class="bi bi-check-lg"></i>
                <span>Guardar Configuración</span>
            </button>
        </div>
    </form>
</div>
@endsection
