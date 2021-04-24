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
                // 'module/tax_declaration/modals/edit_tax_declaration.html',
            ];

            Factory.dtOptions = function () {
                var options = {};

                options = {
                    "dom": 'Bfrtip',
                    "paging": true,
                    "lengthChange": true,
                    "pageLength": 10,
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
                    "columnDefs"   : [ 
                        {
                            "targets"    : 0,
                            "searchable" : false,
                            "orderable"  : true,
                            "className"  : "text-center"
                        },
                        {
                            "targets": 1,
                            "searchable": true,
                            "orderable": true,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                return `${data.year}-${data.mun_code}-${data.brgy_code}-${data.td_code}`
                            }
                        },
                        {
                            "targets": 2,
                            "searchable": true,
                            "orderable": true,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                return `${data.prov_code}-${data.mun_code}-${data.brgy_code}-${data.section}-${data.prop_no}-${data.bldg_no}`
                            }
                        },
                        {
                            "targets": 4,
                            "searchable": true,
                            "orderable": true,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                return `${data.brgy.name}, ${data.mun_prov}`
                            }
                        },
                        {
                            "targets": 5,
                            "searchable": false,
                            "orderable": false,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="View" class="btn btn-default bg-success btn-sm mr-2 text-white"><i class="fas fa-eye"></i></button>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Edit" class="btn btn-default bg-info btn-sm mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<button type="submit" id="thirdButton" data-toggle="tooltip" title="Revise" class="btn btn-default bg-warning btn-sm mr-2 text-white"><i class="fas fa-sync-alt"></i></button>';
                                str += '<button type="submit" id="fourthButton" data-toggle="tooltip" title="Cancel" class="btn btn-default bg-danger btn-sm mr-2 text-white"><i class="fas fa-ban"></i></button>';

                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : "id" 
                        },
                        { 
                            "data" : "td_no"
                        },
                        { 
                            "data" : "pin"
                        },
                        { 
                            "data" : "owner"
                        },
                        { 
                            "data" : "loc"
                        },
                        { 
                            "data" : null,
                        },
                    ]
                };

                return options;
            };

            Factory.dummyData = [
                {
                    id: 1,
                    td_no: {
                        year: '2017',
                        mun_code: '09',
                        brgy_code: '0015',
                        td_code: '00170',
                        td_code_2: '000',
                    },
                    pin: {
                        prov_code: '031',
                        mun_code: '09',
                        brgy_code: '0015',
                        section: '003',
                        prop_no: '29',
                        bldg_no: '1001',
                    },
                    owner: 'Bilasa, Ricardo',
                    address: 'P2, San Francisco, Malilipot, Albay',
                    loc: {
                        brgy: {
                            id: 15,
                            code: '015',
                            name: 'San Jose'
                        },
                        mun_prov: 'Malilipot, Albay'
                    },
                    boundaries: {
                        text: 'Boarding house constructed on Lot No. 197-B, owned by the same declarant.',
                    },
                    type: {
                        name: 'Building',
                        floors: 2,
                    },
                    details: [
                        {
                            classification: {
                                id: 1, 
                                name: 'Improvement',
                            },
                            area: 192.00,
                            unit: {
                                id: 1,
                                name: 'sq.m'
                            },
                            market_value: 1102080.00,
                            actual_use: 'Boarding House',
                            assessment_level: {
                                id: 1,
                                rate: 0.35,
                                display_rate: '35%'
                            },
                            assessed_value: 385730.00,
                        }
                    ],
                    total_market_value: 1102080.00,
                    total_assessed_value: 385730.00,
                    assessed_val_words: 'Three Hundred Eighty Five Thousand Seven Hundred Thirty Pesos',
                    tax_exempt: 'taxable',
                    effectivity: 2021,
                    prev_declaration: {
                        td_no: {
                            id: 1,
                            no: '00294',
                        },
                        owner: 'Same',
                        prev_av: 606800.00
                    },
                    memoranda: 'Reassessment: Per letter request, ocular inspection conducted based on the actual floor area of the structures. Taxes paid until 2020 under O.R #7731618 7/30/2020, MTO-Malilipot, Albay',
                },
            ];

            Factory.revNo = [
                {
                    id: 1,
                    no: 2019,
                },
                {
                    id: 2,
                    no: 2020,
                },
                {
                    id: 3,
                    no: 2021,
                },
            ]

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
            // _this.getDetails = function () {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/UserAccessConfigurationService.php/getDetails');
            // };
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
                blocker.start();

                // Service.getDetails().then( function (res) {
                    $scope.jqDataTableOptions         = Factory.dtOptions();
                    $scope.jqDataTableOptions.buttons = _btnFunc();
                    $scope.jqDataTableOptions.data    = Factory.dummyData;

                    $scope.revision_nos = Factory.revNo

                    blocker.stop();
                // });
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
                    $scope.reviseTaxDec(data, index)
                },
                "fourthButton": function(data, index) {
                    $scope.cancelTaxDec(data, index)
                },
            };

            $scope.addTaxDec = function () {
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

            // $scope.editBarangay = function (data, index) {
            //     var paramData, modalInstance;

            //     paramData = {
            //         data,
            //     }

            //     modalInstance = $uibModal.open({
            //         animation       : true,
            //         keyboard        : false,
            //         backdrop        : 'static',
            //         ariaLabelledBy  : 'modal-title',
            //         ariaDescribedBy : 'modal-body',
            //         templateUrl     : 'edit_barangay.html',
            //         controller      : 'EditBarangayController',
            //         size            : 'md',
            //         resolve         : {
            //             paramData : function () {
            //                 return paramData;
            //             }
            //         }
            //     });

            //     modalInstance.result.then(function (res) {
            //     }, function (res) {
            //         // Result when modal is dismissed
            //     });
            // }

            // $scope.deleteBarangay = function(data, index) {
            //     Alertify.confirm("Are you sure you want to delete the selected barangay?",
            //         function (res) {
            //             if (res) {
            //                 table.DataTable().row('.selected').remove().draw(true);
            //                 Alertify.log('Deleted!');
            //             }
            //         }
            //     );
            // }

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