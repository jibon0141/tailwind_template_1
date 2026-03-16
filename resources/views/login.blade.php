<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        use App\Models\CompanySetting;
        $company = CompanySetting::first();
    @endphp

    <title>{{ $company->company_name ?? 'Tahsin Pharma' }}</title>

    @if($company && $company->logo)
        <link rel="icon" type="image/png"
              href="{{ asset('image/company_logo/'.$company->logo) }}">
    @elseif($company && $company->favicon)
        <link rel="icon" type="image/png"
              href="{{ asset('image/company_favicon/'.$company->favicon) }}">
    @endif

    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-cover bg-center"
      style="background-image: url('{{ asset('image/slider/slider3.jpg') }}');">



    <!-- Login Card -->
<div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">

    <!-- Logo + Company Name -->
    <div class="flex flex-col items-center mb-6">

        @if($company && $company->logo)
            <img src="{{ asset('image/company_logo/'.$company->logo) }}"
                 alt="Company Logo"
                 class="h-20 w-auto object-contain mb-2">
        @endif

    </div>

    @include('admin.include.message')

    <h2 class="text-lg font-semibold text-center text-gray-700 mb-6">
        Log in to your account
    </h2>

    <!-- Login Form -->
    <form action="{{ url('/login') }}" method="POST">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2" for="email">
                Email
            </label>

            <input type="email"
                   name="email"
                   id="email"
                   required
                   placeholder="you@example.com"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md
                          focus:outline-none focus:ring focus:ring-indigo-200
                          focus:border-indigo-500">

            @error('email')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2" for="password">
                Password
            </label>

            <input type="password"
                   name="password"
                   id="password"
                   required
                   placeholder="********"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md
                          focus:outline-none focus:ring focus:ring-indigo-200
                          focus:border-indigo-500">
        </div>

        <!-- Remember -->
        <div class="flex items-center mb-6">
            <input id="remember_me"
                   type="checkbox"
                   name="remember"
                   class="rounded border-gray-300 text-indigo-600 mr-2">

            <label for="remember_me" class="text-gray-700 text-sm">
                Remember me
            </label>
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700
                       text-white font-semibold py-2 px-4 rounded-md
                       focus:outline-none focus:ring focus:ring-indigo-200">
            Log in
        </button>
    </form>

    <!-- Forgot password -->
    <p class="mt-4 text-center text-sm text-gray-600">
        <a href="#" class="underline hover:text-gray-900">
            Forgot your password?
        </a>
    </p>

</div>

</body>
</html>
