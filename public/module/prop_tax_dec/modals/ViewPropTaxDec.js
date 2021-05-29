define([
    'app',
    'moment',
    'airDatepickeri18n',
], function(app, moment, airDatepickeri18n) {
    app.factory('ViewPropTaxDecFactory', [
        'alertify',
        function(alertify) {
            var Factory = {};

            /**
             * `autoloadSettings` autoload params
             * @return {[type]}
             */
            Factory.autoloadSettings = function() {
                // alertify
                alertify.logPosition('bottom right');
                alertify.theme('');
            };

            return Factory;
        }
    ]);

    app.service('ViewPropTaxDecService', [
        '$http',
        function($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            // _this.getDetails = function(parentId) {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/Dashboard/AddIIRService.php/getDetails?parent_id=' + parentId);
            // };
        }
    ]);

    app.controller('ViewPropTaxDecController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$location',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'ViewPropTaxDecFactory',
        'ViewPropTaxDecService',
        function($scope, $uibModalInstance, $timeout, $location, $filter, BlockUI, Alertify, paramData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockViewPropTaxDec');

            /**
             * `_loadDetails` Loading of first needed data from database.
             * @return {[type]} [description]
             */
            _loadDetails = function() {
                // if ($scope.data.loc.hasOwnProperty('no_street')) {
                //     $scope.data.loc.full = `${$scope.data.loc.no_street}\t\t\t\t${$scope.data.loc.brgy.name}\t\t\t\tMalilipot, Albay`
                // } else {
                //     $scope.data.loc.full = `\t\t\t\t\t\t\t\t${$scope.data.loc.brgy.name}\t\t\t\tMalilipot, Albay`
                // }

                if ($scope.data.date.day % 10 == 1) $scope.data.date.day_ord = 'st'
                else if ($scope.data.date.day % 10 == 2) $scope.data.date.day_ord = 'nd'
                else if ($scope.data.date.day % 10 == 3) $scope.data.date.day_ord = 'rd'
                else $scope.data.date.day_ord = 'th'
            };

            $scope.print = function() {
                var innerContents = document.getElementById('print-identifier').innerHTML;
                var popupWinindow = window.open('', '_blank', 'width=800,height=900,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
                popupWinindow.document.open();
                popupWinindow.document.write('<html><head>' +
                    //       ---------------------     HEADER HERE    ---------------------------------
                    '<link href="../node_modules/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">' +
                    '<link href="../public/css/index.css" rel="stylesheet">' +
                    //       ---------------------     PAGE STYLE HERE    ---------------------------------
                    '<style>.table-responsive { min-height: unset !important; overflow-x: unset !important; }</style>' +
                    '</head><body onload="window.print()"><div class="container-fluid">' + innerContents +
                    '</div></body></html>');
                // '</div><script>$.("div.table-responsive").removeClass("table-responsive");</script></body></html>');
                popupWinindow.document.close();
            };

            /**
             * `closeSubMenu` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function() {
                $uibModalInstance.dismiss();
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function() {
                // default settings
                Factory.autoloadSettings();

                $scope.data = paramData.data
                $scope.server_base_url = paramData.server_base_url;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }
    ]);
}); // end define