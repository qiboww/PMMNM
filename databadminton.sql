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

-- Dumping structure for table badminton_store.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table badminton_store.category: ~7 rows (approximately)
INSERT INTO `category` (`id`, `name`, `description`) VALUES
	(1, 'Vợt Yonex', 'Danh mục các dòng vợt cầu lông Yonex'),
	(2, 'Vợt Lining', 'Danh mục các dòng vợt cầu lông Lining'),
	(3, 'Vợt Victor', 'Danh mục các dòng vợt cầu lông Victor'),
	(4, 'Giày cầu lông', 'Danh mục giày chuyên dụng đánh cầu'),
	(5, 'Phụ kiện', 'Danh mục cước, quấn cán, balo, túi vợt'),
	(6, 'Áo cầu lông', 'Trang phục áo cầu lông chất liệu thoáng khí, co giãn tốt'),
	(7, 'Túi vợt', 'Túi đựng vợt cầu lông sẽ hỗ trợ di chuyển, đựng và bảo vệ dụng cụ cầu lông khỏi hư hại, biến dạng.');

-- Dumping structure for table badminton_store.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` text NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table badminton_store.orders: ~3 rows (approximately)
INSERT INTO `orders` (`id`, `customer_name`, `customer_phone`, `customer_address`, `total_price`, `created_at`) VALUES
	(1, 'Quốc Bảo', '0902815729', '611 Thống Nhất, Phường An Hội Đông, TP.HCM', 7400000.00, '2026-06-03 01:23:36'),
	(2, 'Quốc Trung', '0902815722', 'Gò vấp', 7800000.00, '2026-06-03 01:56:27'),
	(3, 'Quốc Bảo', '0902815729', 'Bình Thạnh', 5700000.00, '2026-06-03 02:22:53');

-- Dumping structure for table badminton_store.order_details
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table badminton_store.order_details: ~6 rows (approximately)
INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
	(1, 1, 2, 1, 4200000.00),
	(2, 1, 4, 1, 3200000.00),
	(3, 2, 2, 1, 4200000.00),
	(4, 2, 1, 1, 3600000.00),
	(5, 3, 4, 1, 3200000.00),
	(6, 3, 7, 1, 2500000.00);

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

-- Dumping structure for table badminton_store.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `role` enum('admin','user') DEFAULT 'user',
  `is_locked` tinyint(1) DEFAULT '0',
  `remember_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table badminton_store.users: ~3 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `avatar`, `role`, `is_locked`, `remember_token`, `reset_token`, `created_at`, `is_verified`, `verification_token`) VALUES
	(1, 'Admin', 'admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'default.png', 'admin', 0, NULL, NULL, '2026-06-03 01:49:55', 1, NULL),
	(2, 'Trung', 'trung123@gmail.com', '$2y$10$TK.kpJVROgJz6uHipoSDB.N.zXTXMqqZZaBuNGRsUp9eRZdAq9XsG', '1780452102_2.jfif', 'user', 0, NULL, NULL, '2026-06-03 01:55:34', 0, NULL),
	(3, 'Quốc Bảo', 'phanquocbao134@gmail.com', '$2y$10$2iGibU8mdDhE6GKRVKow/eRej6zeJfKEZVEVYlute3/RHDHWVoRwi', 'default.png', 'user', 0, NULL, NULL, '2026-06-03 02:14:26', 1, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
