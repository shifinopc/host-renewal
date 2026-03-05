<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Login - Host Renewal' }}</title>

        <!-- Quicksand font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

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
    </head>
    <body class="min-h-screen bg-slate-50 flex items-center justify-center font-sans">
        <div class="w-full max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
            <div class="relative hidden md:flex flex-col justify-between bg-gradient-to-br from-indigo-500 via-sky-500 to-cyan-400 p-8 text-white">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-white/10 border border-white/30 flex items-center justify-center font-semibold">
                        HR
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Host Renewal</p>
                        <p class="text-xs text-white/80">Domain & Hosting Manager</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-[0.15em] text-white/70 mb-2">Admin Panel</p>
                    <h1 class="text-3xl font-semibold leading-tight mb-4">
                        Manage servers, domains,<br>and renewals in one place.
                    </h1>
                    <p class="text-sm text-white/80">
                        Track expiry dates, payments, and server load with a clean, modern interface inspired by professional dashboards.
                    </p>
                </div>

                <div class="flex items-center justify-between text-xs text-white/80">
                    <span>Secure Laravel Application</span>
                    <span>Scheduler • Reports • Alerts</span>
                </div>
            </div>

            <div class="p-8 md:p-10">
                @yield('content')
            </div>
        </div>
    </body>
</html>

