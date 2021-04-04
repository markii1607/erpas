define([
    'app'
], function (app) {
    app.factory('EditClassificationFactory', [
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

    app.service('EditClassificationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            // _this.getDetails = function (id) {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/getDetails?id=' + id);
            // }

            /**
             * `archive` Query string that will archive information.
             * @param  {[string]} id
             * @return {[route]}
             */
            // _this.archive = function (id) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/archiveAccess', {'id' : id});
            // }
        }
    ]);

    app.controller('EditClassificationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditClassificationFactory',
        'EditClassificationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditClassification');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                // blocker.start();

            	// Service.getDetails(ParamData.id).then( function (res) {
            	// 	$scope.accessList = angular.copy(res.data.access);

                //     $scope.jqDataTableOptions         = Factory.dtOptions();
                //     $scope.jqDataTableOptions.buttons = _btnFunc();
                //     $scope.jqDataTableOptions.data    = _formatAccess(res.data.access);
            	// });
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
                    Alertify.confirm("Are you sure you want to update this classification?",
                        function (res) {
                            if (res) {
                                // blocker.start();

                                // $timeout( function () {
                                //     Service.save($scope.editClfn).then( function (res) {
                                //         if (res.data.status == true) {
                                //             Alertify.success("Classification successfully added!");

                                //             $uibModalInstance.close($scope.editClfn);
                                //             blocker.stop();
                                //         } else {
                                //             Alertify.error("Classification already exist!");
                                //             blocker.stop();
                                //         }
                                //     });
                                // }, 1000);
                            }
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.editClfn = ParamData.data;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }
    ]);
}); // end define
