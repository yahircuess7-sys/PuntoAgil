<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PuntoÁgil - Sistema de Gestión Comercial y Facturación POS</title>
    <!-- Google Fonts: Plus Jakarta Sans, Instrument Serif & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS CDN with Warm Paper, Deep Forest Pine & Carbon Ink Tonalities -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            50: '#F6F5F0',
                            100: '#EFECE4',
                            200: '#E2DFD5',
                            300: '#CFCBC0',
                            400: '#8C887E',
                            500: '#635F56',
                            600: '#47443D',
                            700: '#2E2C27',
                            800: '#1C1F21',
                            900: '#111315',
                            950: '#0A0C0D',
                        },
                        emerald: {
                            50: '#EEF5F1',
                            100: '#D6E8DF',
                            200: '#B0D4C2',
                            300: '#6EC29E',
                            400: '#34B382',
                            500: '#14533E',
                            600: '#0F3D2E',
                            700: '#0B2E22',
                            800: '#09251B',
                            900: '#071C15',
                            950: '#04100C',
                        },
                        brand: {
                            50: '#EEF5F1',
                            100: '#D6E8DF',
                            200: '#B0D4C2',
                            500: '#14533E',
                            600: '#0F3D2E',
                            700: '#0B2E22',
                            900: '#071C15',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['"Instrument Serif"', 'Georgia', 'serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace']
                    }
                }
            }
        }
    </script>

    <style>
        .font-mono, .tabular-nums { font-variant-numeric: tabular-nums; }
        @media (prefers-reduced-motion: no-preference) {
            @view-transition {
                navigation: auto;
            }

            @keyframes fadeUp {
                from { opacity: 0; translate: 0 22px; }
                to { opacity: 1; translate: 0 0; }
            }

            @keyframes orbFloatA {
                0%, 100% { translate: 0 0; scale: 1; }
                50% { translate: 30px -20px; scale: 1.08; }
            }

            @keyframes orbFloatB {
                0%, 100% { translate: 0 0; scale: 1; }
                50% { translate: -25px 25px; scale: 1.06; }
            }

            @keyframes cardFloat {
                0%, 100% { translate: 0 0; }
                50% { translate: 0 -6px; }
            }

            .animate-fade-up {
                animation: fadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) both;
            }
            .stagger-1 { animation-delay: 80ms; }
            .stagger-2 { animation-delay: 160ms; }
            .stagger-3 { animation-delay: 240ms; }
            .stagger-4 { animation-delay: 340ms; }

            .orb-a { animation: orbFloatA 9s ease-in-out infinite; }
            .orb-b { animation: orbFloatB 11s ease-in-out infinite; }
            .preview-float { animation: cardFloat 6s ease-in-out infinite; }

            .saas-card-hover {
                transition: translate 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                            box-shadow 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                            border-color 0.3s ease;
            }
            .saas-card-hover:hover {
                translate: 0 -6px;
                box-shadow: 0 20px 30px -10px rgba(17, 19, 21, 0.08), 0 8px 16px -6px rgba(15, 61, 46, 0.08);
            }
        }

        .btn-shine {
            position: relative;
            overflow: hidden;
        }
        @media (prefers-reduced-motion: no-preference) {
            .btn-shine::after {
                content: '';
                position: absolute;
                inset: 0;
                translate: -120% 0;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.28), transparent);
                transition: translate 0.6s ease;
            }
            .btn-shine:hover::after {
                translate: 120% 0;
            }
        }
    </style>
