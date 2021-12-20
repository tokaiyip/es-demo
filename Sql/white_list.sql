CREATE TABLE `white_list` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(45) NOT NULL DEFAULT '' COMMENT '类型',
    `content` varchar(255) NOT NULL DEFAULT '' COMMENT '内容',
    `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
    `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;