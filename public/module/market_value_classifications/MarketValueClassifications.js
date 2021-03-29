define([
    'app'
], function (app) {
    app.factory('userAccessConfigurationFactory', [
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
                'module/user_access_configuration/modals/view_access.html',
                'module/user_access_configuration/modals/view_project_accesses.html',
                'module/user_access_configuration/modals/view_deputy_config.html',
                'module/user_access_configuration/modals/sub_modals/access.html',
                'module/user_access_configuration/modals/sub_modals/project_access.html',
                'module/user_access_configuration/modals/sub_modals/add_deputy.html',
                'module/user_access_configuration/modals/sub_modals/change_deputy_status.html',
            ];

            Factory.dtOptions = function () {
                var options = {};

                options = {
                    "dom"            : 'Bfrtip',
                    "paging"         : true,
                    "lengthChange"   : true,
                    "pageLength"     : 25,
                    "searching"      : true,
                    "ordering"       : true,
                    "info"           : true,
                    "select"         : {
                        style : 'single'
                    },
                    "keys"           : {
                        keys: [ 
                            13 /* ENTER */, 
                            38 /* UP */, 
                            40 /* DOWN */ 
                        ]
                    },
                    "mark"           : true,
                    "autoWidth"      : false,
                    "responsive"     : true,
                    "data"           : [],
                    "buttons"        : [],
                    "order"          : [
                        [
                            1, 
                            "desc" 
                        ]
                    ],
                    "columnDefs"   : [ 
                        {
                            "targets"    : 0,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center"
                        }
                    ],
                    "columns"      : [
                        { 
                            "data" : "id" 
                        },
                        { 
                            "data" : "employee_no", 
                        },
                        { 
                            "data" : "full_name", 
                        },
                        { 
                            "data" : "position_name", 
                        },
                        { 
                            "data" : "username", 
                        }
                    ]
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('userAccessConfigurationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/UserAccessConfigurationService.php/getDetails');
            };
        }
    ]);

    app.controller('UserAccessConfigurationController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'userAccessConfigurationFactory',
        'userAccessConfigurationService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockUserAccessConfiguration'), table = angular.element('#userAccessConfiguration');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then( function (res) {
                    $scope.jqDataTableOptions         = Factory.dtOptions();
                    $scope.jqDataTableOptions.buttons = _btnFunc();
                    $scope.jqDataTableOptions.data    = res.data.users;
                });
            };

            /**
             * `_viewAccesses` Viewing of modules.
             * @return {[modal]}
             */
            // _viewAccesses = function () {
            //     var paramData, modalInstance;

            //     paramData = {
            //         'id'        : table.DataTable().rows('.selected').data()[0].id,
            //         'full_name' : table.DataTable().rows('.selected').data()[0].full_name
            //     };

            //     modalInstance = $uibModal.open({
            //         animation       : true,
            //         keyboard        : false,
            //         backdrop        : 'static',
            //         ariaLabelledBy  : 'modal-title',
            //         ariaDescribedBy : 'modal-body',
            //         templateUrl     : 'view_access.html',
            //         controller      : 'ViewAccessController',
            //         size            : 'lg',
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

            /**
             * `_btnFunc` list of button functions.
             * @return {[type]}
             */
            _btnFunc = function () {
                var buttons = [];

                buttons = [];

                buttons.unshift({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default');
                        $(node).addClass('btn bg-olive btn-sm hoverable add'); 
                        $(node).append('<i class="fa fa-users"></i>&nbsp;<span class="hidden-xs hidden-sm">VIEW DEPUTY INFO</span>');
                    },
                    text        : '', 
                    titleAttr   : 'View Deputy Info', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        _viewDeputyInfo(); 
                    },
                    enabled     : true,
                    name        : 'add'
                }); 

                buttons.unshift({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default');
                        $(node).addClass('btn bg-primary btn-sm hoverable add'); 
                        $(node).append('<i class="fa fa-list-alt"></i>&nbsp;<span class="hidden-xs hidden-sm">VIEW POJECT ACCESSES</span>');
                    },
                    text        : '', 
                    titleAttr   : 'View Project Access', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        _viewProjectAccesses(); 
                    },
                    enabled     : true,
                    name        : 'add'
                }); 

                buttons.unshift({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default');
                        $(node).addClass('btn bg-orange btn-sm hoverable edit'); 
                        $(node).append('<i class="fa fa-edit"></i>&nbsp;<span class="hidden-xs hidden-sm">VIEW</span>');
                    }, 
                    text        : '', 
                    titleAttr   : 'View Access', 
                    key: { 
                        key     : '2', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        _viewAccesses(); 
                    },
                    enabled     : false,
                    name        : 'edit'
                }); 
                
                return buttons;
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.parent.header = {
                    title: 'User Access Configuration',
                    showButton: false,
                }

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