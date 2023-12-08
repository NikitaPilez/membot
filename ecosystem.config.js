module.exports = {
    apps: [
        {
            name: 'schedule',
            interpreter: '/usr/bin/php8.1',
            script: './artisan',
            args: 'schedule:work',
            instances: 1,
            autorestart: true,
            watch: false,
            max_memory_restart: '256M',
            kill_timeout: 20000,
            restart_delay: 5000,
            max_restarts: 15,
        },
        {
            name: 'queuestat',
            interpreter: '/usr/bin/php8.1',
            script: './artisan',
            args: 'queue:work --queue=stat',
            instances: 1,
            autorestart: true,
            watch: false,
            max_memory_restart: '256M',
            kill_timeout: 20000,
            restart_delay: 5000,
            max_restarts: 15,
        },
        {
            name: 'queuecontent',
            interpreter: '/usr/bin/php8.1',
            script: './artisan',
            args: 'queue:work --queue=content',
            instances: 1,
            autorestart: true,
            watch: false,
            max_memory_restart: '256M',
            kill_timeout: 20000,
            restart_delay: 5000,
            max_restarts: 15,
        },
    ],
};
