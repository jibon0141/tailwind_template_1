@extends('admin.master')

@section('content')
    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update Investor
                </h3>

                <a href="{{ route('admin.investor.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Investor List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('admin.investor.update', $investor->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $investor->name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Full Name" required>
                            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $investor->email) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="example@mail.com" required>
                            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Contact -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Contact <span class="text-red-500">*</span></label>
                            <input type="text" name="contact" value="{{ old('contact', $investor->contact) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Phone Number" required>
                            @error('contact') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- NID -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">NID <span class="text-red-500">*</span></label>
                            <input type="text" name="nid" value="{{ old('nid', $investor->nid) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="National ID" required>
                            @error('nid') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- NID Front -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">NID Front</label>
                            @if($investor->nid_front)
                                <img src="{{ asset('image/investor/investor_image/nid_front/' . $investor->nid_front) }}" alt="NID Front" class="mb-2 w-32 h-20 object-cover border rounded">
                            @endif
                            <input type="file" name="nid_front" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('nid_front') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- NID Back -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">NID Back</label>
                            @if($investor->nid_back)
                                <img src="{{ asset('image/investor/investor_image/nid_back/' . $investor->nid_back) }}" alt="NID Back" class="mb-2 w-32 h-20 object-cover border rounded">
                            @endif
                            <input type="file" name="nid_back"  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('nid_back') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bank Details -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">Bank Details <span class="text-red-500">*</span></label>
                            <textarea name="bank_details" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" rows="2" required>{{ old('bank_details', $investor->bank_details) }}</textarea>
                            @error('bank_details') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Address -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">Address <span class="text-red-500">*</span></label>
                            <textarea name="address" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" rows="2" required>{{ old('address', $investor->address) }}</textarea>
                            @error('address') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>


                        <!-- =================== NOMINEE SECTION =================== -->

                        <div class="md:col-span-2 mt-4 font-semibold text-gray-700 border-b pb-2">Nominee Details</div>

                        <!-- Nominee Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee Name </label>
                            <input type="text" name="nominee_name" value="{{ old('nominee_name', $investor->nominee_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Nominee Name">
                            @error('nominee_name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee Relation -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee Relation </label>
                            <input type="text" name="nominee_relation" value="{{ old('nominee_relation', $investor->nominee_relation) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Relation with Nominee" >
                            @error('nominee_relation') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee Address -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee Address </label>
                            <textarea name="nominee_address" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" rows="2" required>{{ old('nominee_address', $investor->nominee_address) }}</textarea>
                            @error('nominee_address') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee Contact -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee Contact </label>
                            <input type="text" name="nominee_contact" value="{{ old('nominee_contact', $investor->nominee_contact) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Phone Number" >
                            @error('nominee_contact') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee NID -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee NID </label>
                            <input type="text" name="nominee_nid" value="{{ old('nominee_nid', $investor->nominee_nid) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Nominee NID" >
                            @error('nominee_nid') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee NID Front -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee NID Front</label>
                            @if($investor->nominee_nid_front)
                                <img src="{{ asset('image/investor/investor_nominee_image/nid_front/' . $investor->nominee_nid_front) }}" alt="Nominee NID Front" class="mb-2 w-32 h-20 object-cover border rounded">
                            @endif
                            <input type="file" name="nominee_nid_front" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('nominee_nid_front') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee NID Back -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee NID Back</label>
                            @if($investor->nominee_nid_back)
                                <img src="{{ asset('image/investor/investor_nominee_image/nid_back/' . $investor->nominee_nid_back) }}" alt="Nominee NID Back" class="mb-2 w-32 h-20 object-cover border rounded">
                            @endif
                            <input type="file" name="nominee_nid_back"  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('nominee_nid_back') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominee Bank Details -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">Nominee Bank Details </label>
                            <textarea name="nominee_bank_details" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" rows="2">{{ old('nominee_bank_details', $investor->nominee_bank_details) }}</textarea>
                            @error('nominee_bank_details') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Status ></label>
                            <select name="status" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" >
                                <option value="1" {{ old('status', $investor->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $investor->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update Investor
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
@endsection
