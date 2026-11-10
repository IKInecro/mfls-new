<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard &bull; {{ config('app.name', 'MFLS Ajaran 2027') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('icon/logoo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('icon/logoo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon/logoo.png') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="Admin Dashboard - {{ config('app.name', 'MFLS') }}">
    <meta property="og:description"
        content="Panel Administrasi Resmi MNC University Future Leader Scholarship Ajaran 2027.">
    <meta property="og:image" content="{{ asset('icon/logoo.png') }}">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Tailwind CDN Browser 4, Iconify, SweetAlert2 & jQuery -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Summernote WYSIWYG for Announcements & Templates -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style type="text/tailwindcss">
        @theme {
            --color-primary-blue: #0870B8;
            --color-primary-blue-hover: #065A96;
            --color-primary-gold: #F2B451;
            --color-primary-gold-hover: #D99B26;
            --color-dark-navy: #0B1120;
            --color-navy-card: #0F172A;
            --font-jakarta: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        @layer base {
            html {
                font-family: var(--font-jakarta);
                scroll-behavior: smooth;
            }
            body {
                @apply font-jakarta text-slate-800 bg-slate-50 antialiased selection:bg-[#0870B8] selection:text-white;
            }
            /* Refined Sleek Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="font-jakarta antialiased bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Loading Progress Bar for Seamless Dynamic SPA Navigation -->
    <div id="admin-progress" class="fixed top-0 left-0 h-1 bg-[#0870B8] z-[99999] transition-all duration-200 w-0 opacity-0 pointer-events-none"></div>

    <!-- ===== MOBILE TOPBAR (Hidden on md+) ===== -->
    <header class="md:hidden fixed top-0 left-0 right-0 z-40 bg-[#0B1120]/95 backdrop-blur-md border-b border-slate-800 shadow-lg">
        <div class="flex items-center justify-between px-4 h-16">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="p-1.5 rounded-xl bg-white/5 border border-white/10 shrink-0">
                    <img src="{{ asset('icon/logoo.png') }}" class="h-7 w-auto object-contain" alt="Logo MFLS">
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-black text-white tracking-tight leading-tight">MNC University</span>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            {{ strtoupper(auth()->user()->role ?? 'ADMIN') }}
                        </span>
                    </div>
                    <span class="block text-[10px] font-medium text-slate-400 truncate leading-tight">
                        Future Leader Scholarship Ajaran 2027
                    </span>
                </div>
            </a>
            <button id="sidebarToggle"
                class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700 transition-all border border-slate-700/60"
                aria-label="Toggle Menu Navigasi">
                <span class="iconify text-xl" data-icon="solar:hamburger-menu-linear" id="hamburgerIcon"></span>
            </button>
        </div>
    </header>

    <!-- ===== BACKDROP OVERLAY (Mobile Only) ===== -->
    <div id="sidebarOverlay" 
         class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-xs hidden transition-opacity duration-300"
         onclick="closeSidebar()">
    </div>

    <div class="flex min-h-screen w-full">

        <!-- ===== SIDEBAR (Executive Deep Navy) ===== -->
        <aside id="adminSidebar" 
               class="w-72 bg-[#0B1120] text-slate-300 border-r border-slate-800/90 flex flex-col fixed inset-y-0 left-0 z-50
                      shadow-2xl shadow-slate-950/50 -translate-x-full md:translate-x-0
                      transition-transform duration-300 ease-in-out select-none">

            <!-- Logo Header (Desktop) -->
            <div class="p-6 border-b border-slate-800/80 hidden md:block">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group focus:outline-none">
                    <div class="p-2 rounded-xl bg-white/5 border border-white/10 group-hover:border-blue-500/50 transition-colors shrink-0">
                        <img src="{{ asset('icon/logoo.png') }}" alt="Logo MFLS" class="h-8 w-auto">
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="block text-[10px] font-bold text-amber-400 tracking-wider uppercase">BATCH 3</span>
                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                {{ strtoupper(auth()->user()->role ?? 'ADMIN') }}
                            </span>
                        </div>
                        <span class="block text-xs font-black text-white tracking-tight leading-tight group-hover:text-blue-300 transition-colors">
                            MNC University
                        </span>
                        <span class="block text-[10px] font-semibold text-slate-400 tracking-tight">
                            Future Leader Scholarship
                        </span>
                    </div>
                </a>
            </div>

            <!-- Mobile Topbar Spacer -->
            <div class="md:hidden h-16 shrink-0 border-b border-slate-800 flex items-center justify-between px-6">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menu Navigasi</span>
                <button onclick="closeSidebar()" class="p-1 text-slate-400 hover:text-white">
                    <span class="iconify text-xl" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-grow p-4 space-y-6 overflow-y-auto">
                
                <!-- GROUP 1: UTAMA -->
                @if(auth()->user()->role !== 'dosen')
                <div>
                    <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center justify-between">
                        <span>Navigasi Utama</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <div class="flex items-center gap-3">
                                <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.dashboard') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                    data-icon="solar:chart-square-bold-duotone"></span>
                                <span>Dashboard</span>
                            </div>
                            @if(request()->routeIs('admin.dashboard'))
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            @endif
                        </a>

                        <a href="{{ route('admin.pengaturan.pendaftaran.edit') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                {{ request()->routeIs('admin.pengaturan.pendaftaran.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <div class="flex items-center gap-3">
                                <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.pengaturan.pendaftaran.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                    data-icon="solar:calendar-bold-duotone"></span>
                                <span>Pengaturan Jadwal</span>
                            </div>
                            @if(request()->routeIs('admin.pengaturan.pendaftaran.*'))
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            @endif
                        </a>

                        <a href="{{ route('admin.pengaturan.timeline.index') }}" onclick="closeSidebar()"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                {{ request()->routeIs('admin.pengaturan.timeline.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <div class="flex items-center gap-3">
                                <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.pengaturan.timeline.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                    data-icon="solar:history-bold-duotone"></span>
                                <span>Timeline Seleksi</span>
                            </div>
                            @if(request()->routeIs('admin.pengaturan.timeline.*'))
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            @endif
                        </a>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.email-templates.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.email-templates.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.email-templates.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:letter-bold-duotone"></span>
                                    <span>Kelola Email</span>
                                </div>
                                @if(request()->routeIs('admin.email-templates.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if((auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada') && (in_array(auth()->user()->role, ['akademik', 'palugada']) || in_array(auth()->user()->email, ['dion@gmail.com', 'info@beasiswamncu.com'])))
                            <a href="{{ route('admin.beasiswa.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.beasiswa.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.beasiswa.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:cup-star-bold-duotone"></span>
                                    <span>Seleksi Beasiswa</span>
                                </div>
                                @if(request()->routeIs('admin.beasiswa.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- GROUP 2: SELEKSI & TAHAPAN -->
                <div>
                    <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center justify-between">
                        <span>{{ auth()->user()->role === 'dosen' ? 'Penilaian Seleksi' : 'Seleksi & Data' }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    </div>
                    <div class="space-y-1">
                        <!-- PALUGADA SPECIAL TAB -->
                        @if(auth()->user()->role === 'palugada' || (auth()->user()->role === 'admin' && Route::has('admin.palugada.index')))
                            @php
                                $pendingPalugadaCount = \App\Models\Daftar::where('status', 'diajukan_palugada')->count();
                            @endphp
                            <a href="{{ route('admin.palugada.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.palugada.index') ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-xs' : 'text-amber-400/90 hover:text-amber-200 hover:bg-slate-800/80 border border-amber-500/20' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 text-amber-400"
                                        data-icon="solar:shield-check-bold-duotone"></span>
                                    <span>Verifikasi Final</span>
                                </div>
                                @if($pendingPalugadaCount > 0)
                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black uppercase bg-amber-500 text-slate-950">
                                        {{ $pendingPalugadaCount }}
                                    </span>
                                @elseif(request()->routeIs('admin.palugada.index'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                            <a href="{{ route('admin.pendaftar.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.pendaftar.index') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.pendaftar.index') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:users-group-rounded-bold-duotone"></span>
                                    <span>Seleksi Administrasi</span>
                                </div>
                                @if(request()->routeIs('admin.pendaftar.index'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                            <a href="{{ route('admin.hasil_ujian.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.hasil_ujian.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.hasil_ujian.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:document-text-bold-duotone"></span>
                                    <span>Hasil Ujian</span>
                                </div>
                                @if(request()->routeIs('admin.hasil_ujian.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>

                            <a href="{{ route('admin.seleksi_ujian.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.seleksi_ujian.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.seleksi_ujian.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:shield-star-bold-duotone"></span>
                                    <span>Seleksi Ujian</span>
                                </div>
                                @if(request()->routeIs('admin.seleksi_ujian.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'mentor' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                            <a href="{{ route('admin.penilaian.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.penilaian.index') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.penilaian.index') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:user-id-bold-duotone"></span>
                                    <span>Penilaian Mentor</span>
                                </div>
                                @if(request()->routeIs('admin.penilaian.index'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'mentor' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada' || auth()->user()->role === 'dosen')
                            <a href="{{ route('admin.penilaian.akademik.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.penilaian.akademik.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.penilaian.akademik.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:medal-ribbon-bold-duotone"></span>
                                    <span>Wawancara Akademik</span>
                                </div>
                                @if(request()->routeIs('admin.penilaian.akademik.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->check() && (auth()->user()->role === 'admin' || in_array(auth()->user()->email, ['dendi.pratama@mncu.ac.id', 'muhammad.rezki@mncu.ac.id', 'noval.adi@mncu.ac.id'])))
                            <a href="{{ route('admin.wawancara_bod.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.wawancara_bod.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.wawancara_bod.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:user-hand-up-bold-duotone"></span>
                                    <span>Wawancara BoD</span>
                                </div>
                                @if(request()->routeIs('admin.wawancara_bod.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'akademik')
                            <a href="{{ route('admin.panel_ujian.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.panel_ujian.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.panel_ujian.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:monitor-smartphone-bold-duotone"></span>
                                    <span>Panel Ujian CBT</span>
                                </div>
                                @if(request()->routeIs('admin.panel_ujian.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'akademik' || auth()->user()->role === 'palugada')
                            <a href="{{ route('admin.soal.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.soal.index') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.soal.index') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:pen-new-square-bold-duotone"></span>
                                    <span>Bank Soal CBT</span>
                                </div>
                                @if(request()->routeIs('admin.soal.index'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>

                <!-- GROUP 3: KONTROL SISTEM -->
                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'palugada' || in_array(auth()->user()->email, ['dion@gmail.com', 'adminis@mfls.com', 'info@beasiswamncu.com'])))
                <div>
                    <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center justify-between">
                        <span>Kontrol Sistem</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                    </div>
                    <div class="space-y-1">
                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'palugada' || auth()->user()->email === 'info@beasiswamncu.com')
                            <a href="{{ route('admin.user.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.user.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.user.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:users-group-two-rounded-bold-duotone"></span>
                                    <span>Kelola User</span>
                                </div>
                                @if(request()->routeIs('admin.user.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>

                            <a href="{{ route('admin.activity_log.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.activity_log.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.activity_log.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:history-bold-duotone"></span>
                                    <span>Log Aktivitas</span>
                                </div>
                                @if(request()->routeIs('admin.activity_log.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'panitia' || auth()->user()->role === 'palugada' || in_array(auth()->user()->email, ['dion@gmail.com', 'dept.adminis@mfls.com', 'info@beasiswamncu.com']))
                            <a href="{{ route('admin.berita.index') }}" onclick="closeSidebar()"
                                class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group
                                    {{ request()->routeIs('admin.berita.*') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                                <div class="flex items-center gap-3">
                                    <span class="iconify text-lg transition-transform group-hover:scale-110 {{ request()->routeIs('admin.berita.*') ? 'text-blue-400' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        data-icon="solar:document-bold-duotone"></span>
                                    <span>Manajemen Berita</span>
                                </div>
                                @if(request()->routeIs('admin.berita.*'))
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                @endif
                            </a>
                        @endif

                        <a href="{{ route('pengumuman') }}" target="_blank"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group text-slate-400 hover:text-amber-300 hover:bg-slate-800/60">
                            <div class="flex items-center gap-3">
                                <span class="iconify text-lg transition-transform group-hover:scale-110 text-amber-400"
                                    data-icon="solar:eye-bold-duotone"></span>
                                <span>Live Pengumuman</span>
                            </div>
                            <span class="iconify text-xs text-slate-500 group-hover:text-amber-400" data-icon="solar:arrow-right-up-linear"></span>
                        </a>
                    </div>
                </div>
                @endif

            </nav>

            <!-- Bottom User Profile Card -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 space-y-3 shrink-0">
                @auth
                    @php
                        $user = Auth::user();
                        $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($user->nama ?? $user->name ?? 'Admin') . "&background=0870B8&color=ffffff&bold=true";
                    @endphp
                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 border border-slate-700 bg-slate-800">
                                <img src="{{ $avatarUrl }}" alt="{{ $user->nama ?? 'Admin' }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-xs font-bold text-white truncate leading-tight">
                                        {{ $user->nama ?? $user->name ?? '-' }}
                                    </p>
                                </div>
                                <p class="text-[10px] text-slate-400 truncate leading-tight mt-0.5">
                                    {{ $user->email ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider bg-slate-800 text-blue-400 border border-slate-700 shrink-0">
                            {{ substr($user->role ?? 'ADM', 0, 8) }}
                        </span>
                    </div>
                @endauth

                <form method="POST" action="{{ route('logout') }}" onsubmit="event.preventDefault(); window.confirmLogout(event, this);">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-between w-full px-4 py-2.5 text-xs font-bold text-rose-400 hover:text-white bg-rose-500/10 hover:bg-rose-600/90 border border-rose-500/20 hover:border-rose-600 rounded-xl transition-all duration-200 group cursor-pointer">
                        <span class="group-hover:translate-x-0.5 transition-transform">Keluar Sistem</span>
                        <span class="iconify text-base" data-icon="solar:logout-2-bold-duotone"></span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ===== MAIN CONTENT CONTAINER ===== -->
        <main class="flex-1 md:ml-72 pt-20 md:pt-8 px-4 pb-12 sm:px-6 md:px-8 lg:px-10 min-w-0 bg-slate-50 min-h-screen">
            <div class="max-w-7xl mx-auto transition-opacity duration-200" id="admin-main-content">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Interactive Sidebar Drawer & SPA Navigation Script -->
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const progressBar = document.getElementById('admin-progress');
        const contentContainer = document.getElementById('admin-main-content');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
            if (hamburgerIcon) {
                hamburgerIcon.setAttribute('data-icon', 'solar:close-circle-linear');
            }
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            if (hamburgerIcon) {
                hamburgerIcon.setAttribute('data-icon', 'solar:hamburger-menu-linear');
            }
            document.body.style.overflow = '';
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function () {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }

        // Auto close on window resize to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                closeSidebar();
            }
        });

        // ==========================================
        // SEAMLESS SPA NAVIGATION ENGINE (NO REFRESH)
        // ==========================================
        function updateSidebarActiveStates(targetPath) {
            const nav = document.querySelector('aside#adminSidebar nav');
            if (!nav) return;

            const allLinks = nav.querySelectorAll('a[href]');
            const cleanTarget = targetPath.replace(/\/$/, '');

            allLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (!linkHref || linkHref.startsWith('javascript:') || linkHref.startsWith('#')) return;

                let linkPath = '';
                try {
                    linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '');
                } catch (e) {
                    return;
                }

                // Match exact or child route
                const isExact = (cleanTarget === linkPath);
                const isChild = (linkPath !== '/admin' && cleanTarget.startsWith(linkPath + '/'));
                const isActive = isExact || isChild;

                const icon = link.querySelector('.iconify');
                let dot = link.querySelector('.active-dot');

                // Determine if Palugada link
                const isPalugada = linkPath.includes('/palugada');

                if (isActive) {
                    if (isPalugada) {
                        link.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-xs';
                        if (icon) icon.className = 'iconify text-lg transition-transform group-hover:scale-110 text-amber-400';
                    } else {
                        link.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group bg-blue-600/20 text-blue-400 border border-blue-500/30 shadow-xs';
                        if (icon) icon.className = 'iconify text-lg transition-transform group-hover:scale-110 text-blue-400';
                    }

                    if (!dot) {
                        dot = document.createElement('span');
                        dot.className = `w-1.5 h-1.5 rounded-full ${isPalugada ? 'bg-amber-400' : 'bg-blue-400'} active-dot shrink-0`;
                        link.appendChild(dot);
                    }
                } else {
                    if (isPalugada) {
                        link.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group text-amber-400/90 hover:text-amber-200 hover:bg-slate-800/80 border border-amber-500/20';
                        if (icon) icon.className = 'iconify text-lg transition-transform group-hover:scale-110 text-amber-400';
                    } else {
                        link.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium';
                        if (icon) icon.className = 'iconify text-lg transition-transform group-hover:scale-110 text-slate-500 group-hover:text-slate-300';
                    }
                    if (dot) dot.remove();
                }
            });
        }

        function reexecuteScripts(container) {
            if (!container) return;
            const scripts = container.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Re-render iconify icons
            if (window.Iconify && typeof window.Iconify.scan === 'function') {
                window.Iconify.scan(container);
            }

            // Dispatch global event for listeners
            window.dispatchEvent(new CustomEvent('admin:content-loaded'));
            window.dispatchEvent(new Event('DOMContentLoaded'));
        }

        function navigateSpa(url, pushState = true) {
            if (!contentContainer) {
                window.location.href = url;
                return;
            }

            // Start Progress Bar
            if (progressBar) {
                progressBar.style.transition = 'width 0.25s ease, opacity 0.2s ease';
                progressBar.style.width = '35%';
                progressBar.style.opacity = '1';
            }
            contentContainer.style.opacity = '0.35';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(html => {
                if (progressBar) progressBar.style.width = '85%';
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Update Title
                if (doc.title) document.title = doc.title;

                // 2. Extract and Swap Main Content
                const newContent = doc.getElementById('admin-main-content');
                if (newContent) {
                    contentContainer.innerHTML = newContent.innerHTML;
                } else {
                    window.location.href = url;
                    return;
                }

                // 3. Update History
                if (pushState) {
                    window.history.pushState({ path: url }, '', url);
                }

                // 4. Update Sidebar Highlights
                const targetPath = new URL(url, window.location.origin).pathname;
                updateSidebarActiveStates(targetPath);

                // 5. Scroll to top
                window.scrollTo({ top: 0, behavior: 'instant' });

                // 6. Complete Progress Bar
                if (progressBar) {
                    progressBar.style.width = '100%';
                    setTimeout(() => {
                        progressBar.style.opacity = '0';
                        setTimeout(() => { progressBar.style.width = '0%'; }, 200);
                    }, 150);
                }
                contentContainer.style.opacity = '1';

                // 7. Re-execute scripts
                reexecuteScripts(contentContainer);

                // 8. Close Mobile Drawer
                closeSidebar();
            })
            .catch(err => {
                console.warn('SPA Navigation fallback:', err);
                window.location.href = url;
            });
        }

        // Global Interceptor for Admin Internal Links
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank') return;

            try {
                const targetUrl = new URL(link.href, window.location.origin);
                
                // Only intercept internal /admin routes
                if (targetUrl.origin === window.location.origin && targetUrl.pathname.startsWith('/admin')) {
                    // Bypass download / export / logout routes
                    if (targetUrl.pathname.includes('/export') || 
                        targetUrl.pathname.includes('/download') || 
                        targetUrl.pathname.includes('/logout')) {
                        return;
                    }

                    e.preventDefault();
                    if (window.location.href === targetUrl.href) return;

                    navigateSpa(targetUrl.href, true);
                }
            } catch (err) {
                // Ignore parsing errors and let standard navigation proceed
            }
        });

        // Browser Back/Forward Support
        window.addEventListener('popstate', function (e) {
            navigateSpa(window.location.href, false);
        });
    </script>

    @include('partials.modal-alert')
    @stack('scripts')
</body>

</html>
