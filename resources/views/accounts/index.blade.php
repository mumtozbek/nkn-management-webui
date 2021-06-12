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
            var url = "https://js.devexpress.com/Demos/Mvc/api/DataGridWebApi";
            $("#grid").dxDataGrid({
                dataSource: DevExpress.data.AspNet.createStore({
                    key: "OrderID",
                    loadUrl: url + "/Orders",
                    insertUrl: url + "/InsertOrder",
                    updateUrl: url + "/UpdateOrder",
                    deleteUrl: url + "/DeleteOrder",
                    onBeforeSend: function (method, ajaxOptions) {
                        ajaxOptions.xhrFields = {withCredentials: true};
                    }
                }),
                paging: {
                    pageSize: 10
                },
                pager: {
                    visible: true,
                    allowedPageSizes: [5, 10, 'all'],
                    showPageSizeSelector: true,
                    showInfo: true,
                    showNavigationButtons: true
                },
                remoteOperations: true,
                columns: [{
                    dataField: "CustomerID",
                    caption: "Customer",
                    validationRules: [{
                        type: "stringLength",
                        message: "The field Customer must be a string with a maximum length of 5.",
                        max: 5
                    }],
                    lookup: {
                        dataSource: DevExpress.data.AspNet.createStore({
                            key: "Value",
                            loadUrl: url + "/CustomersLookup",
                            onBeforeSend: function (method, ajaxOptions) {
                                ajaxOptions.xhrFields = {withCredentials: true};
                            }
                        }),
                        valueExpr: "Value",
                        displayExpr: "Text"
                    }
                }, {
                    dataField: "OrderDate",
                    dataType: "date",
                    validationRules: [{
                        type: "required",
                        message: "The OrderDate field is required."
                    }]
                }, {
                    dataField: "Freight",
                    headerFilter: {
                        groupInterval: 100
                    },
                    validationRules: [{
                        type: "range",
                        message: "The field Freight must be between 0 and 2000.",
                        min: 0,
                        max: 2000
                    }]
                }, {
                    dataField: "ShipCountry",
                    validationRules: [{
                        type: "stringLength",
                        message: "The field ShipCountry must be a string with a maximum length of 15.",
                        max: 15
                    }]
                }, {
                    dataField: "ShipVia",
                    caption: "Shipping Company",
                    dataType: "number",
                    lookup: {
                        dataSource: DevExpress.data.AspNet.createStore({
                            key: "Value",
                            loadUrl: url + "/ShippersLookup",
                            onBeforeSend: function (method, ajaxOptions) {
                                ajaxOptions.xhrFields = {withCredentials: true};
                            }
                        }),
                        valueExpr: "Value",
                        displayExpr: "Text"
                    }
                }],
                filterRow: {
                    visible: true
                },
                headerFilter: {
                    visible: true
                },
                groupPanel: {
                    visible: true
                },
                scrolling: {
                    rowRenderingMode: "virtual"
                },
                height: 600,
                showBorders: true,
                masterDetail: {
                    enabled: true,
                    template: function (container, options) {
                        $("<div>")
                            .dxDataGrid({
                                dataSource: DevExpress.data.AspNet.createStore({
                                    loadUrl: url + "/OrderDetails",
                                    loadParams: {orderID: options.data.OrderID},
                                    onBeforeSend: function (method, ajaxOptions) {
                                        ajaxOptions.xhrFields = {withCredentials: true};
                                    }
                                }),
                                showBorders: true
                            }).appendTo(container);
                    }
                },
                editing: {
                    allowAdding: true,
                    allowUpdating: true,
                    allowDeleting: true
                },
                grouping: {
                    autoExpandAll: false
                },
                summary: {
                    totalItems: [{
                        column: "Freight",
                        summaryType: "sum"
                    }],
                    groupItems: [{
                        column: "Freight",
                        summaryType: "sum"
                    }, {
                        summaryType: "count"
                    }]
                }
            });
        });
    </script>
@endpush
