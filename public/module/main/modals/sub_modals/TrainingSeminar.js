define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('trainingSeminarFactory', [
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

    app.service('trainingSeminarService', [
        '$http',
        function ($http) {
            var _this = this;
        }
    ]);

    app.controller('TrainingSeminarController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'subParamData',
        'blockUI',
        'alertify',
        'trainingSeminarFactory',
        'trainingSeminarService',
        function ($scope, $uibModalInstance, $timeout, $filter, SubParamData, BlockUI, Alertify, Factory, Service) {
            var _init, _pluginFormat, _loadDetails, blocker = BlockUI.instances.get('blockTrainingSeminar');


            /**
             * `_loadDetails` Load first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                // console.log($scope.licenseCertificateInfo);
                blocker.start();

                $timeout(function(){
                    angular.element('#from_date').val($scope.trainingSeminarInfo.from_date);
                    angular.element('#to_date').val($scope.trainingSeminarInfo.to_date);

                    blocker.stop();

                }, 100)
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
                    Alertify.confirm("Are you sure you want to add this new training / seminar?",
                        function (res) {
                            if (res) {
                                blocker.start();
                                $scope.trainingSeminarInfo.from_date = angular.element('#from_date').val();
                                $scope.trainingSeminarInfo.to_date = angular.element('#to_date').val();

                                $timeout( function () {
                                    Alertify.success("Training / seminar successfully added!");

                                    $scope.trainingSeminarInfo.id = (SubParamData.mType == 'new') ? SubParamData.rowCount + 1 : SubParamData.rowData.id;

                                    $scope.trainingSeminarInfo.data_status = (SubParamData.mType == 'new') ? 'new' : 'saved';

                                    $uibModalInstance.close($scope.trainingSeminarInfo);

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

                $scope.config              = SubParamData;
                $scope.config.btnLabel     = (SubParamData.mType == 'new') ? 'Save' : 'Update';
                $scope.trainingSeminarInfo = (SubParamData.mType == 'new') ? {}     : angular.copy(SubParamData.rowData);

                _pluginFormat();
                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
