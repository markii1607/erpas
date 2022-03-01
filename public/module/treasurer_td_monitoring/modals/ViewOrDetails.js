define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('ViewOrDetailsFactory', [
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

    app.service('ViewOrDetailsService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            _this.getDetails = function (id) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/AccountingCollectionConfig/CheckGenerationService.php/getOrTdList?ptd_id=' + id);
            }

        }
    ]);

    app.controller('ViewOrDetailsController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'paramData',
        'alertify',
        'ViewOrDetailsFactory',
        'ViewOrDetailsService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, ParamData, Alertify, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockViewOrDetails');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();
                Service.getDetails($scope.orDetails.id).then(res => {
                    if (res.data.td_list != undefined) {
                        $scope.orDetails.td_list = res.data.td_list;
                        blocker.stop();
                    } else {
                        Alertify.error('An error occurred while fetching data. Please contact the administrator.');
                        blocker.stop();
                    }
                })
            }

            $scope.viewTaxDec = function (data) {
                        
                var paramData, modalInstance;

                paramData = {
                    data           : data.td_details,
                    server_base_url: APP.SERVER_BASE_URL,
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_tax_declaration.html',
                    controller      : 'ViewTaxDeclarationController',
                    size            : 'xlg',
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
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.orDetails = ParamData.data;
                console.log($scope.orDetails);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
