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
        },
        {
            "name": "main.market_value_classifications",
            "url": "/market_value_classifications",
            "controller": "MarketValueClassificationsController",
            "templateUrl": "/public/module/market_value_classifications/market_value_classifications.html",
            "deps": "/public/module/market_value_classifications/index.js"
        },
        {
            "name": "main.market_value_subclassifications",
            "url": "/market_value_subclassifications",
            "controller": "MarketValueSubClassificationsController",
            "templateUrl": "/public/module/market_value_subclassifications/market_value_subclassifications.html",
            "deps": "/public/module/market_value_subclassifications/index.js"
        },
        {
            "name": "main.market_value_revisions",
            "url": "/market_value_revisions",
            "controller": "MarketValueRevisionsController",
            "templateUrl": "/public/module/market_value_revisions/market_value_revisions.html",
            "deps": "/public/module/market_value_revisions/index.js"
        },
        {
            "name": "main.barangays_config",
            "url": "/barangays_config",
            "controller": "BarangaysConfigController",
            "templateUrl": "/public/module/barangays_config/barangays_config.html",
            "deps": "/public/module/barangays_config/index.js"
        },
        {
            "name": "main.tax_declaration",
            "url": "/tax_declaration",
            "controller": "TaxDeclarationController",
            "templateUrl": "/public/module/tax_declaration/tax_declaration.html",
            "deps": "/public/module/tax_declaration/index.js"
        },
    ];

    return routes;
});