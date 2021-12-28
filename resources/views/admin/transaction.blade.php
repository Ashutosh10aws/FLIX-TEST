@extends('admin.layouts.template',['menu'=>'transactions'])

@section('page-content')
    <style>
        .filter-wrapper label{
            color:#fff !important;
        }
    </style>
    <div class="page-content">
        <div class="panel panel-boxed">
            <div class="panel-heading">
                <h3 class="panel-title">Transactions</h3>
            </div>
            <div class="panel-body">
                <div class="list-container">
                    <div class="filter-wrapper">
                        <div class="select-country-wrapper">
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_paypal" checked>
                                <label for="show_paypal">Show Paypal</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_crypto" checked>
                                <label for="show_crypto">Show Crypto</label>
                            </div>
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="show_card" checked>
                                <label for="show_card">Card</label>
                            </div>
                        </div>
                    </div>
                    <div class="list-wrapper">
                        <div class="table-responsive">
                            <table class="table" id="item-list-table">
                                <thead class="table-dark">
                                <tr>
                                    <th>Mac Address</th>
                                    <th>App Type</th>
                                    <th>Pay Amount</th>
                                    <th>Payment Type</th>
                                    <th>Payment Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('script')
    <div>
        <script>
            var current_tr, current_playlist_id, dataTable, current_button;
            dataTable=$('#item-list-table').DataTable({});
            $(document).ready(function () {
                updateDataTable();
            })
            function updateDataTable(){
                dataTable.destroy();
                dataTable=$('#item-list-table').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    pageLength: 50,
                    'ajax':{
                        url:site_url+"/admin/getTransactions",
                        data:{
                            show_paypal:$('#show_paypal').prop('checked'),
                            show_crypto:$('#show_crypto').prop('checked'),
                            show_card:$('#show_card').prop('checked'),
                        },
                        "dataSrc": function ( json ) {
                            return json.data;
                        }
                    },
                    'columns': [
                        { data: 'mac_address',sortable:false },
                        { data: 'app_type',sortable:true },
                        { data: 'amount',sortable:true },
                        { data: 'payment_type',sortable:true },
                        { data:'pay_time',sortable:true}
                    ]
                });
            }

            $('#show_paypal').change(function () {
                updateDataTable();
            })
            $('#show_crypto').change(function () {
                updateDataTable();
            })
            $('#show_card').change(function () {
                updateDataTable();
            })
        </script>
    </div>
@endsection





