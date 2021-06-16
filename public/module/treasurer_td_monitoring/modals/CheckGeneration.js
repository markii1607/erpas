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
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/CheckGenerationService.php/getSelectionDetails');
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
                $scope.sampleOrNos = [
                    {
                        'or_no' : '4567859',
                        'show'  : false
                    },
                    {
                        'or_no' : '1111111',
                        'show'  : false
                    },
                    {
                        'or_no' : '2545654',
                        'show'  : false
                    },
                    {
                        'or_no' : '4567859',
                        'show'  : false
                    },
                    {
                        'or_no' : '4567859',
                        'show'  : false
                    },
                    {
                        'or_no' : '4567859',
                        'show'  : false
                    },
                ];
            }

            $scope.showDetails = function(index){
                var paramData, modalInstance;

                paramData = {
                    data : $scope.addTdPayment
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_or_details.html',
                    controller      : 'ViewOrDetailsController',
                    size            : 'md',
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
            $scope.save = function (isValid) {
               /*  if (isValid) {
                    blocker.start();
                    Service.checkTDNoDuplicate($scope.addTaxDec.td_no).then(tdChk => {
                        if (tdChk.data.hasDuplicate != undefined) {
                            if (!tdChk.data.hasDuplicate) {
                                Alertify.confirm("Are you sure you want to add this tax declaration?",
                                    function () {
                                        blocker.start();
                                        
                                        Service.save($scope.addTaxDec).then( function (res) {
                                            if (res.data.status) {
                                                Alertify.success("Tax Declaration successfully added!");
        
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
                                Alertify.alert("Tax Declaration No. <u><b><i>" + tdChk.data.td_no + "</i></b></u> is already existing on the database. Please provide new TD No. to proceed.");
                            }
                            
                            blocker.stop();
                        } else {
                            Alertify.error('An error occurred while validating entries. Please contact the administrator.');
                            blocker.stop();
                        }

                    });
                } else {
                    Alertify.error("All fields marked with * are required!");
                } */
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.addGeneratedCheck = {};
                
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
