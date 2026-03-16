@extends("depo.master")
@section("content")

    <div class="min-h-screen bg-gray-100 py-8">
        <div class="max-w-4xl mx-auto px-4">

            @include('admin.include.message')

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
                <h3 class="text-2xl font-bold text-gray-800">Handle Order</h3>

                <a href="{{ route('depo.sale.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i> Sale List
                </a>
            </div>

            <!-- Form Card -->
            <div class="bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.sale.manage.update',$sale->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Order Status -->
                        <div class="flex flex-col">
                            <label for="order_status" class="mb-2 text-sm font-medium text-gray-700">Order Status</label>
                            <select name="order_status" id="order_status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <option value="1" {{ $sale->order_status == '1' ? 'selected' : '' }}>Pending</option>
                                <option value="2" {{ $sale->order_status == '2' ? 'selected' : '' }}>Approved</option>
                                <option value="3" {{ $sale->order_status == '3' ? 'selected' : '' }}>Delivered</option>
                            </select>
                        </div>



                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update Status
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