</head>
<body class="min-h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-emerald-600 selection:text-white bg-slate-50">

    <!-- Navbar -->
    <header class="sticky top-0 z-30 bg-[#F6F5F0]/90 backdrop-blur-md border-b border-slate-200/90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 group-hover:scale-105 group-hover:rotate-3 transition-transform duration-300">
                    <i class="bi bi-lightning-charge-fill text-xl text-emerald-300"></i>
                </div>
                <div>
                    <span class="text-lg font-bold text-slate-900 tracking-tight leading-none block">Punto<span class="text-emerald-600">Ágil</span></span>
                    <span class="text-[10px] uppercase font-semibold text-emerald-600 tracking-wider">Cloud POS &amp; Inventario</span>
                </div>
            </a>

            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-shine inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm hover:shadow transition-all active:scale-95">
                        <i class="bi bi-speedometer2 text-sm"></i>
                        <span>Ir a mi Panel</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 bg-[#FDFCF9] border border-slate-300 rounded-xl hover:bg-slate-100 hover:border-emerald-500 shadow-sm transition-all active:scale-95">
                        <i class="bi bi-box-arrow-in-right text-sm text-emerald-600"></i>
                        <span>Ingresar al Sistema</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-[#111315] via-[#0F1715] to-[#0A0C0D] text-white pt-16 pb-24 lg:pt-20 lg:pb-28">
        <!-- Warm Forest Pine Ambient Accents -->
        <div class="orb-a absolute -top-24 left-1/4 w-96 h-96 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none"></div>
        <div class="orb-b absolute top-32 right-1/4 w-80 h-80 rounded-full bg-emerald-400/10 blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-emerald-500/15 via-transparent to-transparent pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-up inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300 border border-emerald-400/25 mb-6 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Plataforma SaaS Cloud &middot; Moneda Operativa en Córdobas (C$)</span>
            </div>

            <h1 class="animate-fade-up stagger-1 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight max-w-4xl mx-auto leading-tight">
                Control de inventario y facturación ágil para <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-[#A7F3D0] to-emerald-400">comercios modernos</span>.
            </h1>

            <p class="animate-fade-up stagger-2 mt-6 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                PuntoÁgil centraliza el control de existencias, cálculo automatizado de impuestos y cobro ultra rápido en caja para tu negocio.
            </p>

            <div class="animate-fade-up stagger-3 mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-shine w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-950 bg-emerald-400 hover:bg-emerald-300 rounded-xl shadow-lg shadow-emerald-950/50 transition-all active:scale-95">
                        <span>Acceder a la Consola</span>
                        <i class="bi bi-arrow-right text-sm"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-shine w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-950 bg-emerald-400 hover:bg-emerald-300 rounded-xl shadow-lg shadow-emerald-950/50 transition-all active:scale-95">
                        <span>Iniciar Sesión en el Punto de Venta</span>
                        <i class="bi bi-arrow-right text-sm"></i>
                    </a>
                @endauth

                <a href="#modulos" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-xs font-semibold text-slate-200 bg-white/5 hover:bg-white/10 border border-white/15 rounded-xl transition-all">
                    <span>Explorar Funcionalidades</span>
                    <i class="bi bi-chevron-down text-[11px]"></i>
                </a>
            </div>

            <!-- Interactive SaaS Console Preview Mockup -->
            <div class="animate-fade-up stagger-4 mt-14 max-w-4xl mx-auto">
                <div class="preview-float rounded-2xl bg-[#16191B]/95 border border-slate-700/80 shadow-2xl shadow-black/60 p-3 sm:p-4 backdrop-blur-xl text-left">
                    <!-- Browser Top Bar -->
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800 px-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400/80"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400/80"></span>
                            <span class="ml-2 text-[11px] font-mono text-slate-400">app.puntoagil.cloud/pos</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 text-[10px] font-mono border border-emerald-400/25">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Sincronizado con PostgreSQL
                        </span>
                    </div>

                    <!-- Mini KPI Strip inside Preview -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-xl bg-[#1C2022] border border-slate-700/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Facturación en Córdobas (C$)</span>
                                <span class="text-base font-bold font-mono text-white mt-0.5 block">IVA 15% Configurable</span>
                            </div>
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/25 text-emerald-300 flex items-center justify-center">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1C2022] border border-slate-700/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Control de Stock</span>
                                <span class="text-base font-bold font-mono text-emerald-300 mt-0.5 block">Tiempo Real</span>
                            </div>
                            <div class="w-9 h-9 rounded-lg bg-amber-500/15 text-amber-300 flex items-center justify-center">
                                <i class="bi bi-boxes"></i>
                            </div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-[#1C2022] border border-slate-700/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Reportes &amp; PDF</span>
                                <span class="text-base font-bold font-mono text-white mt-0.5 block">Exportación 1-Click</span>
                            </div>
                            <div class="w-9 h-9 rounded-lg bg-emerald-500/25 text-emerald-300 flex items-center justify-center">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Modules Section -->
    <section id="modulos" class="py-20 bg-[#F6F5F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 animate-fade-up">
                <span class="text-xs uppercase font-bold text-emerald-600 tracking-wider">Arquitectura Modular</span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-2">Módulos Especializados por Perfil</h2>
                <p class="text-sm text-slate-500 mt-2">Diseñados para garantizar rapidez en la atención al cliente y rigurosidad contable en almacén.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- POS Module -->
                <div class="group saas-card-hover animate-fade-up stagger-1 bg-[#FDFCF9] border border-slate-200/90 hover:border-emerald-500/50 rounded-2xl p-7 shadow-sm relative overflow-hidden">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="bi bi-calculator text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Terminal POS Express</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Cobro ultrarrápido en Córdobas (C$) con atajos de teclado, lector de código de barras automático, tickets térmicos 58/80mm y liquidación configurable de IVA 15%.
                    </p>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        <span>Optimizado para cajeros</span>
                        <i class="bi bi-arrow-right-short text-base"></i>
                    </div>
                </div>

                <!-- Inventory Module -->
                <div class="group saas-card-hover animate-fade-up stagger-2 bg-[#FDFCF9] border border-slate-200/90 hover:border-emerald-500/50 rounded-2xl p-7 shadow-sm relative overflow-hidden">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/60 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="bi bi-boxes text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Gestión de Catálogo &amp; Stock</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Auditoría en tiempo real de unidades en bodega, alertas de stock mínimo, importación rápida Excel/CSV y edición en línea con trazabilidad Kardex.
                    </p>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        <span>Control de almacén</span>
                        <i class="bi bi-arrow-right-short text-base"></i>
                    </div>
                </div>

                <!-- Reports & Auditing -->
                <div class="group saas-card-hover animate-fade-up stagger-3 bg-[#FDFCF9] border border-slate-200/90 hover:border-emerald-500/50 rounded-2xl p-7 shadow-sm relative overflow-hidden">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-800 border border-slate-200 flex items-center justify-center mb-5 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                        <i class="bi bi-file-earmark-spreadsheet text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Arqueo Ciego &amp; Reportes</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">
                        Cierre de caja ciego con conteo de billetes en Córdobas, resumen diario en 1 página para WhatsApp/PDF y lista automática de reposición a proveedores.
                    </p>
                    <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        <span>Supervisión administrativa</span>
                        <i class="bi bi-arrow-right-short text-base"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto bg-[#111315] text-slate-400 py-12 border-t border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-emerald-400 text-base"></i>
                <span class="font-bold text-white">PuntoÁgil</span>
                <span class="text-slate-500">&middot; Plataforma Cloud de Gestión Comercial</span>
            </div>
            <div>
                <span>&copy; {{ date('Y') }} PuntoÁgil SaaS. Todos los derechos reservados.</span>
            </div>
        </div>
    </footer>

</body>
</html>
