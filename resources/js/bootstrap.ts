import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Initialize axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

//Don't include this line in production
Pusher.logToConsole = true;


try {
    // Debug logging for environment variables
    console.log('Pusher Config:', {
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
    });

    if (!import.meta.env.VITE_PUSHER_APP_KEY) {
        throw new Error('Pusher app key is not defined in environment variables');
    }

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        encrypted: true,
        disableStats: true,
        authEndpoint: '/broadcasting/auth',
    });

    // Connection monitoring
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Successfully connected to Pusher');
    });

    window.Echo.connector.pusher.connection.bind('error', (error: any) => {
        console.error('Pusher connection error:', error);
    });

        
} catch (error) {
    console.error('Echo initialization error:', error);
}

