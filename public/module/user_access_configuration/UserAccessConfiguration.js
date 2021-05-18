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
                'module/user_access_configuration/modals/add_user.html',
                'module/user_access_configuration/modals/edit_user.html',
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
                    "responsive"     : false,
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
                        },
                        {
                            "targets"    : 5,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center",
                            "render"     :  function(data, type, full, meta) {
                                                if (data == '1') {
                                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#ED3850; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">SUPER ADMIN</span>';
                                                } else if (data == '2') {
                                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#ED389A; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">ADMIN</span>';
                                                } else if (data == '3') {
                                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#38C3ED; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">TREASURER</span>';
                                                } else if (data == '4') {
                                                    return '<span style="display: inline; padding: .2em .6em .3em; background-color:#38E6ED; color: white; border-radius: 25px; font-size: 12px; font-weight: 500">ACCOUNTING</span>';
                                                } else {
                                                    return '';
                                                }

                            }
                        },
                        {
                            "targets"    : 6,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-left",
                            "render"    : function(data, type, full, meta) {
                                                var str = '';
                                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="Reset" class="btn btn-block bg-warning btn-sm mr-2 text-white text-left"><i class="fas fa-history"></i> Reset Password</button>';
                                                str += '<p style="margin-bottom:5px;"></p>';
                                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Edit" class="btn btn-block bg-primary btn-sm mr-2 text-white text-left"><i class="fas fa-edit"></i> Edit User Info</button>';
                                                str += '<p style="margin-bottom:5px;"></p>';
                                                str += '<button type="submit" id="thirdButton" data-toggle="tooltip" title="Delete" class="btn btn-block bg-danger btn-sm mr-2 text-white text-left"><i class="fas fa-trash"></i> Delete User</button>';

                                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : null 
                        },
                        { 
                            "data" : "username", 
                        },
                        { 
                            "data" : "full_name", 
                        },
                        { 
                            "data" : "department", 
                        },
                        { 
                            "data" : "position", 
                        },
                        { 
                            "data" : "access_type", 
                        },
                        { 
                            "data" : null, 
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

            _this.reset = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/UserAccessConfigurationService.php/resetUserPassword', data);
            };

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/UserAccessConfigurationService.php/archiveUser', data);
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
                    if (res.data.users != undefined) {
                        $scope.jqDataTableOptions         = Factory.dtOptions();
                        $scope.jqDataTableOptions.buttons = _btnFunc();
                        $scope.jqDataTableOptions.data    = res.data.users;
                        
                        blocker.stop();
                    } else {
                        Alertify.error('An error occurred while fetching data. Please contact the administrator.');
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

                buttons.unshift({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default btn-secondary');
                        $(node).addClass('btn bg-info text-white btn-sm add'); 
                        $(node).append('<i class="fas fa-plus"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD USER</span>');
                    },
                    text        : '', 
                    titleAttr   : 'Add User', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addUser(); 
                    },
                    enabled     : true,
                    name        : 'add'
                }); 

                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.resetPassword(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.editUser(data, index)
                },
                "thirdButton": function(data, index) {
                    $scope.archiveUser(data, index)
                },
            };

            $scope.addUser = function(){
                var paramData, modalInstance;

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_user.html',
                    controller      : 'AddUserController',
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

            $scope.resetPassword = function(data, index){
                Alertify
                .okBtn("Yes")
                .cancelBtn("No")
                .confirm("Are you sure you want to reset the password for <b><i>" + data.full_name + "'s</i></b> user account?", function(){
                    blocker.start();
                    Service.reset(data).then(res => {
                        if (res.data.status) {
                            Alertify.success("Password reset is successful!");
                            Alertify.alert("DEFAULT PASSWORD: <b>12345</b>");

                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving data. Please contact the administrator.");
                            blocker.stop();
                        }
                    })
                })
            }

            $scope.editUser = function(data, index){
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
                    templateUrl     : 'edit_user.html',
                    controller      : 'EditUserController',
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

            $scope.archiveUser = function(data, index){
                Alertify
                .okBtn("Yes")
                .cancelBtn("No")
                .confirm("Are you sure you want to delete the selected user?", function(){
                    blocker.start();
                    Service.archive(data).then(res => {
                        if (res.data.status) {
                            table.DataTable().row('.selected').remove().draw(true);
                            Alertify.success("Deleted successfully!");

                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving data. Please contact the administrator.");
                            blocker.stop();
                        }
                    })
                })
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "User Access Configuration"
                $scope.header.link.sub = ""
                $scope.header.link.main = "User Access Configuration"

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