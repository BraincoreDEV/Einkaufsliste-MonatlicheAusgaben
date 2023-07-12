/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80033 (8.0.33)
 Source Host           : localhost:3306
 Source Schema         : ausgaben

 Target Server Type    : MySQL
 Target Server Version : 80033 (8.0.33)
 File Encoding         : 65001

 Date: 12/07/2023 15:06:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ausgaben
-- ----------------------------
DROP TABLE IF EXISTS `ausgaben`;
CREATE TABLE `ausgaben`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kategorie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `betrag` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ausgaben
-- ----------------------------
INSERT INTO `ausgaben` VALUES (5, 'Lebensmittel / Tabak', 250.00);
INSERT INTO `ausgaben` VALUES (6, 'Freizeit mit Alenya', 150.00);

SET FOREIGN_KEY_CHECKS = 1;
