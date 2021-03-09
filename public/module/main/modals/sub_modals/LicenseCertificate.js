define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('licenseCertificateFactory', [
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

    app.service('licenseCertificateService', [
        '$http',
        function ($http) {
            var _this = this;

        }
    ]);

    app.controller('LicenseCertificateController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'subParamData',
        'blockUI',
        'alertify',
        'licenseCertificateFactory',
        'licenseCertificateService',
        function ($scope, $uibModalInstance, $timeout, $filter, SubParamData, BlockUI, Alertify, Factory, Service) {
            var _init, _pluginFormat, _loadDetails, blocker = BlockUI.instances.get('blockLicenseCertificate');


            /**
             * `_loadDetails` Load first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                // console.log($scope.licenseCertificateInfo);
                blocker.start();

                $timeout(function(){
                    angular.element('#date_taken').val($scope.licenseCertificateInfo.date_taken);

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
                    Alertify.confirm("Are you sure you want to add this new license / certificate?",
                        function (res) {
                            if (res) {
                                blocker.start();
                                $scope.licenseCertificateInfo.date_taken = angular.element('#date_taken').val();

                                $timeout( function () {
                                    Alertify.success("New license / certificate successfully added!");

                                    $scope.licenseCertificateInfo.id = (SubParamData.mType == 'new') ? SubParamData.rowCount + 1 : SubParamData.rowData.id;

                                    $scope.licenseCertificateInfo.data_status = (SubParamData.mType == 'new') ? 'new' : 'saved';

                                    $uibModalInstance.close($scope.licenseCertificateInfo);

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
                    angular.element('#date_taken').inputmask('mm/dd/yyyy', {
                            placeholder: '__/__/____'
                    });

                    angular.element('#date_taken').datepicker({
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

                $scope.config                 = SubParamData;
                $scope.config.btnLabel        = (SubParamData.mType == 'new') ? 'Save' : 'Update';
                $scope.licenseCertificateInfo = (SubParamData.mType == 'new') ? {}     : SubParamData.rowData;

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
