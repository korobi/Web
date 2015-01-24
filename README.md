# Laravel refactor

**Staging:** https://dev.vq.lc/

**PR:** https://github.com/kashike/yukai-web/pull/1

Copyright 2014 - 2015 Kashike

Working on adapting the system to work with Laravel.

The code found in this respository is private, and is not to be shared with anyone.

## Environment configurations

Be prepared to create a `.env` file in your root directory! You should cover the following settings:

````
QUEUE_DRIVER=
BEANSTALK_HOST=
BEANSTALK_QUEUE=
CACHE_DRIVER=
APP_DEBUG=
APP_KEY=
GITHUB_OAUTH_ID=
GITHUB_OAUTH_SECRET=
GITHUB_REDIRECT_URL=
DB_TYPE=
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
SESSION_DRIVER=
APP_URL=
````

`QUEUE_DRIVER` should be set to "null", "sync", "database", beanstalkd", "sqs", "iron",  or "redis". If you're not using beanstalk, you'll probably want to modify the [config/queue.php](https://github.com/korobi/Web/blob/laravel/config/queue.php) file so that you have some environmental variables you can use to specify the IP, port etc.

`CACHE_DRIVER` should be set to "apc", "file", "array", "database", "memcached" or "redis". If you're not using one of the simpler drivers (e.g. "file") you'll probably want to look at [config/cache.php](https://github.com/korobi/Web/blob/laravel/config/cache.php) and potentially add further environmental variables so you can specify the necessary connection details.

`SESSION_DRIVER` should be set to one of "file", "cookie", "database", "apc", "memcached", "redis" and "array". No further configuration should be necessary relating to these choices but you can modify the lifetime and other similar details in [config/session.php](https://github.com/korobi/Web/blob/laravel/config/session.php).

`DB_TYPE` should be set to one of the [supported database connections](https://github.com/korobi/Web/blob/laravel/config/database.php#L47) in the config/database.php file. This could be "mysql", "sqlite", "pgsql" or "sqlsrv". If you're not using MySQL you'll probably want to change the config file to add in the env variables for the other connection types. `DB_HOST`, `DB_USERNAME` and `DB_PASSWORD` are currently specific to MySQL.

`APP_DEBUG` should be set to a boolean specifying whether debug mode is enabled or not. Do not enable this in production.

`APP_KEY` should be set to a 32 character key used for encrypting data.
