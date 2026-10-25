<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceptar Invitación - PuntoÁgil SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="relative h-full flex items-center justify-center p-4 sm:p-6 text-slate-800 font-sans antialiased">

    <div class="relative w-full max-w-md z-10">
        <div class="text-center mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/25">
                    <i class="bi bi-lightning-charge-fill text-2xl"></i>
                </div>
                <div class="text-left">
                    <div class="text-xl font-bold text-slate-900 tracking-tight leading-none">Punto<span class="text-emerald-600">Ágil</span></div>
                    <div class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mt-1">SaaS Cloud POS</div>
                </div>
            </a>
            <h2 class="mt-5 text-xl font-bold text-slate-900 tracking-tight">Invitación a {{ $invitation->business->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">
                Has sido invitado para unirte con el rol de <strong class="uppercase text-emerald-700">{{ $invitation->role }}</strong>.
            </p>
        </div>

        <div class="bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl shadow-xl shadow-slate-200/50 p-6 sm:p-8">
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5">
                    <i class="bi bi-exclamation-circle-fill text-sm shrink-0 mt-0.5"></i>
                    <ul class="space-y-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('invitations.accept', $invitation->token) }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Correo Electrónico Invitado</label>
                    <input type="email" value="{{ $invitation->email }}" disabled
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-mono">
                </div>

                @if(! $existingUser)
                    <div>
                        <label for="name" class="block font-semibold text-slate-700 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $invitation->name) }}" required
                               placeholder="Ej. Andrea Morales"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label for="password" class="block font-semibold text-slate-700 mb-1">Crear Contraseña (8+ caracteres) <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-semibold text-slate-700 mb-1">Confirmar Contraseña <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none">
                    </div>
                @else
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800">
                        Tu cuenta (<strong>{{ $existingUser->name }}</strong>) ya está registrada en PuntoÁgil. Al confirmar se vinculará tu acceso a <strong>{{ $invitation->business->name }}</strong>.
                    </div>
                @endif

                <button type="submit"
                        class="w-full mt-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-md transition-all">
                    <i class="bi bi-check2-circle text-base"></i>
                    <span>Aceptar Invitación e Ingresar</span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
