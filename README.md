# News Parsing Service

A service that scrapes news articles from other news platforms, modify the ui and displays it.

## About News Parsing Service

The scraping service uses the cli command to make the download for view by administrators and moderators.
administrators have special permissions to delete unwanted articles.

### Parsing features

- from each article, the download should and be saved:
- title
- short description
- picture
- date added

### objective

- parse news that aren't already in the database.
- database queries should be optimized for heavy load
- parsing should be in several parallel processes (via rabbitMQ)
- parsing must be run via cron Features of the page for viewing news from the database:
- the page for viewing news from the database should be available only after authorization in the system (registration is not required)
- Authorized users can be with one of two roles: admin or moderator (the administrator can delete articles)
- there must be pagination at the end of the list of articles (10 per page)

### SETUP

start docker
`docker-compose up -d`

see containers
`docker-compose ps`

access the news service container
`docker exec -it news-parsing-service-symfony-1 bash`

install composer
`composer install`

RUN `chown -R www-data /var/log/dev.log`
RUN `chmod -R ugo+rw /var/log/dev.log`
if the above command doesn't work, cd into /var/log/ then change ownership of dev.log to www-data
RUN `cd /var/log/`
RUN `chown www-data dev.log`
RUN `chmod ugo+rw dev.log`

set up the project database
`php bin/console doctrine:database:create`

make the migrations
`php bin/console make:migration`

execute the migrations
`php bin/console doctrine:migrations:migrate`

consume messages
`php bin/console messenger:consume async -vv`

parse news via cli
`php bin/console app:parse-news`

visit the rabbit mq dashboard on the web to view jobs

look into rabbit mq
`docker exec -it news-parsing-service-rabbitmq-1 bash`
[`rabbitmqctl  list_queues`]
