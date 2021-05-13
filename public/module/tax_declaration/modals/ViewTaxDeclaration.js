define([
    'app',
    'moment',
    'airDatepickeri18n',
], function(app, moment, airDatepickeri18n) {
    app.factory('ViewTaxDeclarationFactory', [
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

    app.service('ViewTaxDeclarationService', [
        '$http',
        function($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            _this.getDetails = function(parentId) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/TaxDeclaration/TaxDeclarationService.php/getTDViewDetails?id=' + parentId);
            };
        }
    ]);

    app.controller('ViewTaxDeclarationController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$location',
        '$filter',
        'blockUI',
        'alertify',
        'paramData',
        'ViewTaxDeclarationFactory',
        'ViewTaxDeclarationService',
        function($scope, $uibModalInstance, $timeout, $location, $filter, BlockUI, Alertify, paramData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockViewTaxDec');

            /**
             * `_loadDetails` Loading of first needed data from database.
             * @return {[type]} [description]
             */
            _loadDetails = function() {
                blocker.start();
                Service.getDetails(paramData.data.id).then(res => {
                    if (res.data.details != undefined) {
                        $scope.data.details = res.data.details;
                        $scope.data.ordinance_date = moment().format('LL');
                        blocker.stop();
                    } else {
                        Alertify.error("An error occurred while fetching data. Please contact the administrator.");
                        blocker.stop();
                    }
                });
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
                console.log(paramData);

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }
    ]);
}); // end define