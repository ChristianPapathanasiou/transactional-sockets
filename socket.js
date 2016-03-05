var server = require('http').createServer();
var io = require('socket.io')(server);

io.on('connection', function(socket){
  console.log('a user connected');
});

var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('project-transaction');

redis.on('message', function(channel, message){
    console.log('message received');
    console.log(channel, message);
    message = JSON.parse(message);

    io.emit(channel, message.data);
});

server.listen(3000);
