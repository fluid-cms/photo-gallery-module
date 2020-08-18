CREATE TABLE `photogallery_gallery` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(80) NOT NULL,
  `description` text NULL,
  `keywords` varchar(200) NULL,
  `created_on` datetime NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `is_publish` tinyint(1) NOT NULL DEFAULT '0',
  `enable_comments` tinyint(1) NOT NULL DEFAULT '0',
  FOREIGN KEY (`created_by_id`) REFERENCES `admin_user` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE 'utf8_czech_ci';

CREATE TABLE `photogallery_photo` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(180) NOT NULL,
  `filename` varchar(80) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `size` int NOT NULL,
  `description` text NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  `photogallery_gallery_id` int(11) NOT NULL,
  FOREIGN KEY (`photogallery_gallery_id`) REFERENCES `photogallery_gallery` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE 'utf8_czech_ci';