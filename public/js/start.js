define([
    'require',
    'angular',
    'app'
], function (require, ng) {
    'use strict';
    require(['domReady!'], function (document) {
        try {
            ng.bootstrap(document, ['erpas']);
        } catch(e) {
            console.error(e.stack || e.message || e);
        }
    });
});