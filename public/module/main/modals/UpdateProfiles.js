define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('updateProfilesFactory', [
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

            Factory.dtOptions = function (customType) {
                var options = {}, _columns;

                _columns = function (type) {
                    switch (type) {
                        case 'sp' :
                            var col = [
                                {
                                    "data"   : "id"
                                },
                                {
                                    "data"      : "spouse_name",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "business_address",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "ch_count",
                                    "className" : "text-capitalize text-center"
                                }
                            ];

                            return col;
                            break;
                        case 'eb' :
                            var col = [
                                {
                                    "data"   : "id"
                                },
                                {
                                    "data"      : "attainment_level_name",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "school_name",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"   : "course_name" ,
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "date_graduated",
                                    "className" : "text-capitalize text-center"
                                },
                                {
                                    "data"      : "honors",
                                    "className" : "text-capitalize"
                                }
                            ];

                            return col;
                            break;
                        case 'lc' :
                            var col = [
                                {
                                    "data"   : "id"
                                },
                                {
                                    "data"      : "name",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "date_taken",
                                    "className" : "text-capitalize text-center"
                                },
                                {
                                    "data"      : "rating",
                                    "className" : "text-capitalize text-right"
                                }
                            ];

                            return col;
                            break;
                        case 'eh' :
                            var col = [
                                {
                                    "data"   : "id"
                                },
                                {
                                    "data"      : "from_date",
                                    "className" : "text-center"
                                },
                                {
                                    "data"      : "to_date",
                                    "className" : "text-center"
                                },
                                {
                                    "data"      : "department_name",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "position_name" ,
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "salary_range" ,
                                    "className" : "text-capitalize text-right"
                                }
                            ];

                            return col;
                            break;
                        case 'ts' :
                            var col = [
                                {
                                    "data"   : "id"
                                },
                                {
                                    "data"      : "from_date",
                                },
                                {
                                    "data"      : "to_date",
                                },
                                {
                                    "data"      : "topic",
                                    "className" : "text-capitalize"
                                },
                                {
                                    "data"      : "organizer",
                                    "className" : "text-capitalize"
                                }
                            ];

                            return col;
                            break;
                        default :
                            break;
                    }
                }

                options = {
                    "dom"            : 'Bfrtip',
                    "paging"         : false,
                    "lengthChange"   : true,
                    "pageLength"     : 25,
                    "searching"      : false,
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
                    "responsive"     : true,
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
                        }
                    ],
                    "columns"      : _columns(customType)
                };

                return options;
            };

            return Factory;
        }
    ]);

    app.service('updateProfilesService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getRegions` Query string that will get regions.
             * @return {[route]}
             */
            _this.getRegions = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getRegions?search=' + search);
            };

            /**
             * `getProvinces` Query string that will get provinces.
             * @return {[route]}
             */
            _this.getProvinces = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getProvinces?search=' + search);
            };

            /**
             * `getCities` Query string that will get cities/municipalities.
             * @return {[route]}
             */
            _this.getCities = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getCities?search=' + search);
            };
            /**
             * `getBarangays` Query string that will get barangays.
             * @return {[route]}
             */
            _this.getBarangays = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getBarangays?search=' + search);
            };

            /**
             * `getDepartments`` Query string that will get departments.
             * @return {[route]}
             */
            _this.getDepartments = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getDepartments?search=' + search);
            };

            /**
             * `getPositions` Query string that will get positions.
             * @return {[route]}
             */
            _this.getPositions = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getPositions?search=' + search);
            };

            /**
             * `update` Query string that will update details.
             * @return {[query]}
             */
            _this.update = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/updatePi', input);
            };

            _this.getDetails = function (id) {
                // return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getDetails');
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/getDetails?id=' + id);
            }

        }
    ]);

    app.controller('UpdateProfilesController', [
        '$scope',
        '$uibModalInstance',
        '$http',
        '$timeout',
        '$filter',
        '$uibModal',
        'paramData',
        'blockUI',
        'alertify',
        'Upload',
        'updateProfilesFactory',
        'updateProfilesService',
        function ($scope, $uibModalInstance, $http, $timeout, $filter, $uibModal, ParamData, BlockUI, Alertify, Upload, Factory, Service) {
            var _init, _loadDetails, _dropzoneConfig, _softConfig, _softFunc, _softMergeData, _softAdd, _softEdit, _softDelete, _pluginFormat, blocker = BlockUI.instances.get('blockUpdatePiFiles'), spTable, ebTable, lcTable, ehTable, tsTable;

            _loadDetails = function () {
                var btnAccess = {
                    'delBtn'  : true,
                    'editBtn' : true,
                    'addBtn'  : true
                };

                blocker.start();

                Service.getDetails(ParamData.id).then( function (res) {
                    // $scope.data = paramData.data;
                    // Service.getDetails().then( function (res) {
                        // $scope.employees = angular.copy(res.data.employees);
                    // console.log(res.data);

                    // personal_informations
                    if (res.data.informations.length > 0) {
                        $scope.updateProfiles.pi                        = angular.copy(res.data.informations[0]);
                        // $scope.updateProfiles.pi.civil_status           = res.data.informations[0].civil_status;
                        $scope.updateProfiles.pi.temp_address_condition = $scope.updateProfiles.pi.address_condition;
                        $scope.updateProfiles.pi.ps_region              = {
                            'id'   : $scope.updateProfiles.pi.ps_region_id,
                            'name' : $scope.updateProfiles.pi.ps_region_name
                        };
                        $scope.updateProfiles.pi.ps_province            = {
                            'id'   : $scope.updateProfiles.pi.ps_province_id,
                            'name' : $scope.updateProfiles.pi.ps_province_name
                        };
                        $scope.updateProfiles.pi.ps_city                = {
                            'id'   : $scope.updateProfiles.pi.ps_city_id,
                            'name' : $scope.updateProfiles.pi.ps_city_name
                        };
                        $scope.updateProfiles.pi.ps_barangay            = {
                            'id'   : $scope.updateProfiles.pi.ps_barangay_id,
                            'name' : $scope.updateProfiles.pi.ps_barangay_name
                        };

                        angular.element('#birthdate').val(res.data.informations[0].birthdate);
                    }

                    // employment_informations
                    if (res.data.employment_informations.length > 0) {
                        $scope.updateProfiles.ei            = angular.copy(res.data.employment_informations[0]);
                        $scope.updateProfiles.ei.ho         = ($scope.updateProfiles.ei.ho == '0') ? 'no' : 'yes';
                        $scope.updateProfiles.ei.fo         = ($scope.updateProfiles.ei.fo == '0') ? 'no' : 'yes';
                        $scope.updateProfiles.ei.position   = {
                            'id'              : $scope.updateProfiles.ei.position_id,
                            'name'            : $scope.updateProfiles.ei.position_name,
                            'department_id'   : $scope.updateProfiles.ei.department_id,
                            'department_name' : $scope.updateProfiles.ei.department_name
                        };
                        $scope.updateProfiles.ei.head       = {
                            'id'        : $scope.updateProfiles.ei.head_id,
                            'full_name' : $scope.updateProfiles.ei.head_name
                        };
    
                        angular.element('#date_hired').val($scope.updateProfiles.ei.date_hired);
                    }

                    // family_backgrounds.
                    if (res.data.family_backgrounds.length > 0) {
                        $scope.updateProfiles.fb = angular.copy(res.data.family_backgrounds[0]);
                    }

                    // other_details.
                    if (res.data.other_details.length > 0) {
                        $scope.updateProfiles.od = res.data.other_details[0];
                    }

                    /**
                     * Datatable configuration
                     *  - spouse
                     *  - education_background
                     *  - license_certificate
                     *  - employment_history
                     */
                    $scope.spJqDtOptions         = Factory.dtOptions('sp');
                    $scope.spJqDtOptions.buttons = _btnFunc(btnAccess, 'sp');
                    $scope.spJqDtOptions.data    = angular.copy(res.data.spouses);
                    // $scope.spJqDtOptions.data    = ($scope.updateProfiles.pi.length > 0) ? (($scope.updateProfiles.pi.civil_status == 'married') ? angular.copy(res.data.spouses) : []) : [];

                    $scope.ebJqDtOptions         = Factory.dtOptions('eb');
                    $scope.ebJqDtOptions.buttons = _btnFunc(btnAccess, 'eb');
                    $scope.ebJqDtOptions.data    = angular.copy(res.data.educational_backgrounds);

                    // license_certificate
                    $scope.lcJqDtOptions         = Factory.dtOptions('lc');
                    $scope.lcJqDtOptions.buttons = _btnFunc(btnAccess, 'lc');
                    $scope.lcJqDtOptions.data    = angular.copy(res.data.license_certificates);

                    // employment_history
                    $scope.ehJqDtOptions         = Factory.dtOptions('eh');
                    $scope.ehJqDtOptions.buttons = _btnFunc(btnAccess, 'eh');
                    $scope.ehJqDtOptions.data    = angular.copy(res.data.employment_histories);

                    // training_seminar
                    $scope.tsJqDtOptions         = Factory.dtOptions('ts');
                    $scope.tsJqDtOptions.buttons = _btnFunc(btnAccess, 'ts');
                    $scope.tsJqDtOptions.data    = angular.copy(res.data.training_seminars);

                    $timeout(function(){
                        _softMergeData('sp');
                        _softMergeData('eb');
                        _softMergeData('lc');
                        _softMergeData('eh');
                        _softMergeData('ts');

                        blocker.stop();
                    }, 1000);
                });
                
            }
            

            /**
             * `selectPhoto` Method run when select photo has been triggered.
             * @param  {object} file
             * @return {void}
             */
            $scope.selectPhoto = function (file) {
                if (file == null) {
                    $scope.showThumbnailLabel = true;
                    $scope.updateProfiles.file    = file;
                } else {
                    $scope.updateProfiles.file    = file;
                    $scope.showThumbnailLabel = false;
                }
            };

            /**
             * `changePhoto` Run function when photo is changed.
             * @return {[void]}
             */
            $scope.changePhoto = function () {
                $scope.changePhotoFormEnable = true;
            };

            /**
             * `cancelChangePhoto` Cancelation of changing of photo.
             * @return {[void]}
             */
            $scope.cancelChangePhoto = function () {
                $scope.changePhotoFormEnable = false;
            };

            /**
             * `_dropzoneConfig` Configuration of dropzoneJs
             * @return {[mixed]}
             */
            _dropzoneConfig = function () {
                //Set options for dropzone
                $scope.dzOptions = {
                    url            : APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/uploadImage',
                    paramName      : 'photo',
                    maxFilesize    : '10',
                    maxFiles       : '1',
                    acceptedFiles  : 'application/*',
                    addRemoveLinks : true,
                    dictDefaultMessage : 'Drop or Upload employee photo.'
                    // init           : function() {
                    //     this.on("maxfilesexceeded", function(file) {
                    //         console.log(file);
                    //         this.removeAllFiles();
                    //         this.addFile(file);
                    //     });
                    // }
                };

                //Handle events for dropzone
                $scope.dzCallbacks = {
                    'addedfile' : function(file){
                        $scope.updateProfiles = file;
                    },
                    'success' : function(file, xhr){
                    },
                    'maxfilesexceeded' : function (file) {
                        $scope.dzMethods.removeAllFiles();
                        $scope.dzMethods.addFile(file);

                        // $timeout( function () {
                        //     $scope.updateProfiles = file;
                        // });
                        // console.log(file);
                    }
                };

                // Handle event for removing file.
                $scope.removeUpdateFile = function(){
                    $scope.dzMethods.removeFile($scope.updateProfiles); //We got $scope.updateProfiles from 'addedfile' event callback
                }
            }

            $scope.toggleTempAddress = function (condition) {
                // console.log(condition);
            };

            /**
             * `triggerHead` Filter position and get the name of the position head after selecting department.
             * @param  {[object]} position
             * @return {[array]}
             */
            $scope.triggerHead = function (position) {
                // console.log(position,' - position');
                // console.log(ParamData.head_employees);
                var filterHead, tempFilterHead = [], heads = [];

                delete $scope.updateProfiles.ei.head;
                
                filterHead = $filter('filter')(ParamData.head_employees, {
                    'position_id' : position.head_id
                }, true);

                heads = angular.copy(filterHead);
                
                // add assistant PM from the list of PM.
                if (position.head_id == '18' || position.head_id == 18) {
                    // assistant_project_manager
                    tempFilterHead = $filter('filter')(ParamData.head_employees, {
                        'position_id' : '60'
                    }, true);

                    // // supply_head
                    // tempFilterHeadSecond = $filter('filter')(ParamData.head_employees, {
                    //     'position_id' : '12'
                    // }, true);

                    // general_manager
                    tempFilterHeadThird = $filter('filter')(ParamData.head_employees, {
                        'position_id' : '41'
                    }, true);

                    // COO
                    tempFilterHeadForth = $filter('filter')(ParamData.head_employees, {
                        'position_id' : '93'
                    }, true);

                    // SVPO II
                    tempFilterHeadFifth = $filter('filter')(ParamData.head_employees, {
                        'position_id' : '28'
                    }, true);

                    // SVPO I
                    tempFilterHeadSixth = $filter('filter')(ParamData.head_employees, {
                        'position_id' : '8'
                    }, true);

                    heads = heads.concat(tempFilterHead);
                    // heads = heads.concat(tempFilterHeadSecond);
                    heads = heads.concat(tempFilterHeadThird);
                    heads = heads.concat(tempFilterHeadForth);
                    heads = heads.concat(tempFilterHeadFifth);
                    heads = heads.concat(tempFilterHeadSixth);
                }
                
                $scope.heads = heads;
            };
0
            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            /**
             * `update` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.update = function (isValid) {
                // console.log($scope.updateProfiles);

                if (isValid) {
                    // if ($scope.updateProfiles.file == null) {
                    //     Alertify.error("No selected photo.");
                    // } else {
                        Alertify.confirm("Are you sure you want to update this 201 Files?",
                            function (res, type) {
                                if (res) {
                                    blocker.start();
                                    // $scope.updateProfiles =  _softConfig(type).dataTable().fnGetData();
                                    $scope.updateProfiles.pi.birthdate = angular.element('#birthdate').val();
                                    $scope.updateProfiles.ei.date_hired = angular.element('#date_hired').val();

                                    Upload.upload({
                                        method  : 'POST',
                                        url     : APP.SERVER_BASE_URL + '/App/Service/Main/UpdateProfilesService.php/updatePi',
                                        data    : $scope.updateProfiles,
                                        headers : {
                                            'Content-Type' :  'application/x-www-form-urlencoded'
                                        }
                                    }).success(
                                        function (res) {
                                            if (res.status == 'true' || res.status == true) {
                                                Alertify.success("201 files successfully updated!");

                                                $scope.updateProfiles.id         = res.id;
                                                // $scope.updateProfiles.photo      = $scope.updateProfiles.file.name;
                                                // $scope.changePhotoFormEnable = false;
                                            
                                                $uibModalInstance.close($scope.updateProfiles);
                                                
                                                blocker.stop();
                                            } else {
                                                Alertify.error("201 files is already exist!");

                                                blocker.stop();
                                            }
                                        }
                                    );
                                }
                            }
                        );
                    // }

                } else {
                    Alertify.error("All fields are required!");
                }
            };

            /**
             * `_softConfig`
             * @param  {[type]} type [description]
             * @return {[type]}      [description]
             */
            _softConfig = function (type) {
                switch (type) {
                    case 'sp' :
                        var temp = {
                            'templateUrl' : 'spouse.html',
                            'controller'  : 'SpouseController',
                            'rowCount'    : spTable.DataTable().data().count(),
                            'dt'          : spTable,
                        };

                        return temp;
                        break;

                    case 'eb' :
                        var temp = {
                            'templateUrl' : 'educational_background.html',
                            'controller'  : 'EducationalBackgroundController',
                            'rowCount'    : ebTable.DataTable().data().count(),
                            'dt'          : ebTable
                        };

                        return temp;
                        break;

                    case 'lc' :
                        var temp = {
                            'templateUrl' : 'license_certificate.html',
                            'controller'  : 'LicenseCertificateController',
                            'rowCount'    : lcTable.DataTable().data().count(),
                            'dt'          : lcTable
                        };

                        return temp;
                        break;

                    case 'eh' :
                        var temp = {
                            'templateUrl' : 'employment_history.html',
                            'controller'  : 'EmploymentHistoryController',
                            'rowCount'    : ehTable.DataTable().data().count(),
                            'dt'          : ehTable
                        };

                        return temp;
                        break;

                    case 'ts' :
                        var temp = {
                            'templateUrl' : 'training_seminar.html',
                            'controller'  : 'TrainingSeminarController',
                            'rowCount'    : tsTable.DataTable().data().count(),
                            'dt'          : tsTable
                        };

                        return temp;
                        break;

                    default :
                        break;
                }
            }

            /**
             * `_softMergeData` Merging data to main scope.
             * @param  {[string]} type
             * @param  {[object]} data
             * @return {[mixed]}
             */
            _softMergeData = function (type, data) {
                console.log(type);
                if (type == 'sp') {
                    console.log(_softConfig(type));
                    delete $scope.updateProfiles.fb.spouses;

                    $scope.updateProfiles.fb.spouses = _softConfig(type).dt.DataTable().rows().data().toArray();
                }

                if (type == 'eb') {
                    delete $scope.updateProfiles.eb;

                    $scope.updateProfiles.eb = _softConfig(type).dt.DataTable().rows().data().toArray();
                }

                if (type == 'lc') {
                    delete $scope.updateProfiles.lc;

                    $scope.updateProfiles.lc = _softConfig(type).dt.DataTable().rows().data().toArray();
                }

                if (type == 'eh') {
                    delete $scope.updateProfiles.eh;

                    $scope.updateProfiles.eh = _softConfig(type).dt.DataTable().rows().data().toArray();
                }

                if (type == 'ts') {
                    delete $scope.updateProfiles.ts;

                    $scope.updateProfiles.ts = _softConfig(type).dt.DataTable().rows().data().toArray();
                }
            }

            /**
             * `_softFunc` Global func for add and edit.
             * @param  {[string]} type  `sp`, `eb`, `lc`, `we`, `ts`
             * @param  {[boolean]} state `true = update` | `false = edit`
             * @return {[mixed]}
             */
            _softFunc = function (type, state) {
                var subParamData, modalInstance;

                subParamData = {
                    mType    : (state) ? 'new'                    : 'edit',
                    mIcon    : (state) ? 'fa fa-plus fa-stack-1x' : 'fa fa-edit fa-stack-1x',
                    rowData  : (state) ? {}                       : _softConfig(type).dt.DataTable().rows('.selected').data()[0],
                    rowCount : _softConfig(type).rowCount
                };

                modalInstance = $uibModal.open({
                    animation       : true,
                    keyboard        : false,
                    backdrop        : 'static',
                    ariaLabelledBy  : 'modal-title',
                    ariaDescribedBy : 'modal-body',
                    templateUrl     : _softConfig(type).templateUrl,
                    controller      : _softConfig(type).controller,
                    size            : 'md',
                    resolve         : {
                        subParamData : function () {
                            return subParamData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {
                    var tempData, index;

                    tempData = angular.copy(res);


                    if (state) {
                        _softConfig(type).dt.DataTable().row.add(tempData).draw().select();
                        _softConfig(type).dt.find('tbody tr').css('cursor', 'pointer');
                    } else {
                        index = _softConfig(type).dt.DataTable().row('.selected').index();

                        _softConfig(type).dt.DataTable().row(index).data(tempData).draw();
                    }

                    _softMergeData(type);
                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            /**
             * `_softAdd` Adding of update data.
             * @return {[modal]} Instances
             */
            _softAdd = function (type) {
                _softFunc(type, true);
            }

            /**
             * `_softEdit` Editing of row data.
             * @param  {[string]} type
             * @return {[mixed]}
             */
            _softEdit = function (type) {
                _softFunc(type, false);
            }

            /**
             * `_softDelete` Deleting of data
             * @return {[void]}
             */
            _softDelete = function (type) {
                Alertify.confirm("Are you sure you want to remove selected row?",
                    function (res) {
                        if (res) {
                             _softConfig(type).dt.DataTable().row('.selected').remove().draw(true);

                            Alertify.success("Selected row successfully removed!");
                        }
                    }
                );
            }

            /**
             * `_btnFunc` list of button functions.
             * @return {[type]}
             */
            _btnFunc = function (user_level, type) {
                var buttons = [];

                if (user_level.delBtn) {
                    buttons.unshift({
                        init        : function(api, node, config) {
                            $(node).removeClass('btn-default');
                            $(node).addClass('btn bg-orange btn-sm hoverable delete');
                            $(node).append('<i class="fa fa-trash"></i>&nbsp;<span class="hidden-xs hidden-sm">REMOVE</span>');
                        },
                        text        : '',
                        titleAttr   : 'Remove Entry',
                        key: {
                            key     : '3',
                            altKey  : true
                        },
                        'action'    : function () {
                            _softDelete(type);
                        } ,
                        enabled     : false,
                        name        : 'delete'
                    });
                }

                if (user_level.editBtn) {
                    buttons.unshift({
                        init        : function(api, node, config) {
                            $(node).removeClass('btn-default');
                            $(node).addClass('btn bg-orange btn-sm hoverable edit');
                            $(node).append('<i class="fa fa-edit"></i>&nbsp;<span class="hidden-xs hidden-sm">EDIT</span>');
                        },
                        text        : '',
                        titleAttr   : 'Edit Entry',
                        key: {
                            key     : '2',
                            altKey  : true
                        },
                        'action'    : function () {
                            _softEdit(type);
                        },
                        enabled     : false,
                        name        : 'edit'
                    });
                }

                if (user_level.addBtn) {
                    buttons.unshift( {
                        init        : function(api, node, config) {
                            $(node).removeClass('btn-default');
                            $(node).addClass('btn bg-orange btn-sm hoverable add');
                            $(node).append('<i class="fa fa-plus-square"></i>&nbsp;<span class="hidden-xs hidden-sm">ADD</span>');
                        },
                        text        : '',
                        titleAttr   : 'Add Update Entry',
                        key: {
                            key     : '1',
                            altKey  : true
                        },
                        'action'    : function () {
                            _softAdd(type);
                        },
                        enabled     : true,
                        name        : 'add'
                    });
                }

                return buttons;
            }

            /**
             * `searchRegion` Seaching of region.
             * @param  {[string]} search
             * @return {[mixed]}
             */
            $scope.searchRegion = function (search) {
                Service.getRegions(search).then(
                    function (res) {
                        $scope.regions = res.data;
                    }
                );
            };

            /**
             * `searchProvince` Seaching of provinces.
             * @param  {[string]} search
             * @return {[mixed]}
             */
            $scope.searchProvince = function (search) {
                Service.getProvinces(search).then(
                    function (res) {
                        $scope.provinces = res.data;
                    }
                );
            };

            /**
             * `searchCity` Seaching of cities/municipalities.
             * @param  {[string]} search
             * @return {[mixed]}
             */
            $scope.searchCity = function (search) {
                Service.getCities(search).then(
                    function (res) {
                        $scope.cities = res.data;
                    }
                );
            };

            /**
             * `searchBarangay` Seaching of barangays.
             * @param  {[string]} search
             * @return {[mixed]}
             */
            $scope.searchBarangay = function (search) {
                Service.getBarangays(search).then(
                    function (res) {
                        $scope.barangays = res.data;
                    }
                );
            };

            /**
             * `searchDepartment` Seaching of department.
             * @param  {[string]} search
             * @return {[mixed]}
             */
            $scope.searchDepartment = function (search) {
                Service.getDepartments(search).then(
                    function (res) {
                        $scope.departments = res.data;
                    }
                );
            };

            /**
             * `searchPosition` Seaching of position.
             * @param  {[string]} search
             * @return {[mixed]}
             */
            $scope.searchPosition = function (search) {
                Service.getPositions(search).then(
                    function (res) {
                        $scope.positions = res.data;
                    }
                );
            };

            /**
             *`addMoreItems` Adding more items for infinite scroll
             */
            $scope.addMoreItems = function () {
                $scope.infiniteScroll.currentItems += $scope.infiniteScroll.numToAdd;
            };

            /**
             * `_pluginFormat`
             * @return {[type]} [description]
             */
            _pluginFormat = function () {
                // angular.forEach($scope.withdrawFormInfo.pr_requests, function (prVal, prKey) {
                $timeout( function () {
                    angular.element('#birthdate').inputmask('mm/dd/yyyy', {
                            placeholder: '__/__/____'
                    });

                    angular.element('#birthdate').datepicker({
                            language : 'en'
                    });
                    angular.element('#date_hired').inputmask('mm/dd/yyyy', {
                        placeholder: '__/__/____'
                    });

                    angular.element('#date_hired').datepicker({
                            language : 'en'
                    });
                }, 200);
				// });
            }

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                Dropzone.autoDiscover = false;

                $scope.showThumbnailLabel    = true;
                $scope.changePhotoFormEnable = false;

                $scope.infiniteScroll              = {};
                $scope.infiniteScroll.numToAdd     = 20;
                $scope.infiniteScroll.currentItems = 20;
                $scope.updateProfiles                  = {
                    'pi' : {
                        'sex'                    : 'male',
                        'civil_status'           : 'single',
                        'temp_address_condition' : 'no',
                        'ps_type'                : 'rented',
                        'pr_type'                : 'owned'
                    },
                    'ei' : {
                        'ho'     : 'no',
                        'fo'     : 'no',
                        'status' : 'provisionary',
                    },
                    'fb' : {},
                    'eb' : {},
                    'lc' : {},
                    'od' : {},
                    'eh' : {},
                    'ts' : {}
                };

                //Apply methods for dropzone
                $scope.dzMethods = {};

                $timeout( function () {
                    spTable = angular.element('#dtSpouse');
                    ebTable = angular.element('#dtEducationalBackground');
                    lcTable = angular.element('#dtLicenseCertificate');
                    ehTable = angular.element('#dtEmploymentHistory');
                    tsTable = angular.element('#dtTrainingSeminar');
                });

                _loadDetails();
                _dropzoneConfig();
                _pluginFormat();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
