@extends('admin.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-7xl mx-auto">

            @include('admin.include.message')

            <!-- Page Header -->
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Assign Depo
                </h3>

                <a href="{{ route('mpo.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    MPO List
                </a>
            </div>


            <form action="{{ route('mpo.add.depo', $mpo->id) }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf
                @method('PUT')

                {{-- Personal Information --}}
                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $mpo->full_name) }}"
                               class="w-full border p-2 rounded" placeholder="Full Name">
                        @error('full_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>


                    <div>
                        <label class="block text-sm font-medium mb-1">Depo</label>
                        <select name="depo_id" class="w-full border p-2 rounded">
                            <option value="">Select Depo</option>
                            @foreach($depos as $depo)
                                <option value="{{ $depo->id }}" {{ old('depo_id', $mpo->depo_id) == $depo->id ? 'selected' : '' }}>
                                    {{ $depo->depo_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('depo_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>
                </div>
                {{-- Submit Button --}}
                <div class="mt-6 text-end">
                    <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded">
                        Update MPO
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection
