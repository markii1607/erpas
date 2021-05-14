define([
    'app',
    'moment', 
], function (app, moment) {
    app.factory('NoPropertyDecFactory', [
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
                'module/no_property_dec/modals/add_no_property_dec.html',
                'module/no_property_dec/modals/edit_no_property_dec.html',
                'module/no_property_dec/modals/view_no_property_dec.html',
            ];

            Factory.dtOptions = function () {
                var options = {};

                options = {
                    "dom": 'Bfrtip',
                    "paging": true,
                    "lengthChange": true,
                    "pageLength": 15,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "select": {
                        style: 'single'
                    },
                    "keys": {
                        keys: [
                            13 /* ENTER */ ,
                            38 /* UP */ ,
                            40 /* DOWN */
                        ]
                    },
                    "mark": true,
                    "autoWidth": false,
                    "responsive": true,
                    "data": [],
                    "buttons": [],
                    "order": [
                        [
                            0,
                            "desc"
                        ]
                    ],
                    "columnDefs"   : [ 
                        {
                            "targets"    : 0,
                            "searchable" : false,
                            "orderable"  : true,
                            "className"  : "text-center"
                        },
                        {
                            "targets"   : 1,
                            "searchable": false,
                            "orderable" : false,
                            "className" : "text-center",
                            "render"    : function(data, type, full, meta) {
                                var str = '';
                                str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="View" class="btn btn-default bg-success btn-md mr-2 text-white"><i class="fas fa-eye"></i></button>';
                                str += '<p style="margin-bottom:5px;"></p>';
                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Edit" class="btn btn-default bg-warning btn-md mr-2 text-white"><i class="fas fa-edit"></i></button>';
                                str += '<p style="margin-bottom:5px;"></p>';
                                str += '<button type="submit" id="thirdButton" data-toggle="tooltip" title="Delete" class="btn btn-default bg-danger btn-md mr-2 text-white"><i class="fas fa-trash"></i></button>';

                                return str;
                            }
                        },
                        {
                            "targets"   : 2,
                            "searchable": true,
                            "orderable" : true,
                            "className" : "text-left",
                            "render"    : function(data, type, full, meta) {
                                return `${data.month} ${data.day}, ${data.year}`
                            }
                        },
                    ],
                    "columns"      : 
                    [
                        { 
                            "data" : null 
                        },
                        { 
                            "data" : null 
                        },
                        { 
                            "data" : "date"
                        },
                        { 
                            "data" : "or_no"
                        },
                        { 
                            "data" : "requestor"
                        },
                        { 
                            "data" : "declarees"
                        },
                        { 
                            "data" : "purpose"
                        },
                    ]
                };

                return options;
            };

            Factory.dummyData = [
                {
                    date: {
                        day: 10,
                        month: 'September',
                        year: 2020
                    },
                    or_no: '7020342',
                    requestor: 'Mark Philip C. Bernardo',
                    declarees: 'SPS. DR. CLEODOSIL R. COPE & RENEE ESTANISLAO',
                    purpose: 'BIR'
                },
                {
                    date: {
                        day: 16,
                        month: 'November',
                        year: 2020
                    },
                    or_no: '5186868',
                    requestor: 'Helen De Lima',
                    declarees: 'JOSEPAT N. CLIMACOSA',
                    purpose: 'DAR Clearance'
                }
            ]

            return Factory;
        }
    ]);

    app.service('NoPropertyDecService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */
            // _this.getDetails = function () {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/NoPropertyDec/NoPropertyDecService.php/getTDCount');
            // };

            // _this.retire = function (data) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/NoPropertyDec/NoPropertyDecService.php/retireNoPropertyDec', data);
            // };

            // _this.archive = function (data) {
            //     return $http.post(APP.SERVER_BASE_URL + '/App/Service/NoPropertyDec/NoPropertyDecService.php/archiveNoPropertyDec', data);
            // };
        }
    ]);

    app.controller('NoPropertyDecController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'NoPropertyDecFactory',
        'NoPropertyDecService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockNoPropertyDec'), table = angular.element('#no_property_dec');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {

                $scope.jqDataTableOptions         = Factory.dtOptions();
                $scope.jqDataTableOptions.data    = Factory.dummyData;
                $scope.jqDataTableOptions.buttons = _btnFunc();

            };

            /**
             * `_btnFunc` list of button functions.
             * @return {[type]}
             */
            _btnFunc = function () {
                var buttons = [];

                buttons = [];

                buttons.push({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default btn-secondary');
                        $(node).addClass('btn bg-info text-white btn-sm add'); 
                        $(node).append('<i class="fas fa-plus"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD</span>');
                    },
                    text        : '', 
                    titleAttr   : 'Add New', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addNoPropertyDec(); 
                    },
                    enabled     : true,
                    name        : 'add'
                });
                
                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.viewNoPropertyDec(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.editNoPropertyDec(data, index)
                },
                "thirdButton": function(data, index) {
                    $scope.deleteNoPropertyDec(data, index)
                },
            };

            $scope.addNoPropertyDec = function () {
                var paramData, modalInstance;

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_no_property_dec.html',
                    controller      : 'AddNoPropertyDecController',
                    size            : 'md',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    // console.log('addREsult: ', res);
                    // table.DataTable().row.add(res).draw();
                    // table.find('tbody tr').css('cursor', 'pointer');
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.editNoPropertyDec = function (data, index) {
                var paramData, modalInstance;

                paramData = {
                    data,
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'edit_no_property_dec.html',
                    controller      : 'EditNoPropertyDecController',
                    size            : 'md',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    // console.log("editResult: ", res);
                    // table.DataTable().row(index).data(res).draw();
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.viewNoPropertyDec = function (data, index) {
                var paramData, modalInstance;

                paramData = {
                    data,
                    server_base_url: $scope.server.base_url,
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'view_no_property_dec.html',
                    controller      : 'ViewNoPropertyDecController',
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

            $scope.deleteNoPropertyDec = function(data, index){
                Alertify
                .okBtn("Yes")
                .cancelBtn("Cancel")
                .confirm("Are you sure you want to delete this certification?",
                    function () {
                        // blocker.start();
                        // Service.archive(data).then(res => {
                        //     if (res.data.status) {
                        //         table.DataTable().row('.selected').remove().draw(true);
                        //         Alertify.log('Deleted!');
                                
                        //         blocker.stop();
                        //     } else {
                        //         Alertify.error("ERROR! Please contact the administrator.");
                        //         blocker.stop();
                        //     }
                        // });
                    }
                );
            }

            // $scope.setFilterStatus = function(filter){
            //     $scope.filters.status = filter;

            //     $scope.filterTaxDec();
            // }

            // $scope.filterTaxDec = function (params = []) {

            //     console.log('params: ', params);
            //     var paramData;

            //     paramData = {
            //         'status'        : $scope.filters.status,
            //         'rev_id'        : params.rev_id,
            //         'td_no'         : params.td_no,
            //         'pin'           : params.pin,
            //         'owner'         : params.owner,
            //         'lot_no'        : params.lot_no,
            //         'brgy_id'       : params.brgy_id,
            //         'type'          : params.type,
            //         'category'      : params.category,
            //         'class_id'      : params.class_id,
            //         'actual_use'    : params.actual_use,
            //         'date_from'     : params.date_from,
            //         'date_to'       : params.date_to,
            //     };
                

            //     $timeout( function () {
            //         _softConfig().dt.data('dt_params', angular.copy(paramData)); // parse dynamic data
            //         _softConfig().dt.DataTable().draw();           // reload datatable
            //     }, 100);
            // };

            // _softConfig = function(){
            //     var temp = {
            //         'rowCount' : table.DataTable().data().count(),
            //         'dt'       : table
            //     };

            //     return temp;
            // }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "No Property Declaration"
                $scope.header.link.sub = "Certifications"
                $scope.header.link.main = "No Property Declaration"
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