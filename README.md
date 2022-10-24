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
after starting the container, 
please wait for the application server to completely install dependencies 
then start the apache server before the application can be accessed

start docker
`docker-compose up -d`

visit the project on your browser
`localhost:9000`

see containers
`docker-compose ps`

access the news service container
`docker exec -it news_parsing_service-server bash`

consume messages
`symfony console messenger:consume async -vv`

open a new cli in docker to parse news via cli
`docker exec -it news_parsing_service-server bash`

parse news via cli
`symfony console app:parse-news`

visit the rabbit mq dashboard on the web to view jobs

look into rabbit mq
`docker exec -it rabbitmq bash`
[`rabbitmqctl  list_queues`]
