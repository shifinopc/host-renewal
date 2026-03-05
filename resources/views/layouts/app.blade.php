<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Host Renewal') }}</title>

        <!-- Quicksand font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Material Symbols (icons) -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" />

        <!-- Tailwind CSS via CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Quicksand"', 'system-ui', 'sans-serif'],
                        },
                    },
                },
            };
        </script>

        <!-- Turbo (Hotwire) -->
        <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js" data-turbo-track="reload"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Alpine.js for small interactions -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-slate-50 min-h-screen flex font-sans">
        <aside class="w-64 bg-white flex flex-col border-r border-slate-200">
            <!-- Brand / App -->
            <div class="px-5 py-4 flex items-center justify-between border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-sm font-semibold shadow-sm">
                        HR
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Hostlab</p>
                        <p class="text-[11px] text-slate-400">Renewal Management</p>
                    </div>
                </div>
                <span class="text-[10px] text-slate-300">⌄</span>
            </div>

            <nav class="flex-1 px-4 py-4 text-sm flex flex-col">
                <!-- Menu -->
                <div class="mb-5">
                    <p class="text-[11px] font-semibold text-slate-400 uppercase mb-2">Menu</p>
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-2 px-3 py-2.5 rounded-xl border {{ request()->routeIs('dashboard') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-rounded text-[18px]">
                                space_dashboard
                            </span>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('domains.index') }}"
                           class="flex items-center gap-2 px-3 py-2.5 rounded-xl border {{ request()->routeIs('domains.*') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-rounded text-[18px]">
                                language
                            </span>
                            <span>Domains</span>
                        </a>
                        <a href="{{ route('servers.index') }}"
                           class="flex items-center gap-2 px-3 py-2.5 rounded-xl border {{ request()->routeIs('servers.*') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-rounded text-[18px]">
                                dns
                            </span>
                            <span>Servers</span>
                        </a>
                        <a href="{{ route('customers.index') }}"
                           class="flex items-center gap-2 px-3 py-2.5 rounded-xl border {{ request()->routeIs('customers.*') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-rounded text-[18px]">
                                group
                            </span>
                            <span>Customers</span>
                        </a>
                        <div class="space-y-1" x-data="{ paymentsOpen: {{ request()->routeIs('payments.*') ? 'true' : 'false' }} }">
                            <button type="button" @@click="paymentsOpen = !paymentsOpen"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl border {{ request()->routeIs('payments.*') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-rounded text-[18px]">payments</span>
                                    <span>Payments</span>
                                </div>
                                <span class="material-symbols-rounded text-[16px] transition-transform" :class="paymentsOpen ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="paymentsOpen" x-cloak class="pl-5 space-y-0.5">
                                <a href="{{ route('payments.all') }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] {{ request()->routeIs('payments.all') ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span class="material-symbols-rounded text-[16px]">list</span>
                                    All Payments
                                </a>
                                <a href="{{ route('payments.all') }}?add=1"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] {{ request()->routeIs('payments.all') && request()->has('add') ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span class="material-symbols-rounded text-[16px]">add_circle</span>
                                    Add Payment
                                </a>
                                <a href="{{ route('payments.reports') }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-[13px] {{ request()->routeIs('payments.reports') ? 'text-indigo-600 font-semibold bg-indigo-50' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span class="material-symbols-rounded text-[16px]">analytics</span>
                                    Reports
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('proforma-invoices.index') }}"
                           class="flex items-center gap-2 px-3 py-2.5 rounded-xl border {{ request()->routeIs('proforma-invoices.*') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-rounded text-[18px]">receipt_long</span>
                            <span>Proforma invoice</span>
                        </a>

                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('settings.index') }}"
                               class="flex items-center gap-2 px-3 py-2.5 rounded-xl border {{ request()->routeIs('settings.*') ? 'border-indigo-500 bg-indigo-50 text-indigo-600 font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <span class="material-symbols-rounded text-[18px]">
                                    tune
                                </span>
                                <span>Settings</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Spacer -->
                <div class="flex-1"></div>

                <!-- Profile -->
                <div class="mb-2 border-t border-slate-100 pt-3">
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-50 cursor-pointer">
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-[13px] font-semibold text-slate-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                                <p class="text-[11px] text-slate-400">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-300">›</span>
                    </div>
                </div>
            </nav>
        </aside>

        <main class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
                <div>
                    <h1 class="text-lg font-semibold text-slate-800">
                        {{ $pageTitle ?? 'Dashboard' }}
                    </h1>
                    @isset($pageSubtitle)
                        <p class="text-xs text-slate-500">{{ $pageSubtitle }}</p>
                    @endisset
                </div>
                <div class="flex items-center gap-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs px-3 py-1.5 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-50">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <section class="flex-1 p-6 space-y-4">
                @yield('content')
            </section>
        </main>

        @if (session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: @json(session('status')),
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation error',
                            text: @json($errors->first()),
                        });
                    }
                });
            </script>
        @endif
    </body>
</html>

