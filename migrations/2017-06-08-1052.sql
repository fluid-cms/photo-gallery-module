ALTER TABLE `photogallery_gallery`
ADD `author_id` int(11) NULL,
ADD `author_name` varchar(64) NULL AFTER `author_id`;

ALTER TABLE `photogallery_gallery`
ADD FOREIGN KEY (`author_id`) REFERENCES `admin_user` (`id`) ON DELETE SET NULL
