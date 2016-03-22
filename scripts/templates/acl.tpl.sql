
CREATE TABLE `kx_admin_role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '角色id',
  `name` varchar(32) NOT NULL COMMENT '角色名',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '角色描述',
  `access_status` tinyint(4) DEFAULT '1' COMMENT '0,表示不可删除',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='角色表';


CREATE TABLE `kx_admin_user` (
  `admin_uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `nickname` varchar(32) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` char(15) DEFAULT NULL,
  `create_uid` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='后台用户信息表';


CREATE TABLE `kx_admin_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `admin_uid` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='用户角色关联表';


CREATE TABLE `kx_admin_access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',  
  PRIMARY KEY (`access_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='访问权限表';



CREATE TABLE `kx_admin_node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(100) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='操作节点表';


CREATE TABLE `kx_admin_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `level` tinyint(11) NOT NULL COMMENT '0:Group; 1:Node',
  `node_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `sort` int(11) NOT NULL,
  `icon` varchar(32) NOT NULL COMMENT '图标',
  `status` tinyint(4) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间', 
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='菜单列表';
