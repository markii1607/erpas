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

            return Factory;
        }
    ]);

    app.service('EditClassificationService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueClassification/MarketValueClassificationService.php/saveUpdatedClassification', data);
            }
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
                    Alertify.confirm("Are you sure you want to update this classification?", function () {
                        blocker.start();
    
                        Service.save($scope.editClfn).then( function (res) {
                            if (res.data.status) {
                                Alertify.success("Classification successfully updated!");
    
                                $uibModalInstance.close(res.data.rowData);
                                blocker.stop();
                            } else {
                                Alertify.error("An error occurred while saving! Please contact the administrator.");
                                blocker.stop();
                            }
                        });
                    });
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
