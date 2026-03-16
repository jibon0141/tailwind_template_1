@extends('depo.master')

<!-- CUSTOM SELECT2 STYLE -->
<style>
    /* Match Select2 with your other inputs */
    #chemist_house_select + .select2-container .select2-selection {
        height: 42px !important;          /* same as date/inputs */
        line-height: 42px !important;     /* vertical text alignment */
        padding: 0 10px;                  /* optional inner padding */
        border-radius: 0.375rem;          /* tailwind rounded */
        border: 1px solid #d1d5db;       /* same as other inputs */
    }
    #chemist_house_select + .select2-container .select2-selection__arrow {
        height: 42px !important;          /* arrow same height */
    }
    #chemist_house_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;     /* vertically center text */
    }
</style>

@section('content')
    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('depo.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create Chemist House Payment
                </h3>

                <a href="{{ route('depo.chemist-house-due-payment.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Payment List
                </a>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.chemist-house-due-payment.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <input type="hidden" name="chemist_house_id" id="chemist_house_id">
                        <input type="hidden" name="chemist_house_name" id="chemist_house_name_hidden">

                        <!-- Chemist House Name (Select2) -->
                        <div class="flex flex-col relative">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Chemist House Name <span class="text-red-500">*</span>
                            </label>
                            <select id="chemist_house_select" style="width: 100%;">
                                <option value="">Select Chemist House...</option>
                            </select>
                            @error('chemist_house_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Contact
                            </label>
                            <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Contact" readonly>
                        </div>

                        <!-- Payment Date -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Payment Date
                            </label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                        </div>

                        <!-- Depo Account -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Account <span class="text-red-500">*</span>
                            </label>
                            <select name="account_id" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">-- Select Depo Account --</option>
                                @foreach($depoAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_name }} ({{ number_format($account->balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Due Balance -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Due Balance <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" id="balance" name="balance" value="{{ old('balance') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Due Balance" readonly>
                        </div>

                        <!-- Receiving Amount -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Receiving Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="receiving_amount" value="{{ old('receiving_amount') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter Receiving Amount">
                        </div>

                        <!-- Document Upload -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Document
                            </label>
                            <input type="file" name="document" id="document"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </div>

                    </div>

                    <!-- Note -->
                    <div class="flex flex-col mt-4">
                        <label class="mb-2 text-sm font-medium text-gray-600">Note</label>
                        <textarea name="note" rows="3" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                  placeholder="Enter any notes...">{{ old('note') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Payment
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#chemist_house_select').select2({
                placeholder: 'Select Chemist House...',
                allowClear: true,
                ajax: {
                    url: '{{ route("depo.chemist-house-due-payment.get-data") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { query: params.term || '' };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(c => ({
                                id: c.id,
                                text: c.shop_name + ' (' + c.owner_name + ')',
                                shop_name: c.shop_name,
                                contact: c.contact,
                                balance: c.chemist_house_due_account?.due_balance
                            }))
                        };
                    },
                    cache: true
                },
                templateResult: function(c) {
                    if (!c.id) return c.text;
                    return $('<div class="flex justify-between"><span>' + c.text + '</span></div>');
                },
                templateSelection: function(c) {
                    if (!c.id) return c.text;
                    return c.text;
                }
            });

            // Auto-fill contact & balance on select
            $('#chemist_house_select').on('select2:select', function(e) {
                const data = e.params.data;
                $('#chemist_house_id').val(data.id || '');
                $('#contact').val(data.contact || '');
                $('#balance').val(data.balance || '');
                $('#chemist_house_name_hidden').val(data.shop_name || '');
            });

            // Clear fields when cleared
            $('#chemist_house_select').on('select2:clear', function() {
                $('#chemist_house_id').val('');
                $('#contact').val('');
                $('#balance').val('');
                $('#chemist_house_name_hidden').val('');
            });
        });
    </script>
@endsection
