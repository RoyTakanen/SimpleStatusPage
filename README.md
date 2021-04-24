# SSP - SimpleStatusPage
A fairly simple status page with clean UI.

## Installation

**DO NOT USE SQLite IN PRODUCTION BECAUSE THE DATABASE CAN BE DOWNLOADED BY ANYONE**

You have to copy files into a directory that is accessible by a web server. This can be done by using git command. 
```shell
git clone https://github.com/kaikkitietokoneista/SimpleStatusPage.git
```
After that you have to run `composer install` command to install required Composer packages. When you are ready you should be able to go to [http://localhost/setup.php](http://localhost/setup.php). Now you can choose which database do you want to use (MySQL is recommended). 

## How it works?

The program runs a cron job that saves info of the watched services (they are in the watch.json file) to the database. The fron page takes the info from the database. Cron job requires an environment variable IS_ENV to work. This is stops bots and automated database filling scripts. The database is accessed by Medoo library which stops sql injections. 