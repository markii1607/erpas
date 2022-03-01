define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('CheckGenerationFactory', [
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

    app.service('CheckGenerationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getRecords = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/AccountingCollectionConfig/CheckGenerationService.php/getRecords', {date_range : data});
            }
            
            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/AccountingCollectionConfig/CheckGenerationService.php/saveCheckDetails', data);
            }
        }
    ]);

    app.controller('CheckGenerationController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'CheckGenerationFactory',
        'CheckGenerationService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockCheckGeneration');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
            }

            $scope.getRecords = function(){
                blocker.start();
                Service.getRecords($scope.addGeneratedCheck.date_range).then(res => {
                    if (res.data.records != undefined) {
                        $scope.or_records = res.data.records;
                        if($scope.or_records.length == 0) Alertify.alert('<b>NO RECORDS FOUND!<br>Please try again by adjusting the Date Range.</b>');
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                })
            }

            $scope.computeAmount = function(data) {
                console.log(data);
                if (data.selected) {
                    $scope.addGeneratedCheck.total_amount += parseFloat(data.amount_paid);
                } else {
                    if($scope.addGeneratedCheck.total_amount > 0) $scope.addGeneratedCheck.total_amount -= parseFloat(data.amount_paid);
                }
            }

            $scope.showDetails = function(data){
                var paramData, modalInstance;

                paramData = {
                    data
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_or_details.html',
                    controller      : 'ViewOrDetailsController',
                    size            : 'xxxlg',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                }, function (res) {
                    // Result when modal is dismissed
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
                var tempRecords = [];
                angular.forEach($scope.or_records, (value, key) => {
                    if (value.selected != undefined) {
                        if (value.selected) tempRecords.push({id : value.id});
                    }
                })

                if (tempRecords.length > 0) {
                    Alertify.confirm("Please confirm saving of data.", function(){
                        blocker.start();
                        $scope.addGeneratedCheck.records = tempRecords;
                        Service.save($scope.addGeneratedCheck).then(res => {
                            if (res.data.status) {
                                Alertify.success("Successfully saved data!");
                                blocker.stop();

                                $uibModalInstance.close(res.data.rowData);
                            } else {
                                Alertify.error('An error occurred while saving. Please contact the administrator.');
                                blocker.stop();
                            }
                        })
                    })
                } else {
                    Alertify.log('Please select at least one(1) O.R. number.');
                }
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.addGeneratedCheck = {
                    total_amount : 0
                };
                
                $timeout(function() {
                    angular.element('#date_range').datepicker({
                        language: 'en',
                        autoClose: true,
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.addGeneratedCheck.date_range = angular.copy(formattedDate);
                        }
                    });
                }, 500);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
