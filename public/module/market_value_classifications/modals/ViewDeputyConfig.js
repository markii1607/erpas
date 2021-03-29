define([
    'app'
], function (app) {
    app.factory('viewDeputyConfigFactory', [
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

            Factory.dtOptions = function () {
                var options = {};

                options = {
                    "dom"            : 'Brtip',
                    "paging"         : true,
                    "lengthChange"   : true,
                    "pageLength"     : 25,
                    "searching"      : true,
                    "ordering"       : false,
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
                            "className"  : "text-center",
                        },
                        {
                            "targets"    : 1,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center",
                            "defaultContent" : "<button id='firstButton' type='button' class='btn btn-danger btn-xs waves-effect'><i class='glyphicon glyphicon-remove'></i></button>"
                        },
                        {
                            "targets"    : 2,
                            "searchable" : true,
                            "orderable"  : true,
                            "className"  : "text-left",
                            "render"     : function(data, type, full, meta) {
                                                return '<b class="text-uppercase">' + data + '</b>' + '<br>' + full.position_name + ' - <small>' + full.department_name + '</small>';
                            }
                        },
                        {
                            "targets"   : 3,
                            "className" : "text-center",
                            "render"    : function (data, type, row) {
                            	var output;

                            	if (data == 'ON') {
	                            	output = '<span class="label label-success">ON</span>';
                            	} else if (data == 'OFF') {
	                            	output = '<span class="label label-danger">OFF</span>';
                                }

                            	return output;
                            }
                        },
                        {
                            "targets"    : 4,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center",
                            "render"    : function (data, type, row) {

                                console.log(data);

                            	var output;


                                // (data.approve == 1) ?  output = output + '<span class="label bg-indigo">APPROVALS</span>' : '';
                                // (data.upload == 1)  ?  output = output + '<span class="label bg-indigo">UPLOADING</span>' : '';

                                output =  (data.approve == 1) ?  '<span class="label label-info">APPROVALS</span> <br>' : '';
                                output += (data.upload == 1)  ?'<span class="label label-primary">UPLOADING</span>' : '';


                            	// if (data == 'ON') {
	                            // 	output = '<span class="label label-success">ON</span>';
                            	// } else if (data == 'OFF') {
	                            // 	output = '<span class="label label-danger">OFF</span>';
                                // }

                            	return output;
                            }
                        },
                        {
                            "targets"    : 5,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center",
                            "defaultContent" : "<button id='secondButton' type='button' class='btn btn-primary btn-xs waves-effect'><i class='glyphicon glyphicon-pencil'></i></button>"
                        },
                    ],
                    "columns"      : [
                        { 
                            "data"      : "id" 
                        },
                        { 
                            "data"      : null 
                        },
                        { 
                            "data"      : "full_name", 
                        },
                        { 
                            "data"      : "status", 
                        },
                        { 
                            "data"      : "priviledges", 
                        },
                        { 
                            "data"      : null, 
                        }
                    ],
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('viewDeputyConfigService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewDeputyConfigService.php/getDetails?id=' + id);
            }

            /**
             * `archive` Query string that will archive information.
             * @param  {[string]} id
             * @return {[route]}
             */
            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewDeputyConfigService.php/changeAccountStatus', data);
            }

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewDeputyConfigService.php/archiveDeputy', data);
            }
        }
    ]);

    app.controller('ViewDeputyConfigController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'viewDeputyConfigFactory',
        'viewDeputyConfigService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, _add, _edit, _archive, _formatAccess, blocker = BlockUI.instances.get('blockViewAccess'), subTable;

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

            	Service.getDetails(ParamData.id).then( function (res) {
                    $scope.userDeputies = angular.copy(res.data.deputies);
                    
                    $scope.jqDataTableOptions         = Factory.dtOptions();
                    $scope.jqDataTableOptions.data    = $scope.userDeputies;

                    blocker.stop();
            	});
            };

            $scope.rowBtns =  {
                'firstButton'   : function(data, index) {
                        $scope.archiveData(data, index);
                },
                'secondButton'   : function(data, index) {
                        $scope.updateData(data, index);
                },
            };

            $scope.archiveData = function(data, index){
                Alertify.confirm('<b>This data is saved in the database. Are you sure you want this removed?</b>', function(confirmation){
                    if (confirmation) {
                        Service.archive(data).then(function(res){
                            if (res.data.status) {
                                Alertify.success('Successfully removed data!')
                                subTable.DataTable().row(index).remove().draw(true);
                            } else {
                                Alertify.error('Error! Debug if you can. ;)');
                            }
                        });
                    }
                });
            };

            $scope.updateData = function (data, index) {
                console.log(data);
                var subParamData, modalInstance;

                subParamData = {
                    'id'        : data.id,
                    'status'    : data.status,
                    'data'      : data
                };

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'change_deputy_status.html',
                    controller      : 'ChangeDeputyStatusController',
                    size            : 'md',
                    resolve         : {
                        subParamData : function () {
                            return subParamData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    console.log(res);
                    // subTable.DataTable().row(index).data().status = angular.copy(res);
                    subTable.DataTable().row(index).data(res).draw();
                }, function (res) {
                    // Result when modal is dismissed
                });
            };

            $scope.saveAccountStatus = function (status) {
                switch (status) {
                    case 'on-leave':
                        $scope.deputyConfigData.chk_online  = false;
                        $scope.deputyConfigData.chk_offline = false;
                        $scope.userAccountStatus = 'on-leave';

                        break;

                    case 'online':
                        $scope.deputyConfigData.chk_onleave = false;
                        $scope.deputyConfigData.chk_offline = false;
                        $scope.userAccountStatus = 'online';

                        break;

                    case 'offline':
                        $scope.deputyConfigData.chk_onleave = false;
                        $scope.deputyConfigData.chk_online  = false;
                        $scope.userAccountStatus = 'offline';

                        break;
                
                    default:
                        $scope.deputyConfigData.chk_onleave = false;
                        $scope.deputyConfigData.chk_online  = false;
                        $scope.deputyConfigData.chk_offline = false;
                        $scope.userAccountStatus = null;
                        break;
                }

                var tempData = {
                    id  :   ParamData.id, 
                    onleaveStatus : $scope.deputyConfigData.chk_onleave,
                    onlineStatus  : $scope.deputyConfigData.chk_online,
                    offlineStatus : $scope.deputyConfigData.chk_offline,
                };

                Service.save(tempData).then(function(res){
                    if (res.data.status) {
                        Alertify.success('You have updated the account status of ' + '<b><i>' + ParamData.full_name + '</i></b>.');
                    } else {
                        Alertify.error('Failed to update status. Debug if you can. ;)');
                    }
                });

            };       
            
            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                var status = null;
                if ($scope.deputyConfigData.chk_onleave) status = 'on-leave';
                if ($scope.deputyConfigData.chk_online)  status = 'online';
                if ($scope.deputyConfigData.chk_offline) status = 'offline';
                $uibModalInstance.close(status);
            };

            /**
             * `_add` Adding of new data.
             * @return {[modal]} Instances
             */
            $scope.addDeputy = function () {
                var subParamData, modalInstance;

                subParamData = {
                    'user_id' : angular.copy(ParamData.id),
                };

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_deputy.html',
                    controller      : 'AddDeputyController',
                    size            : 'md',
                    resolve         : {
                        subParamData : function () {
                            return subParamData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                	/* var rows = angular.copy(subTable.DataTable().rows().data().toArray());

                    subTable.DataTable().rows().remove().draw(true);

					rows.unshift(res);

                    var newRows = _formatAccess(rows); */

                    angular.forEach(res, function (val, key) {
                        subTable.DataTable().row.add(val).draw().select();
                        subTable.find('tbody tr').css('cursor', 'pointer');
                    });
                }, function (res) {
                    // Result when modal is dismissed
                });
            };

            _edit = function () {
                var data = subTable.DataTable().rows('.selected').data()[0];
                if (data != undefined) {
                    
                    var subParamData, modalInstance;
                    subParamData = {
                        'data'    :  data,
                        'mIcon'   : 'fa fa-edit fa-stack-1x',
                        'mType'   : 'edit',
                    };
    
                    modalInstance = $uibModal.open({
                        animation       : true,
                        keyboard        : false,
                        backdrop        : 'static',
                        ariaLabelledBy  : 'modal-title',
                        ariaDescribedBy : 'modal-body',
                        templateUrl     : 'project_access.html',
                        controller      : 'ProjectAccessController',
                        size            : 'md',
                        resolve         : {
                            subParamData : function () {
                                return subParamData;
                            }
                        }
                    });
    
                    modalInstance.result.then(function (res) {

                        var index = subTable.DataTable().row('.selected').index();
                        subTable.DataTable().row(index).data(res).draw();

                    }, function (res) {
                        // Result when modal is dismissed
                    });
                } else {
                    Alertify.alert('<b>No selected row!</b>');
                }
            };

            /**
             * `_archive` Archiving of information.
             * @return {[mixed]}
             */
            _archive = function () {
                Alertify.confirm("Are you sure you want to delete this selected project access?",
                    function (res) {
                        if (res) {
                            blocker.start();

                            $timeout( function () {
                                Service.archive(subTable.DataTable().rows('.selected').data()[0].id).then( function (res) {
                                    if (res.data.status) {
                                        subTable.DataTable().row('.selected').remove().draw(true);

                                        Alertify.success("Project access successfully deleted!");

                                        blocker.stop();
                                    } else {
                                        Alertify.error("Cannot delete data that is already used in the system.");
                                        
                                        blocker.stop();
                                    }
                                });
                            }, 1000);
                        }
                    }
                );
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
                        $(node).removeClass('btn-default'); 
                        $(node).addClass('btn bg-orange btn-sm hoverable delete'); 
                        $(node).append('<i class="fa fa-trash"></i>&nbsp;<span class="hidden-xs hidden-sm">REMOVE</span>'); 
                    }, 
                    text        : '', 
                    titleAttr   : 'Remove Project Access', 
                    key: { 
                        key     : '3', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        _archive(); 
                    } ,
                    enabled     : false,
                    name        : 'delete'
                });

                buttons.unshift({ 
                    init        : function(api, node, config) { 
                        $(node).removeClass('btn-default'); 
                        $(node).addClass('btn bg-orange btn-sm hoverable edit'); 
                        $(node).append('<i class="fa fa-edit"></i>&nbsp;<span class="hidden-xs hidden-sm">EDIT</span>'); 
                    }, 
                    text        : '', 
                    titleAttr   : 'Edit Project Access', 
                    key: { 
                        key     : '2', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        _edit(); 
                    } ,
                    enabled     : false,
                    name        : 'edit'
                });
            
                buttons.unshift( { 
                    init        : function(api, node, config) { 
                        $(node).removeClass('btn-default');
                        $(node).addClass('btn bg-orange btn-sm hoverable add');
                        $(node).append('<i class="fa fa-plus-square"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD</span>');
                    }, 
                    text        : '', 
                    titleAttr   : 'Add New Project Access', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    },
                    'action'    : function () { 
                        _add(); 
                    },
                    enabled     : true,
                    name        : 'add'
                }); 
                
                return buttons;
            };

            /**
             *`addMoreItems` Adding more items for infinite scroll
             */
            $scope.addMoreItems = function () {
                $scope.infiniteScroll.currentItems += $scope.infiniteScroll.numToAdd;
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.infiniteScroll              = {};
                $scope.infiniteScroll.numToAdd     = 20;
                $scope.infiniteScroll.currentItems = 20;
                
                $scope.form = {
                	'full_name' : ParamData.full_name
                };

                $scope.userAccountStatus = ParamData.account_status;

                $scope.deputyConfigData = {
                    chk_onleave : ($scope.userAccountStatus == 'on-leave') ? true : false,
                    chk_online  : ($scope.userAccountStatus == 'online')   ? true : false,
                    chk_offline : ($scope.userAccountStatus == 'offline')  ? true : false,
                };
                
                $timeout( function () {
                    subTable = angular.element('#viewAccess');
                });

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
