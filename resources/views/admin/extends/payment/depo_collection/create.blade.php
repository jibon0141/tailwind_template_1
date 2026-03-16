@extends('admin.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Payment
                </h3>

                <a href="{{ route('admin.depo-due-collection.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Payment List
                </a>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('admin.depo-due-collection.create') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <input type="number" name="depo_id" id="depo_id" hidden>

                        <!-- Depo Name -->
                        <div class="flex flex-col relative">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Name<span class="text-red-500"> </span>
                            </label>
                            <input type="text" name="depo_name" id="depo_name" value="{{ old('depo_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Search Depo Name...." autocomplete="off">
                            <div id="Depo-list"
                                 class="absolute z-10 w-full bg-white border border-gray-300 rounded max-h-60 overflow-y-auto hidden"
                                 style="top: 100%; left: 0;"></div>

                            @error('depo_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Depo Contact -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Phone <span class="text-red-500"></span>
                            </label>
                            <input type="text" step="0.01" name="contact" id="contact" value="{{ old('contact') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter Depo Phone">
                            @error('contact')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Payment Date  -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" step="0.01" name="payment_date" id="payment_date" value="{{ old('payment_date',now()->format('Y-m-d')) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter purchase price">
                            @error('payment_date')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Depo Account --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Account <span class="text-red-500"></span>
                            </label>

                            <select name="depo_account_id" id="depo_account_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">-- Select Depo Account --</option>
                            </select>

                        </div>

                        {{-- Company Account --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Company Account <span class="text-red-500">*</span>
                            </label>

                            <select name="account_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">-- Select Company Account --</option>

                                @foreach($companyAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_name }} ({{ $account->balance }})
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Depo Receivable -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Receivable Amount <span class="text-red-500"></span>
                            </label>
                            <input type="number" step="0.01" id="balance" name="balance" value="{{ old('balance') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Payable Balance">
                            @error('mrp')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Receiving Amount -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Paying Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="receiving_amount" value="{{ old('receiving_amount') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter Paying Amount">
                            @error('receiving_amount')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>



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

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                const depoInput = document.getElementById('depo_name');
                const depoList = document.getElementById('Depo-list');
                const depoIdInput = document.getElementById('depo_id');
                const contactInput = document.getElementById('contact');
                const balanceInput = document.getElementById('balance');
                const depoAccountSelect = document.getElementById('depo_account_id');

                $('#depo_name').on('input', function () {

                    const query = this.value.trim();

                    if (!query) {
                        depoList.innerHTML = '';
                        depoList.classList.add('hidden');
                        return;
                    }

                    fetch(`/admin/getDueDepoData?query=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(res => {

                            const depos = res.data; // ✅ correct

                            depoList.innerHTML = '';
                            depoList.classList.remove('hidden');

                            if (!Array.isArray(depos) || !depos.length) {
                                depoList.classList.add('hidden');
                                return;
                            }

                            depos.forEach(depo => {

                                const item = document.createElement('div');
                                item.className = 'px-3 py-2 cursor-pointer hover:bg-gray-100';
                                item.textContent = depo.depo_name;

                                item.onclick = function () {

                                    //  Set values
                                    depoInput.value = depo.depo_name;
                                    depoIdInput.value = depo.id;
                                    contactInput.value = depo.contact || '';
                                    balanceInput.value = depo.balance || 0;

                                    //  Reset & populate depo accounts
                                    depoAccountSelect.innerHTML =
                                        `<option value="">-- Select Depo Account --</option>`;

                                    if (Array.isArray(depo.account)) {
                                        depo.account.forEach(acc => {
                                            const opt = document.createElement('option');
                                            opt.value = acc.id;
                                            opt.textContent = `${acc.account_name} (${acc.balance})`;
                                            depoAccountSelect.appendChild(opt);
                                        });
                                    }

                                    depoList.classList.add('hidden');
                                    depoList.innerHTML = '';
                                };

                                depoList.appendChild(item);
                            });
                        })
                        .catch(err => console.error(err));
                });

                // Hide list when clicking outside
                document.addEventListener('click', function (e) {
                    if (!depoInput.contains(e.target) && !depoList.contains(e.target)) {
                        depoList.classList.add('hidden');
                    }
                });

            });
        </script>
    @endsection



