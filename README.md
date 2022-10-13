# News Parsing Service

## About News Parsing Service

## SETUP

start docker
docker-compose up -d

see containers
docker-compose ps

access the database in container
docker-compose exec database mysql --user root --password news_parsing_service

set up database
symfony console doctrine:database:create

make migrations
symfony console make:migration

execute migrations
symfony console doctrine:migrations:migrate

consume messages
symfony console messenger:consume async -vv

parse news via cli
symfony console app:parse-news

look into rabbit mq
docker exec -it example-symfony-messenger_rabbitmq_1 bash
