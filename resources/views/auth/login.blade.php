@php($title = 'Login - Host Renewal')

@extends('layouts.auth')

@section('content')
    <div class="max-w-sm mx-auto">
        <div class="mb-6">
            <p class="text-xs font-semibold text-indigo-600 uppercase tracking-[0.18em] mb-2">Welcome</p>
            <h2 class="text-2xl font-semibold text-slate-900 mb-1">Sign in to dashboard</h2>
            <p class="text-xs text-slate-500">
                Default admin account:
                <span class="font-mono text-slate-700">admin / 123qwe</span>
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
            @csrf

            <div>
                <label for="login" class="block text-xs font-medium text-slate-700 mb-1">
                    Username or Email
                </label>
                <input
                    id="login"
                    name="login"
                    type="text"
                    value="{{ old('login', 'admin') }}"
                    required
                    autofocus
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-medium text-slate-700 mb-1">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    value="123qwe"
                    required
                    class="block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="inline-flex items-center gap-2 text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                    <span>Remember me</span>
                </label>
            </div>

            <button
                type="submit"
                class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Continue
            </button>
        </form>
    </div>
@endsection

