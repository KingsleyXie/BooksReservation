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
