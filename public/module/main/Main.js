define([
    'app'
], function(app) {
    app.factory('mainFactory', [
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
                alertify.theme('')
            }

            /**
             * `templates` Modal templates.
             * @type {Array}
             */
            Factory.templates = [
                'module/main/modals/change_password.html',
                'module/main/modals/update_profiles.html',
                'module/main/modals/update_image.html',
                'module/main/modals/birthday_greetings.html',
                'module/pi_files/modals/sub_modals/spouse.html',
                'module/pi_files/modals/sub_modals/educational_background.html',
                'module/pi_files/modals/sub_modals/license_certificate.html',
                'module/pi_files/modals/sub_modals/employment_history.html',
                'module/pi_files/modals/sub_modals/training_seminar.html'
            ];

            return Factory;
        }
    ]);

    app.service('mainService', [
        '$http',
        function($http) {
            var _this = this;

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            _this.getDetails = function() {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/getDetails');
            };

            /**
             * `getDetails` Query string that will get first needed details.
             * @return {[query]}
             */
            _this.getNotifications = function () {
                return $http.get(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/getNotifications');
            };

            
            /**
             * `signOut` Query string that will signout sessioned user.
             * @param  {[string]} input
             * @return {[object]}
             */
            _this.signOut = function() {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/signOut');
            }

            /**
             * `autoRefreshSession` Query string that will auto refresh session after 23 minutes.
             * @return {[array]}
             */
            _this.autoRefreshSession = function() {
                return $http.post(APP.SERVER_BASE_URL + '/App/Service/Main/MainService.php/autoRefreshSession');
            }

            // /**
            //  * `getDetails` Query string that will get first needed details.
            //  * @return {[route]}
            //  */
            // _this.getDetails = function () {
            //     return $http.get(APP.SERVER_BASE_URL + '/App/Service/PiFiles/PiFilesService.php/getDetails');
            // };
        }
    ]);

    app.controller('MainController', [
        '$scope',
        '$filter',
        '$timeout',
        '$location',
        '$window',
        '$rootScope',
        '$uibModal',
        'blockUI',
        // 'socket',
        'alertify',
        'mainFactory',
        'mainService',
        // function ($scope, $filter, $timeout, $location, $window, $rootScope, $uibModal, BlockUI, Socket, Alertify, Factory, Service) {
        function($scope, $filter, $timeout, $location, $window, $rootScope, $uibModal, BlockUI, Alertify, Factory, Service) {
            var _init, _loadDetails, _pluginFormat, _dstpNotif, _autoRefreshSession, blocker = BlockUI.instances.get('blockMain');

            _loadNotificationDetails = function () {
                Service.getNotifications().then(function (res) {


                    angular.forEach(res.data, function (value, key) {
                        // console.log(res.data[key]);
                        angular.forEach(res.data[key], function (value_c, key_c) {
                        
                            var temp_date = new Date(value_c.created_at);
                            var temp_current_date = new Date();
                                //Convert Date to Milliseconds 
                            var temp_date_of_Data_milliseconds = temp_date.getTime();
                            var temp_current_date_of_Data_milliseconds = temp_current_date.getTime();
                                //A Number, representing the number of milliseconds since midnight January 1, 1970
    
                            var date_difference = Math.round(( ( ( ( temp_current_date_of_Data_milliseconds - temp_date_of_Data_milliseconds) / 1000 ) / 60) / 60 ) / 24) ;
    
                            res.data[key][key_c].time_passed_log = date_difference;
                        });

                    });

                    $scope.notifications          = res.data.notifications;
                    $scope.tasks                  = res.data.tasks;
                    $scope.notifications_inactive = res.data.notifications_inactive;
                    $scope.tasks_inactive         = res.data.tasks_inactive;

                    // $scope.notifications.sort(_sortNotifFunction("created_at",'desc'));
                    // $scope.tasks.sort(_sortNotifFunction("created_at",'desc'));
                    // $scope.notifications_inactive.sort(_sortNotifFunction("created_at",'desc'));
                    // $scope.tasks_inactive.sort(_sortNotifFunction("created_at",'desc'));
                });

                // var currentdate = new Date(); 
                // var datetime = "Last Sync: " + currentdate.getDate() + "/"
                // + (currentdate.getMonth()+1)  + "/" 
                // + currentdate.getFullYear() + " @ "  
                // + currentdate.getHours() + ":"  
                // + currentdate.getMinutes() + ":" 
                // + currentdate.getSeconds();

                // console.log(datetime);

                // console.log("I'VE LOADED NOTIFICATIONS");
            }

            $scope.goToTransacDoc = function(prs, aob, po, withdrawal, unq_notif, unq_task){
                if(prs = '1'){
                    $location.path('/main/my_prs');
                }else if(aob = '1'){

                }else if(po = '1'){

                }else if(withdrawal = '1'){

                }else if(unq_notif = '1'){
                    
                }else if(unq_task = '1'){

                }else{
                    
                }
            }
            
            _sortNotifFunction = function(property, order){
                var sort_order = 1;
                if(order === "desc"){
                    sort_order = -1;
                }
                return function (a, b){
                    // a should come before b in the sorted order
                    if(a[property] < b[property]){
                            return -1 * sort_order;
                    // a should come after b in the sorted order
                    }else if(a[property] > b[property]){
                            return 1 * sort_order;
                    // a and b are the same
                    }else{
                            return 0 * sort_order;
                    }
                }
            }

            /**
             * `_loadDetails` Loading of details needed in `main` route.
             * @return {mixed}
             */
            _loadDetails = function() {
                Service.getDetails().then(function(res) {
                    var mname;
                    console.log(res.data);
                    $rootScope.access_lists = angular.copy(res.data.accessed_sub_menus);
                    $rootScope.check_session = angular.copy(res.data.check_session);

                    $scope.employees = angular.copy(res.data.employees);
                    $scope.head_employees = angular.copy(res.data.head_employees);

                    // if (!$rootScope.check_session) {
                    //     $location.path('/');
                    // } else {
                    mname = (res.data.employee_informations[0].mname != "") ? $filter('limitTo')(res.data.employee_informations[0].mname, 1, 0) + '. ' : "";

                        $scope.employee          = angular.copy(res.data.employee_informations[0]);
                        $scope.employee.photo    = ($scope.employee.user_image == '' || $scope.employee.user_image == null || $scope.employee.user_image == 'null') ? '' : $scope.employee.user_image;
                        $scope.employee.fullname = res.data.employee_informations[0].fname + ' ' + mname + res.data.employee_informations[0].lname;
                    //     $location.path('/main/dashboard');
                    // }

                    // Socket.emit('online-user', {
                    //     'full_name' : $scope.employee.full_name
                    // });


                    $(document).on('keypress', function (evt) {

                        if (evt.keyCode === 13) {
                            $('#collapse1').unbind('collapse1');
                        }

                    });

                    $(document).keypress(function (e) {



                        if ($('#collapse1').attr("aria-expanded") == "true" && (e.keycode == 13 || e.which == 13)) {
                            var message = document.getElementById('input_message').value;

                            var add_chat_message_details = {
                                "username": $scope.employee.fullname,
                                "message": message,
                                "date": $scope.getCurrentDate
                            };

                            // console.log(add_chat_message_details);
                            // Socket.emit('new_message', add_chat_message_details);
                            document.getElementById('input_message').value = "";


                        }


                    })

                    // _dstpNotif();
                });
            };

            $scope.toNotifMod = function() {
                $location.path('/main/user_notifications');
            }
            $scope.toTaskMod = function() {
                $location.path('/main/user_tasks');
            }
            $scope.toDevTool = function() {
                $location.path('/main/developer_tool');
            }

            $scope.loadHappyBirthday = function () {
                var paramData, modalInstance;

                paramData = {

                };

                modalInstance = $uibModal.open({
                    animation: true,
                    keyboard: false,
                    backdrop: 'static',
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'birthday_greetings.html',
                    controller: 'BirthdayGreetingsController',
                    size: 'md',
                    resolve: {
                        paramData: function () {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function (res) {

                }, function (res) {
                    // Result when modal is dismissed
                });
            }

            /**
             * `_dstpNotif` Implementation of Desktop Notification.
             * @return {[void]}
             */
            _dstpNotif = function() {
                var originUrl = $scope.server.base_origin + '/arpeggio_dev';
                // var originUrl = $scope.server.base_origin + '/arpeggio';

                if (!("Notification" in window)) {
                    console.error("Desktop notifications is not supported by this browser. Try another.");

                    return;
                } else if (Notification.permission !== "granted") {
                    Notification.requestPermission();
                } else {
                    var notification = new Notification('Construction Planning', {
                        icon: 'http://cdn.sstatic.net/stackexchange/img/logos/so/so-icon.png',
                        body: "You have a project estimate for approval!",
                    });

                    notification.onclick = function() {
                        console.log(originUrl);
                        // $location.path('/main/construction_planning');
                        // $window.location.href = originUrl + "/public/#/main/construction_planning";
                    };
                }
            }

            /**
             * `externalLink` Loading of external module.
             * @return {[string]}
             * arpeggio link
             * http://localhost/arpeggio_dev/public/index.html#!/main/dashboard/
             * node link
             * http://localhost/node/public/#/main/module
             */
            $scope.externalLink = function(link) {
                // local
                // var originUrl = $scope.server.base_origin + '/node';
                var originUrl = $scope.server.base_origin + '/node_dev';

                // server
                // var originUrl = $scope.server.base_origin + '/web/node';

                // vps
                // var originUrl = "http://node.scdcapp.com";

                $window.location.href = originUrl + "/public/#/main/" + link;
            }

            /**
             * `signOut` signOut employee and destroy session session.
             * @return {[mixed]}
             */
            $scope.signOut = function() {
                // blocker.start('Signing out...');

                Service.signOut().then(function(res) {
                    $timeout(function() {
                        // Socket.emit('offline-user', {
                        //     'full_name' : $scope.employee.full_name
                        // });
                        // Socket.emit('disconnect_user', {'full_name' : $scope.employee.full_name});

                        $rootScope.check_session = false;
                        $location.path('/');
                    }, 100);
                });
            };

            /**
             * `changePassword` Changing of password.
             * @return {[mixed]}
             */
            $scope.changePassword = function() {
                var paramData, modalInstance;

                paramData = {};

                modalInstance = $uibModal.open({
                    animation: true,
                    keyboard: false,
                    backdrop: 'static',
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'change_password.html',
                    controller: 'ChangePasswordController',
                    size: 'md',
                    resolve: {
                        paramData: function() {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function(res) {
                    Alertify.confirm("We recommend to re-login your account for security purposes.",
                        function(res) {
                            if (res) {
                                $scope.signOut();
                            }
                        }
                    );
                }, function(res) {
                    // Result when modal is dismissed
                });
            }

            /**
             * `updateProfile` Updating of profile.
             * @return {[mixed]}
             */
            $scope.updateProfile = function(data) {
                var paramData, modalInstance;

                paramData = {
                    'data': $scope.employees[0],
                    'id': $scope.employees[0].id,
                    'head_employees': $scope.head_employees
                };

                modalInstance = $uibModal.open({
                    animation: true,
                    keyboard: false,
                    backdrop: 'static',
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'update_profiles.html',
                    controller: 'UpdateProfilesController',
                    size: 'lg',
                    resolve: {
                        paramData: function() {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function(res) {
                    // var index = table.DataTable().row('.selected').index();

                    // table.DataTable().row(index).data(res).draw();
                }, function(res) {
                    // Result when modal is dismissed
                });
            }

            /**
             * `updateProfile` Updating of profile.
             * @return {[mixed]}
             */
            $scope.updateImage = function(data) {
                var paramData, modalInstance;
                // console.log($scope.employees);
                paramData = {
                    'data': $scope.employees[0],
                    'id': $scope.employees[0].id,
                    'head_employees': $scope.head_employees,
                    'server_ftp_url': $scope.photoConfig
                };

                modalInstance = $uibModal.open({
                    animation: true,
                    keyboard: false,
                    backdrop: 'static',
                    ariaLabelledBy: 'modal-title',
                    ariaDescribedBy: 'modal-body',
                    templateUrl: 'update_image.html',
                    controller: 'UpdateImageController',
                    size: 'md',
                    resolve: {
                        paramData: function() {
                            return paramData;
                        }
                    }
                });

                modalInstance.result.then(function(res) {
                    // var index = table.DataTable().row('.selected').index();

                    // table.DataTable().row(index).data(res).draw();
                }, function(res) {
                    // Result when modal is dismissed
                });
            }

            /**
             * `getCurrentDate` get current date.
             * @return {[mixed]}
             */
            $scope.getCurrentDate = function () {
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth() + 1; //January is 0!
                var yyyy = today.getFullYear();

                if (dd < 10) {
                    dd = '0' + dd
                }

                if (mm < 10) {
                    mm = '0' + mm
                }

                today = mm + '/' + dd + '/' + yyyy;
                return today;

            }

            /**
             * `Toggle Chat` 
             * @return {[mixed]}
             */
            $scope.ToggleChat = function () {
                if ($scope.ChatToggle == true) {
                    $scope.ChatToggle = false;
                    // Socket.emit('toggle_new_user', {
                    //     "user": $scope.employee.fullname,
                    //     "status": "left"
                    // });
                } else {
                    $scope.ChatToggle = true;
                    // Socket.emit('toggle_new_user', {
                    //     "user": $scope.employee.fullname,
                    //     "status": "join"
                    // });
                }
            }


            /**
             * `typeListener` Listen to the Changes in Message Input
             * @return {[mixed]}
             */
            $scope.typeListener = function () {
                // blocker.start('Signing out...');

                console.log($scope.input_message_body);
            };


            /**
             * `_autoRefreshSession` Auto refresh session after 23 minutes.
             * @return {[void]}
             */
            _autoRefreshSession = function() {
                $timeout(function() {
                    Service.autoRefreshSession().then(function() {

                        _autoRefreshSession();
                    });
                }, 1380000);
            };

            /**
             * `_init` First things first.
             * @return {mixed}
             */
            _init = function() {
                // default settings
                Factory.autoloadSettings();
                $scope.photoConfig = $scope.server.ftp_url;

                $scope.contentheader = {
                    title: '',
                    module: '',
                    subModule: '',
                    subModuleChild: '',
                };
                $scope.ChatToggle = false;
                $scope.templates = Factory.templates;
                $scope.employee = {};
                $scope.no_of_clients = [];

                // if ($location.path() === "/main/hdf_visitor") $scope.isHdfVisitor = true;
                // else $scope.isHdfVisitor = false;

                _loadDetails();
                _loadNotificationDetails();
                // Socket.emit('online-user', {'full_name' : $scope.employee.fullname});


                /* Socket.on('newclientconnect', function (data) {

                    console.log("CLIENT");
                    $scope.no_of_clients.push(data.full_name);
                    console.log($scope.no_of_clients);


                }); */


                // Socket Client: Function to Tell Chat Use is Active
                /* Socket.on('toggleUserActive', function (user) {

                    console.log(user);

                    // Create Div ROW for message Body
                    var divRow = document.createElement('div');
                    divRow.id = 'block';
                    divRow.className = 'row';

                    // Create Username Text in Small Tag 
                    var divClassMessageBody_UserOnline = document.createElement('small');
                    divClassMessageBody_UserOnline.className = 'text-right';
                    if (user.status == "join") {
                        divClassMessageBody_UserOnline.setAttribute("style", "color: green;");
                        divClassMessageBody_UserOnline.innerHTML = user.user + " joined the chat.";
                    } else {
                        divClassMessageBody_UserOnline.setAttribute("style", "color: red;");
                        divClassMessageBody_UserOnline.innerHTML = user.user + " left the chat.";
                    }
                    divRow.appendChild(divClassMessageBody_UserOnline);

                    document.getElementById('messagebox_body').appendChild(divRow);

                }); */

                /* Socket.on('show_message', function (data) {
                    // $scope.chat_Logs.push(data);

                    console.log($scope.chat_Logs);


                    // Create Div ROW for message Body
                    var divRow = document.createElement('div');
                    divRow.id = 'block';
                    divRow.className = 'row';


                    // Create Div class for Image
                    var divClassPic = document.createElement('div');
                    divClassPic.className = 'col-md-3 col-xs-3';


                    // Create Image for User
                    var divClassPic_Image = document.createElement('img');
                    divClassPic_Image.src = "http://www.bitrebels.com/wp-content/uploads/2011/02/Original-Facebook-Geek-Profile-Avatar-1.jpg";
                    divClassPic_Image.style.height = '50px';
                    divClassPic_Image.style.width = '50px';


                    divClassPic.appendChild(divClassPic_Image);


                    // Create Div class for Message Body
                    var divClassMessageBody = document.createElement('div');
                    divClassMessageBody.className = 'col-md-9 col-xs-9 text-left';

                    // Create Username Text in Small Tag 
                    var divClassMessageBody_smallTest_Username = document.createElement('small');
                    divClassMessageBody_smallTest_Username.className = 'text-right';
                    divClassMessageBody_smallTest_Username.setAttribute("style", "color: blue;");

                    divClassMessageBody_smallTest_Username.innerHTML = data.username;

                    // Create Message Text in Div 
                    var divClassMessageBody_Message = document.createElement('div');
                    divClassMessageBody_Message.setAttribute("style", "white-space: normal !important; ");
                    // divClassMessageBody_Message.style.whiteSpace = "normal !important";
                    divClassMessageBody_Message.innerHTML = data.message;

                    // Add divClassMessageBody Content
                    divClassMessageBody.appendChild(divClassMessageBody_smallTest_Username);
                    divClassMessageBody.appendChild(divClassMessageBody_Message);


                    var divClassMessageBodyDivider = document.createElement('hr');

                    // Add divRow Content
                    divRow.appendChild(divClassPic);
                    divRow.appendChild(divClassMessageBody);

                    document.getElementById('messagebox_body').appendChild(divClassMessageBodyDivider);
                    document.getElementById('messagebox_body').appendChild(divRow);


                    var element = document.getElementById('messagebox_body');
                    element.scrollTop = element.scrollHeight - element.clientHeight;

                }); */
                _autoRefreshSession();

            };





            _init();
        }
    ]);
}); // end define