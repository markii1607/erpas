define([
    'app'
], function (app) {
    app.factory('viewAccessFactory', [
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
                            "targets"   : 2,
                            "render"    : function (data, type, row) {
                            	var splitData, output;
                            	
                            	output    = '';
                            	splitData = (data == '' || angular.isUndefined(data)) ? [] : data.split(',');

                            	if (splitData[0]) {
	                            	output = output + '<span class="label label-warning">ADD</span>&nbsp;';
                            	}

                            	if (splitData[1]) {
	                            	output = output + '<span class="label label-warning">EDIT</span>&nbsp;';
                            	}

                            	if (splitData[2]) {
	                            	output = output + '<span class="label label-warning">DELETE</span>';
                            	}

                            	if (splitData.length == 0) {
	                            	output = output + '<span class="label label-primary">PARENT MENU</span>';
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
                            "data"      : "name", 
                        },
                        { 
                            "data"      : "level", 
                        }
                    ],
		            "createdRow" : function(row, data, dataIndex) {
		                if (data.parent == null) {
		                    $(row).addClass( "bg-red" );
		                }
		            },
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('viewAccessService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/getDetails?id=' + id);
            }

            /**
             * `archive` Query string that will archive information.
             * @param  {[string]} id
             * @return {[route]}
             */
            _this.archive = function (id) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/archiveAccess', {'id' : id});
            }
        }
    ]);

    app.controller('ViewAccessController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'viewAccessFactory',
        'viewAccessService',
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
                    $scope.jqDataTableOptions.data    = _formatAccess(res.data.access);
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
             * `_formatAccess` For access from parent down to child modules
             * @param  {[object]} data
             * @return {[object]}
             */
            _formatAccess = function (data) {
            	var parents, output = [];

            	parents = $filter('filter')(data, {
            		'parent' : null
            	}, true);

            	angular.forEach(parents, function (pVal, pKey) {
            		output.push(pVal);

            		modules = $filter('filter')(data, {
            			'parent' : pVal.menu_id
            		}, true);

            		angular.forEach(modules, function (mVal, mKey) {
	            		output.push(mVal);
            		});
            	});

            	return output;
            };

            /**
             * `_add` Adding of new data.
             * @return {[modal]} Instances
             */
            _add = function () {
                var subParamData, modalInstance;

                subParamData = {
                    'user_id' : angular.copy(ParamData.id),
                    'rowData' : null,
                    'mIcon'   : 'fa fa-plus fa-stack-1x',
                    'mType'   : 'add',
                    'allRows' : subTable.DataTable().rows().data().toArray()
                };

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'access.html',
                    controller      : 'AccessController',
                    size            : 'md',
                    resolve         : {
                        subParamData : function () {
                            return subParamData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                	var rows = angular.copy(subTable.DataTable().rows().data().toArray());

                    subTable.DataTable().rows().remove().draw(true);

					rows.unshift(res);

                    var newRows = _formatAccess(rows);

                    angular.forEach(newRows, function (val, key) {
                        subTable.DataTable().row.add(val).draw().select();
                        subTable.find('tbody tr').css('cursor', 'pointer');
                    });
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            /**
             * `_archive` Archiving of information.
             * @return {[mixed]}
             */
            _archive = function () {
                Alertify.confirm("Are you sure you want to delete this selected user access?",
                    function (res) {
                        if (res) {
                            blocker.start();

                            $timeout( function () {
                                Service.archive(subTable.DataTable().rows('.selected').data()[0].id).then( function (res) {
                                    if (res.data == 'true') {
                                        subTable.DataTable().row('.selected').remove().draw(true);

					                	var rows    = angular.copy(subTable.DataTable().rows().data().toArray());
					                    var newRows = _formatAccess(rows);

					                    subTable.DataTable().rows().remove().draw(true);

					                    angular.forEach(newRows, function (val, key) {
					                        subTable.DataTable().row.add(val).draw().select();
					                        subTable.find('tbody tr').css('cursor', 'pointer');
					                    });

                                        Alertify.success("User access successfully deleted!");

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
                    titleAttr   : 'Remove Menu Access', 
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
            
                buttons.unshift( { 
                    init        : function(api, node, config) { 
                        $(node).removeClass('btn-default');
                        $(node).addClass('btn bg-orange btn-sm hoverable add');
                        $(node).append('<i class="fa fa-plus-square"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD</span>');
                    }, 
                    text        : '', 
                    titleAttr   : 'Add New Menu Access', 
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
