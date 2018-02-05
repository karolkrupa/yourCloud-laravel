yourCloud
=========

A simple system for storing files in the cloud like OneDrive or OwnCloud based on the Laravel framework with Backbone.js.

## Installation
1. Copy file `.env.example` as `.env`
2. Configure your `.env` file. (database section)
3. Run command `php artisan key:generate` in project root directory
3. Run command `php artisan passport:install` in project root directory
4. Run `php artisan migrate` command
5. Configure your web server for `public` directory


## Configuring `.env` file
```
...
APP_URL=http://yourcloud.test // Your domain url

...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yourcloud // Your database name
DB_USERNAME=root // Your database user
DB_PASSWORD=secret // Your database password
```

## Some Sreenshots
##### Main Page
![yourCloud Screenshot 1](docs/Screenshoots/yourCloud1.jpg?raw=true "yourCloud1")
##### Tag files Page
![yourCloud Screenshot 1](docs/Screenshoots/yourCloud2.jpg?raw=true "yourCloud1")
##### Favorites files Page
![yourCloud Screenshot 1](docs/Screenshoots/yourCloud3.jpg?raw=true "yourCloud1")
