define([
    'app',
    'moment', 
], function (app, moment) {
    app.factory('RptCollectionReportFactory', [
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

            /**
             * `templates` Modal templates.
             * @type {Array}
             */
            Factory.templates = [
                // 'module/properties_dec/modals/add_properties_dec.html',
                // 'module/properties_dec/modals/edit_properties_dec.html',
                // 'module/properties_dec/modals/view_properties_dec.html',
            ];

            return Factory;
        }
    ]);

    app.service('RptCollectionReportService', [
        '$http',
        function ($http) {
            var _this = this;

            // /**
            //  * `getDetails` Query string that will get first needed details.
            //  * @return {[route]}
            //  */
            // _this.getDetails = function () {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/RptCollectionReport/RptCollectionReportService.php/getDetails');
            // };

            // // _this.retire = function (data) {
            // //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/RptCollectionReport/RptCollectionReportService.php/retireRptCollectionReport', data);
            // // };

            // _this.archive = function (data) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/RptCollectionReport/RptCollectionReportService.php/archiveRptCollectionReport', data);
            // };
        }
    ]);

    app.controller('RptCollectionReportController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'RptCollectionReportFactory',
        'RptCollectionReportService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockRptCollectionReport'), table = angular.element('#properties_dec');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {

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
                    '<style type="text/css" media="print">@page { size: landscape; margin: 5mm 5mm 5mm 5mm; }</style>' +
                    '</head><body onload="window.print()"><div class="container-fluid">' + innerContents +
                    '</div></body></html>');
                // '</div><script>$.("div.table-responsive").removeClass("table-responsive");</script></body></html>');
                popupWinindow.document.close();
            };

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Statement of Real Property Tax Declaration"
                $scope.header.link.sub = "Tabular Reports"
                $scope.header.link.main = "Statement of Real Property Tax Declaration"
                $scope.header.showButton = false

                $scope.templates = Factory.templates;

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define