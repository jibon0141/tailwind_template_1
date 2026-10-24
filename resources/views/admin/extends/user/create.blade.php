@extends("admin.master")
@section("content")
<div class="min-h-screen p-4 sm:p-6 lg:p-8" style="background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 100%);">
    <div class="max-w-4xl mx-auto">
        @include('admin.include.message')
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                        <i class="fa fa-user-plus text-sm"></i>
                    </span>
                    Create New User
                </h3>
                <p class="text-sm text-gray-500 mt-1 ml-13">Add a new user to the system</p>
            </div>
            <a href="{{ route('admin.user.index') }}"
               class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm transition-all duration-200 hover:shadow-md">
                <i class="fa fa-reply"></i>
                Back to Users
            </a>
        </div>
        <div class="bg-white/80 rounded-2xl shadow-lg shadow-indigo-100/50 border border-white/50 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="fa fa-info-circle text-emerald-500"></i>
                <span class="font-semibold text-gray-700">User Information</span>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.user.create') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                   placeholder="Enter full name">
                            @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email
                            </label>
                            <div class="relative">
                                <i class="fa fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all !pl-10"
                                       placeholder="Enter email address">
                            </div>
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all !pl-10"
                                       placeholder="Enter phone number">
                            </div>
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Reference ID <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="ref_id" value="{{ old('ref_id') }}"
                                   class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                   placeholder="Enter reference ID" id="ref_id"
                                   data-referrer-url="{{ route('admin.user.get-referrer') }}"
                                   oninput="lookupReferrer(this.value.trim())">
                            <div id="referrerInfo" class="hidden mt-2"></div>
                            @error('ref_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="password" name="password" id="password"
                                       class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all !pl-10 !pr-10"
                                       placeholder="Enter password">
                                <button type="button" onclick="togglePassword('password', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all !pl-10 !pr-10"
                                       placeholder="Confirm password">
                                <button type="button" onclick="togglePassword('password_confirmation', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4 h-11 items-center">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}
                                    class="text-indigo-600 focus:ring-indigo-500 rounded-full">
                                    <span class="text-sm text-gray-600">Active</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="status" value="0" {{ old('status', '0') == '0' ? 'checked' : '' }}
                                    class="text-red-500 focus:ring-red-400 rounded-full">
                                    <span class="text-sm text-gray-600">Inactive</span>
                                </label>
                            </div>
                            @error('status')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="mt-8 pt-5 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.user.index') }}"
                           class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 transition-all w-full sm:w-auto text-center">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-xl shadow-lg shadow-emerald-200 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5 w-full sm:w-auto">
                            <i class="fa fa-save mr-1.5"></i>
                            Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function togglePassword(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa fa-eye';
        }
    }

    function lookupReferrer(refId) {
        if (typeof refId !== 'string') refId = '';
        var info = document.getElementById('referrerInfo');
        if (!info) return;
        if (!refId) {
            info.classList.add('hidden');
            info.innerHTML = '';
            return;
        }
        var input = document.getElementById('ref_id');
        var url = input ? input.getAttribute('data-referrer-url') : '{{ route("admin.user.get-referrer") }}';
        fetch(url + '?ref_id=' + encodeURIComponent(refId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(function (res) { return res.json(); })
            .then(function (res) {
                if (res.success) {
                    info.classList.remove('hidden');
                    info.innerHTML = '<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-sm"><i class="fa fa-user-check text-xs"></i> ' +
                        res.name +
                        (res.phone ? ' <span class="text-indigo-400">— ' + res.phone + '</span>' : '') +
                        '</span>';
                } else {
                    info.classList.remove('hidden');
                    info.innerHTML = '<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-sm"><i class="fa fa-exclamation-triangle text-xs"></i> ' +
                        (res.message || 'Invalid reference ID.') +
                        '</span>';
                }
            })
            .catch(function () {
                info.classList.add('hidden');
                info.innerHTML = '';
            });
    }
    if (document.getElementById('ref_id') && document.getElementById('ref_id').value) {
        lookupReferrer(document.getElementById('ref_id').value.trim());
    }
</script>
@endsection
