## Books Reservation
This is an online web system wrote for books :books: reseravtion, typically old books collected from graduates :mortar_board:. The project has been maintained since May 2017, which is excatly the time it was first deployed online, and then in the next year it was updated and worked in production again. Later in November 2018 it was used as my database coursework :balloon: and so I made even more modifications and optimizations to the core modules.

### Brief Introduction
*Note: In this system, `reserve` refers to the reserve operation itself while `reservation` is used to refer to the reserved order......maybe still kind of confusing = _ =!*

- User Module :orange_book::
  - Welcome modal will be displayed once the page is loaded, and then after clicking the confirm button you can get paged books' brief data ordered by their entry time, while books with no surplus will be showed in the final page
  - Navbar displays three options, you can get all books' list, search :mag: books via keywords or search reservation via your student ID
  - Commit or modify a reservation: Choose no more than **three** books and then you can commit the reservation after filling in the information form, and the reservation can be modified :pencil: any time after its commit
  - Student ID is used as unique identifier :pushpin: in this system, so it can not be modified, and an reservation with reserved student ID will not be committed.

- Admin Module :lock::
  - After logged in, this page will display all existed books with their detailed information :book:, add and update functions are provided as well, and the cursor will be automatically focused on remaining amount area when updating a book, so that update operations can be more efficient
  - To add a book to database, you will need to scan :camera_flash: or input its ISBN, if there is no data responses after requesting the ISBN-based search, information of the book will need to be inputted manually
  - All reservations will be displayed in the page from  "show all reservations" link :link:, so that administrators can use them to hand out the reserved books

**The API used in this system to get books' information from their ISBN is provided by [douban](https://developers.douban.com/wiki/?title=book_v2), and the Front-End UI is based on [Materialize](https://github.com/Dogfalo/materialize)**.

Source of sample SideNav header picture was [here](https://assets.entrepreneur.com/content/3x2/1300/20150115183825-books-reading.jpeg) and it was processed to fit in its element position.

A Brief Chinese introduction and direction of this system lays :u6709: in [`Instructions.md`](Instructions.md).

### Experience Demo Sites
You can experience this entire system on [https://db-demo.kingsley.cc](https://db-demo.kingsley.cc), and here are some different versions deployed :rocket: online:

- [Version 0 :candy:](https://demos.kingsleyxie.cn/books-reservation/) should be the furthest version of this system, deployed on May, 2017, it is now an online demo which you can test with anyway you want.

- [Version 1 :icecream:](https://cs2018.kingsleyxie.cn) is in production mode, deployed on May, 2018. Reservation's add and modify interfaces are currently closed, so please **DO NOT TEST WITH IT**.

- [Version 2 :lollipop:](https://db-demo.kingsley.cc) is the link given above, it is used as a demo for my database coursework presentation, and this is also the up-to-date version. It is strongly recommended to experience with it if you want to try the system, since it has significant updates :alembic: and optimizations :zap: from the former two versions.

## Dependency Installation
Commands are as follows, based on my coursework presentation demo site using Ubuntu 16.04, the process to install LNMP/LAMP is ignored here. Besides, for convenience, any user permission related problem are also not shown either.

### Elasticsearch with `IK` analysis plugin :package: and `Java` environment :coffee:

References:
  - [How To Install Oracle Java JDK 11 / 8 on Ubuntu 16.04 / Linux Mint 18](https://www.itzgeek.com/how-tos/linux/ubuntu-how-tos/install-java-jdk-8-on-ubuntu-14-10-linux-mint-17-1.html)
  - [How To Install and Configure Elasticsearch on Ubuntu 16.04](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-elasticsearch-on-ubuntu-16-04)
  - [IK Analysis for Elasticsearch](https://github.com/medcl/elasticsearch-analysis-ik/).

```shell
$ sudo add-apt-repository -y ppa:webupd8team/java
$ sudo apt-get update
$ sudo apt-get install -y oracle-java8-installer

$ wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.5.0.deb

# Install Elasticsearch, enable and start its service
$ sudo dpkg -i elasticsearch-6.5.0.deb
$ sudo systemctl enable elasticsearch.service
$ sudo systemctl start elasticsearch

# Install IK analysis plugin and restart Elasticsearch
$ sudo ./bin/elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v6.5.0/elasticsearch-analysis-ik-6.5.0.zip
$ sudo systemctl restart elasticsearch
```

### Redis database for session cache and book search result cache :beers:

References:
  - [How to Install and Configure a Redis Cluster on Ubuntu 16.04](https://www.linode.com/docs/applications/big-data/how-to-install-and-configure-a-redis-cluster-on-ubuntu-1604/)
  - [want to run redis-server in background nonstop](https://stackoverflow.com/questions/24221449/want-to-run-redis-server-in-background-nonstop/33316249#33316249)

```shell
$ sudo apt-get update && sudo apt-get upgrade
$ sudo apt install make gcc libc6-dev tcl

$ wget http://download.redis.io/redis-stable.tar.gz
$ tar xvzf redis-stable.tar.gz
$ cd redis-stable

# Build Redis and try unit tests
$ make install
$ make test

# Start Redis in background
$ redis-server --daemonize yes
```

### Laravel project dependencies :heavy_plus_sign:

```shell
$ curl https://getcomposer.org/installer > composer.phar
$ php composer.phar
$ php composer.phar install
```

## Deploy Guide
### Initialization :wrench:
First, test if MySQL, Redis and Elasticsearch is running, execute these commands for them respectively:

```shell
$ mysql -uusername -ppassword
$ redis-cli

# Elasticsearch listens on port 9200 and provides RESTful API to use
$ curl localhost:9200
```

Then just change configurations in `.env` file which you should copy from `.env.example`, typically you may  just change `DB_*` to your mysql host, username and password for this project, and `REDIS_*` should just be the default config if you didn't change it. `ADMIN_PATH` is used as a random string :see_no_evil: for the admin pages' URL.

Remember to give write permission to `storage` directory, and you can just start using the system after following operations:

```shell
# Generate an app key for the Laravel project
$ php artisan key:generate

# Create database and corresponding test data
$ php artisan migrate:fresh --seed
```

Since the sync :speech_balloon: between MySQL and Elasticseach is only done when adding or updating book in admin module, this database operation makes no synchronization between them, so you'll need to manually do that, luckily I've wrote an API for that operation, just sign in admin module and visit `api/admin/init-index` will make everything in the right position.

### Reset all dirty data :boom:
You may want to clear all changes after several test operations, only need three commands are needed for that:

```shell
# Reset database structure and test data
$ php artisan migrate:fresh --seed

# Remove Elasticsearch's index for this project
$ curl -X DELETE "localhost:9200/books_reservation_index" | json_pp

# Clear seach caches stored in Redis
$ redis-cli FLUSHALL
```

And of course the `api/admin/init-index` URL should be visited again to recreate indices for these test books.

Made By Kingsley With :heart:
