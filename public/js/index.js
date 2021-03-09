var express = require('express');
var app     = express();
var server  = require('http').Server(app);
var io      = require('socket.io')(server);

app.use(express.static(__dirname + '/..'));

io.on('connection', function(socket) {

    /**
     * socket that will logs the user who is online in the system.
     */
    socket.on('online-user', function (user) {
        console.log(user.full_name, 'is online.');
    });

    /**
     * socket that will logs the user who is offline in the system.
     */
    socket.on('offline-user', function (user) {
        console.log(user.full_name, ' is offline.');
    });
});

server.listen(3000, function() {
    console.log('server up and running at 3000 port');
});