@extends('depo.master')
@section('content')
    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            @include('depo.include.message')

            <!-- Page Header -->
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                   Purchase Verification
                </h3>

                <a href="{{ route('depo.purchase.pending') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Pending Purchase
                </a>
            </div>

            <!-- Form Card -->

            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.pending-purchase.update', $distribute->id) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <div class="flex flex-col">
                        <label class="mb-2 text-sm font-medium text-gray-600">Status</label>
                        <select name="order_status" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            <option>---select---</option>
                            <option value="1" {{ $distribute->order_status == 1 ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ $distribute->order_status == 2 ? 'selected' : '' }}>Approved</option>
{{--                            <option value="3" {{ $distribute->order_status == 3 ? 'selected' : '' }}>Delivered</option>--}}
                            <option value="4" {{ $distribute->order_status == 4 ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Verify Purchase
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
