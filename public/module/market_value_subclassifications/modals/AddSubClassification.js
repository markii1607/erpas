define([
    'app'
], function (app) {
    app.factory('AddSubClassificationFactory', [
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

    app.service('AddSubClassificationService', [
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
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueSubclassification/MarketValueSubclassificationService.php/saveNewSubClassification', data);
            }
        }
    ]);

    app.controller('AddSubClassificationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddSubClassificationFactory',
        'AddSubClassificationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddSubClassification');

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
                    Alertify.confirm("Are you sure you want to add this sub-classification?",
                        function () {
                            blocker.start();
    
                            Service.save($scope.addSubClfn).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("Sub-Classification successfully added!");
        
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
                
                $scope.addSubClfn = {};

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
