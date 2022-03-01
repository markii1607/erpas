define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('AccountingCollectionConfigFactory', [
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
                'module/accounting_collection_config/modals/check_generation.html',
                'module/treasurer_td_monitoring/modals/view_or_details.html',
                'module/tax_declaration/modals/view_tax_declaration.html',
            ];

            Factory.dtOptions = function () {
                var options = {}, _columns, _ajaxUrl, _columnDefs;

                _columns = function () {
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

                    return col;
                }

                _columnDefs = function () {

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

                    return columnDefs;
                }

                _ajaxUrl = function (type) {
                    var ajax = {
                        "url"  : APP.SERVER_BASE_URL + '/App/Service/AccountingCollectionConfig/AccountingCollectionConfigService.php/getDtChkDetails',
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
                    "columnDefs"   : _columnDefs(),
                    "columns"      : _columns(),
                };

                options.processing = true;
                options.serverSide = true;
                options.ajax       = _ajaxUrl();

                return options;
            };

            return Factory;
        }
    ]);

    app.controller('AccountingCollectionConfigController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'AccountingCollectionConfigFactory',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockCollections'), cl_table = angular.element('#collections_tbl');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {

                // $scope.jqDataTableOptions = Factory.dtOptions('tax_dec');
                $scope.ctDataTableOptions = Factory.dtOptions();

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

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Accounting Department"
                $scope.header.link.sub = ""
                $scope.header.link.main = "Transactions and Configuration"
                $scope.header.showButton = false

                $scope.templates = Factory.templates;

                $scope.search = {};

                /* $timeout(function() {
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
                }, 500); */

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define