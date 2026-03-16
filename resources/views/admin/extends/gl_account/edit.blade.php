@extends("admin.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100 flex flex-col items-center">
        <div class="w-full max-w-3xl px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between w-full">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update GL Account
                </h3>

                <a href="{{ route('admin.gl-account.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    GL Account List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow w-full">
                <form action="{{ route('admin.gl-account.update', $account->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Account Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Account Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="account_name"
                                   value="{{ old('account_name', $account->account_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., Cash Account" required>
                            @error('account_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update GL Account
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
