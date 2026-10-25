<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - PuntoÁgil SaaS</title>
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
            <h2 class="mt-5 text-xl font-bold text-slate-900 tracking-tight">Recuperar Contraseña</h2>
            <p class="text-xs text-slate-500 mt-1">Ingresa tu correo corporativo y te enviaremos un enlace de restablecimiento.</p>
        </div>

        <div class="bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl shadow-xl shadow-slate-200/50 p-6 sm:p-8">
            @if(session('status'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-2.5">
                    <i class="bi bi-check-circle-fill text-sm shrink-0 mt-0.5 text-emerald-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Correo Electrónico Registrado
                    </label>
                    <div class="relative">
                        <i class="bi bi-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               placeholder="tu-correo@puntoagil.com"
                               required
                               autofocus
                               class="w-full pl-10 pr-4 py-2.5 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>

                <button type="submit"
                        class="w-full mt-2 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all">
                    <i class="bi bi-send-fill"></i>
                    <span>Enviar Enlace de Recuperación</span>
                </button>
            </form>
        </div>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800 transition-colors inline-flex items-center gap-1">
                <i class="bi bi-arrow-left"></i>
                <span>Volver al inicio de sesión</span>
            </a>
        </div>
    </div>
</body>
</html>
