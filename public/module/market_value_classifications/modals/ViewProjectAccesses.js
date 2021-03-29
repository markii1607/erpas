define([
    'app'
], function (app) {
    app.factory('viewProjectAccessesFactory', [
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
                    "dom"            : 'Bfrtip',
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
                            "className"  : "text-center"
                        },
                        {
                            "className" : "text-center",
                            "targets"   : 3,
                            "render"    : function (data, type, row) {
                            	var splitData, output;
                            	
                            	output    = '';
                            	splitData = (data == '' || angular.isUndefined(data)) ? [] : data.split(',');

                            	if (splitData[0] == 1) {
	                            	output = output + '<span class="label label-warning">ADD</span>&nbsp;';
                            	}

                            	if (splitData[1] == 1) {
	                            	output = output + '<span class="label label-warning">EDIT</span>&nbsp;';
                            	}

                            	if (splitData[2] == 1) {
	                            	output = output + '<span class="label label-warning">DELETE</span>';
                            	}

                            	return output;
                            }
                        }
                    ],
                    "columns"      : [
                        { 
                            "data"   : "id" 
                        },
                        { 
                            "data"      : "project_code", 
                        },
                        { 
                            "data"      : "project_name", 
                        },
                        { 
                            "data"      : "level", 
                        }
                    ],
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('viewProjectAccessesService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewProjectAccessesService.php/getDetails?id=' + id);
            }

            /**
             * `archive` Query string that will archive information.
             * @param  {[string]} id
             * @return {[route]}
             */
            _this.archive = function (id) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewProjectAccessesService.php/archiveAccess', {'id' : id});
            }
        }
    ]);

    app.controller('ViewProjectAccessesController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'viewProjectAccessesFactory',
        'viewProjectAccessesService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, _add, _edit, _archive, _formatAccess, blocker = BlockUI.instances.get('blockViewAccess'), subTable;

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

            	Service.getDetails(ParamData.id).then( function (res) {
            		$scope.accessList = angular.copy(res.data.access);

                    $scope.jqDataTableOptions         = Factory.dtOptions();
                    $scope.jqDataTableOptions.buttons = _btnFunc();
                    $scope.jqDataTableOptions.data    = res.data.access;
                    blocker.stop();
            	});
            }

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to save this menu details?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                $timeout( function () {
                                    $scope.menu.type = angular.copy(ParamData.mType);

                                    Service.save($scope.menu).then( function (res) {
                                        if (res.data.status == true) {
                                            Alertify.success("Menu details successfully added!");

                                            $scope.menu.id = res.data.id;

                                            $uibModalInstance.close($scope.menu);
                                            blocker.stop();
                                        } else {
                                            Alertify.error("Menu name already exist!");
                                            blocker.stop();
                                        }
                                    });
                                }, 1000);
                            }
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };

            /**
             * `_add` Adding of new data.
             * @return {[modal]} Instances
             */
            _add = function () {
                var subParamData, modalInstance;

                subParamData = {
                    'user_id' : angular.copy(ParamData.id),
                    'mIcon'   : 'fa fa-plus fa-stack-1x',
                    'mType'   : 'add',
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
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.form = {
                	'full_name' : ParamData.full_name
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
