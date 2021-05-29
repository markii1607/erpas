define([
    'app'
], function (app) {
    app.factory('SignatoriesConfigurationFactory', [
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
                'module/signatories_configuration/modals/add_approver.html',
                'module/signatories_configuration/modals/edit_approver.html',
            ];

            Factory.dtOptions = function () {
                var options = {};

                options = {
                    "dom"            : 'Bfrtip',
                    "paging"         : true,
                    "lengthChange"   : true,
                    "pageLength"     : 25,
                    "searching"      : true,
                    "ordering"       : true,
                    "info"           : true,
                    "select"         : {
                        style : 'single'
                    },
                    "keys"           : {
                        keys: [ 
                            13 /* ENTER */, 
                            38 /* UP */, 
                            40 /* DOWN */ 
                        ]
                    },
                    "mark"           : true,
                    "autoWidth"      : false,
                    "responsive"     : false,
                    "data"           : [],
                    "buttons"        : [],
                    "order"          : [
                        [
                            1, 
                            "desc" 
                        ]
                    ],
                    "columnDefs"   : [ 
                        {
                            "targets"    : 0,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center"
                        },
                        {
                            "targets"    : 1,
                            "searchable" : true,
                            "orderable"  : true,
                            "className"  : "text-center",
                            "render"     :  function(data, type, full, meta) {
                                                str = '<dl>' + 
                                                            '<dt>NAME</dt>' + 
                                                            '<dd>' + data.name + '</dd>' +
                                                            '<dt>POSITION</dt>' + 
                                                            '<dd>' + data.position + '</dd>' +
                                                      '</dl>';

                                                
                                                return str;

                            }
                        },
                        {
                            "targets"    : 2,
                            "searchable" : true,
                            "orderable"  : true,
                            "className"  : "text-center",
                            "render"     :  function(data, type, full, meta) {
                                            str =   '<dl>' + 
                                                        '<dt>NAME</dt>' + 
                                                        '<dd>' + data.name + '</dd>' +
                                                        '<dt>POSITION</dt>' + 
                                                        '<dd>' + data.position + '</dd>' +
                                                    '</dl>';

                                                
                                                return str;

                            }
                        },
                        {
                            "targets"    : 4,
                            "searchable" : false,
                            "orderable"  : false,
                            "className"  : "text-center",
                            "render"    : function(data, type, full, meta) {
                                                var str = '';
                                                // str += '<button type="submit" id="firstButton" data-toggle="tooltip" title="Edit" class="btn btn-block bg-primary btn-sm mr-2 text-white text-left"><i class="fas fa-edit"></i> Edit Approver Set</button>';
                                                // str += '<p style="margin-bottom:5px;"></p>';
                                                str += '<button type="submit" id="secondButton" data-toggle="tooltip" title="Delete" class="btn bg-danger btn-md mr-2 text-white text-left"><i class="fas fa-trash"></i></button>';

                                                return str;
                            }
                        },
                    ],
                    "columns"      : [
                        { 
                            "data" : null 
                        },
                        { 
                            "data" : "approvers.prov", 
                        },
                        { 
                            "data" : "approvers.mun", 
                        },
                        { 
                            "data" : "created_at", 
                        },
                        { 
                            "data" : null, 
                        }
                    ]
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('SignatoriesConfigurationService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/SignatoriesConfiguration/SignatoriesConfigurationService.php/getDetails');
            };

            _this.archive = function (data) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/SignatoriesConfiguration/SignatoriesConfigurationService.php/archiveApprover', data);
            };
        }
    ]);

    app.controller('SignatoriesConfigurationController', [
        '$scope',
        '$uibModal',
        '$timeout',
        'blockUI',
        'alertify',
        'SignatoriesConfigurationFactory',
        'SignatoriesConfigurationService',
        function ($scope, $uibModal, $timeout, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _btnFunc, _viewAccesses, blocker = BlockUI.instances.get('blockSignatoriesConfiguration'), table = angular.element('#signatoriesConfiguration');

            /**
             * `_loadDetails` Load first needed data
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();
                Service.getDetails().then( function (res) {
                    if (res.data.approvers != undefined) {
                        $scope.jqDataTableOptions         = Factory.dtOptions();
                        $scope.jqDataTableOptions.buttons = _btnFunc();
                        $scope.jqDataTableOptions.data    = res.data.approvers;
                        
                        blocker.stop();
                    } else {
                        Alertify.error('An error occurred while fetching data. Please contact the administrator.');
                        blocker.stop();
                    }
                });
            };

            /**
             * `_btnFunc` list of button functions.
             * @return {[type]}
             */
            _btnFunc = function () {
                var buttons = [];

                buttons = [];

                buttons.unshift({ 
                    init        : function(api, node, config) {
                        $(node).removeClass('btn-default btn-secondary');
                        $(node).addClass('btn bg-info text-white btn-sm add'); 
                        $(node).append('<i class="fas fa-plus"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD APPROVER SET</span>');
                    },
                    text        : '', 
                    titleAttr   : 'Add Approver Set', 
                    key: { 
                        key     : '1', 
                        altKey  : true 
                    }, 
                    'action'    : function () { 
                        $scope.addApproverSet(); 
                    },
                    enabled     : true,
                    name        : 'add'
                }); 

                return buttons;
            }

            $scope.rowBtns = {
                "firstButton": function(data, index) {
                    $scope.editApproverSet(data, index)
                },
                "secondButton": function(data, index) {
                    $scope.archiveApproverSet(data, index)
                },
            };

            $scope.addApproverSet = function(){
                var paramData, modalInstance;

                paramData = {}

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'add_approver.html',
                    controller      : 'AddApproverController',
                    size            : 'md',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    console.log('addREsult: ', res);
                    table.DataTable().row.add(res).draw();
                    table.find('tbody tr').css('cursor', 'pointer');
                    
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.editApproverSet = function(data, index){
                var paramData, modalInstance;

                paramData = {
                    data
                }

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : 'edit_approver.html',
                    controller      : 'EditApproverController',
                    size            : 'md',
                    resolve         : {
                        paramData : function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    console.log("editResult: ", res);
                    table.DataTable().row(index).data(res).draw();
                    
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            $scope.archiveApproverSet = function(data, index){
                Alertify
                .okBtn("Yes")
                .cancelBtn("No")
                .confirm("Are you sure you want to delete the selected set of approvers?", function(){
                    blocker.start();
                    Service.archive(data).then(res => {
                        if (res.data.status) {
                            table.DataTable().row('.selected').remove().draw(true);
                            Alertify.success("Deleted successfully!");

                            blocker.stop();
                        } else {
                            Alertify.error("An error occurred while saving data. Please contact the administrator.");
                            blocker.stop();
                        }
                    })
                })
            }

            /**
             * `_init` Initialize first things first
             * @return {mixed}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                $scope.header.title = "Signatories Configuration"
                $scope.header.link.sub = ""
                $scope.header.link.main = "Signatories Configuration"

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