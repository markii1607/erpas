define([
    'app'
], function (app) {
    app.factory('TaxDeclarationFactory', [
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
                'module/tax_declaration/modals/add_tax_declaration.html',
                'module/tax_declaration/modals/advance_search.html',
                'module/tax_declaration/modals/edit_tax_declaration.html',
                'module/tax_declaration/modals/view_tax_declaration.html',
            ];

            Factory.dtOptions = function () {
                var options = {};

                options = {
                    "dom": 'Bfrtip',
                    "paging": true,
                    "lengthChange": true,
                    "pageLength": 15,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "select": {
                        style: 'single'
                    },
                    "keys": {
                        keys: [
                            13 /* ENTER */ ,
                            38 /* UP */ ,
                            40 /* DOWN */
                        ]
                    },
                    "mark": true,
                    "autoWidth": false,
                    "responsive": true,
                    "data": [],
                    "buttons": [],
                    "order": [
                        [
                            0,
                            "asc"
                        ]
                    ],
                    "processing"     : true,
                    "serverSide"     : true,
                    "ajax"           : 
                    {
                        "url"  : APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/getDetails',
                        "data" : function (data) {
                            var temp = {
                                'advanced_search' : {
                                    'sample' : ''
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
                    },
                    "columnDefs"   : [ 
                        {
                            "targets"    : 0,
                            "searchable" : false,
                            "orderable"  : true,
                            "className"  : "text-center"
                        },
                        {
                            "targets": 6,
                            "searchable": true,
                            "orderable": true,
                            "className": "text-left",
                            "render": function(data, type, full, meta) {
                                var strStreet = (data != '') ? data : '';
                                return strStreet + ', ' + full.barangay.name;
                            }
                        },
                        {
                            "targets": 7,
                            "searchable": false,
                            "orderable" : false,
                            "className" : "text-center",
                            "render"    : function(data, type, full, meta) {
                                if (data == '1') {
                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#5cb85c; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">ACTIVE</span>';
                                } else if (data == '2') {
                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#f0ad4e; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">RETIRED</span>';
                                } else if (data == '3') {
                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#d9534f; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">CANCELED</span>';
                                }
                            }
                        },
                        {
                            "targets": 8,
                            "searchable": false,
                            "orderable": false,
                            "className": "text-left",
                            "render": function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="View" class="btn btn-default bg-success btn-sm mr-2 text-white"><i class="fas fa-eye"></i></button>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Edit" class="btn btn-default bg-info btn-sm mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<button type="submit" id="thirdButton" data-toggle="tooltip" title="Retire" class="btn btn-default bg-warning btn-sm mr-2 text-white"><i class="fas fa-ban"></i></button>';
                                str += '<button type="submit" id="fourthButton" data-toggle="tooltip" title="Delete" class="btn btn-default bg-danger btn-sm mr-2 text-white"><i class="fas fa-trash"></i></button>';

                                return str;
                            }
                        }
                    ],
                    "columns"      : [
                        { 
                            "data" : null 
                        },
                        { 
                            "data" : "rev_year"
                        },
                        { 
                            "data" : "td_no"
                        },
                        { 
                            "data" : "pin"
                        },
                        { 
                            "data" : "property_kind"
                        },
                        { 
                            "data" : "owner"
                        },
                        { 
                            "data" : "prop_location_street"
                        },
                        { 
                            "data" : "status"
                        },
                        { 
                            "data" : null,
                        },
                    ]
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('TaxDeclarationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/getDetails');
            };

            _this.retire = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/retireTaxDeclaration', data);
            };

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/archiveTaxDeclaration', data);
            };
        }
    ]);

    app.controller('TaxDeclarationController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'TaxDeclarationFactory',
        'TaxDeclarationService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockTaxDeclarations'), table = angular.element('#tax_declarations');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {

                $scope.jqDataTableOptions         = Factory.dtOptions();
                $scope.jqDataTableOptions.buttons = _btnFunc();

                /* blocker.start();
                Service.getDetails().then( function (res) {
                    $scope.jqDataTableOptions         = Factory.dtOptions();
                    $scope.jqDataTableOptions.buttons = _btnFunc();
                    $scope.jqDataTableOptions.data    = res.data.tax_declarations;

                    blocker.stop();
                }); */
            };

            /**
             * `_btnFunc` list of button functions.
             * @return {[type]}
             */
            _btnFunc = function () {
                var buttons = [];

                buttons = [];

                buttons.push({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default btn-secondary');
                        $(node).addClass('btn bg-info text-white btn-sm add'); 
                        $(node).append('<i class="fas fa-plus"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD</span>');
                    },
                    text        : '', 
                    titleAttr   : 'Add Tax Declaration', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addTaxDec(); 
                    },
                    enabled     : true,
                    name        : 'add'
                });
                
                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.viewTaxDec(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.editTaxDec(data, index)
                },
                "thirdButton": function(data, index) {
                    $scope.retireTaxDec(data, index)
                },
                "fourthButton": function(data, index) {
                    $scope.deleteTaxDec(data, index)
                },
            };

            $scope.addTaxDec = function () {
                var paramData, modalInstance;

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_tax_declaration.html',
                    controller      : 'AddTaxDeclarationController',
                    size            : 'xxlg',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    console.log('addREsult: ', res);
                    table.DataTable().row.add(res).draw();
                    table.find('tbody tr').css('cursor', 'pointer');
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.showAdvSearch = function () {
                var paramData, modalInstance;

                // paramData = {
                //     'id'        : table.DataTable().rows('.selected').data()[0].id,
                //     'full_name' : table.DataTable().rows('.selected').data()[0].full_name
                // };

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'advance_search.html',
                    controller      : 'AdvanceSearchController',
                    size            : 'xlg',
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

            $scope.editTaxDec = function (data, index) {
                var paramData, modalInstance;

                paramData = {
                    data,
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'edit_tax_declaration.html',
                    controller      : 'EditTaxDeclarationController',
                    size            : 'xxlg',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    console.log("editResult: ", res);
                    table.DataTable().row(index).data(res).draw();
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.viewTaxDec = function (data, index) {
                var paramData, modalInstance;

                paramData = {
                    data,
                    server_base_url: $scope.server.base_url,
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_tax_declaration.html',
                    controller      : 'ViewTaxDeclarationController',
                    size            : 'xlg',
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

            $scope.retireTaxDec = function(data, index){
                Alertify
                .okBtn("Yes")
                .cancelBtn("Cancel")
                .confirm("Are you sure you want to retire Tax Declaration#<b><u>" + data.td_no + "</u></b>?",
                function () {
                    blocker.start();
                    Service.retire(data).then(res => {
                        if (res.data.status) {
                            table.DataTable().row(index).data(res.data.rowData).draw();
                            Alertify.log('Successfully marked Tax Declaration as RETIRED!');
                            
                            blocker.stop();
                        } else {
                            Alertify.error("ERROR! Please contact the administrator.");
                            blocker.stop();
                        }
                    });
                }
            );
            }

            $scope.deleteTaxDec = function(data, index){
                Alertify
                .okBtn("Yes")
                .cancelBtn("Cancel")
                .confirm("Are you sure you want to delete Tax Declaration#<b><u>" + data.td_no + "</u></b>?",
                    function () {
                        blocker.start();
                        Service.archive(data).then(res => {
                            if (res.data.status) {
                                table.DataTable().row('.selected').remove().draw(true);
                                Alertify.log('Deleted!');
                                
                                blocker.stop();
                            } else {
                                Alertify.error("ERROR! Please contact the administrator.");
                                blocker.stop();
                            }
                        });
                    }
                );
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Tax Declaration of Real Property"
                $scope.header.link.sub = ""
                $scope.header.link.main = "Tax Declaration of Real Property"
                $scope.header.showButton = false

                $scope.templates = Factory.templates;

                $scope.filters = {}

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define