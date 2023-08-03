# Passenger

## Documentaion

### Installation
* git clone https://github.com/haewhybabs/passenger.git
* composer install
* set up the env (preferably rename .env.example  to .env)
* php artisan key:generate
* php artisan migrate (to set up the database)
* php artisan serve (to start the server)



### Run command with (to download the postcodes and save them to the db)
php artisan postcodes:import

NB: The command might take a long time to process because the data is about 1.8 million.


***postman link:https://documenter.getpostman.com/view/5742682/2s9Xxwxa4C***


**Author:: Ayobami Babalola**
