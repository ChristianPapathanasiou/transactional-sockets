# Transactional Sockets
This is a Proof of Concept for a payment system to reserve an amount of money while a user fills out their payment information.

# Virtual Machine Environment
Uses [Homestead](https://laravel.com/docs/master/homestead).
Uses Event Scheduler to run a job which clears expired transactions from the db

# Tools
Uses [Socket.io](https://socket.io) for real time values.