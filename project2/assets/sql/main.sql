-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for youtube
CREATE DATABASE IF NOT EXISTS `youtube` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `youtube`;

-- Dumping structure for table youtube.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `reactions_ibfk_2` (`video_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table youtube.comments: ~1 rows (approximately)
INSERT INTO `comments` (`id`, `user_id`, `video_id`, `message`, `date_added`) VALUES
	(1, 1, 1, 'Goeie video', '2024-12-13 09:21:18'),
	(2, 1, 1, 'testje', '2024-12-13 10:02:34');

-- Dumping structure for table youtube.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profilePicturePath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table youtube.users: ~0 rows (approximately)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `profilePicturePath`) VALUES
	(1, 'tom', 'tomtiedemann30@gmail.com', '$argon2id$v=19$m=32768,t=4,p=1$aXUuM1hFeFhURUVBbzRnUw$ggGaOgQGNhp/nH1dzlfmfJRgKwkg/fJNAsb9PMijFCw', 'uploads/profile_pictures/1.png');

-- Dumping structure for table youtube.videos
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table youtube.videos: ~2 rows (approximately)
INSERT INTO `videos` (`id`, `user_id`, `video_path`, `thumbnail_path`, `title`, `description`, `date_added`) VALUES
	(1, 1, 'uploads/videos/1/video1.mp4', 'images/default-thumbnail.png', 'test', 'malek legend', '2024-12-12 10:00:48'),
	(2, 1, 'uploads/videos/1/video2.mp4', 'images/default-thumbnail.png', 'test1', 'testje', '2024-12-13 09:23:40');

-- Dumping structure for table youtube.video_history
CREATE TABLE IF NOT EXISTS `video_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `last_position` int(11) DEFAULT NULL,
  `last_viewed` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`video_id`),
  KEY `video_id` (`video_id`),
  CONSTRAINT `video_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `video_history_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table youtube.video_history: ~1 rows (approximately)
INSERT INTO `video_history` (`id`, `user_id`, `video_id`, `last_position`, `last_viewed`) VALUES
	(1, 1, 1, 1, '2024-12-13 11:40:52');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
