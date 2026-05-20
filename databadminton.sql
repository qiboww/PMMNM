-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for badminton_store
CREATE DATABASE IF NOT EXISTS `badminton_store` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `badminton_store`;

-- Dumping structure for table badminton_store.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table badminton_store.category: ~6 rows (approximately)
INSERT INTO `category` (`id`, `name`, `description`) VALUES
	(1, 'Vợt Yonex', 'Danh mục các dòng vợt cầu lông Yonex'),
	(2, 'Vợt Lining', 'Danh mục các dòng vợt cầu lông Lining'),
	(3, 'Vợt Victor', 'Danh mục các dòng vợt cầu lông Victor'),
	(4, 'Giày cầu lông', 'Danh mục giày chuyên dụng đánh cầu'),
	(5, 'Phụ kiện', 'Danh mục cước, quấn cán, balo, túi vợt'),
	(6, 'Áo cầu lông', 'Trang phục áo cầu lông chất liệu thoáng khí, co giãn tốt');

-- Dumping structure for table badminton_store.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table badminton_store.product: ~10 rows (approximately)
INSERT INTO `product` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
	(1, 'Yonex Astrox 88D Pro', 'Vợt thiên công, đập cầu mạnh mẽ', 3600000.00, '1779244438_YonexAstrox88DPro.jfif', 1),
	(2, 'Yonex Nanoflare 1000Z', 'Vợt nhẹ đầu, phản tạt nhanh', 4200000.00, '1779244717_Yonex Nanoflare 1000Z.png', 1),
	(3, 'Lining Halbertec 8000', 'Vợt cân bằng, kiểm soát toàn diện', 3400000.00, '1779244733_Lining Halbertec 8000.jfif', 2),
	(4, 'Lining Aeronaut 9000C', 'Vợt có rãnh thoát khí, vung vợt nhanh', 3200000.00, '1779244742_Lining Aeronaut 9000C.png', 2),
	(5, 'Victor Thruster Ryuga II', 'Vợt thân cứng, tấn công uy lực', 3500000.00, '1779244750_Victor Thruster Ryuga II.jfif', 3),
	(6, 'Victor DriveX 9X', 'Vợt công thủ toàn diện, linh hoạt', 3100000.00, '1779244757_Victor DriveX 9X.jfif', 3),
	(7, 'Yonex SHB 65Z3', 'Giày cầu lông êm ái, bám sân tốt', 2500000.00, '1779244801_Yonex SHB 65Z3.png', 4),
	(8, 'Cước Yonex BG65 Ti', 'Cước cầu lông nảy, độ bền cao', 150000.00, '1779244763_Cước Yonex BG65 Ti.jfif', 5),
	(9, 'Yonex Astrox 99 Pro', 'Vợt thiên công hạng nặng, đập cầu cắm, thân cứng.', 3800000.00, '1779244839_Yonex Astrox 99 Pro.jfif', 1),
	(10, 'Áo Cầu Lông Yonex YN07', 'Thoáng mát', 200000.00, '1779246242_AoYN07.jfif', 6);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
