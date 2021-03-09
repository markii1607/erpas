define([
    'app'
], function (app) {
    app.factory('changeDeputyStatusFactory', [
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

    app.service('changeDeputyStatusService', [
        '$http',
        function ($http) {
            var _this = this;

            /**
             * `save` Query string that will update details.
             * @return {[query]}
             */
            _this.update = function (input) {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/UserAccessConfiguration/AddDeputyService.php/updateDeputyStatus', input);
            };
        }
    ]);

    app.controller('ChangeDeputyStatusController', [
        '$scope',
        '$uibModalInstance',
        '$timeout',
        'blockUI',
        'alertify',
        'subParamData',
        'changeDeputyStatusFactory',
        'changeDeputyStatusService',
        function ($scope, $uibModalInstance, $timeout, BlockUI, Alertify, SubParamData, Factory, Service) {
            var _init, _loadDetails, blocker = BlockUI.instances.get('blockAccessForm');

            /**
             * `_loadDetails` Fetching of first needed details.
             * @return {[mixed]}
             */
            _loadDetails = function () {

                $scope.priviledges = $scope.adaptPriviledges(SubParamData.data.priviledges, "bool");
                $scope.updatedStatus = {};
                $scope.deputyStatus = SubParamData.status;
                console.log($scope.updatedStatus);
            };

            $scope.update = function (){
                $scope.updatedStatus.priviledges = $scope.priviledges;
              
                Alertify.confirm("Are you sure you want to change this deputy's status?", function(res){
                    if (res) {
                        $scope.updatedStatus.id = SubParamData.id;
                        console.log($scope.updatedStatus);
                        Service.update($scope.updatedStatus).then(function(res){
                            if (res.data.status) {
                                Alertify.success('Successfully updated status!');
                                SubParamData.data.status = ($scope.updatedStatus.newStatus) ? 'ON' : 'OFF';
                                $uibModalInstance.close(SubParamData.data);
                            } else {
                                Alertify.error('Error! Debug if you can. ;)');
                            }
                        });
                    }
                });
            };

            $scope.adaptPriviledges = function(data, adaptTo){
               
                if(adaptTo == "bool"){
                    angular.forEach(data , function(value, key){
                        (value == 1) ? data[key] = true : false;
                    });
                }else{
                    angular.forEach(data , function(value, key){
                        (value == true) ? data[key]  = 1 : 0;
                        (value == false) ? data[key] = 0 : 1;
                    });
                }

                return data; 
               
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
            $scope.save = function (isValid) {
                if (isValid) {
                    Alertify.confirm("Save data?",
                        function (res) {
                            if (res) {
                                blocker.start();

                                $timeout( function () {
                                    var tempData = {
                                        user_id :   SubParamData.user_id,
                                        tblData :   $scope.userDeputies
                                    };
                                    Service.save(tempData).then(function(res){
                                        if (res.data.status) {
                                            Alertify.success("Successfully saved data!");
                                            $uibModalInstance.close(res.data.tblData);
                                            blocker.stop();
                                        } else {
                                            Alertify.error("Error! Debug if you can. ;)");
                                            blocker.stop();
                                        }
                                    });
                                }, 1000);
                            }
                        }
                    );
                } else {
                    Alertify.error("All fields are required!");
                }
            };

            /**
             *`addMoreItems` Adding more items for infinite scroll
             */
            $scope.addMoreItems = function () {
                $scope.infiniteScroll.currentItems += $scope.infiniteScroll.numToAdd;
            };

            /**
             * `_init` Initialize first things first
             * @return {[void]}
             */
            _init = function () {
                // default settings
                Factory.autoloadSettings();

                // infinite scroll settings
                $scope.infiniteScroll              = {};
                $scope.infiniteScroll.numToAdd     = 20;
                $scope.infiniteScroll.currentItems = 20;

                $scope.userDeputies = [];

                _loadDetails();
            };

            /**
             * Run _init() func
             */
            _init();
        }]
    );
}); // end define
