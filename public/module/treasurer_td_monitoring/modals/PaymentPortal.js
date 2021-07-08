define([
    'app',
    'moment',
    'airDatepickeri18n',
], function (app, moment) {
    app.factory('PaymentPortalFactory', [
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

    app.service('PaymentPortalService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.save = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TreasurerTdMonitoring/AddTDPaymentService.php/savePaymentDetails', data);
            }

        }
    ]);

    app.controller('PaymentPortalController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'paramData',
        'alertify',
        'PaymentPortalFactory',
        'PaymentPortalService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, ParamData, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockPaymentPortal');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                $scope.addPaymentDetail.transaction_date = moment().format('LL');
                console.log($scope.addPaymentDetail.transaction_date);
            }

            $scope.addRow = function() {
                $scope.addPaymentDetail.records.push({
                    declarant: null,
                    location: null,
                    lot_no: null,
                    td_no: null,
                    land: null,
                    improvement: null,
                    total: null,
                    tax_due: null,
                })
            }

            $scope.removeRow = function(index) {
                $scope.addPaymentDetail.records.splice(index, 1)
            }

            $scope.addRowInstallment = function(index) {
                $scope.td_records[index].payments.push(
                    {
                        full_payment    : $scope.td_records[index].tax_due,
                        penalty_amount  : 0,
                        total_per_row   : $scope.td_records[index].tax_due,
                    }
                )

                // $scope.computeTotal();
            }

            $scope.removeRowInstallment = function(index, subIndex) {
                $scope.td_records[index].payments.splice(subIndex, 1)
                // $scope.computeTotal();
            }

            $scope.computeTotalPerRow = function(index, subIndex){
                $scope.td_records[index].payments[subIndex].total_per_row = Number($scope.td_records[index].payments[subIndex].full_payment) + Number($scope.td_records[index].payments[subIndex].penalty_amount);
                $scope.td_records[index].payments[subIndex].total_per_row = Math.round(($scope.td_records[index].payments[subIndex].total_per_row + Number.EPSILON) * 100) / 100;
                // $scope.computeTotal();
            }

            $scope.computeTotal = function(condition = ''){
                var total = 0;
                angular.forEach($scope.td_records, (value, key) => {
                    angular.forEach(value.payments, (pvalue, pkey) => {
                        total += Number(pvalue.total_per_row)
                    })
                })
                
                $scope.addPaymentDetail.total_basic = total
                $scope.addPaymentDetail.total_sef   = total
                $scope.addPaymentDetail.grand_total = total * 2

                return (condition == 'total') ? total*2 : total
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
                
                Alertify.confirm("Saving the following data will marked the TD Number/s as PAID. Continue?",
                    function () {
                        blocker.start();
                        
                        _formatSubmittedData();
                        Service.save($scope.addPaymentDetail).then( function (res) {
                            if (res.data.status) {
                                Alertify.success("You have successfully saved the payment details of the seleted TD Number/s!");

                                $uibModalInstance.close(res.data.rowData);
                                blocker.stop();
                            } else {
                                Alertify.error("An error occurred while saving! Please contact the administrator.");
                                blocker.stop();
                            }
                        });
                    }
                );
            };

            _formatSubmittedData = function(data) {
                angular.forEach($scope.td_records, (value, key) => {
                    $scope.addPaymentDetail.records.push(
                        {
                            id          : value.id,
                            tax_due     : value.tax_due,
                            payments    : value.payments
                        }
                    );
                })
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.td_records = ParamData.data.records
                angular.forEach($scope.td_records, function(value, key) {
                    $scope.td_records[key].tax_due  = Number(value.total_assessed_value) * 0.01;
                    $scope.td_records[key].tax_due  = Math.round(($scope.td_records[key].tax_due + Number.EPSILON) * 100) / 100;
                    $scope.td_records[key].payments = [
                        {
                            full_payment    : $scope.td_records[key].tax_due,
                            penalty_amount  : 0,
                            total_per_row   : $scope.td_records[key].tax_due,
                        }
                    ];
                })
                console.log($scope.td_records);
                $scope.addPaymentDetail = {
                    total_basic : 0,
                    total_sef   : 0,
                    grand_total : 0,
                    records     : []
                };

                // $scope.computeTotal();

                $timeout(function() {
                    angular.element('#transaction_date').datepicker({
                        language: 'en',
                        autoClose: true,
                        dateFormat: 'MM d, yyyy',
                        position: 'bottom left',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.addPaymentDetail.transaction_date = angular.copy(formattedDate);
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
