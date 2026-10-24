@extends("admin.master")
@section("content")
<div class="min-h-screen p-4 sm:p-6 lg:p-8" style="background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 100%);">
    <div class="max-w-4xl mx-auto">
        @include('admin.include.message')
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                        <i class="fa fa-user text-sm"></i>
                    </span>
                    User Details
                </h3>
                <p class="text-sm text-gray-500 mt-1 ml-13">{{ $user->name }} — {{ $user->user_id }}</p>
            </div>
            <a href="{{ route('admin.user.index') }}"
               class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm transition-all duration-200 hover:shadow-md">
                <i class="fa fa-reply"></i>
                Back to Users
            </a>
        </div>

        {{-- Basic Info --}}
        <div class="bg-white/80 rounded-2xl shadow-lg shadow-indigo-100/50 border border-white/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-between">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fa fa-user-circle"></i>
                    Basic Information
                </h5>

            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Name</label>
                        <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">User ID</label>
                        <p class="text-sm font-semibold text-gray-800">{{ $user->user_id }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Phone</label>
                        <p class="text-sm font-semibold text-gray-800">{{ $user->phone ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Email</label>
                        <p class="text-sm font-semibold text-gray-800">{{ $user->email ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Reference ID</label>
                        <p class="text-sm font-semibold text-gray-800">{{ $user->ref_id ?? '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Status</label>
                        @if($user->status)
                            <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional Info --}}
        <div class="mt-6 bg-white/80 rounded-2xl shadow-lg shadow-indigo-100/50 border border-white/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 flex items-center justify-between">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fa fa-id-card"></i>
                    Additional Information
                </h5>
                @if(can_access('admin.user.update'))
                <button type="button" onclick="openDetailModal()"
                        class="px-3 py-1.5 text-xs font-medium text-white bg-white/20 hover:bg-white/30 rounded-lg transition">
                    <i class="fa fa-edit mr-1"></i> Edit
                </button>
                @endif
            </div>
            <div class="p-6">
                @if($user->detail)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Father's Name</label>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->detail->father_name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Mother's Name</label>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->detail->mother_name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Age</label>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->detail->age ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Gender</label>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->detail->gender ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">NID</label>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->detail->nid ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Address</label>
                            <p class="text-sm font-semibold text-gray-800">{{ $user->detail->address ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Profile Image</label>
                            @if($user->detail->image)
                                <img src="{{ asset('image/user/' . $user->detail->image) }}" alt="Profile" onclick="openLightbox(this.src)"
                                     class="h-20 w-20 rounded-xl object-cover border cursor-pointer hover:opacity-80 transition">
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">NID Front Image</label>
                            @if($user->detail->nid_front_image)
                                <img src="{{ asset('image/user/' . $user->detail->nid_front_image) }}" alt="NID Front" onclick="openLightbox(this.src)"
                                     class="h-20 rounded-xl object-cover border cursor-pointer hover:opacity-80 transition">
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">NID Back Image</label>
                            @if($user->detail->nid_back_image)
                                <img src="{{ asset('image/user/' . $user->detail->nid_back_image) }}" alt="NID Back" onclick="openLightbox(this.src)"
                                     class="h-20 rounded-xl object-cover border cursor-pointer hover:opacity-80 transition">
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-sky-100 flex items-center justify-center">
                            <i class="fa fa-id-card text-sky-400"></i>
                        </div>
                        <p class="text-sm text-gray-400 mb-4">No additional information added yet.</p>
                        @if(can_access('admin.user.update'))
                        <button type="button" onclick="openDetailModal()"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-lg shadow-sky-200 transition">
                            <i class="fa fa-plus"></i> Add Information
                        </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Nominees --}}
        <div class="mt-6 bg-white/80 rounded-2xl shadow-lg shadow-indigo-100/50 border border-white/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-between">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fa fa-users"></i>
                    Nominees
                </h5>
                @if($user->nominees->isEmpty())
                    @if(can_access('admin.user.nominee.store'))
                    <button type="button" onclick="openNomineeModal()"
                            class="px-3 py-1.5 text-xs font-medium text-white bg-white/20 hover:bg-white/30 rounded-lg transition">
                        <i class="fa fa-plus mr-1"></i> Add
                    </button>
                    @endif
                @endif
            </div>
            <div class="p-6">
                @if($user->nominees->isEmpty())
                    <div class="text-center py-8">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i class="fa fa-user-plus text-emerald-400"></i>
                        </div>
                        <p class="text-sm text-gray-400">No nominee added yet.</p>
                    </div>
                @else
                    @foreach($user->nominees as $nominee)
                    <div class="bg-gradient-to-br from-white to-emerald-50/30 rounded-xl border border-emerald-100/60 overflow-hidden">
                        <div class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-600">
                            <div class="flex items-center justify-between">
                                <h6 class="text-white font-semibold text-sm">{{ $nominee->name }}</h6>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-[10px] font-medium text-emerald-900 bg-emerald-300/80 rounded-full">{{ $nominee->relation }}</span>
                                    @if(can_access('admin.user.nominee.update'))
                                    <button type="button" onclick="openEditNominee({{ $nominee->id }}, '{{ $nominee->name }}', '{{ $nominee->relation }}', '{{ $nominee->phone }}', '{{ $nominee->email }}', '{{ $nominee->address }}', '{{ $nominee->image }}', '{{ $nominee->nid_front_image }}', '{{ $nominee->nid_back_image }}')"
                                            class="text-white/70 hover:text-white transition text-xs" title="Edit">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    @endif
                                    @if(can_access('admin.user.nominee.destroy'))
                                    <form action="{{ route('admin.user.nominee.destroy', [$user->id, $nominee->id]) }}" method="POST" onsubmit="return confirm('Delete this nominee?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white/70 hover:text-red-300 transition text-xs" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-emerald-50/50 rounded-lg p-2.5">
                                    <span class="text-[10px] font-medium text-gray-400 uppercase">Phone</span>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $nominee->phone ?? '—' }}</p>
                                </div>
                                <div class="bg-emerald-50/50 rounded-lg p-2.5">
                                    <span class="text-[10px] font-medium text-gray-400 uppercase">Email</span>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5 truncate">{{ $nominee->email ?? '—' }}</p>
                                </div>
                            </div>
                            @if($nominee->address)
                            <div class="bg-emerald-50/50 rounded-lg p-2.5">
                                <span class="text-[10px] font-medium text-gray-400 uppercase">Address</span>
                                <p class="text-sm text-gray-700 mt-0.5">{{ $nominee->address }}</p>
                            </div>
                            @endif
                            @if($nominee->image || $nominee->nid_front_image || $nominee->nid_back_image)
                            <div>
                                <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider flex items-center gap-1 mb-2">Documents</span>
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($nominee->image)
                                    <img src="{{ asset('image/nominee/' . $nominee->image) }}" alt="Nominee" onclick="openLightbox(this.src)"
                                         class="h-14 w-14 rounded-lg object-cover border-2 border-emerald-100 cursor-pointer hover:opacity-80 transition">
                                    @endif
                                    @if($nominee->nid_front_image)
                                    <img src="{{ asset('image/nominee/' . $nominee->nid_front_image) }}" alt="NID Front" onclick="openLightbox(this.src)"
                                         class="h-14 rounded-lg object-cover border-2 border-emerald-100 cursor-pointer hover:opacity-80 transition">
                                    @endif
                                    @if($nominee->nid_back_image)
                                    <img src="{{ asset('image/nominee/' . $nominee->nid_back_image) }}" alt="NID Back" onclick="openLightbox(this.src)"
                                         class="h-14 rounded-lg object-cover border-2 border-emerald-100 cursor-pointer hover:opacity-80 transition">
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit Basic Info Modal --}}
<div id="basic-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-between">
            <h5 class="text-white font-semibold"><i class="fa fa-user-circle"></i> Edit Basic Information</h5>
            <button type="button" onclick="closeModal('basic-modal')" class="text-white/80 hover:text-white transition">
                <i class="fa fa-times"></i>
            </button>
        </div>
        @if(can_access('admin.user.update'))
        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="basic">
            {{-- pass detail fields through so controller doesn't wipe them --}}
            <input type="hidden" name="father_name" value="{{ $user->detail?->father_name }}">
            <input type="hidden" name="mother_name" value="{{ $user->detail?->mother_name }}">
            <input type="hidden" name="age" value="{{ $user->detail?->age }}">
            <input type="hidden" name="gender" value="{{ $user->detail?->gender }}">
            <input type="hidden" name="address" value="{{ $user->detail?->address }}">
            <input type="hidden" name="nid" value="{{ $user->detail?->nid }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $user->name }}" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ $user->phone }}" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}"
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reference ID <span class="text-red-500">*</span></label>
                    <input type="text" name="ref_id" value="{{ $user->ref_id }}" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                            class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password"
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all"
                           placeholder="Leave blank to keep current">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('basic-modal')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-lg shadow-lg shadow-indigo-200 transition">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

{{-- Edit Additional Info Modal --}}
<div id="detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 flex items-center justify-between shrink-0">
            <h5 class="text-white font-semibold flex items-center gap-2">
                <i class="fa fa-id-card"></i> Edit Additional Information
            </h5>
            <button type="button" onclick="closeModal('detail-modal')" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition">
                <i class="fa fa-times"></i>
            </button>
        </div>

        @if(can_access('admin.user.update'))
        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="detail">
            <input type="hidden" name="name" value="{{ $user->name }}">
            <input type="hidden" name="phone" value="{{ $user->phone }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            <input type="hidden" name="status" value="{{ $user->status }}">
            <input type="hidden" name="ref_id" value="{{ $user->ref_id }}">

            {{-- Scrollable body --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-5">

                {{-- Personal Details --}}
                <div>
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="flex-1 h-px bg-sky-100"></span> Personal Details <span class="flex-1 h-px bg-sky-100"></span>
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Father's Name</label>
                            <input type="text" name="father_name" value="{{ $user->detail?->father_name }}"
                                   class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-sky-400 focus:ring focus:ring-sky-200/50 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Mother's Name</label>
                            <input type="text" name="mother_name" value="{{ $user->detail?->mother_name }}"
                                   class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-sky-400 focus:ring focus:ring-sky-200/50 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Age</label>
                            <input type="number" name="age" value="{{ $user->detail?->age }}" min="1" max="150"
                                   class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-sky-400 focus:ring focus:ring-sky-200/50 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Gender</label>
                            <select name="gender" class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-sky-400 focus:ring focus:ring-sky-200/50 transition-all text-sm">
                                <option value="">Select</option>
                                <option value="male"   {{ $user->detail?->gender == 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $user->detail?->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ $user->detail?->gender == 'other'  ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">NID</label>
                            <input type="text" name="nid" value="{{ $user->detail?->nid }}"
                                   class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-sky-400 focus:ring focus:ring-sky-200/50 transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Address</label>
                            <textarea name="address" rows="2" class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-sky-400 focus:ring focus:ring-sky-200/50 transition-all text-sm">{{ $user->detail?->address }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Documents --}}
                <div>
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="flex-1 h-px bg-sky-100"></span> Documents <span class="flex-1 h-px bg-sky-100"></span>
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Profile Image --}}
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Profile Photo</label>
                            @if($user->detail?->image)
                                <img src="{{ asset('image/user/' . $user->detail->image) }}" alt="Profile"
                                     class="h-16 w-16 rounded-lg object-cover border border-gray-200 mb-2">
                            @endif
                            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg"
                                   class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        </div>
                        {{-- NID Front --}}
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NID Front</label>
                            @if($user->detail?->nid_front_image)
                                <img src="{{ asset('image/user/' . $user->detail->nid_front_image) }}" alt="NID Front"
                                     class="h-16 w-full rounded-lg object-cover border border-gray-200 mb-2">
                            @endif
                            <input type="file" name="nid_front_image" accept="image/jpeg,image/png,image/jpg"
                                   class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        </div>
                        {{-- NID Back --}}
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NID Back</label>
                            @if($user->detail?->nid_back_image)
                                <img src="{{ asset('image/user/' . $user->detail->nid_back_image) }}" alt="NID Back"
                                     class="h-16 w-full rounded-lg object-cover border border-gray-200 mb-2">
                            @endif
                            <input type="file" name="nid_back_image" accept="image/jpeg,image/png,image/jpg"
                                   class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="closeModal('detail-modal')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-white hover:bg-gray-100 rounded-xl border border-gray-200 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 rounded-xl shadow-lg shadow-sky-200 transition">
                    <i class="fa fa-save mr-1.5"></i> Save Changes
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

{{-- Add Nominee Modal --}}
<div id="nominee-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-between">
            <h5 class="text-white font-semibold"><i class="fa fa-user-plus"></i> Add Nominee</h5>
            <button type="button" onclick="closeModal('nominee-modal')" class="text-white/80 hover:text-white transition">
                <i class="fa fa-times"></i>
            </button>
        </div>
        @if(can_access('admin.user.nominee.store'))
        <form action="{{ route('admin.user.nominee.store', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relation <span class="text-red-500">*</span></label>
                    <input type="text" name="relation" placeholder="Spouse, Father, etc." required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" required
                              class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all file:!rounded-lg file:!border-0 file:!bg-emerald-50 file:!text-emerald-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-emerald-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NID Front <span class="text-red-500">*</span></label>
                    <input type="file" name="nid_front_image" accept="image/jpeg,image/png,image/jpg" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all file:!rounded-lg file:!border-0 file:!bg-emerald-50 file:!text-emerald-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-emerald-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NID Back <span class="text-red-500">*</span></label>
                    <input type="file" name="nid_back_image" accept="image/jpeg,image/png,image/jpg" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all file:!rounded-lg file:!border-0 file:!bg-emerald-50 file:!text-emerald-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-emerald-100">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('nominee-modal')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-lg shadow-lg shadow-emerald-200 transition">
                    <i class="fa fa-save mr-1"></i> Save
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

{{-- Edit Nominee Modal --}}
<div id="edit-nominee-modal" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 z-10 px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-between rounded-t-2xl">
            <h5 class="text-white font-semibold flex items-center gap-2">
                <i class="fa fa-user-edit"></i>
                Edit Nominee
            </h5>
            <button type="button" onclick="closeModal('edit-nominee-modal')"
                    class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition">
                <i class="fa fa-times"></i>
            </button>
        </div>
        @if(can_access('admin.user.nominee.update'))
        <form id="edit-nominee-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit_nominee_name" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="edit_nominee_phone" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="edit_nominee_email" required
                       class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relation <span class="text-red-500">*</span></label>
                    <input type="text" name="relation" id="edit_nominee_relation" required
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                <textarea name="address" id="edit_nominee_address" required rows="2"
                          class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                <div id="edit_nominee_image_current" class="mb-2 hidden">
                    <img id="edit_nominee_image_preview" class="h-16 w-16 rounded-lg object-cover border-2 border-emerald-100 mb-1">
                    <p class="text-xs text-emerald-600 font-medium">Current image</p>
                </div>
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg"
                       class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all file:!rounded-lg file:!border-0 file:!bg-emerald-50 file:!text-emerald-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-emerald-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NID Front</label>
                    <div id="edit_nominee_nid_front_current" class="mb-2 hidden">
                        <img id="edit_nominee_nid_front_preview" class="h-14 rounded-lg object-cover border-2 border-emerald-100 mb-1">
                        <p class="text-xs text-emerald-600 font-medium">Current image</p>
                    </div>
                    <input type="file" name="nid_front_image" accept="image/jpeg,image/png,image/jpg"
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all file:!rounded-lg file:!border-0 file:!bg-emerald-50 file:!text-emerald-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-emerald-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NID Back</label>
                    <div id="edit_nominee_nid_back_current" class="mb-2 hidden">
                        <img id="edit_nominee_nid_back_preview" class="h-14 rounded-lg object-cover border-2 border-emerald-100 mb-1">
                        <p class="text-xs text-emerald-600 font-medium">Current image</p>
                    </div>
                    <input type="file" name="nid_back_image" accept="image/jpeg,image/png,image/jpg"
                           class="form-input w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring focus:ring-indigo-200/50 transition-all file:!rounded-lg file:!border-0 file:!bg-emerald-50 file:!text-emerald-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-emerald-100">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('edit-nominee-modal')"
                        class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 rounded-lg shadow-lg shadow-emerald-200 transition">
                    <i class="fa fa-save mr-1"></i> Update
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

{{-- Image Lightbox --}}
<div id="image-lightbox" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/70 backdrop-blur-sm" onclick="closeLightbox(event)">
    <button type="button" onclick="closeLightbox()" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition z-10">
        <i class="fa fa-times text-lg"></i>
    </button>
    <div class="max-w-[90vw] max-h-[90vh] flex items-center justify-center" onclick="event.stopPropagation()">
        <img id="lightbox-image" src="" alt="Preview" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl object-contain">
    </div>
</div>
@endsection

@section('scripts')
<script>
function openBasicModal() {
    document.getElementById('basic-modal').classList.remove('hidden');
    document.getElementById('basic-modal').classList.add('flex');
}

function openDetailModal() {
    document.getElementById('detail-modal').classList.remove('hidden');
    document.getElementById('detail-modal').classList.add('flex');
}

function openNomineeModal() {
    document.getElementById('nominee-modal').classList.remove('hidden');
    document.getElementById('nominee-modal').classList.add('flex');
}

function openEditNominee(id, name, relation, phone, email, address, image, nidFront, nidBack) {
    const form = document.getElementById('edit-nominee-form');
    form.action = '{{ url('admin/user') }}/' + {{ $user->id }} + '/nominee/' + id;

    document.getElementById('edit_nominee_name').value = name;
    document.getElementById('edit_nominee_phone').value = phone;
    document.getElementById('edit_nominee_email').value = email;
    document.getElementById('edit_nominee_relation').value = relation;
    document.getElementById('edit_nominee_address').value = address;

    const imgCurrent = document.getElementById('edit_nominee_image_current');
    const imgPreview = document.getElementById('edit_nominee_image_preview');
    if (image) {
        imgCurrent.classList.remove('hidden');
        imgPreview.src = '{{ asset('image/nominee') }}/' + image;
    } else {
        imgCurrent.classList.add('hidden');
    }

    const nfCurrent = document.getElementById('edit_nominee_nid_front_current');
    const nfPreview = document.getElementById('edit_nominee_nid_front_preview');
    if (nidFront) {
        nfCurrent.classList.remove('hidden');
        nfPreview.src = '{{ asset('image/nominee') }}/' + nidFront;
    } else {
        nfCurrent.classList.add('hidden');
    }

    const nbCurrent = document.getElementById('edit_nominee_nid_back_current');
    const nbPreview = document.getElementById('edit_nominee_nid_back_preview');
    if (nidBack) {
        nbCurrent.classList.remove('hidden');
        nbPreview.src = '{{ asset('image/nominee') }}/' + nidBack;
    } else {
        nbCurrent.classList.add('hidden');
    }

    document.getElementById('edit-nominee-modal').classList.remove('hidden');
    document.getElementById('edit-nominee-modal').classList.add('flex');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}

function openLightbox(src) {
    document.getElementById('lightbox-image').src = src;
    document.getElementById('image-lightbox').classList.remove('hidden');
    document.getElementById('image-lightbox').classList.add('flex');
}

function closeLightbox(e) {
    if (e && e.target !== e.currentTarget) return;
    document.getElementById('image-lightbox').classList.add('hidden');
    document.getElementById('image-lightbox').classList.remove('flex');
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-black\\/40')) {
        e.target.closest('.fixed').classList.add('hidden');
        e.target.closest('.fixed').classList.remove('flex');
    }
});
</script>
@endsection