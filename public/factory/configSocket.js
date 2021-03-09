/**
 * `socket` For external system implementation.
 *
 *  Note : to link socket to other system, angular.module() must be the same to all external and parent system.
 */
angular.module('scdc').factory('socket', [
    function () {
        var socket = io.connect('http://127.0.0.1:3000');
        // var socket = io.connect('http://172.25.150.29:3000');
        // var socket = io.connect('http://192.168.0.116:3000');
        // var socket = io.connect('http://192.168.43.249:3000');
        // var socket = io.connect('http://192.168.21.16:3000');
        // var socket = io.connect('http://192.168.50.10:3000');

        return {
            on   : function(eventName, callback) {
                socket.on(eventName, callback);
            },
            emit : function(eventName, data) {
                socket.emit(eventName, data);
            }
        };
    }
]);

/**
 * `socket` For internal usage that uses require.js plugin.
 */
// define([
//     'app'
// ], function (app) {
//     app.factory('socket', [
//         function () {
//             var socket = io.connect('http://127.0.0.1:3000');
//             // var socket = io.connect('http://192.168.32.88:3000');
//             // var socket = io.connect('http://192.168.30.97:3000');

//             return {
//                 on   : function(eventName, callback) {
//                     socket.on(eventName, callback);
//                 },
//                 emit : function(eventName, data) {
//                     socket.emit(eventName, data);
//                 }
//             };
//         }
//     ]);
// });