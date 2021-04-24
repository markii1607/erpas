define([
    'app',
    'airDatepickeri18n',
], function (app) {
    app.factory('AdvanceSearchFactory', [
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

            Factory.classifications = [
                {
                    id: 1,
                    name: 'Residential',
                },
                {
                    id: 2,
                    name: 'Commercial',
                },
                {
                    id: 3,
                    name: 'Industrial',
                },
                {
                    id: 4,
                    name: 'Improvement',
                },
            ];

            return Factory;
        }
    ]);

    app.service('AdvanceSearchService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @param  {[type]} id
             * @return {[type]}
             */
            // _this.getDetails = function (id) {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/getDetails?id=' + id);
            // }

            /**
             * `archive` Query string that will archive information.
             * @param  {[string]} id
             * @return {[route]}
             */
            // _this.archive = function (id) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/ViewAccessService.php/archiveAccess', {'id' : id});
            // }
        }
    ]);

    app.controller('AdvanceSearchController', [
        '$scope',
        '$uibModal',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'AdvanceSearchFactory',
        'AdvanceSearchService',
        function ($scope, $uibModal, $uibModalInstance, $timeout, $filter, BlockUI, Alertify, ParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAddTaxDec');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                // blocker.start();

            	// Service.getDetails(ParamData.id).then( function (res) {
            	// 	$scope.accessList = angular.copy(res.data.access);

                //     $scope.jqDataTableOptions         = Factory.dtOptions();
                //     $scope.jqDataTableOptions.buttons = _btnFunc();
                //     $scope.jqDataTableOptions.data    = _formatAccess(res.data.access);
            	// });
                $scope.classifications = Factory.classifications
            }

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            $scope.search = function () {
                // blocker.start();

                // $timeout( function () {
                //     Service.save($scope.addBrgy).then( function (res) {
                //         if (res.data.status == true) {
                //             Alertify.success("Classification successfully added!");

                //             $uibModalInstance.close($scope.addBrgy);
                //             blocker.stop();
                //         } else {
                //             Alertify.error("Classification already exist!");
                //             blocker.stop();
                //         }
                //     });
                // }, 1000);
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                
                $scope.search = {};

                $timeout(function() {
                    angular.element('#date_range').datepicker({
                        language: 'en',
                        autoClose: true,
                        position: 'top center',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.search.date_range = angular.copy(formattedDate);
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
