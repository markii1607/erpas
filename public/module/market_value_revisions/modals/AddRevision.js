define([
    'app'
], function (app) {
    app.factory('AddRevisionFactory', [
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

    app.service('AddRevisionService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/MarketValueRevisionService.php/getSelectionDetails');
            }

            _this.getSubClassifications = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/MarketValueRevisionService.php/getSubClassSelection?id=' + id);
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/MarketValueRevisionService.php/saveNewMarketValue', data);
            }

        }
    ]);

    app.controller('AddRevisionController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddRevisionFactory',
        'AddRevisionService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddRevision');

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
                Service.getSubClassifications($scope.addRev.classification.id).then(res => {
                    if (res.data.sub_classifications != undefined) {
                        if(res.data.sub_classifications.length == 0) Alertify.alert("<b><i>" + $scope.addRev.classification.name + "</i></u> has no existing sub-classification data.");
                        $scope.sub_classifications = res.data.sub_classifications;
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                });
            };

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add these details?",
                        function () {
                            blocker.start();
    
                            Service.save($scope.addRev).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("Market value details successfully added!");
        
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
                
                $scope.addRev = {
                    disableSubClass: true,
                };

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
