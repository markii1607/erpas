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
            "name": "main.signatories_configuration",
            "url": "/signatories_configuration",
            "controller": "SignatoriesConfigurationController",
            "templateUrl": "/public/module/signatories_configuration/signatories_configuration.html",
            "deps": "/public/module/signatories_configuration/index.js"
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
        {
            "name": "main.no_property_dec",
            "url": "/no_property_dec",
            "controller": "NoPropertyDecController",
            "templateUrl": "/public/module/no_property_dec/no_property_dec.html",
            "deps": "/public/module/no_property_dec/index.js"
        },
        {
            "name": "main.prop_tax_dec",
            "url": "/prop_tax_dec",
            "controller": "PropTaxDecController",
            "templateUrl": "/public/module/prop_tax_dec/prop_tax_dec.html",
            "deps": "/public/module/prop_tax_dec/index.js"
        },
        {
            "name": "main.properties_dec",
            "url": "/properties_dec",
            "controller": "PropertiesDecController",
            "templateUrl": "/public/module/properties_dec/properties_dec.html",
            "deps": "/public/module/properties_dec/index.js"
        },
        {
            "name": "main.treasurer_td_monitoring",
            "url": "/treasurer_td_monitoring",
            "controller": "TreasurerTdMonitoringController",
            "templateUrl": "/public/module/treasurer_td_monitoring/treasurer_td_monitoring.html",
            "deps": "/public/module/treasurer_td_monitoring/index.js"
        },
        {
            "name": "main.qr_assessment_report",
            "url": "/qr_assessment_report",
            "controller": "QRAssessmentReportController",
            "templateUrl": "/public/module/qr_assessment_report/qr_assessment_report.html",
            "deps": "/public/module/qr_assessment_report/index.js"
        },
        {
            "name": "main.rpt_collection_report",
            "url": "/rpt_collection_report",
            "controller": "RptCollectionReportController",
            "templateUrl": "/public/module/rpt_collection_report/rpt_collection_report.html",
            "deps": "/public/module/rpt_collection_report/index.js"
        },
        {
            "name": "main.consolidated_rpt_report",
            "url": "/consolidated_rpt_report",
            "controller": "ConsolidatedRptReportController",
            "templateUrl": "/public/module/consolidated_rpt_report/consolidated_rpt_report.html",
            "deps": "/public/module/consolidated_rpt_report/index.js"
        },
    ];

    return routes;
});