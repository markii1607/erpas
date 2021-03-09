define([
    'app'
], function (app) {
    app.factory('updateImageFactory', [
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

    app.service('updateImageService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[route]}
             */ 
            _this.getDetails = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/LightDailyAccomplishmentReportService.php/getDetails');
            };

            _this.archive = function(id){
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/AddLightDailyAccomplishmentReportService.php/archiveImage', {id : id});
            }
        }
    ]);

    app.controller('UpdateImageController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'blockUI',
        'alertify',
        'Upload',
        'paramData',
        'updateImageFactory',
        'updateImageService',
        function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, Upload, ParamData, Factory, Service) {
            var _init, _pluginFormat, blocker = BlockUI.instances.get('blockViewJobsForm');

            _loadDetails = function () {
                // blocker.start();

            	// Service.getDetails().then( function (res) {
                //     $scope.attachments = res.data.attachments;
                
                //     // angular.element('#date_needed').val(ParamData.rowData.date_needed);

				// 	blocker.stop();
            	// });
            }

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
            // $scope.save = function (isValid) {
            //     if (isValid) {
            //         Alertify.confirm("Are you sure you want to save this requisition details?",
            //             function (res) {
            //                 if (res) {
            //                     blocker.start();

            //                     $timeout( function () {
            //                         Alertify.success("Employee successfully added!");
            //                         $scope.viewJobsInfo.id            = (ParamData.mType == 'new') ? ParamData.rowCount + 1 : ParamData.rowData.id;
            //                         $scope.viewJobsInfo.position_name = angular.copy($scope.viewJobsInfo.position.name);
			// 						$scope.viewJobsInfo.date_needed = angular.element('#date_needed').val();

            //                         console.log($scope.viewJobsInfo);

            //                         $uibModalInstance.close($scope.viewJobsInfo);
            //                         blocker.stop();
            //                     }, 1000);
            //                 }
            //             }
            //         );
            //     } else {
            //         Alertify.error("All fields are required!");
            //     }
            // };

            $scope.removeAttachment = function (index, attachment) {
                // console.log(index);
                console.log(attachment);
                Alertify.confirm("Are you sure you want to remove this image?", function(confirmation){
                    if (confirmation) {
                        // if (attachment.data_status == 'saved') {
                        //     Service.archive(attachment).then(function(res){
                        //         if (res.data.status) {
                        //             $scope.viewAttachmentsInfo.attachments.splice(index, 1);
                        //             Alertify.success('Attachment removed!');
                        //         } else {
                        //             Alertify.error('Failed to remove attachment! Please try again.');
                        //         }
                        //     });
                        // } else {
                        //     $scope.viewAttachmentsInfo.attachments.splice(index, 1);
                        //     Alertify.success('Attachment removed!');
                        // }

                        Upload.upload({
                            method  :   'POST',
                            url     :   APP.SERVER_BASE_URL + '/App/Service/LightDailyAccomplishmentReport/AddLightDailyAccomplishmentReportService.php/archiveImage',
                            data    :   attachment,
                            headers :   {
                                'Content-Type' :  'application/x-www-form-urlencoded'
                            }
                        }).success(
                            function(res){
                                // if (attachment.data_status == 'saved') {
                                    // Service.archive(attachment).then(function(res){
                                        if (res.status) {
                                            $scope.viewAttachmentsInfo.splice(index, 1);
                                            Alertify.success('Attachment removed!');
                                        } else {
                                            Alertify.error('Failed to remove attachment! Please try again.');
                                        }
                                    // });
                                // } else {
                                //     $scope.viewAttachmentsInfo.attachments.splice(index, 1);
                                //     Alertify.success('Attachment removed!');
                                // }
                            }
                        );
                    }
                })
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();
                // $scope.photoConfig = $scope.server.ftp_url;

                $scope.viewAttachmentsInfo = ParamData.data;
                console.log($scope.viewAttachmentsInfo);
                $scope.viewAttachmentsConfig = 
                {
                    server_ftp_url : ParamData.server_ftp_url,
                    server_base_url : APP.SERVER_BASE_URL
                };
                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
