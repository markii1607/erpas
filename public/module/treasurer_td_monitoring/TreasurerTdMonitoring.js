define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('TreasurerTdMonitoringFactory', [
        'alertify',
        function (alertify) {
            var Factory = {};

            /**
             * `autoloadSettings` autoload params
             * @return {[type]}
             */
            Factory.autoloadSettings = function () {
                // alertify
                alertify.logPosition('bottom right');
                alertify.theme('')
            };

            /**
             * `templates` Modal templates.
             * @type {Array}
             */
            Factory.templates = [
                'module/treasurer_td_monitoring/modals/add_td_payment.html',
                'module/treasurer_td_monitoring/modals/payment_portal.html',
                'module/treasurer_td_monitoring/modals/check_generation.html',
                'module/treasurer_td_monitoring/modals/view_or_details.html',
                'module/tax_declaration/modals/view_tax_declaration.html',
            ];

            Factory.dtOptions = function (type) {
                var options = {}, _columns, _ajaxUrl, _columnDefs;

                _columns = function (type) {
                    if (type == 'tax_dec') {
                        var col = [
                            {
                                "data"   : null
                            },
                            {
                                "data"   : "transaction_date",
                            },
                            {
                                "data"   : "or_no",
                            },
                            {
                                "data"   : "amount_paid",
                            },
                            {
                                "data"   : "paid_by",
                            },
                            {
                                "data"   : "collector_name",
                            },
                            {
                                "data"   : null,
                            },
                        ];
                    } else if (type == 'collections') {
                        var col = [
                            {
                                "data"   : null
                            },
                            {
                                "data"   : "date_generated",
                            },
                            {
                                "data"   : "check_no",
                            },
                            {
                                "data"   : "total_amount",
                            },
                            {
                                "data"   : "or_numbers",
                            },
                            {
                                "data"   : "collector_name",
                            },
                        ];
                    }

                    return col;
                }

                _columnDefs = function (type) {

                    if (type == 'tax_dec') {
                        var columnDefs = [
                            {
                                "targets"    : 5,
                                "searchable" : true,
                                "orderable"  : true,
                                "className"  : "text-left",
                                "render"     : function(data, type, full, meta){
                                    return '<span class="text-uppercase"><b>' + data + '</b></span><br><small>' + full.collector_position + '</small>';
                                }
                            },
                            {
                                "targets"    : 6,
                                "searchable" : true,
                                "orderable"  : true,
                                "className"  : "text-center",
                                "render"     : function(data, type, full, meta){
                                    
                                    var str = '';
                                    str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="View" class="btn btn-default bg-success btn-md mr-2 text-white"><i class="fas fa-eye"></i> View O.R. Details</button>';

                                    return str;
                                }
                            },
                        ];
                    } else if (type == 'collections') {
                        var columnDefs = [
                            {
                                "targets"    : 4,
                                "searchable" : true,
                                "orderable"  : true,
                                "className"  : "text-left",
                                "render"     : function(data, type, full, meta){
                                    var str =   '<ul>';
                                                    angular.forEach(data, (value, key) => {
                                                        str += '<li>' + value.or_no + '</li>';
                                                    })
                                        str +=  '</ul>';

                                    return str;
                                }
                            },
                            {
                                "targets"    : 5,
                                "searchable" : true,
                                "orderable"  : true,
                                "className"  : "text-left",
                                "render"     : function(data, type, full, meta){
                                    return '<span class="text-uppercase"><b>' + data + '</b></span><br><small>' + full.collector_position + '</small>';
                                }
                            },
                        ];
                    }

                    return columnDefs;
                }

                _ajaxUrl = function (type) {
                    if (type == 'tax_dec') {
                        var ajax = {
                            "url"  : APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/TreasurerTdMonitoringService.php/getDtOrDetails',
                            "data" : function (data) {
                                var temp = {
                                    'advanced_search' : {
                                        'date_range' : ''
                                    }
                                };
    
                                // Retrieve dynamic parameters
                                var dt_params = angular.element('#tax_declarations').data('dt_params');
                                // console.log('dt_params: ', dt_params);
                                // Add dynamic parameters to the data object sent to the server
                                if (dt_params) {
                                    temp.advanced_search = dt_params;
                                }
    
                                angular.extend(data, temp);
                            }
                        }
                    } else if (type == 'collections') {
                        var ajax = {
                            "url"  : APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/TreasurerTdMonitoringService.php/getDtChkDetails',
                            "data" : function (data) {
                                var temp = {
                                    'advanced_search' : {
                                        'date_range'   : '',
                                    }
                                };
    
                                // Retrieve dynamic parameters
                                var dt_params = angular.element('#collections_tbl').data('dt_params');
                                // console.log('dt_params: ', dt_params);
                                // Add dynamic parameters to the data object sent to the server
                                if (dt_params) {
                                    temp.advanced_search = dt_params;
                                }
    
                                angular.extend(data, temp);
                            }
                        }
                    }

                    return ajax;
                }

                options = {
                    "dom"           : 'Bfrtip',
                    "paging"        : true,
                    "lengthChange"  : true,
                    "pageLength"    : 5,
                    "searching"     : true,
                    "ordering"      : true,
                    "info"          : true,
                    "select"        : 
                    {
                        style: 'single'
                    },
                    "keys"          : 
                    {
                        keys: [
                            13 /* ENTER */ ,
                            38 /* UP */ ,
                            40 /* DOWN */
                        ]
                    },
                    "mark"          : true,
                    "autoWidth"     : false,
                    "responsive"    : false,
                    "data"          : [],
                    "buttons"       : [],
                    "order"         : 
                    [
                        [
                            0,
                            "asc"
                        ]
                    ],
                    "columnDefs"   : _columnDefs(type),
                    "columns"      : _columns(type),
                };

                options.processing = true;
                options.serverSide = true;
                options.ajax       = _ajaxUrl(type);

                return options;
            };

            return Factory;
        }
    ]);

    app.controller('TreasurerTdMonitoringController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'TreasurerTdMonitoringFactory',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockTaxDeclarations'), 
            td_table = angular.element('#tax_declarations'),
            cl_table = angular.element('#collections_tbl');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {

                $scope.jqDataTableOptions = Factory.dtOptions('tax_dec');
                $scope.ctDataTableOptions = Factory.dtOptions('collections');

            };

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.showDetails(data, index)
                },
            };

            $scope.showDetails = function(data){
                var paramData, modalInstance;

                paramData = {
                    data
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_or_details.html',
                    controller      : 'ViewOrDetailsController',
                    size            : 'md',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.openTdPaymentProtal = function () {
                var modalInstance;

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_td_payment.html',
                    controller      : 'AddTDPaymentController',
                    size            : 'xlg',
                });

                modalInstance.result.then(function (res) {
                    console.log('addREsult: ', res);
                    td_table.DataTable().row.add(res).draw();
                    td_table.find('tbody tr').css('cursor', 'pointer');
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.openCheckPortal = function () {
                var modalInstance;

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'check_generation.html',
                    controller      : 'CheckGenerationController',
                    size            : 'md',
                });

                modalInstance.result.then(function (res) {
                    console.log('addREsult: ', res);
                    cl_table.DataTable().row.add(res).draw();
                    cl_table.find('tbody tr').css('cursor', 'pointer');
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.filterTaxDec = function (params = []) {

                console.log('params: ', params);
                var paramData;

                paramData = {
                    'status'        : $scope.filters.status,
                    'rev_id'        : params.rev_id,
                    'td_no'         : params.td_no,
                    'pin'           : params.pin,
                    'owner'         : params.owner,
                    'lot_no'        : params.lot_no,
                    'brgy_id'       : params.brgy_id,
                    'type'          : params.type,
                    'category'      : params.category,
                    'class_id'      : params.class_id,
                    'actual_use'    : params.actual_use,
                    'date_from'     : params.date_from,
                    'date_to'       : params.date_to,
                };
                

                $timeout( function () {
                    _softConfig().dt.data('dt_params', angular.copy(paramData)); // parse dynamic data
                    _softConfig().dt.DataTable().draw();           // reload datatable
                }, 100);
            };

            _softConfig = function(){
                var temp = {
                    'rowCount' : table.DataTable().data().count(),
                    'dt'       : table
                };

                return temp;
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Treasurer's Department"
                $scope.header.link.sub = ""
                $scope.header.link.main = "Transactions and Configuration"
                $scope.header.showButton = false

                $scope.templates = Factory.templates;

                $scope.search = {};

                $timeout(function() {
                    angular.element('#date_range_tbl1').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.search.date_range_tbl1 = angular.copy(formattedDate);
                        }
                    });
                    angular.element('#date_range_tbl2').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.search.date_range_tbl2 = angular.copy(formattedDate);
                        }
                    });
                }, 500);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define