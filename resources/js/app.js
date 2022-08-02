// require('./bootstrap');
//
// require('alpinejs');


import Echo from "laravel-echo";
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'laravel_rdb',
    cluster: 'ap2',
    wsHost: window.location.hostname,
    // wssHost: window.location.hostname,
    // encrypted: true,
    wsPort: 6001,
    // wssPort: 6001,
    forceTLS: false,
    disableStats: true,
    // enabledTransports: ['ws', 'wss']
});
