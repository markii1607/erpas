define([
    'app',
], function (app) {
    app.factory('spouseFactory', [
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

    app.service('spouseService', [
        '$http',
        function ($http) {
            var _this = this;

              /**
             * `getDetails` Query string that will get positions.
             * @return {[route]}
             */
            _this.getDetails = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/SpouseService.php/getDetails');
            };

            /**
             * `save` Query string that will save details.
             * @return {[query]}
             */
            _this.save = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/SpouseService.php/saveSpouseDetails', input);
            };

        }
    ]);

    app.controller('SpouseController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'subParamData',
        'blockUI',
        'alertify',
        'spouseFactory',
        'spouseService',
        function ($scope, $uibModalInstance, $timeout, $filter, SubParamData, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockSpouse');

            /**
             * 
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then(function (res) {
                    $scope.spouses = angular.copy(res.data.spouses);

                    blocker.stop();
                });
            };

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            /**
             * `addChild` Pushing child data.
             */
            $scope.addChild = function () {
                var temp = angular.copy($scope.childInfo);

                $scope.spouseInfo.children.unshift(temp);

                delete $scope.childInfo.ch_name;
                delete $scope.childInfo.ch_age;

                angular.element('#ch-name').focus();
            };

            /**
             * TODO:
             * 
             * `editChild` Editing of child name and age
             * @return {[void]}
             */
            $scope.editChild = function (index) {};

            /**
             * `deleteChild` D
             * @param  {[type]} index [description]
             * @return {[type]}       [description]
             */
            $scope.deleteChild = function (index) {
                Alertify.confirm("Are you sure you want to remove this child from the list?",
                    function (res) {
                        if (res) {
                            $timeout( function () {
                                $scope.spouseInfo.children.splice(index, 1);
                            });
                        }
                    }
                )
            }

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this new spouse?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                $timeout( function () {
                                    Alertify.success("New spouse successfully added!");

                                    $scope.spouseInfo.id       = (SubParamData.mType == 'new') ? SubParamData.rowCount + 1 : SubParamData.rowData.id;
                                    $scope.spouseInfo.ch_count = $scope.spouseInfo.children.length;

                                    $scope.spouseInfo.data_status = (SubParamData.mType == 'new') ? 'new' : 'saved';

                                    $uibModalInstance.close($scope.spouseInfo);

                                    blocker.stop();
                                }, 1000);
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

                $scope.config          = SubParamData;
                $scope.config.btnLabel = (SubParamData.mType == 'new') ? 'Save' : 'Update';
                $scope.childInfo       = {};
                $scope.spouseInfo      = (SubParamData.mType == 'new') ? { 'children' : [] } : SubParamData.rowData;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
