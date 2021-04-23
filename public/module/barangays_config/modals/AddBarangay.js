define([
    'app'
], function (app) {
    app.factory('AddBarangayFactory', [
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

    app.service('AddBarangayService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/BarangaysConfig/BarangaysConfigService.php/saveNewBarangay', data);
            }
        }
    ]);

    app.controller('AddBarangayController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddBarangayFactory',
        'AddBarangayService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddBarangay');

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
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this barangay?",
                        function () {
                            blocker.start();
    
                            Service.save($scope.addBrgy).then( function (res) {
                                if (res.data.status) {
                                    Alertify.success("Barangay successfully added!");
        
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
                
                $scope.addBrgy = {};

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
