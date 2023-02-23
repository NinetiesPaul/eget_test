# eGet PHP Test

_This is an exercise to evaluate my level of knowledge in PHP's Laravel framework._

This is a simple API REST application with the following features:
- **User registering and logging**
- **JWT authentication**
- **Create/read/update/delete tasks**
- **Asynchronous e-mail notification**
- **Unit testing**
- **Using Eloquent features and Laravel's Job queuing**

This project uses the following technologies:
- **Apache served PHP's Laravel Application**
- **PostgreSQL Database enviroment**
- **Docker**

## Setting up the environment

Having Docker installed, clone this repository, then cd into the folder and run the following commands:
```
docker-compose build --no-cache
docker-compose up -d
docker-compose exec php chmod -R 777 storage
docker-compose exec php composer install
cp .env.example .env
```
Set up the .env file with the database connection details from the docker-compose.yml. The default info is as follows:
```
DB_CONNECTION=pgsql
DB_HOST=eget_test_db
DB_PORT=5432
DB_DATABASE=eget_test
DB_USERNAME=root
DB_PASSWORD=root
```
After that, set up the database and JWT feature
```
docker-compose exec php php artisan migrate
docker-compose exec php php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
docker-compose exec php php artisan jwt:secret
```
The main app configuration is now done. Now, to send e-mail notifications that are created on certain PUT requests, you're gonna have to set up the mailing configurations on the ```.env``` file. The following are responsible for that:
```
MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
````
You can use any mailing service to test this. e.g. https://mailtrap.io. Having the configuration ready, you're also gonna have to get the queue worker running, so run the following:
```
docker-compose exec php php artisan queue:work
```
Mailing occurs on two scenarios:
- **When running PUT /api/task{taskId} to change the user responsible for that task (e.g. { "user_id": "3" })**
- **When running PUT /api/task{taskId} to set the task to closed (e.g. { "status": "closed" })**

Now the project is completely set up

## Unit testing
To use Laravel's built in Unit testing feature, you need to setup the testing database. For that, run the following command:
```
docker-compose exec php php artisan --env=testing migrate
```
Once that command is finished, perform tests with:
```
docker-compose exec php php artisan test tests/Feature
```

## Insomnia testing
I also added an [Insomnia](https://insomnia.rest/download) import file with detailed info on the API's endpoints. Fire up Insomnia, look for a dropdown menu with "Import/Export" options, chose Import > From file and look inside the resources folder for the import file

