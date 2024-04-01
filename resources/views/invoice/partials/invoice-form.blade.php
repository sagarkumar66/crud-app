<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            @if(isset($invoice))
                {{ __('Edit Invoice') }}
            @else
                {{ __('Add Invoice') }}
            @endif
        </h2>
    </header>

    <form method="post" 
          @if(isset($invoice))
              action="{{ route('invoices.update', $invoice->id) }}"
          @else
              action="{{ route('invoices.store') }}"
          @endif
          class="mt-6 space-y-6">
        @csrf
        @if(isset($invoice))
            @method('PUT')
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
        @endif

        <div class="max-w-xl">
            <div>
                <x-input-label for="user_id" :value="__('User')" />
                <x-select-input id="user_id" name="user_id" class="mt-1 block w-full" required>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if(isset($invoice) && $invoice->user_id == $user->id) selected @endif>{{ $user->first_name }}</option>
                    @endforeach
                </x-select-input>
                <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
            </div>

            <div class="mt-5">
                <x-input-label for="description" :value="__('Description')" />
                <x-textarea-input id="description" name="description" class="mt-1 block w-full" rows="3" required>{{ isset($invoice) ? $invoice->description : old('description') }}</x-textarea-input>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div class="mt-5">
                <x-input-label for="address" :value="__('Address')" />
                <x-textarea-input id="address" name="address" class="mt-1 block w-full" rows="3" required>{{ isset($invoice) ? $invoice->address : old('address') }}</x-textarea-input>
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>

            <div class="mt-5">
                <x-input-label for="payment_status" :value="__('Payment Status')" />
                <x-select-input id="payment_status" name="payment_status" class="mt-1 block w-full" required>
                    <option value="1" @if(isset($invoice) && $invoice->payment_status == 1) selected @endif>{{ __('Paid') }}</option>
                    <option value="0" @if(isset($invoice) && $invoice->payment_status == 0) selected @endif>{{ __('Unpaid') }}</option>
                </x-select-input>
                <x-input-error class="mt-2" :messages="$errors->get('payment_status')" />
            </div>

            <div class="mt-5">
                <x-input-label for="payment_date" :value="__('Payment Date')" />
                <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full" value="{{ isset($invoice) ? $invoice->payment_date : old('payment_date') }}" required />
                <x-input-error class="mt-2" :messages="$errors->get('payment_date')" />
            </div>
        </div>

        <div class="mt-6 max-w-3xl">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Invoice Items') }}</h3>

            <table class="mt-2 w-full">
                <thead>
                    <tr>
                        <th class="py-2">{{ __('Name') }}</th>
                        <th class="py-2">{{ __('Quantity') }}</th>
                        <th class="py-2">{{ __('Amount') }}</th>
                        <th class="py-2">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="invoiceItems">
                    @if(isset($invoice))
                        @foreach($invoice->items as $item)
                            <tr>
                                <td><input type="text" name="item_name[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $item->name }}" required></td>
                                <td><input type="number" name="item_quantity[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $item->quantity }}" required></td>
                                <td><input type="number" name="item_amount[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $item->amount }}" required></td>
                                <td><button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-red-500 bg-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" onclick="deleteRow(this)">{{ __('Delete') }}</button></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <div class="mt-4">
                <span class="text-red-500 hidden" id="invoice-item-error">{{ __('Cannot delete the only row.') }}</span><br>
                <x-primary-button type="button" class="bg-blue-500" style="background-color: #007bff;" onclick="addInvoiceItem()">{{ __('Add Item') }}</x-primary-button>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Invoice') }}</x-primary-button>
        </div>
    </form>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var currentPath = window.location.pathname;

        if (currentPath.includes("create")) {
            addInvoiceItem();
        }

        var today = new Date().toISOString().split('T')[0];
        // Set the max attribute of the payment_date input to today's date
        document.getElementById('payment_date').setAttribute('max', today);
    });

    function addInvoiceItem() {
        var newRow = `
            <tr>
                <td><input type="text" name="item_name[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></td>
                <td><input type="number" name="item_quantity[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></td>
                <td><input type="number" name="item_amount[]" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></td>
                <td><button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-red-500 bg-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" onclick="deleteRow(this)">{{ __('Delete') }}</button></td>
            </tr>
        `;
        document.getElementById('invoice-item-error').classList.add('hidden');
        document.getElementById('invoiceItems').insertAdjacentHTML('beforeend', newRow);
    }

    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        var rowCount = document.getElementById('invoiceItems').rows.length;
        if (rowCount > 1) { // Check if there's more than one row
            row.parentNode.removeChild(row);
        } else {
            document.getElementById('invoice-item-error').classList.remove('hidden');
            setTimeout(function() {
                document.getElementById('invoice-item-error').classList.add('hidden');
            }, 3000);
        }
    }
</script>
