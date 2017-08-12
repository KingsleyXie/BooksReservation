DROP DATABASE IF EXISTS `books_reservation`;
CREATE DATABASE `books_reservation` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE books_reservation;

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books`(
	`bookID` INTEGER NOT NULL AUTO_INCREMENT,
	`ISBN` VARCHAR(15) DEFAULT "",
	`title` VARCHAR(60) NOT NULL,
	`author` VARCHAR(60) NOT NULL,
	`isMultipleAuthor` INTEGER NOT NULL,
	`press` VARCHAR(60) NOT NULL,
	`pubdate` VARCHAR(15) NOT NULL,
	`image` VARCHAR(100) NOT NULL/* DEFAULT "https://upload.wikimedia.org/wikipedia/commons/2/2c/JZM1947.jpg"*/,
	`bookCategory` VARCHAR(15) NOT NULL,
	`grade` VARCHAR(15),
	`major` VARCHAR(15),
	`extracurricularCategory` VARCHAR(15),
	`remainingAmount` INTEGER NOT NULL,
	`importTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	`updateTime` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	PRIMARY KEY(`bookID`),
	UNIQUE KEY `bookID_UNIQUE`(`bookID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT=3001;

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students`(
	`studentID` INTEGER NOT NULL AUTO_INCREMENT,
	`studentName` VARCHAR(60) NOT NULL,
	`studentNo` VARCHAR(15) NOT NULL,
	`dormitory` VARCHAR(15) NOT NULL,
	`contact` VARCHAR(15) NOT NULL,
	`importTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(`studentID`),
	UNIQUE KEY `studentID_UNIQUE`(`studentID`),
	UNIQUE KEY `studentNo_UNIQUE`(`studentNo`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT=5001;

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations`(
	`reservationNo` INTEGER NOT NULL AUTO_INCREMENT,
	`date` VARCHAR(15) NOT NULL,
	`timePeriod` VARCHAR(15) NOT NULL,
	`studentID` INTEGER NOT NULL,
	`bookID_1` INTEGER NOT NULL,
	`bookID_2` INTEGER NOT NULL,
	`bookID_3` INTEGER NOT NULL,
	`submitTime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updateTime` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	PRIMARY KEY(`reservationNo`),
	UNIQUE KEY `reservationNo_UNIQUE`(`reservationNo`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8 AUTO_INCREMENT=1001;



-- The following are test data
INSERT INTO `books`
	(`ISBN`, `title`, `author`, `isMultipleAuthor`, `press`, `pubdate`, `image`, `bookCategory`, `grade`, `major`, `extracurricularCategory`, `remainingAmount`)
VALUES
	('9787208061699', '江泽民和他的母校上海交通大学', '上海交通大学 编著', 0, '上海人民出版社', '2006-01-01', 'https://img3.doubanio.com/lpic/s2927042.jpg', 'CategoryA', '大三', '计联计创', NULL, 1),
	('9780321714114', 'C++ Primer', 'Stanley B. Lippman', 1, 'Addison-Wesley Professional', '2012-8-16', 'https://img1.doubanio.com/lpic/s29252317.jpg', 'CategoryA', '大三', '计联计创', NULL, 0),
	('9787111075660', 'TCP/IP详解 卷1：协议', 'W.Richard Stevens', 0, '机械工业出版社', '2000-4-1', 'https://img3.doubanio.com/lpic/s1543906.jpg', 'CategoryB', NULL, NULL, '计算机专业类书籍', 27),
	('9787532736546', '他改变了中国', '[美] 罗伯特·劳伦斯·库恩', 0, '上海译文出版社', '2005-01', 'https://img1.doubanio.com/lpic/s1305338.jpg', 'CategoryB', NULL, NULL, '学习辅导资料', 6),
	('9787208100039', '江泽民在上海', '明锐', 1, '上海人民出版社', '2011-7-28', 'https://img1.doubanio.com/lpic/s26582097.jpg', 'CategoryB', NULL, NULL, '文学类书籍', 3),
	('9787111407010', '算法导论（原书第3版）', 'Thomas H.Cormen', 1, '机械工业出版社', '2012-12', 'https://img3.doubanio.com/lpic/s25648004.jpg', 'CategoryB', NULL, NULL, '计算机专业类书籍', 17);
