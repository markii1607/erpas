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
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/getAdvSearchSelectionDetails');
            }

            _this.formatEntries = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/setDataSearchFormat', data);
            }

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
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAdvancedSearch');

            /**
             * `_loadDetails` Load first Needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

            	Service.getDetails().then( function (res) {
            		if (res.data.revision_years != undefined) {
                        $scope.revision_nos     = res.data.revision_years;
                        $scope.classifications  = res.data.classifications;
                        $scope.barangays        = res.data.barangays;

                        blocker.stop();
                    } else {
                        Alertify.error('Failed to fetch data for selection. Please notify the administrator.');
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

            $scope.saveSearch = function () {
                blocker.start();
                Service.formatEntries($scope.search).then(res => {
                    if (res.data.rev_id != undefined) {
                        $uibModalInstance.close(res.data);
                        blocker.stop();
                    } else {
                        Alertify.error('An error occurred while filtering data. Please contact the administrator.');
                        blocker.stop();
                    }
                });
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
