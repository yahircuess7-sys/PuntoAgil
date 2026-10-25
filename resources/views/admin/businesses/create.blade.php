@extends('layouts.app')

@section('title', 'Registrar Nuevo Negocio - PuntoÁgil')
@section('page-title', 'Registrar Nuevo Negocio (Tenant)')
@section('page-subtitle', 'Da de alta una nueva empresa en PuntoÁgil y asigna su cuenta Propietaria (Owner)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.businesses.index') }}" class="pa-btn-ghost text-xs">
            <i class="fas fa-arrow-left"></i> Volver al listado de empresas
        </a>
        <span class="pa-badge-amber text-[11px]">
            <i class="fas fa-shield-halved mr-1"></i> Super Admin Global
        </span>
    </div>

    <form action="{{ route('admin.businesses.store') }}" method="POST" class="pa-card p-6 sm:p-8 space-y-6">
        @csrf

        {{-- Sección 1: Datos de la Empresa --}}
        <div>
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Datos Comerciales del Negocio</h3>
                    <p class="text-xs text-slate-500">Información fiscal y de contacto de la nueva empresa.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div class="sm:col-span-2">
                    <label class="pa-label">Nombre Comercial / Razón Social <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="pa-input" placeholder="Ej. Comercializadora del Norte S.A.">
                    @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pa-label">RFC / NIT / Identificación Fiscal</label>
                    <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="pa-input" placeholder="Ej. CNO240101AAA">
                    @error('tax_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pa-label">Teléfono Comercial</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="pa-input" placeholder="Ej. +52 55 1234 5678">
                    @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pa-label">Correo de Contacto del Negocio</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="pa-input" placeholder="contacto@empresa.com">
                    @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pa-label">Dirección Física</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="pa-input" placeholder="Av. Principal #123, Centro">
                    @error('address') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Sección 2: Propietario (Owner) --}}
        <div class="pt-2">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-xs">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Propietario del Negocio (Rol Owner)</h3>
                    <p class="text-xs text-slate-500">Si el correo ya pertenece a un usuario registrado en PuntoÁgil, se le asociará como Owner de este nuevo negocio.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="pa-label">Nombre Completo del Propietario <span class="text-rose-500">*</span></label>
                    <input type="text" name="owner_name" value="{{ old('owner_name') }}" required class="pa-input" placeholder="Ej. Roberto Gómez">
                    @error('owner_name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="pa-label">Correo Electrónico del Propietario <span class="text-rose-500">*</span></label>
                    <input type="email" name="owner_email" value="{{ old('owner_email') }}" required class="pa-input" placeholder="propietario@empresa.com">
                    @error('owner_email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="pa-label">Contraseña Inicial (si es usuario nuevo)</label>
                    <input type="password" name="owner_password" class="pa-input" placeholder="Mínimo 8 caracteres (requerido para cuentas nuevas)">
                    <p class="text-[11px] text-slate-400 mt-1">Si el correo ya existe en el sistema, conservará su contraseña actual y recibirá membresía Owner en esta empresa.</p>
                    @error('owner_password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.businesses.index') }}" class="pa-btn-secondary text-xs">Cancelar</a>
            <button type="submit" class="pa-btn-primary text-xs">
                <i class="fas fa-check-circle"></i> Crear Negocio y Asignar Owner
            </button>
        </div>
    </form>
</div>
@endsection
