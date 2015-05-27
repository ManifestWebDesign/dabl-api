SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for session
-- ----------------------------
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
`userId`  int(10) UNSIGNED NOT NULL ,
`authToken`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`phpSessionToken`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`created`  datetime NOT NULL ,
`updated`  datetime NOT NULL ,
PRIMARY KEY (`userId`),
FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
UNIQUE INDEX `sessionId` (`authToken`) USING BTREE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
`id`  int(255) UNSIGNED NOT NULL AUTO_INCREMENT ,
`username`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`passwordHash`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`created`  datetime NOT NULL ,
`updated`  datetime NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

