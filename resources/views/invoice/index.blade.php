<style>
    .dataTables_wrapper thead th {
        text-align: left !important;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
            <div class="flex items-center gap-4">
                <x-anchor-button href="{{ route('invoices.create') }}">
                    {{ __('Add Invoice') }}
                </x-anchor-button>
            </div>
            <br>
                <div class="max-w-7xl">
                <table id="invoices-table" class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Description</th>
                            <th>Address</th>
                            <th>Payment Status</th>
                            <th>Payment Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('#invoices-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('invoices.data') }}",
            columns: [
                { 
                    data: 'users.first_name', 
                    name: 'users.first_name',
                    render: function(data, type, full, meta) {
                        return full.first_name + ' ' + full.last_name;
                    } 
                },
                { 
                    data: 'description', 
                    name: 'description',
                    render: function(data) {
                        return (data.length > 50) ? data.substr(0, 50) + '...' : data;
                    }
                },
                { 
                    data: 'address', 
                    name: 'address',
                    render: function(data) {
                        return (data.length > 50) ? data.substr(0, 50) + '...' : data;
                    }
                },
                { 
                    data: 'payment_status', 
                    name: 'payment_status',
                    render: function(data) {
                        return data == 1 ? 'Paid' : 'Unpaid';
                    }
                },
                { data: 'payment_date', name: 'payment_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
