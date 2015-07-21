
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- session
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `session`;

CREATE TABLE `session`
(
	`phpSessionToken` VARCHAR(255) NOT NULL,
	`userId` INTEGER(10) NOT NULL,
	`authToken` VARCHAR(255) NOT NULL,
	`sessionEnd` DATETIME,
	`created` DATETIME NOT NULL,
	`updated` DATETIME NOT NULL,
	PRIMARY KEY (`phpSessionToken`),
	UNIQUE INDEX `sessionId` (`authToken`(255)),
	INDEX `userId` (`userId`(10)),
	CONSTRAINT `session_ibfk_1`
		FOREIGN KEY (`userId`)
		REFERENCES `user` (`id`)
		ON UPDATE CASCADE
		ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
	`id` INTEGER(255) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(255) NOT NULL,
	`passwordHash` VARCHAR(255) NOT NULL,
	`created` DATETIME NOT NULL,
	`updated` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
