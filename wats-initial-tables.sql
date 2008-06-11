-- DROP DATABASE `inventory`;
-- CREATE DATABASE `inventory`;
-- USE `inventory`;

CREATE TABLE IF NOT EXISTS `log` (
	`logID`			BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`incidentDate`		TIMESTAMP,
	`level`			ENUM('fatal', 'error', 'warning', 'notice', 'info', 'debug'),
	`user`			VARCHAR(100) NOT NULL DEFAULT "",
	`subsystem`		VARCHAR(100) NOT NULL DEFAULT "",
	`remoteAddress`		VARCHAR(100) NOT NULL DEFAULT "",
	`error`			TEXT NOT NULL,
	
	PRIMARY KEY (`logID`)	
	
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `vendor` (
	`vendorID`	BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`vendorName`	VARCHAR(50) NOT NULL,
	`vendorPhone`	VARCHAR(30) NOT NULL DEFAULT "",
	
	`supportPhone`	VARCHAR(30) NOT NULL DEFAULT "",
	`supportURL`	TEXT NULL,
	
	PRIMARY KEY (`vendorID`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `type` (
	`typeID`	BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`typeName`	VARCHAR(50) NOT NULL,

	PRIMARY KEY (`typeID`)
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `model` (
	`modelID`	BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`modelName`	VARCHAR(50) NOT NULL,
	`defaultValue`	DECIMAL(10,2) NOT NULL,
	
	`typeID`	BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
	`vendorID`	BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
	
	PRIMARY KEY (`modelID`),
	
	FOREIGN KEY (`typeID`) REFERENCES `type` (`typeID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE,
	FOREIGN KEY (`vendorID`) REFERENCES `vendor` (`vendorID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE
) ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `status` (
	`statusID`	BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	
	`statusName`	VARCHAR(50) NOT NULL,

	PRIMARY KEY (`statusID`)
	
	
) ENGINE = InnoDB;

REPLACE INTO `type`   VALUES (1, "Desktop Computer"), (2, "Laptop Computer"), (3, "Projector");
REPLACE INTO `status` VALUES (1, "Active"), (2, "Inventory"), (3, "Recycled"), (4, "Lost"), (5, "Stolen");
REPLACE INTO `vendor` VALUES (1, "Dell", "", "", "http://support.dell.com");
REPLACE INTO `model`  VALUES (1, "Latitude D600", 2, 1);

CREATE TABLE IF NOT EXISTS `building` (
	`buildingID`	BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`buildingName` VARCHAR(50),
	
	PRIMARY KEY(`buildingID`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `room` (
	`roomID`		VARCHAR(20) NOT NULL,
	`roomName`	TEXT,
	`floor`		INT UNSIGNED NOT NULL DEFAULT 1,
	
	`buildingID`	BIGINT UNSIGNED NOT NULL,
	
	PRIMARY KEY(`roomID`),
	FOREIGN KEY (`buildingID`) REFERENCES `building` (`buildingID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `person` (
	`personID`	VARCHAR(20) NOT NULL,
	
	`username`	VARCHAR(41) NULL,
	`password`	CHAR(40) NOT NULL DEFAULT "*",
	
	`email`		VARCHAR(100) NOT NULL DEFAULT "",
	
	`nameFirst`	VARCHAR(40) NOT NULL DEFAULT "",
	`nameLast`	VARCHAR(40) NOT NULL DEFAULT "",
	
	`roomID`	VARCHAR(20) NULL,
	
	`isCurrent`	TINYINT(1) NOT NULL DEFAULT 1,
	
	PRIMARY KEY (`personID`),
	
	FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`)
		ON DELETE SET NULL
		ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `device` (
	`deviceID`	VARCHAR(50) NOT NULL,
	`assetTag`	INT UNSIGNED NULL,
	`deviceName`	VARCHAR(50) NOT NULL DEFAULT "",
	
	
	`modelID`	BIGINT(20) UNSIGNED NULL DEFAULT NULL,
	
	`value`		DECIMAL(10,2)NULL DEFAULT NULL,
	
	`dateInventoried` DATE,
	`inventoriedBy` BIGINT(20) UNSIGNED NOT NULL
	`datePurchased`	DATE DEFAULT NULL,
	`dateRemoved`	DATE DEFAULT NULL,
	`statusID`	BIGINT(20) UNSIGNED NULL DEFAULT 1,
	
	PRIMARY KEY (`deviceID`),
	
	FOREIGN KEY (`modelID`) REFERENCES `model` (`modelID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE,	
	FOREIGN KEY (`statusID`) REFERENCES `status` (`statusID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE,
	FOREIGN KEY (`inventoriedBy`) REFERENCES `person` (`personID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE
) ENGINE=InnoDB;





CREATE TABLE IF NOT EXISTS `assignment` (
	`assignmentID`	BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`deviceID`	VARCHAR(50) NOT NULL,
	`personID`	VARCHAR(20) NULL,
	`roomID`	VARCHAR(20) NULL,
	
	`dateAssigned`	DATETIME NOT NULL,
	`dateRemoved`	DATETIME NULL,
	
	PRIMARY KEY (`assignmentID`),
	
	FOREIGN KEY (`deviceID`) REFERENCES `device` (`deviceID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE,
	FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE,
	FOREIGN KEY (`personID`) REFERENCES `person` (`personID`)
		ON DELETE NO ACTION
		ON UPDATE CASCADE	
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `config` (
	`key`	VARCHAR(100) NOT NULL,
	`value`	TEXT,
	
	PRIMARY KEY (`key`)
) ENGINE=InnoDB;

REPLACE INTO `config` VALUES 
	('unixroot', "/home/wats"),
	('webroot', "http://127.0.0.1/"),
	('theme', "example1"),
	('themedir', "http://127.0.0.1/themes");
	
CREATE TABLE IF NOT EXISTS `preference` (
	`personID`	VARCHAR(20) NOT NULL,
	`preference`	VARCHAR(30) NOT NULL,
	`value`		TEXT,
	
	PRIMARY KEY (`personID`, `preference`),
	
	FOREIGN KEY (`personID`) REFERENCES `person` (`personID`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `person_role` (
	`personID`	VARCHAR(20) NOT NULL,
	`roleID`	VARCHAR(20) NOT NULL,
	
	PRIMARY KEY (`personID`, `roleID`),
	
	FOREIGN KEY (`personID`) REFERENCES `person` (`personID`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB;



REPLACE INTO `person` VALUES ("001", "admin", SHA1("*"), "admin@localhost.localdomain", "Admin", "Istrator", NULL, 1);
REPLACE INTO `person_role` VALUES ("001", "view"), ("001", "inventory"), ("001", "admin"), ("001", "request");


CREATE TABLE IF NOT EXISTS `device_fetch_queue` (
	`deviceID`	VARCHAR(50) NOT NULL,	
	`dateAdded`	TIMESTAMP,
	
	PRIMARY KEY (`deviceID`)
) ENGINE = InnoDB;