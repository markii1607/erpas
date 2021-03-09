define([
    'angular',
    'js/routes.js',
    'uiRoute',
    // 'bootstrap',
    // 'adminLTE',
    'coreui',
    'alertify',
    'ngBlockUI',
    'ngAnimate',
    'uiBootstrap',
    'ngSanitize',
    'inputMask',
    'ngInputModified',
    'uiSelect',
    'ngDropzone',
    'ngMoment',
    'dhtmlGantt',
    'ngFileUpload',
    'lodash'
], function(angular, ngRoutes) {
    'use strict';

    var module = angular.module('scdc', [
        'ui.router',
        'ngAnimate',
        'ngAlertify',
        'blockUI',
        'ngSanitize',
        'ui.bootstrap',
        'ngInputModified',
        'ui.select',
        'thatisuday.dropzone',
        'angularMoment',
        'ngFileUpload'
    ]);

    /**
     *
     * @param dependencies
     * @returns {{resolver: *[]}}
     * @private
     */
    var _resolver = function(dependencies) {
        return {
            resolver: [
                '$q',
                function($q) {
                    var deferred = $q.defer();

                    require([dependencies], function() {
                        deferred.resolve();
                    });

                    return deferred.promise;
                } // end function
            ]
        }; // end return
    }; // end _resolver

    module.config([
        '$controllerProvider',
        '$compileProvider',
        '$filterProvider',
        '$provide',
        '$urlRouterProvider',
        '$stateProvider',
        '$httpProvider',
        'blockUIConfig',
        function($controllerProvider, $compileProvider, $filterProvider, $provide, $routeProvider, $stateProvider, $httpProvider, blockUIConfig) {
            var initInjector, $http, base_url;

            module.controller = $controllerProvider.register;
            module.directive = $compileProvider.directive;
            module.filter = $filterProvider.register;
            module.factory = $provide.factory;
            module.service = $provide.service;

            /**
             * server and development implementation.
             */
            path_name = window.location.pathname.match(/.*(erpas\/)/gi)[0];
            base_url = path_name.slice(0, path_name.length - 1);

            /**
             * vps implementation
             */
            // base_url     = "http://arpeggio.scdcapp.com";

            initInjector = angular.injector(['ng']);
            $http = initInjector.get('$http');

            // dynamic routes
            angular.forEach(ngRoutes.states, function(rVal, rKey) {
                $stateProvider
                    .state(rVal.name, {
                        url: rVal.url,
                        templateUrl: base_url + rVal.templateUrl,
                        resolve: _resolver(base_url + rVal.deps),
                        controller: rVal.controller
                    });

                $routeProvider.otherwise('/');
                // $routeProvider.otherwise('/main/dashboard');
            });


            // Disable automatically blocking of the user interface
            blockUIConfig.autoBlock = false;

            // evaluate our code specially http request and call a digest afterwards
            $httpProvider.useApplyAsync(true);
        } // end function
    ]); // end config

    module.controller('IndexController', [
        '$scope',
        '$window',
        '$location',
        function($scope, $window, $location) {
            var _init, _checkDevTool, _promptMessages;

            /**
             * `_checkDevTool` Check dev tool for restriction purposes.
             * @return {[redirect]}
             */
            _checkDevTool = function() {
                var element = new Image();

                Object.defineProperty(element, 'id', {
                    get: function() {
                        /* TODO */
                        window.location.href = "../restriction.html";
                    }
                });
            };

            /**
             * `_promptMessages` Centralize promt messages.
             * @return {[object]}
             */
            _promptMessages = function() {
                $scope.promptMsg = {
                    save: "Do you want to save this record?",
                    update: "Are you sure you want to save the changes?",
                    remove: "Proceeding will permanently delete the record/s. Continue anyway?",
                    cancel: "Are you sure you want to cancel?",
                };
            }

            /**
             * `_init` First things first.
             * @return {mixed}
             */
            _init = function() {
                /**
                 * server and development implementation configuration
                 */
                $scope.server = {
                    'base_origin': new $window.URL($location.absUrl()).origin,
                    'base_url'    : '/erpas',
                };

                $scope.global = {
                    'prev_route': ''
                };

                // _checkDevTool();
                _promptMessages();
            };

            _init();
        }
    ]);

    module.filter('titleCase', function() {
        return function(input) {
            input = input || '';
            return input.replace(/\w\S*/g, function(txt) { return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase(); });
        };
    })

    module.run([
        '$timeout',
        '$rootScope',
        '$state',
        function($timeout, $rootScope, $state) {

            $rootScope.$on('$stateChangeStart',
                function(event, toState, toParams, fromState, fromParams) {
                    var accessGranted = false;

                    console.log('Running');
                    console.log($rootScope.check_session);

                    $timeout(function() {
                        // if ($rootScope.check_session != undefined || $rootScope.check_session) {
                        //     if ($rootScope.access_lists != undefined) {
                        //         if ($rootScope.access_lists.length > 0) {
                        //             angular.forEach($rootScope.access_lists, function (alVal, alKey) {
                        //                 var linkName = "main." + alVal.link;

                        //                 if (toState.name == linkName || (toState.name == '/dashboard')) {
                        //                     accessGranted = true;
                        //                 }

                        //             });

                        //             $timeout( function () {
                        //                 console.log(accessGranted);
                        //                 // if (toState.url != '/dashboard') {
                        //                     // if (!accessGranted) {
                        //                     //     $state.transitionTo(fromState.name);
                        //                     // }
                        //                 // }
                        //             }, 100);
                        //         }
                        //     }
                        // }
                    });
                }
            );

            $rootScope.$on('$stateChangeSuccess',
                function(event, toState, toParams, fromState, fromParams) {
                    console.log('Success');
                    console.log($rootScope.check_session);
                    console.log(toState.url);
                    console.log(toState.name);

                    $timeout(function() {
                        if ($rootScope.check_session === true) {
                            if (toState.url == '/' || toState.url == '/main') {
                                $state.transitionTo('main.dashboard');
                            }
                        }

                        if ($rootScope.check_session === false) {
                            $state.transitionTo('/');
                        }

                        // if ($rootScope.check_session === undefined || !$rootScope.check_session) {
                        //     $state.transitionTo('/');
                        // } else if (toState.url == '/' && $rootScope.check_session) {
                        //     $state.transitionTo(fromState.name);
                        // } else if (toState.url == '/main') {
                        //     if (($rootScope.check_session === undefined || !$rootScope.check_session)) {
                        //         $state.transitionTo('/');
                        //     } else {
                        //         $state.transitionTo(fromState.name);
                        //     }
                        // } else if (toState.url == '/main/dashboard') {
                        //     if (($rootScope.check_session === undefined || !$rootScope.check_session)) {
                        //         $state.transitionTo('/');
                        //     } else {
                        //         $state.transitionTo(fromState.name);
                        //     }
                        // }
                    });
                }
            );

            /**
             * Service Worker Configuration
             * @param  {[]}
             * @return {[]}
             */
            if ('serviceWorker' in navigator) {
                // navigator.serviceWorker.register('./serviceworker.js', { scope: '/arpeggio/public/' }).then(
                    navigator.serviceWorker.register('./serviceworker.js', {scope: '/arpeggio_template/public/'}).then(
                    function(registration) {
                        var serviceWorker;

                        if (registration.installing) {
                            serviceWorker = registration.installing;
                        } else if (registration.waiting) {
                            serviceWorker = registration.waiting;
                        } else if (registration.active) {
                            serviceWorker = registration.active;
                        }

                        if (serviceWorker) {
                            console.log("ServiceWorker phase:", serviceWorker.state);

                            serviceWorker.addEventListener('statechange', function(e) {
                                console.log("ServiceWorker phase:", e.target.state);
                            });
                        }
                    }
                ).catch(
                    function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    }
                );
            } else {
                console.log("this browser does NOT support service worker");
            }
        }
    ]);

    module.directive('modalMovable', ['$document',
        function($document) {
            return {
                restrict: 'AC',
                link: function(scope, iElement, iAttrs) {
                    var startX = 0,
                        startY = 0,
                        x = 0,
                        y = 0;

                    var dialogWrapper = iElement.parent();

                    dialogWrapper.css({
                        position: 'relative'
                    });

                    dialogWrapper.on('mousedown', function(event) {
                        // Prevent default dragging of selected content
                        event.preventDefault();
                        startX = event.pageX - x;
                        startY = event.pageY - y;
                        $document.on('mousemove', mousemove);
                        $document.on('mouseup', mouseup);
                    });

                    function mousemove(event) {
                        y = event.pageY - startY;
                        x = event.pageX - startX;
                        dialogWrapper.css({
                            top: y + 'px',
                            left: x + 'px'
                        });
                    }

                    function mouseup() {
                        $document.unbind('mousemove', mousemove);
                        $document.unbind('mouseup', mouseup);
                    }
                }
            };
        }
    ]);

    module.directive('extChange', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, modelCtrl) {
                var lastUserInput = modelCtrl.masterValue;

                modelCtrl.$viewChangeListeners.push(function() {
                    //console.log("viewValue", modelCtrl.$viewValue);
                    //console.log("modelValue", modelCtrl.$modelValue);
                    lastUserInput = modelCtrl.masterValue;
                });

                scope.$watch(attrs.ngModel, function(value) {
                    if (value !== lastUserInput) {
                        scope.$eval(attrs.extChange, { $value: modelCtrl.masterValue });
                    }
                    console.log("ng-model watch ", value);
                    console.log("lastUserInput", lastUserInput);
                    console.log("temp", modelCtrl.masterValue);
                });
            }
        }
    });

    module.filter('encodeURIComponent', function() {
        return window.encodeURIComponent;
    });

    return module;
});
