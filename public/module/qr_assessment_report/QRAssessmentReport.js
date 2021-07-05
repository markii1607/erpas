define([
    'app',
    'moment', 
    'airDatepickeri18n'
], function (app, moment) {
    app.factory('QRAssessmentReportFactory', [
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

            // Factory.dtOptions = function () {
            //     var options = {};

            //     options = {
            //         "dom": 'Bfrtip',
            //         "paging": true,
            //         "lengthChange": true,
            //         "pageLength": 15,
            //         "searching": true,
            //         "ordering": true,
            //         "info": true,
            //         "select": {
            //             style: 'single'
            //         },
            //         "keys": {
            //             keys: [
            //                 13 /* ENTER */ ,
            //                 38 /* UP */ ,
            //                 40 /* DOWN */
            //             ]
            //         },
            //         "mark": true,
            //         "autoWidth": false,
            //         "responsive": true,
            //         "data": [],
            //         "buttons": [],
            //         "order": [
            //             [
            //                 0,
            //                 "desc"
            //             ]
            //         ],
            //         "columnDefs"   : [ 
            //             {
            //                 "targets"    : 0,
            //                 "searchable" : false,
            //                 "orderable"  : true,
            //                 "className"  : "text-center"
            //             },
            //             {
            //                 "targets"   : 1,
            //                 "searchable": false,
            //                 "orderable" : false,
            //                 "className" : "text-center",
            //                 "render"    : function(data, type, full, meta) {
            //                     var str = '';
            //                     str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="View" class="btn btn-default bg-success btn-md mr-2 text-white"><i class="fas fa-eye"></i></button>';
            //                     str += '<p style="margin-bottom:5px;"></p>';
            //                     str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Edit" class="btn btn-default bg-warning btn-md mr-2 text-white"><i class="fas fa-edit"></i></button>';
            //                     str += '<p style="margin-bottom:5px;"></p>';
            //                     str += '<button type="submit" id="thirdButton" data-toggle="tooltip" title="Delete" class="btn btn-default bg-danger btn-md mr-2 text-white"><i class="fas fa-trash"></i></button>';

            //                     return str;
            //                 }
            //             },
            //             {
            //                 "targets"   : 2,
            //                 "searchable": true,
            //                 "orderable" : true,
            //                 "className" : "text-left",
            //                 "render"    : function(data, type, full, meta) {
            //                     return `${data.month} ${data.day}, ${data.year}`
            //                 }
            //             },
            //         ],
            //         "columns"      : 
            //         [
            //             { 
            //                 "data" : null 
            //             },
            //             { 
            //                 "data" : null 
            //             },
            //             { 
            //                 "data" : "date"
            //             },
            //             { 
            //                 "data" : "or_no"
            //             },
            //             { 
            //                 "data" : "requestor"
            //             },
            //             { 
            //                 "data" : "purpose"
            //             },
            //         ]
            //     };

            //     return options;
            // };

            return Factory;
        }
    ]);

    app.service('QRAssessmentReportService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */
            _this.getDetails = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/QrAssessmentReport/QrAssessmentReportService.php/getDetails', data);
            };
        }
    ]);

    app.controller('QRAssessmentReportController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'QRAssessmentReportFactory',
        'QRAssessmentReportService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockQRAssessmentReport'), table = angular.element('#qr_assessment');

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
                    '<style type="text/css" media="print">body { -webkit-print-color-adjust: exact; } @page { size: A3 landscape; } #big-table { display: block !important; overflow-x: visible !important; width: 100% !important; height: auto !important; }</style>' +
                    '</head><body onload="window.print()"><div class="container-fluid">' + innerContents +
                    '</div></body></html>');
                // '</div><script>$.("div.table-responsive").removeClass("table-responsive");</script></body></html>');
                popupWinindow.document.close();
            };

            $scope.search = function(){
                if ($scope.filter.date_range != null) {
                    
                    blocker.start();
                    Service.getDetails($scope.filter).then(res => {
                        if (!res.data.input_error) {
                            if (res.data.records != undefined) {
                                $scope.records      = res.data.records;
                                $scope.mun_assessor = res.data.mun_assessor;
                                $scope.computeOverallTotal();
                                console.log($scope.records);
                                blocker.stop();
                            } else {
                                Alertify.error('An error occurred while saving. Please contact the administrator.');
                                blocker.stop();
                            }
                        } else {
                            Alertify.log('Invalid date range! Please make sure that FROM and TO dates are properly defined.');
                            blocker.stop();
                        }
                    })
                } else {
                    Alertify.log('Please select a specific date range to proceed.');
                }
            }

            $scope.getTotal = function(data, value_type, classification = ''){
                
                if (data != undefined) {
                    var total = 0;
                    if (value_type == 'mv') {
                        if (classification == 'residential') {
                            total = parseFloat(data.land) + parseFloat(data.building.below_limit) + parseFloat(data.building.above_limit) + parseFloat(data.machinery) + parseFloat(data.others);
                        } else {
                            total = parseFloat(data.land) + parseFloat(data.building.building) + parseFloat(data.machinery) + parseFloat(data.others);
                        }
                    } else {
                        total = parseFloat(data.land) + parseFloat(data.building) + parseFloat(data.machinery) + parseFloat(data.others);
                    }

                    return total;
                } else {
                    return '';
                }

            }

            $scope.computeCollectibles = function(data, type){
                if (data != undefined) {
                    var total_av            = parseFloat(data.land) + parseFloat(data.building) + parseFloat(data.machinery) + parseFloat(data.others);
                    var basic_tax           = total_av * 0.01;
                    var sef_tax             = total_av * 0.01;
                    var total_collectibles  = basic_tax + sef_tax;
    
                    if (type == 'basic') return basic_tax
                    else if (type == 'sef') return sef_tax
                    else if (type == 'total') return total_collectibles
                    else return '0.00'
                } else {
                    return '';
                }
            }

            $scope.computeOverallTotal = function(){
                $scope.records.taxable.total_land_area      = 0;
                $scope.records.taxable.total_rpu_land       = 0;
                $scope.records.taxable.total_rpu_building   = 0;
                $scope.records.taxable.total_rpu_machinery  = 0;
                $scope.records.taxable.total_rpu_others     = 0;
                $scope.records.taxable.overall_total_rpu    = 0;
                $scope.records.taxable.total_mv_land        = 0;
                $scope.records.taxable.total_mv_bldg_below  = 0;
                $scope.records.taxable.total_mv_bldg_above  = 0;
                $scope.records.taxable.total_mv_building    = 0;
                $scope.records.taxable.total_mv_machinery   = 0;
                $scope.records.taxable.total_mv_others      = 0;
                $scope.records.taxable.overall_total_mv     = 0;
                $scope.records.taxable.total_av_land        = 0;
                $scope.records.taxable.total_av_building    = 0;
                $scope.records.taxable.total_av_machinery   = 0;
                $scope.records.taxable.total_av_others      = 0;
                $scope.records.taxable.overall_total_av     = 0;
                $scope.records.taxable.total_basic_tax      = 0;
                $scope.records.taxable.total_sef_tax        = 0;
                $scope.records.taxable.overall_total_tax    = 0;

                let taxable_fields = [
                    'residential',
                    'agricultural',
                    'cultural',
                    'industrial',
                    'mineral',
                    'timber',
                    'special',
                    'sp_machineries',
                    'sp_cultural',
                    'sp_scientific',
                    'sp_hospital',
                    'sp_lwua',
                    'sp_gocc',
                    'sp_recreation',
                    'sp_others',
                ]

                taxable_fields.map(key => {
                    $scope.records.taxable.total_land_area      += parseFloat($scope.records.taxable[key].total_land_area_sqm);

                    $scope.records.taxable.total_rpu_land       += parseFloat($scope.records.taxable[key].no_rpu.land);
                    $scope.records.taxable.total_rpu_building   += parseFloat($scope.records.taxable[key].no_rpu.building);
                    $scope.records.taxable.total_rpu_machinery  += parseFloat($scope.records.taxable[key].no_rpu.machinery);
                    $scope.records.taxable.total_rpu_others     += parseFloat($scope.records.taxable[key].no_rpu.others);

                    $scope.records.taxable.total_mv_land        += parseFloat($scope.records.taxable[key].market_value.land);
                    $scope.records.taxable.total_mv_bldg_below  += parseFloat($scope.records.taxable[key].market_value.building.below_limit);
                    $scope.records.taxable.total_mv_bldg_above  += parseFloat($scope.records.taxable[key].market_value.building.above_limit);
                    $scope.records.taxable.total_mv_building    += parseFloat($scope.records.taxable[key].market_value.building.building);
                    $scope.records.taxable.total_mv_machinery   += parseFloat($scope.records.taxable[key].market_value.machinery);
                    $scope.records.taxable.total_mv_others      += parseFloat($scope.records.taxable[key].market_value.others);

                    $scope.records.taxable.total_av_land        += parseFloat($scope.records.taxable[key].assessed_value.land);
                    $scope.records.taxable.total_av_building    += parseFloat($scope.records.taxable[key].assessed_value.building);
                    $scope.records.taxable.total_av_machinery   += parseFloat($scope.records.taxable[key].assessed_value.machinery);
                    $scope.records.taxable.total_av_others      += parseFloat($scope.records.taxable[key].assessed_value.others);

                    $scope.records.taxable.total_basic_tax      += parseFloat($scope.records.taxable[key].basic_tax);
                    $scope.records.taxable.total_sef_tax        += parseFloat($scope.records.taxable[key].sef_tax);
                })
                $scope.records.taxable.overall_total_rpu = parseFloat($scope.records.taxable.total_rpu_land) + parseFloat($scope.records.taxable.total_rpu_building) + parseFloat($scope.records.taxable.total_rpu_machinery) + parseFloat($scope.records.taxable.total_rpu_others);
                $scope.records.taxable.overall_total_mv  = parseFloat($scope.records.taxable.total_mv_land) + parseFloat($scope.records.taxable.total_mv_bldg_below) + parseFloat($scope.records.taxable.total_mv_bldg_above) + parseFloat($scope.records.taxable.total_mv_building) + parseFloat($scope.records.taxable.total_mv_machinery) + parseFloat($scope.records.taxable.total_mv_others);
                $scope.records.taxable.overall_total_av  = parseFloat($scope.records.taxable.total_av_land) + parseFloat($scope.records.taxable.total_av_building) + parseFloat($scope.records.taxable.total_av_machinery) + parseFloat($scope.records.taxable.total_av_others);
                $scope.records.taxable.overall_total_tax = parseFloat($scope.records.taxable.total_basic_tax) + parseFloat($scope.records.taxable.total_sef_tax);

                $scope.records.exempt.total_land_area       = 0;
                $scope.records.exempt.total_rpu_land        = 0;
                $scope.records.exempt.total_rpu_building    = 0;
                $scope.records.exempt.total_rpu_machinery   = 0;
                $scope.records.exempt.total_rpu_others      = 0;
                $scope.records.exempt.overall_total_rpu     = 0;
                $scope.records.exempt.total_mv_land         = 0;
                $scope.records.exempt.total_mv_building     = 0;
                $scope.records.exempt.total_mv_machinery    = 0;
                $scope.records.exempt.total_mv_others       = 0;
                $scope.records.exempt.overall_total_mv      = 0;
                $scope.records.exempt.total_av_land         = 0;
                $scope.records.exempt.total_av_building     = 0;
                $scope.records.exempt.total_av_machinery    = 0;
                $scope.records.exempt.total_av_others       = 0;
                $scope.records.exempt.overall_total_av      = 0;

                let exempt_fields = [
                    'government',
                    'religious',
                    'charitable',
                    'educational',
                    'machineries_lwd',
                    'machineries_gocc',
                    'pcep',
                    'reg_coop',
                    'others'
                ]

                exempt_fields.map(key => {
                    $scope.records.exempt.total_land_area      += parseFloat($scope.records.exempt[key].total_land_area_sqm);

                    $scope.records.exempt.total_rpu_land       += parseFloat($scope.records.exempt[key].no_rpu.land);
                    $scope.records.exempt.total_rpu_building   += parseFloat($scope.records.exempt[key].no_rpu.building);
                    $scope.records.exempt.total_rpu_machinery  += parseFloat($scope.records.exempt[key].no_rpu.machinery);
                    $scope.records.exempt.total_rpu_others     += parseFloat($scope.records.exempt[key].no_rpu.others);
                    
                    $scope.records.exempt.total_mv_land        += parseFloat($scope.records.exempt[key].market_value.land);
                    $scope.records.exempt.total_mv_building    += parseFloat($scope.records.exempt[key].market_value.building.building);
                    $scope.records.exempt.total_mv_machinery   += parseFloat($scope.records.exempt[key].market_value.machinery);
                    $scope.records.exempt.total_mv_others      += parseFloat($scope.records.exempt[key].market_value.others);
                    
                    $scope.records.exempt.total_av_land        += parseFloat($scope.records.exempt[key].assessed_value.land);
                    $scope.records.exempt.total_av_building    += parseFloat($scope.records.exempt[key].assessed_value.building);
                    $scope.records.exempt.total_av_machinery   += parseFloat($scope.records.exempt[key].assessed_value.machinery);
                    $scope.records.exempt.total_av_others      += parseFloat($scope.records.exempt[key].assessed_value.others);
                })
                $scope.records.exempt.overall_total_rpu = parseFloat($scope.records.exempt.total_rpu_land) + parseFloat($scope.records.exempt.total_rpu_building) + parseFloat($scope.records.exempt.total_rpu_machinery) + parseFloat($scope.records.exempt.total_rpu_others);
                $scope.records.exempt.overall_total_mv  = parseFloat($scope.records.exempt.total_mv_land) + parseFloat($scope.records.exempt.total_mv_building) + parseFloat($scope.records.exempt.total_mv_machinery) + parseFloat($scope.records.exempt.total_mv_others);
                $scope.records.exempt.overall_total_av  = parseFloat($scope.records.exempt.total_av_land) + parseFloat($scope.records.exempt.total_av_building) + parseFloat($scope.records.exempt.total_av_machinery) + parseFloat($scope.records.exempt.total_av_others);
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Quarterly Report on Real Property Assessment"
                $scope.header.link.sub = "Tabular Reports"
                $scope.header.link.main = "Quarterly Report on Real Property Assessment"
                $scope.header.showButton = false

                $scope.filter = {};
                $timeout(function() {
                    angular.element('#date_range').datepicker({
                        language: 'en',
                        autoClose: true,
                        dateFormat: 'MM dd, yyyy',
                        maxDate: new Date(), 
                        onSelect: function(formattedDate, date, inst) {
                            $scope.filter.date_range = angular.copy(formattedDate);
                        }
                    });
                }, 500);

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