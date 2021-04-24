define([
    'app'
], function (app) {
    app.factory('MarketValueClassificationsFactory', [
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
                'module/market_value_classifications/modals/add_classification.html',
                'module/market_value_classifications/modals/edit_classification.html',
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
                            "targets": 2,
                            "searchable": false,
                            "orderable": false,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="Edit Classification" class="btn btn-default bg-success btn-sm mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Delete Classification" class="btn btn-default bg-warning btn-sm text-white"><i class="fas fa-trash"></i></button>';

                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : "id" 
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
                    name: 'Residential',
                },
                {
                    id: 2,
                    name: 'Commercial',
                },
                {
                    id: 3,
                    name: 'Industrial',
                },
                {
                    id: 4,
                    name: 'Improvement',
                },
            ];

            return Factory;
        }
    ]);

    app.service('MarketValueClassificationsService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueClassification/MarketValueClassificationService.php/getDetails');
            };

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueClassification/MarketValueClassificationService.php/archiveClassification', data);
            };
        }
    ]);

    app.controller('MarketValueClassificationsController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'MarketValueClassificationsFactory',
        'MarketValueClassificationsService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockMarketValueClassifications'), table = angular.element('#marketValueClassifications');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then( function (res) {
                    if (res.data.classifications != undefined) {

                        $scope.jqDataTableOptions         = Factory.dtOptions();
                        $scope.jqDataTableOptions.buttons = _btnFunc();
                        $scope.jqDataTableOptions.data    = res.data.classifications;
                        // $scope.jqDataTableOptions.data    = Factory.dummyData;

                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data! Please contact the administrator.");
                        blocker.stop();
                    }

                });
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
                    titleAttr   : 'Add Classification', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addClassification(); 
                    },
                    enabled     : true,
                    name        : 'add'
                });
                
                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.editClassification(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.deleteClassification(data, index)
                },
            };

            $scope.addClassification = function () {
                var paramData, modalInstance;

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_classification.html',
                    controller      : 'AddClassificationController',
                    size            : 'md',
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

            $scope.editClassification = function (data, index) {
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
                    templateUrl     : 'edit_classification.html',
                    controller      : 'EditClassificationController',
                    size            : 'md',
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

            $scope.deleteClassification = function(data, index) {
                Alertify.confirm("Are you sure you want to delete the selected classification?", function () {
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
                });
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Classifications"
                $scope.header.link.sub = "Market Value"
                $scope.header.link.main = "Classifications"
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