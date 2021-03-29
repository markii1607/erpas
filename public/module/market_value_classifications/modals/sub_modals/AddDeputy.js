define([
    'app'
], function (app) {
    app.factory('addDeputyFactory', [
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

    app.service('addDeputyService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails`
             * @return {[type]} [description]
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/AddDeputyService.php/getDetails');
            }

            /**
             * `save` Query string that will update details.
             * @return {[query]}
             */
            _this.save = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/AddDeputyService.php/saveDeputies', input);
            };
        }
    ]);

    app.controller('AddDeputyController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'blockUI',
        'alertify',
        'subParamData',
        'addDeputyFactory',
        'addDeputyService',
        function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, SubParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAccessForm');

            /**
             * `_loadDetails` Fetching of first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                Service.getDetails().then( function (res) {
                    $scope.users = angular.copy(res.data.users);
                });
            };

            $scope.addSequence = function (index) {
                $scope.userDeputies.push({
                });
            };

            $scope.removeSequence = function (index) {
                $scope.userDeputies.splice(index,1)
            };
            

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
                    Alertify.confirm("Save data?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                $timeout( function () {
                                    var tempData = {
                                        user_id :   SubParamData.user_id,
                                        tblData :   $scope.userDeputies
                                    };
                                    Service.save(tempData).then(function(res){
                                        if (res.data.status) {
                                            Alertify.success("Successfully saved data!");
                                            $uibModalInstance.close(res.data.tblData);
                                            blocker.stop();
                                        } else {
                                            Alertify.error("Error! Debug if you can. ;)");
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
             *`addMoreItems` Adding more items for infinite scroll
             */
            $scope.addMoreItems = function () {
                $scope.infiniteScroll.currentItems += $scope.infiniteScroll.numToAdd;
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                // infinite scroll settings
                $scope.infiniteScroll              = {};
                $scope.infiniteScroll.numToAdd     = 20;
                $scope.infiniteScroll.currentItems = 20;

                $scope.userDeputies = [];

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
