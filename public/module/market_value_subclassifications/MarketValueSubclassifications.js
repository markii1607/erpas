define([
    'app'
], function (app) {
    app.factory('MarketValueSubClassificationsFactory', [
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
                'module/market_value_subclassifications/modals/add_subclassification.html',
                'module/market_value_subclassifications/modals/edit_subclassification.html',
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
                        // {
                        //     "targets"    : 1,
                        //     "searchable" : true,
                        //     "orderable"  : true,
                        //     "className"  : "text-left",
                        //     "render"     : function(data, type, full, meta){
                        //                         return data.name;
                        //     }
                        // },
                        {
                            "targets": 3,
                            "searchable": false,
                            "orderable": false,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="Edit Sub-Classification" class="btn btn-default bg-success btn-sm mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Delete Sub-Classification" class="btn btn-default bg-warning btn-sm text-white"><i class="fas fa-trash"></i></button>';

                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : "id" 
                        },
                        { 
                            "data" : "classification.name"
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


            return Factory;
        }
    ]);

    app.service('MarketValueSubClassificationsService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueSubclassification/MarketValueSubclassificationService.php/getDetails');
            };

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueSubclassification/MarketValueSubclassificationService.php/archiveSubClassification', data);
            };
        }
    ]);

    app.controller('MarketValueSubClassificationsController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'MarketValueSubClassificationsFactory',
        'MarketValueSubClassificationsService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockMarketValueSubClassifications'), table = angular.element('#marketValueSubClassifications');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then( function (res) {
                    if (res.data.subClassifications != undefined) {
                        
                        $scope.jqDataTableOptions         = Factory.dtOptions();
                        $scope.jqDataTableOptions.buttons = _btnFunc();
                        $scope.jqDataTableOptions.data    = res.data.subClassifications;
    
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
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
                    titleAttr   : 'Add Subclassification', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addSubClassification(); 
                    },
                    enabled     : true,
                    name        : 'add'
                });
                
                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.editSubClassification(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.deleteSubClassification(data, index)
                },
            };

            $scope.addSubClassification = function () {
                var paramData, modalInstance;

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_subclassification.html',
                    controller      : 'AddSubClassificationController',
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

            $scope.editSubClassification = function (data, index) {
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
                    templateUrl     : 'edit_subclassification.html',
                    controller      : 'EditSubClassificationController',
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

            $scope.deleteSubClassification = function(data, index) {
                Alertify.confirm("Are you sure you want to delete the selected sub-classification?",
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

                $scope.header.title = "Sub-Classifications"
                $scope.header.link.sub = "Market Value"
                $scope.header.link.main = "Sub-Classifications"
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