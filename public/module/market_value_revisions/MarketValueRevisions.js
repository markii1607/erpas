define([
    'app'
], function (app) {
    app.factory('MarketValueRevisionsFactory', [
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
                'module/market_value_revisions/modals/add_revision.html',
                'module/market_value_revisions/modals/edit_revision.html',
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
                            "targets": 6,
                            "searchable": false,
                            "orderable": false,
                            "className": "text-center",
                            "render": function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="Edit Revision" class="btn btn-default bg-success btn-sm mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Delete Revision" class="btn btn-default bg-warning btn-sm text-white"><i class="fas fa-trash"></i></button>';

                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : "id" 
                        },
                        { 
                            "data" : "classification_name"
                        },
                        { 
                            "data" : "sub_classification_name"
                        },
                        { 
                            "data" : "rev_no"
                        },
                        { 
                            "data" : "market_value"
                        },
                        { 
                            "data" : "description"
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
                    classification_name: 'Residential',
                    sub_classification_name: 'R1',
                    rev_no: 'R1-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
                {
                    id: 2,
                    classification_name: 'Residential',
                    sub_classification_name: 'R2',
                    rev_no: 'R2-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
                {
                    id: 3,
                    classification_name: 'Residential',
                    sub_classification_name: 'R3',
                    rev_no: 'R3-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
                {
                    id: 4,
                    classification_name: 'Commercial',
                    sub_classification_name: 'C1',
                    rev_no: 'C1-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
                {
                    id: 5,
                    classification_name: 'Commercial',
                    sub_classification_name: 'C2',
                    rev_no: 'C2-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
                {
                    id: 6,
                    classification_name: 'Commercial',
                    sub_classification_name: 'C3',
                    rev_no: 'C3-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
                {
                    id: 7,
                    classification_name: 'Industrial',
                    sub_classification_name: 'I1',
                    rev_no: 'I1-1000',
                    market_value: 1000,
                    description: 'Sample Description',
                },
            ];

            return Factory;
        }
    ]);

    app.service('MarketValueRevisionsService', [
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

    app.controller('MarketValueRevisionsController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'MarketValueRevisionsFactory',
        'MarketValueRevisionsService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockMarketValueRevisions'), table = angular.element('#marketValueRevisions');

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
                    titleAttr   : 'Add Revision', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addRevision(); 
                    },
                    enabled     : true,
                    name        : 'add'
                });
                
                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.editRevision(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.deleteRevision(data, index)
                },
            };

            $scope.addRevision = function () {
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
                    templateUrl     : 'add_revision.html',
                    controller      : 'AddRevisionController',
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

            $scope.editRevision = function (data, index) {
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
                    templateUrl     : 'edit_revision.html',
                    controller      : 'EditRevisionController',
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

            $scope.deleteRevision = function(data, index) {
                Alertify.confirm("Are you sure you want to delete the selected revision?",
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

                $scope.header.title = "Revisions"
                $scope.header.link.sub = "Market Value"
                $scope.header.link.main = "Revisions"
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