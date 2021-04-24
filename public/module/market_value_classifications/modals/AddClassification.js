define([
    'app'
], function (app) {
    app.factory('AddClassificationFactory', [
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

    app.service('AddClassificationService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueClassification/MarketValueClassificationService.php/saveNewClassification', data);
            }
        }
    ]);

    app.controller('AddClassificationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddClassificationFactory',
        'AddClassificationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddClassification');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
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
            $scope.save = function (isFormValid) {
                if (isFormValid) {
                    Alertify.confirm("Are you sure you want to add this classification?", function () {
                        blocker.start();
    
                        Service.save($scope.addClfn).then( function (res) {
                            if (res.data.status) {
                                Alertify.success("Classification successfully added!");
    
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
                
                $scope.addClfn = {};

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
