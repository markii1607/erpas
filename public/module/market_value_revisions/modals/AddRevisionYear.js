define([
    'app'
], function (app) {
    app.factory('AddRevisionYearFactory', [
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

    app.service('AddRevisionYearService', [
        '$http',
        function ($http) {
            var _this = this;

            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/AddRevisionYearService.php/getRevisionYearDetails');
            }

            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/AddRevisionYearService.php/saveNewRevisionYear', data);
            }

            _this.update = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/AddRevisionYearService.php/saveUpdatedRevisionYear', data);
            }

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/MarketValueRevision/AddRevisionYearService.php/archiveRevisionYear', data);
            }
        }
    ]);

    app.controller('AddRevisionYearController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AddRevisionYearFactory',
        'AddRevisionYearService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddRevisionYear');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();
                Service.getDetails().then(res => {
                    if (res.data.revision_years != undefined) {
                        $scope.revision_years = res.data.revision_years;
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
            $scope.save = function () {
                Alertify.confirm("Are you sure you want to add this revision year?", function () {
                    blocker.start();

                    Service.save($scope.addRvnYr).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Revision Year successfully added!");
                            $scope.revision_years.push(res.data.rowData);
                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving! Please contact the administrator.");
                            blocker.stop();
                        }
                    });
                });
            };

            $scope.triggerUpdate = function(data, index){
                $scope.editRvnYr.id   = data.id;
                $scope.editRvnYr.year = data.year;
                $scope.selectedIndex  = index;
                $scope.enableEdit     = true;
            }

            $scope.edit = function () {
                Alertify.confirm("Are you sure you want to edit this revision year?", function () {
                    blocker.start();

                    Service.update($scope.editRvnYr).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Revision Year successfully updated!");
                            $scope.revision_years[$scope.selectedIndex] = res.data.rowData;
                            $scope.enableEdit = false;
                            delete $scope.editRvnYr;

                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving! Please contact the administrator.");
                            blocker.stop();
                        }
                    });
                });
            };

            $scope.archive = function (data, index) {
                Alertify.confirm("Are you sure you want to delete this revision year?", function () {
                    blocker.start();

                    Service.archive(data).then( function (res) {
                        if (res.data.status) {
                            Alertify.success("Revision Year successfully deleted!");
                            $scope.revision_years.splice(index, 1);
                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving! Please contact the administrator.");
                            blocker.stop();
                        }
                    });
                });
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.addRvnYr = {};
                $scope.editRvnYr = {};
                $scope.enableEdit = false;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
