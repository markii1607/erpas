define([
    'app'
], function (app) {
    app.factory('BarangaysConfigFactory', [
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
                'module/barangays_config/modals/add_barangay.html',
                'module/barangays_config/modals/edit_barangay.html',
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
                            2,
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
                            "targets": 3,
                            "searchable": false,
                            "orderable": false,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="Edit Barangay" class="btn btn-default bg-success btn-sm mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Delete Barangay" class="btn btn-default bg-warning btn-sm text-white"><i class="fas fa-trash"></i></button>';

                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : "id" 
                        },
                        { 
                            "data" : "code"
                        },
                        { 
                            "data" : "name"
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
                    code: '01',
                    name: 'Barangay 01 Poblacion',
                },
                {
                    id: 2,
                    code: '02',
                    name: 'Barangay 02 Poblacion',
                },
                {
                    id: 3,
                    code: '03',
                    name: 'Barangay 03 Poblacion',
                },
                {
                    id: 4,
                    code: '04',
                    name: 'Barangay 04 Poblacion',
                },
                {
                    id: 5,
                    code: '05',
                    name: 'Barangay 05 Poblacion',
                },
                {
                    id: 6,
                    code: '06',
                    name: 'Binitayan',
                },
                {
                    id: 7,
                    code: '07',
                    name: 'Calbayog',
                },
                {
                    id: 8,
                    code: '08',
                    name: 'Canaway',
                },
                {
                    id: 9,
                    code: '09',
                    name: 'Salvacion',
                },
                {
                    id: 1,
                    code: '010',
                    name: 'San Antonio - Santicon',
                },
                {
                    id: 11,
                    code: '011',
                    name: 'San Antonio - Sulong',
                },
                {
                    id: 12,
                    code: '012',
                    name: 'San Francisco',
                },
                {
                    id: 13,
                    code: '013',
                    name: 'San Isidro Ilawod',
                },
                {
                    id: 14,
                    code: '014',
                    name: 'San Isidro Iraya',
                },
                {
                    id: 15,
                    code: '015',
                    name: 'San Jose',
                },
                {
                    id: 16,
                    code: '016',
                    name: 'San Roque',
                },
                {
                    id: 17,
                    code: '017',
                    name: 'Sta. Cruz',
                },
                {
                    id: 18,
                    code: '018',
                    name: 'Sta. Teresa',
                },
            ];

            return Factory;
        }
    ]);

    app.service('BarangaysConfigService', [
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

    app.controller('BarangaysConfigController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'BarangaysConfigFactory',
        'BarangaysConfigService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockBarangays'), table = angular.element('#barangays');

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
                    titleAttr   : 'Add Barangay', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addBarangay(); 
                    },
                    enabled     : true,
                    name        : 'add'
                });
                
                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.editBarangay(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.deleteBarangay(data, index)
                },
            };

            $scope.addBarangay = function () {
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
                    templateUrl     : 'add_barangay.html',
                    controller      : 'AddBarangayController',
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

            $scope.editBarangay = function (data, index) {
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
                    templateUrl     : 'edit_barangay.html',
                    controller      : 'EditBarangayController',
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

            $scope.deleteBarangay = function(data, index) {
                Alertify.confirm("Are you sure you want to delete the selected barangay?",
                    function (res) {
                        if (res) {
                            table.DataTable().row('.selected').remove().draw(true);
                            Alertify.log('Deleted!');
                        }
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

                $scope.header.title = "Barangays Configuration"
                $scope.header.link.sub = ""
                $scope.header.link.main = ""
                $scope.header.showButton = false

                $scope.templates = Factory.templates;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define