define([
    'app'
], function (app) {
    app.factory('projectAccessFactory', [
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

    app.service('projectAccessService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails`
             * @return {[type]} [description]
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ProjectAccessService.php/getDetails');
            }

            /**
             * `save` Query string that will update details.
             * @return {[query]}
             */
            _this.save = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ProjectAccessService.php/saveAccess', input);
            };

            _this.update = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ProjectAccessService.php/updateAccess', input);
            };
        }
    ]);

    app.controller('ProjectAccessController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'blockUI',
        'alertify',
        'subParamData',
        'projectAccessFactory',
        'projectAccessService',
        function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, SubParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAccessForm');

            /**
             * `_loadDetails` Fetching of first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                Service.getDetails().then( function (res) {
                    $scope.projects = angular.copy(res.data.projects);
                });
            };

            $scope.addSequence = function (index) {
                $scope.addProjectAccess.push({
                    level : '1,1,1'
                });
            };

            $scope.removeSequence = function (index) {
                $scope.addProjectAccess.splice(index,1);
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
                    Alertify.confirm("Are you sure you want to configure access?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                $timeout( function () {
                                    if (SubParamData.mType == 'add') {
                                        
                                        var tempData = {
                                            user_id     :   SubParamData.user_id,
                                            accesses    :   $scope.addProjectAccess
                                        };
    
                                        Service.save(tempData).then( function (res) {
                                            if (res.data.status) {
                                                Alertify.success("Access to project/s successfully configured!");
                                                $uibModalInstance.close(res.data.accesses);
                                                blocker.stop();
                                            } else {
                                                Alertify.error("Error! Debug if you can. ;)");
                                                blocker.stop();
                                            }
                                        });

                                    } else if (SubParamData.mType == 'edit') {
                                        Service.update($scope.updateProjectAccess).then(function(res){
                                            if (res.data.status) {
                                                Alertify.success("Successfully updated!");
                                                $uibModalInstance.close($scope.updateProjectAccess);
                                                blocker.stop();
                                            } else {
                                                Alertify.error("Error! Debug if you can. ;)");
                                                blocker.stop();
                                            }
                                        });
                                    }
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

                $scope.accessConf          = angular.copy(SubParamData);
                $scope.accessConf.mLabel   = (SubParamData.mType == 'add') ? 'NEW'  : 'UPDATE';
                $scope.accessConf.btnLabel = (SubParamData.mType == 'add') ? 'Save' : 'Update';

                if (SubParamData.mType == 'edit') $scope.updateProjectAccess = SubParamData.data;
                console.log($scope.updateProjectAccess);

                $scope.addProjectAccess = [];

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
