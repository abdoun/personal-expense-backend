/*
Navicat MySQL Data Transfer

Source Server         : local_bash
Source Server Version : 80020
Source Host           : localhost:3306
Source Database       : personal_expense

Target Server Type    : MYSQL
Target Server Version : 80020
File Encoding         : 65001

Date: 2020-08-02 18:03:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `categories`
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3AF34668A76ED395` (`user_id`),
  CONSTRAINT `FK_3AF34668A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES ('1', '1', 'food', null);
INSERT INTO `categories` VALUES ('2', '1', 'transport', null);
INSERT INTO `categories` VALUES ('3', '1', 'tax', null);
INSERT INTO `categories` VALUES ('4', '1', 'salary', null);
INSERT INTO `categories` VALUES ('5', '2', 'food', null);

-- ----------------------------
-- Table structure for `doctrine_migration_versions`
-- ----------------------------
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of doctrine_migration_versions
-- ----------------------------
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20200716100339', '2020-07-27 16:43:12', '29857');
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20200727145925', '2020-07-27 17:12:27', '130972');

-- ----------------------------
-- Table structure for `expenses`
-- ----------------------------
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `user_id` int NOT NULL,
  `qty` int NOT NULL,
  `date_time` datetime NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2496F35B12469DE2` (`category_id`),
  KEY `IDX_2496F35BA76ED395` (`user_id`),
  CONSTRAINT `FK_2496F35B12469DE2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `FK_2496F35BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of expenses
-- ----------------------------
INSERT INTO `expenses` VALUES ('2', '1', '1', '-10', '2020-07-15 17:25:49', null);
INSERT INTO `expenses` VALUES ('3', '4', '1', '1700', '2020-07-01 17:26:39', null);
INSERT INTO `expenses` VALUES ('4', '5', '2', '-15', '2020-07-16 17:27:16', null);
INSERT INTO `expenses` VALUES ('5', '1', '1', '-5', '2020-07-04 10:03:00', 'asdf');
INSERT INTO `expenses` VALUES ('6', '3', '1', '-50', '2020-07-22 12:37:00', 'TV');
INSERT INTO `expenses` VALUES ('7', '2', '1', '-110', '2020-07-01 12:37:00', '');
INSERT INTO `expenses` VALUES ('8', '1', '1', '-30', '2020-07-15 12:37:00', '');
INSERT INTO `expenses` VALUES ('9', '1', '1', '-10', '2020-07-28 12:38:00', '');
INSERT INTO `expenses` VALUES ('10', '1', '1', '-13', '2020-07-12 12:38:00', '');
INSERT INTO `expenses` VALUES ('11', '1', '1', '-8', '2020-07-24 12:39:00', '');
INSERT INTO `expenses` VALUES ('12', '1', '1', '-20', '2020-07-14 12:39:00', '');
INSERT INTO `expenses` VALUES ('13', '1', '1', '-10', '2020-07-28 23:44:00', '');
INSERT INTO `expenses` VALUES ('14', '1', '1', '-15', '2020-07-20 12:40:00', '');
INSERT INTO `expenses` VALUES ('15', '3', '1', '-16', '2020-07-17 18:27:00', 'Electric');
INSERT INTO `expenses` VALUES ('16', '3', '1', '-20', '2020-07-01 18:28:00', 'Sport');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', 'abdoun79@gmail.com', 'abdoun79', 'e10adc3949ba59abbe56e057f20f883e', '1', null);
INSERT INTO `users` VALUES ('2', 'abdoun1979@hotmail.com', 'abdoun1979', 'e10adc3949ba59abbe56e057f20f883e', '1', null);
