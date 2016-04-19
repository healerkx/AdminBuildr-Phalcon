
CREATE TABLE `kx_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `user_name` varchar(128) NOT NULL DEFAULT '' COMMENT '用户名',
  `company_id` int(11) NOT NULL DEFAULT '0' COMMENT '公司ID',
  `user_mobile` varchar(12) NOT NULL DEFAULT '' COMMENT '用户手机',
  `user_age` int(11) NOT NULL DEFAULT '0' COMMENT '用户年龄',
  `user_avatar` varchar(128) NOT NULL DEFAULT '' COMMENT '用户头像',
  `user_resume` varchar(128) NOT NULL DEFAULT '' COMMENT '用户简历',
  `province` int(11) NOT NULL DEFAULT '0' COMMENT '省ID',
  `city` int(11) NOT NULL DEFAULT '0' COMMENT '市ID',
  `county` int(11) NOT NULL DEFAULT '0' COMMENT '区ID',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表';


CREATE TABLE `kx_ompany` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`company_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='公司表';
