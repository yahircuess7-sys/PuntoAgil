@extends('layouts.app')

@section('title', 'Verificar Correo Electrónico - PuntoÁgil')

@section('content')
<div class="max-w-lg mx-auto mt-10">
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-card p-6 sm:p-8 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
            <i class="bi bi-envelope-check-fill text-2xl"></i>
        </div>

        <h1 class="text-xl font-bold text-slate-900">Verifica tu Correo Electrónico</h1>
        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
            Para proteger la seguridad de tu empresa en PuntoÁgil, revisa tu bandeja de entrada y haz clic en el enlace de verificación enviado a <strong>{{ auth()->user()->email }}</strong>.
        </p>

        @if(session('status'))
            <div class="mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-center gap-2">
                <i class="bi bi-check-circle-fill text-emerald-600"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-sm">
                    <i class="bi bi-send-fill"></i>
                    <span>Reenviar Correo de Verificación</span>
                </button>
            </form>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all">
                <span>Ir al Panel</span>
            </a>
        </div>
    </div>
</div>
@endsection
