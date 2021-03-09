var express = require('express');
var app     = express();
var server  = require('http').Server(app);
var io      = require('socket.io')(server);

app.use(express.static(__dirname + '/..'));


var clients = 0;

io.on('connection', function(socket) {
    // clients++;
 
    // socket.broadcast.emit('newclientconnect',{ 'description': clients + ' clients connected!'})
    
    socket.on('connect_user', function (client) {
        clients++;
        console.log(client + " is Online" );
        console.log(clients + " CLIENTS");
        io.emit('newclientconnect',{ 'full_name': client });
     });

    socket.on('disconnect_user', function (client) {
       clients--;
       console.log(client + " is Logged Off" );
       console.log(clients + " CLIENTS");
       io.emit('newclientconnect',{ 'no_of_clients': clients });
    });

    /**
     * socket that will logs the user who is online in the system.
     */
    socket.on('online-user', function (user) {
        console.log(user.full_name, 'is online.');

        // socket.broadcast.emit("toggleUserActiveOnline", user.full_name)
    });

    /**
     * socket that will logs the user who is offline in the system.
     */

    socket.on('offline-user', function (user) {
        console.log(user.full_name, 'is offline.');
        // socket.broadcast.emit("toggleUserActiveOffline", user.full_name)
    });

    /**
     * socket that will log the active user in the chat.
     */
    socket.on('toggle_new_user', function (chat_user_details) {
            io.emit("toggleUserActive", chat_user_details);
    });

    /**
     * 
     */
    socket.on('prs-transaction', function (data) {
        io.emit('prs-approval', {
            'type'         : data.type,
            'recipient_id' : data.recipient_id,
            'param_data'   : data.param_data
        });
    });

    socket.on('change_username', function (user_data) {
        socket.username = data.username;
    });

    socket.on('new_message', function (data_message) {
        console.log(data_message);
        io.emit('show_message', data_message);
    });

    // socket.broadcast.emit("showMessage", { name: 'Anonymous', message: 'A NEW USER HAS JOINED' })

    // socket.on('sendMessage', message => io.emit('showMessage', message))

    // socket.on('new-transaction', function(transaction) {
    //     if (transaction.type == 'admin_work_order') {
    //         io.emit('notif-admin-work-order', {
    //             message : 'New Transaction',
    //             data    : transaction
    //         });
    //     }
    // });
});

server.listen(3000, function() {
    console.log('server up and running at 3000 port');
});