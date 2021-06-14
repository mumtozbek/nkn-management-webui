@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="mb-4">
                    {{ __('Managing Accounts') }}
                </h2>

                @if(session()->has('flash'))
                    <div class="alert alert-success">
                        {{ session()->get('flash') }}
                        <button type="button" class="close" data-dismiss="alert">x</button>
                    </div>
                @endif

                <div class="dx-viewport demo-container">
                    <div id="grid"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $("#grid").dxDataGrid({
                dataSource: DevExpress.data.AspNet.createStore({
                    key: "id",
                    loadUrl: "{{ route('accounts.index') }}",
                    {{--insertUrl: "{{ route('accounts.store') }}",--}}
                        {{--updateUrl: ""{{ route('accounts.update') }}"",--}}
                        {{--deleteUrl: ""{{ route('accounts.destroy') }}"",--}}
                    onBeforeSend: function (method, ajaxOptions) {
                        ajaxOptions.xhrFields = {withCredentials: true};
                    }
                }),
                // paging: {
                //     pageSize: 10
                // },
                // pager: {
                //     visible: true,
                //     allowedPageSizes: [5, 10, 'all'],
                //     showPageSizeSelector: true,
                //     showInfo: true,
                //     showNavigationButtons: true
                // },
                remoteOperations: true,
                columns: [{
                    dataField: "id",
                    dataType: "text"
                }, {
                    dataField: "name",
                    dataType: "text"
                }, {
                    dataField: "provider",
                    dataType: "text"
                }],
                // filterRow: {
                //     visible: true
                // },
                // headerFilter: {
                //     visible: true
                // },
                // groupPanel: {
                //     visible: true
                // },
                scrolling: {
                    rowRenderingMode: "virtual"
                },
                height: 600,
                showBorders: true,
                // masterDetail: {
                //     enabled: true,
                //     template: function (container, options) {
                //         $("<div>")
                //             .dxDataGrid({
                //                 dataSource: DevExpress.data.AspNet.createStore({
                //                     loadUrl: url + "/OrderDetails",
                //                     loadParams: {orderID: options.data.OrderID},
                //                     onBeforeSend: function (method, ajaxOptions) {
                //                         ajaxOptions.xhrFields = {withCredentials: true};
                //                     }
                //                 }),
                //                 showBorders: true
                //             }).appendTo(container);
                //     }
                // },
                // editing: {
                //     allowAdding: true,
                //     allowUpdating: true,
                //     allowDeleting: true
                // },
                // grouping: {
                //     autoExpandAll: false
                // },
                // summary: {
                //     totalItems: [{
                //         column: "Freight",
                //         summaryType: "sum"
                //     }],
                //     groupItems: [{
                //         column: "Freight",
                //         summaryType: "sum"
                //     }, {
                //         summaryType: "count"
                //     }]
                // }
            });
        });
    </script>
@endpush
