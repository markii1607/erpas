define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('employmentHistoryFactory', [
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

    app.service('employmentHistoryService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get positions.
             * @return {[route]}
             */
            _this.getDetails = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/EmploymentHistoryService.php/getDetails');
            };
        }
    ]);

    app.controller('EmploymentHistoryController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'subParamData',
        'blockUI',
        'alertify',
        'employmentHistoryFactory',
        'employmentHistoryService',
        function ($scope, $uibModalInstance, $timeout, $filter, SubParamData, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _pluginFormat, blocker = BlockUI.instances.get('blockEmploymentHistory');

            /**
             * `_loadDetails` Load first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then( function (res) {
                    $scope.positions   = angular.copy(res.data.positions);
                    $scope.departments = angular.copy(res.data.departments);

                    // $scope.educationalBackgroundInfo.department = ;
                    console.log($scope.employmentHistoryInfo);
                    angular.element('#from_date').val($scope.employmentHistoryInfo.from_date);
                    angular.element('#to_date').val($scope.employmentHistoryInfo.to_date);

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
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this new employment history?",
                        function (res) {
                            if (res) {
                                blocker.start();
                                $scope.employmentHistoryInfo.from_date = angular.element('#from_date').val();
                                $scope.employmentHistoryInfo.to_date = angular.element('#to_date').val();

                                $timeout( function () {
                                    Alertify.success("Employment history successfully added!");

                                    $scope.employmentHistoryInfo.id              = (SubParamData.mType == 'new') ? SubParamData.rowCount + 1 : SubParamData.rowData.id;
                                    // $scope.employmentHistoryInfo.position_name   = $scope.employmentHistoryInfo.position;
                                    // $scope.employmentHistoryInfo.department_name = $scope.employmentHistoryInfo.department;
                                    $scope.employmentHistoryInfo.data_status = (SubParamData.mType == 'new') ? 'new' : 'saved';

                                    $uibModalInstance.close($scope.employmentHistoryInfo);

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
             * `_pluginFormat`
             * @return {[type]} [description]
             */
            _pluginFormat = function () {
                // angular.forEach($scope.withdrawFormInfo.pr_requests, function (prVal, prKey) {
                $timeout( function () {
                    angular.element('#from_date').inputmask('mm/dd/yyyy', {
                            placeholder: '__/__/____'
                    });

                    angular.element('#from_date').datepicker({
                            language : 'en'
                    });
                    angular.element('#to_date').inputmask('mm/dd/yyyy', {
                        placeholder: '__/__/____'
                    });

                    angular.element('#to_date').datepicker({
                            language : 'en'
                    });
                }, 200);
				// });
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.config                = SubParamData;
                $scope.config.btnLabel       = (SubParamData.mType == 'new') ? 'Save' : 'Update';
                $scope.employmentHistoryInfo = (SubParamData.mType == 'new') ? {}     : angular.copy(SubParamData.rowData);

                _loadDetails();
                _pluginFormat();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
