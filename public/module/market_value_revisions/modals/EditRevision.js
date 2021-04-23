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

            return Factory;
        }
    ]);

    app.service('EditRevisionService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/MarketValueRevisionService.php/getSelectionDetails');
            }

            _this.getSubClassifications = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/MarketValueRevisionService.php/getSubClassSelection?id=' + id);
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/MarketValueRevisionService.php/saveUpdatedMarketValue', data);
            }
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
                blocker.start();

            	Service.getDetails(ParamData.id).then( function (res) {
            		if (res.data.classifications != undefined) {
                        $scope.classifications = res.data.classifications;
                        $scope.revision_years  = res.data.revision_years;
                        $scope.selectSubClasses();
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.")
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

            $scope.selectSubClasses = function() {
                blocker.start();
                Service.getSubClassifications($scope.editRev.classification.id).then(res => {
                    if (res.data.sub_classifications != undefined) {
                        if(res.data.sub_classifications.length == 0) Alertify.alert("<b><i>" + $scope.editRev.classification.name + "</i></u> has no existing sub-classification data.");
                        $scope.sub_classifications = res.data.sub_classifications;
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                });
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add these market value details?",
                        function () {
                            blocker.start();
    
                            Service.save($scope.editRev).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("Market value details successfully updated!");
        
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
                
                $scope.editRev = ParamData.data;
                $scope.editRev.classification    = ParamData.data.sub_classification.classification;
                $scope.editRev.subclassification = ParamData.data.sub_classification;
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
