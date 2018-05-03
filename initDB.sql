CREATE DATABASE books_reservation
DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE books_reservation;

CREATE TABLE book (
	id INTEGER NOT NULL AUTO_INCREMENT,
	isbn VARCHAR(15) UNIQUE DEFAULT NULL,
	title VARCHAR(60) NOT NULL,
	author VARCHAR(60) NOT NULL,
	publisher VARCHAR(60) NOT NULL,
	pubdate VARCHAR(15) NOT NULL,
	cover VARCHAR(100),
	quantity INTEGER NOT NULL DEFAULT 1,
	imported TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	ON UPDATE CURRENT_TIMESTAMP NOT NULL,
	PRIMARY KEY(id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1000;

CREATE TABLE reservation (
	id INTEGER NOT NULL AUTO_INCREMENT,
	stuname VARCHAR(60) NOT NULL,
	stuno VARCHAR(15) NOT NULL,
	dorm VARCHAR(15) NOT NULL,
	contact VARCHAR(15) NOT NULL,
	takeday VARCHAR(15) NOT NULL,
	taketime VARCHAR(15) NOT NULL,
	book0 INTEGER DEFAULT NULL,
	book1 INTEGER DEFAULT NULL,
	book2 INTEGER DEFAULT NULL,
	submited TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	ON UPDATE CURRENT_TIMESTAMP NOT NULL,
	PRIMARY KEY(id),
	FOREIGN KEY (book0) REFERENCES book(id),
	FOREIGN KEY (book1) REFERENCES book(id),
	FOREIGN KEY (book2) REFERENCES book(id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT = 6000;



-- Following are test data for books
/*
INSERT INTO book
	(isbn, title, author, publisher, pubdate, cover, quantity)
VALUES
	('9787208061699', '江泽民和他的母校上海交通大学', '上海交通大学 编著', '上海人民出版社', '2006-01-01', 'https://img3.doubanio.com/lpic/s2927042.jpg', 1),
	('9780321714114', 'C++ Primer', 'Stanley B. Lippman 等', 'Addison-Wesley Professional', '2012-8-16', 'https://img1.doubanio.com/lpic/s29252317.jpg', 0),
	('9787111075660', 'TCP/IP详解 卷1：协议', 'W.Richard Stevens', '机械工业出版社', '2000-4-1', 'https://img3.doubanio.com/lpic/s1543906.jpg', 27),
	('9787532736546', '他改变了中国', '[美] 罗伯特·劳伦斯·库恩', '上海译文出版社', '2005-01', 'https://img1.doubanio.com/lpic/s1305338.jpg', 6),
	('9787208100039', '江泽民在上海', '明锐 等', '上海人民出版社', '2011-7-28', 'https://img1.doubanio.com/lpic/s26582097.jpg', 3),
	('9787111407010', '算法导论（原书第3版）', 'Thomas H.Cormen 等', '机械工业出版社', '2012-12', 'https://img3.doubanio.com/lpic/s25648004.jpg', 17);
*/
