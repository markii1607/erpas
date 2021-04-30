define([
    'app'
], function (app) {
    app.factory('EditSubClassificationFactory', [
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

    app.service('EditSubClassificationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[type]}
             */
             _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueSubclassification/MarketValueSubclassificationService.php/getSelectionDetails');
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueSubclassification/MarketValueSubclassificationService.php/saveUpdatedSubClassification', data);
            }
        }
    ]);

    app.controller('EditSubClassificationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'EditSubClassificationFactory',
        'EditSubClassificationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockEditSubClassification');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();
            	Service.getDetails().then( function (res) {
                    if (res.data.classifications != undefined) {
                        
                        $scope.classifications = res.data.classifications;
                        blocker.stop();
                        
                    } else {
                        Alertify.error("An error occurred while fetching data! Please contact the administrator.");
                        blocker.stop();
                    }
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
                    Alertify.confirm("Are you sure you want to update this sub-classification?",
                        function () {
                            blocker.start();

                            Service.save($scope.editSubClfn).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("Sub-Classification successfully updated!");
        
                                    $uibModalInstance.close(res.data.rowData);
                                    blocker.stop();
                                } else {
                                    Alertify.error("An error occurred while saving! Please contact the administrator.");
                                    blocker.stop();
                                }
                            });
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

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
