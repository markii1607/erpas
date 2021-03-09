define([
    'app',
    'airDatepickeri18n'
], function (app) {
    app.factory('educationalBackgroundFactory', [
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

    app.service('educationalBackgroundService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get positions.
             * @return {[route]}
             */
            _this.getDetails = function (search) {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/EducationalBackgroundService.php/getDetails');
            };

            /**
             * `save` Query string that will save details.
             * @return {[query]}
             */
            _this.save = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/EducationalBackgroundService.php/saveEducationalBackgroundDetails', input);
            };
        }
    ]);

    app.controller('EducationalBackgroundController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        '$filter',
        'subParamData',
        'blockUI',
        'alertify',
        'educationalBackgroundFactory',
        'educationalBackgroundService',
        function ($scope, $uibModalInstance, $timeout, $filter, SubParamData, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _pluginFormat, blocker = BlockUI.instances.get('blockEducationalBackground');

            /**
             * `_loadDetails` Load first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {
                blocker.start();

                Service.getDetails().then( function (res) {
                    $scope.attainments = angular.copy(res.data.attainments);
                    $scope.schools     = angular.copy(res.data.schools);
                    $scope.courses     = angular.copy(res.data.courses);

                    // console.log(SubParamData.mType);
                    if(SubParamData.mType == 'edit'){
                        $scope.educationalBackgroundInfo.attainment   = {
                            'id'   : $scope.educationalBackgroundInfo.attainment_level_id,
                            'name' : $scope.educationalBackgroundInfo.attainment_level_name
                        };
    
                    };
                    
                    if(SubParamData.mType == 'edit'){
                        $scope.educationalBackgroundInfo.school   = {
                            'id'   : $scope.educationalBackgroundInfo.school_id,
                            'name' : $scope.educationalBackgroundInfo.school_name
                        };
    
                    };

                    if(SubParamData.mType == 'edit'){
                        $scope.educationalBackgroundInfo.course   = {
                            'id'   : $scope.educationalBackgroundInfo.course_id,
                            'name' : $scope.educationalBackgroundInfo.course_name
                        };
    
                    };
                    // get date picker data.
                    angular.element('#date_graduated').val($scope.educationalBackgroundInfo.date_graduated);

                    blocker.stop();
                });
            };

            /**
             * `createTag` Creating of new tag.
             * @return {[object]}
             */
            $scope.createTag = function (newTag) {
                return {
                    id   : null,
                    name : newTag
                };
            };

            /**
             * `closeModal` Closing of modal.
             * @return {[void]}
             */
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            };

            /**
             * `save` Post data from form to database.
             * @param  {Boolean} isValid
             * @return {Object}
             */
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Are you sure you want to add this new educational background?",
                        function (res) {
                            if (res) {
                                blocker.start();
                                $scope.educationalBackgroundInfo.date_graduated = angular.element('#date_graduated').val();

                                $timeout( function () {
                                    if ($scope.educationalBackgroundInfo.attainment.id == null || $scope.educationalBackgroundInfo.course.id == null || $scope.educationalBackgroundInfo.school.id == null) {
                                        Service.save($scope.educationalBackgroundInfo).then( function (res) {
                                            Alertify.success("Educational background successfully added!");

                                            $scope.educationalBackgroundInfo.attainment.id = ($scope.educationalBackgroundInfo.attainment.id == null) ? res.data.attainment_id : null;
                                            $scope.educationalBackgroundInfo.school.id     = ($scope.educationalBackgroundInfo.school.id == null)     ? res.data.school_id     : null;
                                            $scope.educationalBackgroundInfo.course.id     = ($scope.educationalBackgroundInfo.course.id == null)     ? res.data.course_id     : null;

                                            $scope.educationalBackgroundInfo.id                    = (SubParamData.mType == 'new') ? SubParamData.rowCount + 1 : SubParamData.rowData.id;
                                            $scope.educationalBackgroundInfo.attainment_level_name = $scope.educationalBackgroundInfo.attainment.name;
                                            $scope.educationalBackgroundInfo.school_name           = $scope.educationalBackgroundInfo.school.name;
                                            $scope.educationalBackgroundInfo.course_name           = $scope.educationalBackgroundInfo.course.name;

                                            $scope.educationalBackgroundInfo.data_status = "new";
                                            
                                            $uibModalInstance.close($scope.educationalBackgroundInfo);
                                        });
                                    } else {
                                        Alertify.success("Educational background successfully added!");

                                        $scope.educationalBackgroundInfo.id                    = (SubParamData.mType == 'new') ? SubParamData.rowCount + 1 : SubParamData.rowData.id;
                                        $scope.educationalBackgroundInfo.attainment_level_name = $scope.educationalBackgroundInfo.attainment.name;
                                        $scope.educationalBackgroundInfo.school_name           = $scope.educationalBackgroundInfo.school.name;
                                        $scope.educationalBackgroundInfo.course_name           = $scope.educationalBackgroundInfo.course.name;
                                        
                                        $uibModalInstance.close($scope.educationalBackgroundInfo);
                                    }

                                    blocker.stop();
                                }, 1000);
                            }
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };

             /**
             * `_pluginFormat`
             * @return {[type]} [description]
             */
            _pluginFormat = function () {
                // angular.forEach($scope.withdrawFormInfo.pr_requests, function (prVal, prKey) {
                $timeout( function () {
                    angular.element('#date_graduated').inputmask('mm/dd/yyyy', {
                            placeholder: '__/__/____'
                    });

                    angular.element('#date_graduated').datepicker({
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

                $scope.config                    = SubParamData;
                $scope.config.btnLabel           = (SubParamData.mType == 'new') ? 'Save' : 'Update';
                $scope.educationalBackgroundInfo = (SubParamData.mType == 'new') ? {}     : angular.copy(SubParamData.rowData);

                _loadDetails();
                _pluginFormat();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
