define([
    'app'
], function (app) {
    app.factory('EditRevisionFactory', [
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

            Factory.dummyClfn = [
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
            ]

            Factory.dummySubClfn = [
                {
                    id: 1,
                    classification_name: 'Residential',
                    name: 'R1',
                },
                {
                    id: 2,
                    classification_name: 'Residential',
                    name: 'R2',
                },
                {
                    id: 3,
                    classification_name: 'Residential',
                    name: 'R3',
                },
                {
                    id: 4,
                    classification_name: 'Commercial',
                    name: 'C1',
                },
                {
                    id: 5,
                    classification_name: 'Commercial',
                    name: 'C2',
                },
                {
                    id: 6,
                    classification_name: 'Commercial',
                    name: 'C3',
                },
                {
                    id: 7,
                    classification_name: 'Industrial',
                    name: 'I1',
                },
            ]

            return Factory;
        }
    ]);

    app.service('EditRevisionService', [
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

    app.controller('EditRevisionController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditRevisionFactory',
        'EditRevisionService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditRevision');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                $scope.classifications = Factory.dummyClfn
                $scope.editRev.classification = $filter('filter')($scope.classifications, {
                    'name': $scope.editRev.classification_name,
                }, true)[0];
                $scope.selectSubClasses($scope.editRev.classification, 1)
                $scope.editRev.subclassification = $filter('filter')($scope.subclassifications, {
                    'name': $scope.editRev.sub_classification_name,
                }, true)[0];
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

            $scope.selectSubClasses = function(model, loadDet = 0) {
                if (loadDet == 0) delete $scope.editRev.subclassification

                $scope.subclassifications = $filter('filter')(Factory.dummySubClfn, {
                    'classification_name': model.name,
                }, true);

                $scope.editRev.disableSubClass = false
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this revision?",
                        function (res) {
                            if (res) {
                                // blocker.start();

                                // $timeout( function () {
                                //     Service.save($scope.addClfn).then( function (res) {
                                //         if (res.data.status == true) {
                                //             Alertify.success("Classification successfully added!");

                                //             $uibModalInstance.close($scope.addClfn);
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
                
                $scope.editRev = ParamData.data
                $scope.editRev.disableSubClass = true

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
