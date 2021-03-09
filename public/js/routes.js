define(function() {
    var routes = {};

    routes.states = [
        {
            "name": "/",
            "url": "/",
            "controller": "LoginController",
            "templateUrl": "/public/module/login/login.html",
            "deps": "/public/module/login/index.js"
        },
        {
            "name": "main",
            "url": "/main",
            "controller": "MainController",
            "templateUrl": "/public/module/main/main.html",
            "deps": "/public/module/main/index.js"
        },
        {
            "name": "main.dashboard",
            "url": "/dashboard",
            "controller": "DashboardController",
            "templateUrl": "/public/module/dashboard/dashboard.html",
            "deps": "/public/module/dashboard/index.js"
        },
        {
            "name": "main.user_access_configuration",
            "url": "/user_access_configuration",
            "controller": "UserAccessConfigurationController",
            "templateUrl": "/public/module/user_access_configuration/user_access_configuration.html",
            "deps": "/public/module/user_access_configuration/index.js"
        }
    ];

    return routes;
});