#// script de generacion de stylefile

#// configuracion de nuevo objeto stylefile acciones
INSERT INTO `Resource` (`id`,`resourceName`,`nickName`) VALUES
(92,'admin_stylefile_index','View stylefile'),
(93,'admin_stylefile_editdegree','Edit stylefile'),
(94,'admin_stylefile_delete','Delete stylefile'),
(95,'admin_stylefile_savedegree','Save stylefile'),
(96,'admin_stylefile_degreegrid','Load stylefile');

#// configuracion de roles y permisos
INSERT INTO `RoleResource` (`idRole`,`idResource`,`access`) VALUES (3,96,1),(4,96,1),(2,96,1),(1,94,1),(1,93,1),(1,96,1),(1,95,1),(1,92,1);

#// -1 tabla de Stylefile
CREATE TABLE `StyleFile` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `styleDescription` varchar(100) CHARACTER SET utf8 NOT NULL,
  `styleShortDescription` varchar(100) CHARACTER SET utf8 NOT NULL,
  `stylefile` varchar(100) CHARACTER SET utf8 NOT NULL,
  `oldId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci

#// -2 tabla de log stylefile
CREATE TABLE `StyleFileLog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) CHARACTER SET utf8 NOT NULL,
  `action` varchar(100) CHARACTER SET utf8 NOT NULL,
  `stylefile` varchar(100) CHARACTER SET utf8 NOT NULL,
  `oldId` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci
