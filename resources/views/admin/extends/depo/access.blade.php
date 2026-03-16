@extends('admin.master')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">

            {{-- Greeting --}}
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    👋 Hello, {{ Auth::user()->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    This is a preview of the depo login access
                </p>
            </div>

            {{-- Dummy Login Form --}}
            <div class="space-y-4">

                {{-- Email --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Email
                    </label>
                    <input
                        type="email"
                        value="{{ $depo->user->email }}"
                        readonly
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed focus:outline-none"
                    >
                </div>

                {{-- Dummy Password --}}
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Password
                    </label>
                    <input
                        type="password"
                        value="********"
                        readonly
                        class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed focus:outline-none"
                    >
                </div>

            </div>

            {{-- Enter Button --}}
            <div class="mt-6">
                <a href="{{ $loginUrl }}"
                   target="_blank"
                   class="w-full inline-block text-center bg-indigo-600 hover:bg-indigo-700
                      text-white font-semibold py-2 px-4 rounded-md transition">
                    🚀 Enter Depo Panel
                </a>
            </div>

            {{-- Footer Info --}}
            <p class="text-xs text-gray-500 text-center mt-4">
                🔒 This is a secure one-click login. Credentials are not editable.
            </p>

        </div>
    </div>
@endsection
