## Books Reservation
This is an online web system wrote in HTML, CSS, Javascript and PHP for reserving books, which is actually an entry level web system for web development newbies like me.

### Brief Introduction
*Note: In this system, `reserve` refers to the reserve operation itself while `reservation` is used to refer to the reserved order......maybe still kind of confusing = _ =!*

- **Reserve Module:**
  - Relative Path: `./`
  - Welcome modal will be displayed once the page is visited, and then after clicking the confirm button you can get all the books' brief data ordered by their entry time, while books with no surplus will be showed in the bottom
  - Navbar displays three options, you can search books by inputting key words or selecting category, the third function is provided to search reservation via student ID
  - Commit or modify a reservation: Choose no more than **three** books and then you can commit the reservation after filling in the information form, and the reservation can be modified any time after its commit
  - Student ID is used as unique identifier in this system, so it can not be modified, and an reservation with reserved student ID will not be committed.

- Books Admin Module:
  - Relative Path: `./admin`
  - After logged in, this page will display all existed books with their detailed information, add and update functions are provided as well, and the cursor will be automatically focused on remaining amount area to make operations more efficient
  - To add a book to database, you will need to input its ISBN first and then select its category, if no data responses after requesting the ISBN-based search, information of the book will need to be inputted manually

- Reservations Module:
  - Relative Path: `./admin/reservations`
  - All reservations will be displayed in this page so that administrators can use them to hand out the reserved books

**The API used in this system to get books' information from their ISBN is provided by [douban](https://developers.douban.com/wiki/?title=book_v2), and the Front-End UI is based on [Materialize](https://github.com/Dogfalo/materialize)**

You can also see brief introductions and directions of this system inside [`./assets/directions.md`](./assets/directions.md) in Chinese

Source of [sample SideNav header picture](./assets/pictures/books.png) was [here](https://assets.entrepreneur.com/content/3x2/1300/20150115183825-books-reading.jpeg) and it was processed to fit in its element position

You can experience this entire system on [https://projects.kingsleyxie.cn/books-reservation/](https://projects.kingsleyxie.cn/books-reservation/)
 
## Temporary Brief Deploy Guide For v2.0.0+
This README is currently not up-to-date with the project version due to lacking of time. Commands are shown below to give a brief deploy guide, based on my demo site using Ubuntu 16.04, and the process to install LNMP/LAMP is ignored here. Besides, for convience, any user permission related problem are also not shown.

### Elasticsearch with `ik` analysis plugin, and `Java` environment:

Refrences:
  - [How To Install Oracle Java JDK 11 / 8 on Ubuntu 16.04 / Linux Mint 18](https://www.itzgeek.com/how-tos/linux/ubuntu-how-tos/install-java-jdk-8-on-ubuntu-14-10-linux-mint-17-1.html)
  - [How To Install and Configure Elasticsearch on Ubuntu 16.04](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-elasticsearch-on-ubuntu-16-04)
  - [IK Analysis for Elasticsearch](https://github.com/medcl/elasticsearch-analysis-ik/).

```shell
$ sudo add-apt-repository -y ppa:webupd8team/java
$ sudo apt-get update
$ sudo apt-get install -y oracle-java8-installer

$ wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.5.0.deb
$ sudo dpkg -i elasticsearch-6.5.0.deb
$ sudo systemctl enable elasticsearch.service
$ sudo systemctl start elasticsearch

$ sudo ./bin/elasticsearch-plugin install https://github.com/medcl/elasticsearch-analysis-ik/releases/download/v6.5.0/elasticsearch-analysis-ik-6.5.0.zip
$ sudo systemctl restart elasticsearch
```

### Redis database for session cache and book search result cache

Refrences:
  - [How to Install and Configure a Redis Cluster on Ubuntu 16.04](https://www.linode.com/docs/applications/big-data/how-to-install-and-configure-a-redis-cluster-on-ubuntu-1604/)
  - [want to run redis-server in background nonstop](https://stackoverflow.com/questions/24221449/want-to-run-redis-server-in-background-nonstop/33316249#33316249)

```shell
$ sudo apt-get update && sudo apt-get upgrade
$ sudo apt install make gcc libc6-dev tcl

$ wget http://download.redis.io/redis-stable.tar.gz
$ tar xvzf redis-stable.tar.gz
$ cd redis-stable

$ make install
$ make test

$ redis-server --daemonize yes
```

### Project initialization with composer and Laravel's Artisan commands

```shell
$ curl https://getcomposer.org/installer > composer.phar
$ php composer.phar
$ php composer.phar install

$ php artisan migrate:fresh --seed
```
