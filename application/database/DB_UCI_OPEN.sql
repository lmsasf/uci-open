-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 16-03-2016 a las 14:21:20
-- Versión del servidor: 5.1.73
-- Versión de PHP: 5.3.3-7+squeeze19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `uci_open`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Ads`
--

DROP TABLE IF EXISTS `Ads`;
CREATE TABLE IF NOT EXISTS `Ads` (
  `idAds` int(10) NOT NULL AUTO_INCREMENT,
  `idAdsType` int(11) DEFAULT NULL,
  `userId` int(10) DEFAULT NULL,
  `adsRedirectURL` varchar(100) DEFAULT NULL,
  `adsImageURL` varchar(100) DEFAULT NULL,
  `adsBeginDate` date DEFAULT NULL,
  `adsEndDate` date DEFAULT NULL,
  `adsActive` varchar(45) DEFAULT NULL,
  `adsSection` int(3) DEFAULT NULL,
  `adsName` varchar(100) DEFAULT NULL,
  `idOCWTypes` varchar(45) DEFAULT NULL,
  `adsAllCategories` tinyint(1) DEFAULT NULL,
  `adsWithPages` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idAds`),
  KEY `FK_sponsor_person` (`userId`),
  KEY `fk_Ads_AdsType1_idx` (`idAdsType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Volcar la base de datos para la tabla `Ads`
--

INSERT INTO `Ads` (`idAds`, `idAdsType`, `userId`, `adsRedirectURL`, `adsImageURL`, `adsBeginDate`, `adsEndDate`, `adsActive`, `adsSection`, `adsName`, `idOCWTypes`, `adsAllCategories`, `adsWithPages`) VALUES
(5, 1, NULL, '', '.', '2016-02-15', NULL, '1', 0, 'Promo1', '1', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AdsCategory`
--

DROP TABLE IF EXISTS `AdsCategory`;
CREATE TABLE IF NOT EXISTS `AdsCategory` (
  `idAds` int(10) NOT NULL,
  `idCat` int(11) unsigned NOT NULL,
  `idAdsCategory` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idAdsCategory`),
  KEY `fk_AdsCategory_Ads1_idx` (`idAds`),
  KEY `fk_AdsCategory_Category1_idx` (`idCat`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `AdsCategory`
--

INSERT INTO `AdsCategory` (`idAds`, `idCat`, `idAdsCategory`) VALUES
(5, 19, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AdsOCW`
--

DROP TABLE IF EXISTS `AdsOCW`;
CREATE TABLE IF NOT EXISTS `AdsOCW` (
  `idAdsOCW` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idAds` int(10) NOT NULL,
  `idOCW` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idAdsOCW`),
  KEY `fk_AdsOCW_Ads1_idx` (`idAds`),
  KEY `fk_AdsOCW_OCW1_idx` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `AdsOCW`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AdsType`
--

DROP TABLE IF EXISTS `AdsType`;
CREATE TABLE IF NOT EXISTS `AdsType` (
  `idAdsType` int(11) NOT NULL AUTO_INCREMENT,
  `adsTypName` varchar(100) NOT NULL,
  `adsTypVisibility` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idAdsType`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `AdsType`
--

INSERT INTO `AdsType` (`idAdsType`, `adsTypName`, `adsTypVisibility`) VALUES
(1, 'Donation', 1),
(2, 'Sponsortship', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Author`
--

DROP TABLE IF EXISTS `Author`;
CREATE TABLE IF NOT EXISTS `Author` (
  `idPer` int(11) NOT NULL,
  `idOCW` int(10) unsigned NOT NULL,
  `autSequence` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idPer`,`idOCW`),
  KEY `FK_author_OCW` (`idOCW`),
  KEY `FK_author_person` (`idPer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `Author`
--

INSERT INTO `Author` (`idPer`, `idOCW`, `autSequence`) VALUES
(9, 4184, 0),
(135, 4183, 0),
(135, 4185, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `AuthorOCW`
--

DROP TABLE IF EXISTS `AuthorOCW`;
CREATE TABLE IF NOT EXISTS `AuthorOCW` (
  `idPer` int(11) NOT NULL,
  `idDeg` int(11) NOT NULL,
  `idOCW` int(11) NOT NULL,
  PRIMARY KEY (`idPer`,`idDeg`,`idOCW`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `AuthorOCW`
--

INSERT INTO `AuthorOCW` (`idPer`, `idDeg`, `idOCW`) VALUES
(0, 0, 4183),
(0, 0, 4184),
(0, 0, 4185),
(33, 1, 81),
(33, 1, 609),
(33, 1, 2350),
(33, 1, 2351),
(33, 1, 2352),
(33, 1, 2353),
(33, 1, 2354),
(33, 1, 2355),
(33, 1, 2356),
(33, 1, 2357),
(33, 1, 2358),
(33, 1, 2359),
(33, 1, 2360),
(33, 1, 2361),
(33, 1, 2362),
(33, 1, 2363),
(36, 1, 95),
(36, 1, 2436),
(36, 1, 2437),
(36, 1, 2438),
(36, 1, 2439),
(36, 1, 2440),
(36, 1, 2441),
(36, 1, 2442),
(36, 1, 2443),
(36, 1, 2444),
(36, 1, 2445),
(36, 1, 2446),
(36, 1, 2447),
(36, 1, 2448),
(36, 1, 2450),
(36, 1, 2451),
(36, 1, 2452),
(36, 1, 2453),
(36, 1, 2454),
(36, 1, 2455),
(36, 1, 2456),
(36, 1, 2621),
(36, 1, 2622),
(36, 1, 2623),
(36, 1, 2624),
(36, 1, 2625),
(42, 4, 88),
(43, 1, 606),
(64, 1, 2528),
(65, 1, 15),
(65, 1, 20),
(65, 1, 353),
(65, 1, 354),
(65, 1, 355),
(65, 1, 356),
(65, 1, 357),
(65, 1, 358),
(65, 1, 359),
(65, 1, 360),
(65, 1, 361),
(65, 1, 362),
(65, 1, 363),
(65, 1, 364),
(65, 1, 365),
(65, 1, 366),
(65, 1, 367),
(65, 1, 368),
(65, 1, 369),
(65, 1, 370),
(65, 1, 371),
(65, 1, 372),
(65, 1, 373),
(65, 1, 374),
(65, 1, 375),
(65, 1, 376),
(65, 1, 377),
(65, 1, 378),
(65, 1, 379),
(65, 1, 380),
(65, 1, 381),
(65, 1, 382),
(65, 1, 383),
(65, 1, 384),
(65, 1, 385),
(65, 1, 386),
(65, 1, 387),
(65, 1, 388),
(65, 1, 389),
(65, 1, 390),
(65, 1, 391),
(65, 1, 392),
(65, 1, 393),
(65, 1, 394),
(65, 1, 395),
(65, 1, 396),
(65, 1, 397),
(65, 1, 398),
(65, 1, 399),
(65, 1, 400),
(65, 1, 401),
(65, 1, 402),
(65, 1, 403),
(65, 1, 404),
(65, 1, 405),
(65, 1, 406),
(65, 1, 407),
(65, 1, 408),
(65, 1, 434),
(65, 1, 435),
(65, 1, 436),
(65, 1, 437),
(65, 1, 438),
(65, 1, 439),
(65, 1, 440),
(65, 1, 441),
(65, 1, 442),
(65, 1, 443),
(65, 1, 444),
(65, 1, 445),
(65, 1, 446),
(65, 1, 447),
(65, 1, 448),
(65, 1, 449),
(65, 1, 450),
(65, 1, 451),
(65, 1, 452),
(78, 1, 605),
(84, 1, 607),
(87, 1, 84),
(87, 1, 85),
(87, 1, 86),
(87, 1, 1165),
(87, 1, 1348),
(87, 1, 1378),
(87, 1, 1379),
(87, 1, 1380),
(87, 1, 1381),
(87, 1, 1438),
(87, 1, 1485),
(87, 1, 1491),
(87, 1, 1495),
(87, 1, 1497),
(87, 1, 1499),
(87, 1, 1502),
(87, 1, 1504),
(87, 1, 1506),
(87, 1, 1510),
(87, 1, 1512),
(87, 1, 1513),
(87, 1, 1516),
(87, 1, 1561),
(87, 1, 1614),
(87, 1, 1657),
(87, 1, 1659),
(87, 1, 1700),
(87, 1, 2382),
(87, 1, 2383),
(87, 1, 2384),
(87, 1, 2385),
(87, 1, 2386),
(87, 1, 2387),
(87, 1, 2388),
(87, 1, 2389),
(87, 1, 2390),
(87, 1, 2391),
(87, 1, 2392),
(87, 1, 2393),
(87, 1, 2394),
(87, 1, 2395),
(87, 1, 2396),
(87, 1, 2397),
(87, 1, 2398),
(87, 1, 2402),
(87, 1, 2403),
(87, 1, 2404),
(87, 1, 2406),
(87, 1, 2408),
(87, 1, 2409),
(87, 1, 2410),
(87, 1, 2411),
(87, 1, 2412),
(87, 1, 2413),
(87, 1, 2414),
(87, 1, 2415),
(87, 1, 2416),
(87, 1, 2417),
(87, 1, 2418),
(87, 1, 2419),
(87, 1, 2420),
(87, 1, 2421),
(87, 1, 2422),
(87, 1, 2424),
(87, 1, 2426),
(87, 1, 2427),
(87, 1, 2428),
(87, 1, 2429),
(87, 1, 2430),
(87, 1, 2431),
(87, 1, 2432),
(87, 1, 2459),
(87, 1, 2460),
(87, 1, 2461),
(87, 1, 2462),
(87, 1, 2463),
(87, 1, 2464),
(87, 1, 2465),
(87, 1, 2466),
(87, 1, 2467),
(87, 1, 2468),
(87, 1, 2469),
(87, 1, 2470),
(87, 1, 2471),
(87, 1, 2472),
(87, 1, 2474),
(87, 1, 2475),
(87, 1, 2476),
(87, 1, 2477),
(87, 1, 2478),
(87, 1, 2479),
(87, 1, 2482),
(87, 1, 2483),
(87, 1, 2484),
(87, 1, 2485),
(87, 1, 2486),
(87, 1, 2487),
(87, 1, 2488),
(87, 1, 2489),
(87, 1, 2490),
(87, 1, 2491),
(87, 1, 2492),
(87, 1, 2626),
(87, 1, 2627),
(87, 1, 2628),
(87, 1, 2630),
(87, 1, 2631),
(87, 1, 2633),
(87, 1, 2634),
(87, 1, 2635),
(87, 1, 2636),
(94, 1, 79),
(94, 1, 2494),
(96, 1, 601),
(96, 1, 602),
(96, 1, 604),
(102, 1, 788),
(102, 1, 789),
(102, 1, 790),
(102, 1, 791),
(102, 1, 792),
(102, 1, 793),
(102, 1, 794),
(102, 1, 797),
(102, 1, 802),
(102, 1, 2273),
(102, 1, 2946),
(102, 1, 3458),
(102, 1, 3460),
(102, 1, 3464),
(105, 1, 3857),
(107, 1, 80),
(107, 1, 1407),
(107, 1, 1550),
(107, 1, 1551),
(107, 1, 1552),
(107, 1, 1553),
(107, 1, 1554),
(107, 1, 1555),
(107, 1, 1556),
(107, 1, 1557),
(135, 3, 2177),
(135, 3, 3819),
(141, 7, 131),
(184, 1, 11),
(184, 1, 3701),
(185, 1, 2933),
(185, 1, 3367),
(185, 1, 3368),
(185, 1, 3371),
(185, 1, 3679),
(203, 5, 2513),
(211, 1, 3034),
(211, 1, 3035),
(211, 1, 3036),
(211, 1, 3037),
(211, 1, 3038),
(211, 1, 3039),
(211, 1, 3040),
(211, 1, 3044),
(211, 1, 3049),
(211, 1, 3051),
(211, 1, 3059),
(211, 1, 3060),
(211, 1, 3061),
(211, 1, 3062),
(211, 1, 3063),
(211, 1, 3064),
(211, 1, 3065),
(211, 1, 3066),
(211, 1, 3067),
(211, 1, 3068),
(211, 1, 3069),
(211, 1, 3253),
(211, 1, 3254),
(211, 1, 3256),
(211, 1, 3257),
(211, 1, 3258),
(231, 1, 18),
(231, 1, 3703),
(251, 1, 7),
(275, 1, 3673),
(276, 1, 2531),
(277, 1, 728),
(277, 1, 3681),
(286, 1, 128),
(288, 7, 797),
(291, 7, 799),
(292, 1, 801),
(293, 7, 800),
(297, 1, 802),
(299, 1, 133),
(301, 7, 795),
(302, 7, 803),
(303, 7, 796),
(304, 1, 4),
(304, 1, 19),
(304, 1, 1151),
(304, 1, 3835),
(306, 1, 585),
(306, 1, 2619),
(307, 1, 2617),
(309, 2, 788),
(318, 1, 792),
(339, 1, 684),
(339, 1, 685),
(339, 1, 686),
(339, 1, 687),
(339, 1, 688),
(339, 1, 689),
(339, 1, 690),
(339, 1, 691),
(339, 1, 692),
(339, 1, 693),
(339, 1, 694),
(339, 1, 695),
(339, 1, 696),
(339, 1, 697),
(339, 1, 698),
(339, 1, 699),
(339, 1, 700),
(339, 1, 701),
(339, 1, 702),
(339, 1, 703),
(339, 1, 704),
(339, 1, 705),
(339, 1, 706),
(339, 1, 2399),
(339, 1, 2405),
(340, 1, 720),
(340, 1, 721),
(340, 1, 722),
(340, 1, 723),
(340, 1, 724),
(340, 1, 725),
(340, 1, 2917),
(346, 1, 2549),
(352, 1, 2886),
(352, 1, 2898),
(352, 1, 2899),
(352, 1, 2900),
(352, 1, 2901),
(352, 1, 2938),
(352, 1, 2963),
(352, 1, 2964),
(352, 1, 2965),
(352, 1, 2966),
(352, 1, 2975),
(352, 1, 2976),
(352, 1, 2977),
(352, 1, 2978),
(352, 1, 2993),
(352, 1, 3070),
(352, 1, 3071),
(352, 1, 3072),
(352, 1, 3121),
(352, 1, 3122),
(352, 1, 3125),
(352, 1, 3142),
(352, 1, 3376),
(353, 1, 2681),
(353, 1, 2825),
(353, 1, 2826),
(353, 1, 2827),
(353, 1, 2828),
(353, 1, 2829),
(353, 1, 2830),
(353, 1, 2831),
(353, 1, 2874),
(353, 1, 2875),
(353, 1, 2876),
(353, 1, 2877),
(353, 1, 2878),
(353, 1, 2879),
(353, 1, 2881),
(353, 1, 2882),
(353, 1, 2883),
(353, 1, 2884),
(353, 1, 2885),
(353, 1, 2925),
(354, 1, 2931),
(355, 1, 2909),
(355, 1, 2936),
(356, 1, 2732),
(356, 1, 2736),
(356, 1, 2737),
(356, 1, 2759),
(356, 1, 2798),
(356, 1, 2807),
(356, 1, 2808),
(356, 1, 2809),
(356, 1, 2867),
(356, 1, 2870),
(356, 1, 2871),
(356, 1, 2872),
(356, 1, 2908),
(356, 1, 2909),
(356, 1, 2936),
(361, 1, 3466),
(368, 1, 3259),
(368, 1, 3275),
(391, 7, 798),
(392, 1, 875),
(406, 1, 3705),
(407, 1, 3619),
(407, 1, 3620),
(407, 1, 3621),
(407, 1, 3622),
(407, 1, 3625),
(407, 1, 3626),
(407, 1, 3627),
(407, 1, 3628),
(407, 1, 3629),
(407, 1, 3630),
(407, 1, 3631),
(407, 1, 3636),
(407, 1, 3643),
(407, 1, 3644),
(407, 1, 3646),
(407, 1, 3647),
(407, 1, 3669),
(407, 1, 3670),
(407, 1, 3671),
(407, 1, 3672),
(407, 1, 3805),
(410, 1, 3705),
(410, 1, 3813);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Books`
--

DROP TABLE IF EXISTS `Books`;
CREATE TABLE IF NOT EXISTS `Books` (
  `idBooks` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bookName` varchar(250) DEFAULT NULL,
  `bookUrl` varchar(250) DEFAULT NULL,
  `bookAuthor` varchar(100) DEFAULT NULL,
  `bookImg` varchar(100) DEFAULT NULL,
  `bookImageURL` varchar(200) DEFAULT NULL,
  `bookISBN` varchar(30) DEFAULT NULL,
  `bookPrincipal` int(2) DEFAULT '0',
  PRIMARY KEY (`idBooks`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `Books`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `BooksOCW`
--

DROP TABLE IF EXISTS `BooksOCW`;
CREATE TABLE IF NOT EXISTS `BooksOCW` (
  `idBooks` int(11) unsigned NOT NULL,
  `idOCW` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idBooks`,`idOCW`),
  KEY `fk_ocw_booksocw_idx` (`idOCW`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `BooksOCW`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `BrokenLinks`
--

DROP TABLE IF EXISTS `BrokenLinks`;
CREATE TABLE IF NOT EXISTS `BrokenLinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ocwID` int(11) NOT NULL,
  `field` varchar(70) NOT NULL,
  `link` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_1` (`field`,`ocwID`,`link`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8344 ;

--
-- Volcar la base de datos para la tabla `BrokenLinks`
--

INSERT INTO `BrokenLinks` (`id`, `ocwID`, `field`, `link`) VALUES
(146, 2304, 'colfrequentlyQuest', 'http://learn.uci.edu/openedweek/opchem.html'),
(3, 6, 'Course.ocwBypassUrlCourse', 'http://www.kocw.net/home/search/kemView.do?kemId=163024'),
(1, 5, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/09s/60235/homepage/index.html'),
(8343, 6, 'Course.ocwUrlCourse', 'http://www.kocw.net/home/search/kemView.do?kemId=163024'),
(4, 20, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/Chem51A/Chem51A_UCI_OCW_Organic_Chemistry_Video_Course.php'),
(5, 23, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/08f/22110/home'),
(8, 49, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/ocw/courses/07f/12070'),
(10, 73, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/11w/mae10/'),
(11, 74, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/11s/19120/MAE-165-advanced-manufacturing-and-rapid-prototyping.html'),
(13, 83, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/11w/54520/index.html'),
(14, 85, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/09w/67170'),
(15, 86, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/08f/67270/index.html'),
(16, 87, 'Course.ocwUrlCourse', 'http://ocw.uci.edu/opencourses/07f/67085/index.html'),
(6, 41, 'description', 'http://learn.uci.edu/oo/getOCWPage.php?course=OC0111223&amp;lesson=1&amp;topic=1&amp;page=1'),
(7, 42, 'description', 'http://learn.uci.edu/oo/getOCWPage.php?course=OC0111223&amp;lesson=1&amp;topic=1&amp;page=1\n'),
(9, 56, 'description', 'http://openstudy.com/channels/UC-Irvine+Fundamentals+of+Clinical+Trials+%28OCW%29'),
(12, 75, 'description', 'http://openstudy.com/channels/UC-Irvine+Medical+Product+Quality+Systems+%28OCW%29'),
(17, 92, 'description', 'http://openstudy.com/channels/UC-Irvine%3A+Regulatory+Requirements+for+Pharmaceutical+Products+%28OCW%29'),
(18, 149, 'description', 'http://ocw.uci.edu/courses/physics_3a_basic_physics.htm'),
(19, 152, 'description', 'http://ocw.uci.edu/courses/physics_3a_basic_physics.html.'),
(20, 340, 'description', 'http://learn.uci.edu/openchem'),
(22, 341, 'description', 'http://learn.uci.edu/openchem'),
(24, 343, 'description', 'http://learn.uci.edu/openchem'),
(26, 344, 'description', 'http://learn.uci.edu/openchem'),
(28, 349, 'description', 'http://learn.uci.edu/openchem'),
(30, 353, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(31, 354, 'description', 'http://learn.uci.edu/openchem'),
(33, 355, 'description', 'http://learn.uci.edu/openchem'),
(35, 356, 'description', 'http://learn.uci.edu/openchem'),
(37, 357, 'description', 'http://learn.uci.edu/openchem'),
(40, 358, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(41, 359, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(42, 360, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(43, 361, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(44, 362, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(45, 363, 'description', 'http://learn.uci.edu/openchem'),
(47, 364, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(48, 365, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(49, 366, 'description', 'http://learn.uci.edu/openchem'),
(52, 367, 'description', 'http://learn.uci.edu/openchem'),
(55, 369, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(56, 370, 'description', 'http://learn.uci.edu/openchem'),
(59, 372, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(60, 373, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(61, 374, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(62, 375, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(63, 376, 'description', 'http://learn.uci.edu/openchem'),
(65, 376, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(66, 377, 'description', 'http://learn.uci.edu/openchem'),
(67, 377, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(68, 378, 'description', 'http://learn.uci.edu/openchem'),
(69, 378, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(70, 379, 'description', 'http://learn.uci.edu/openchem'),
(71, 379, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(72, 380, 'description', 'http://learn.uci.edu/openchem'),
(74, 380, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(75, 381, 'description', 'http://learn.uci.edu/openchem'),
(77, 381, 'description', 'http://ocw.uci.edu/courses/chem_203_organic_spectroscopy.htm'),
(1475, 382, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1477, 383, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1479, 384, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1481, 385, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1483, 386, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1485, 387, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1487, 388, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1489, 389, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1491, 390, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1493, 391, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1495, 392, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1497, 393, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1499, 394, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1501, 395, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1503, 396, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1505, 397, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1507, 398, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1509, 399, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1511, 400, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1513, 401, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1515, 402, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1517, 403, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1519, 404, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1521, 405, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1523, 406, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1525, 407, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1527, 408, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(78, 409, 'description', 'http://learn.uci.edu/openchem.html'),
(79, 415, 'description', 'http://learn.uci.edu/openchem.html'),
(80, 420, 'description', 'http://learn.uci.edu/openchem.html'),
(81, 425, 'description', 'http://learn.uci.edu/openchem.html'),
(82, 428, 'description', 'http://learn.uci.edu/openchem.html'),
(84, 432, 'description', 'http://learn.uci.edu/openchem.html'),
(85, 434, 'description', 'http://learn.uci.edu/openchem'),
(86, 453, 'description', 'http://learn.uci.edu/openchem.html'),
(87, 454, 'description', 'http://learn.uci.edu/openchem.html'),
(88, 455, 'description', 'http://learn.uci.edu/openchem.html'),
(89, 456, 'description', 'http://learn.uci.edu/openchem.html'),
(90, 457, 'description', 'http://learn.uci.edu/openchem.html'),
(91, 458, 'description', 'http://learn.uci.edu/openchem.html'),
(92, 459, 'description', 'http://learn.uci.edu/openchem.html'),
(93, 460, 'description', 'http://learn.uci.edu/openchem.html'),
(94, 461, 'description', 'http://learn.uci.edu/openchem.html'),
(95, 464, 'description', 'http://learn.uci.edu/openchem.html'),
(96, 468, 'description', 'http://learn.uci.edu/openchem.html'),
(97, 469, 'description', 'http://learn.uci.edu/openchem.html'),
(98, 470, 'description', 'http://learn.uci.edu/openchem.html'),
(99, 471, 'description', 'http://learn.uci.edu/openchem.html'),
(100, 472, 'description', 'http://learn.uci.edu/openchem.html'),
(101, 473, 'description', 'http://learn.uci.edu/openchem.html'),
(102, 474, 'description', 'http://learn.uci.edu/openchem.html'),
(103, 475, 'description', 'http://learn.uci.edu/openchem.html'),
(104, 476, 'description', 'http://learn.uci.edu/openchem.html'),
(105, 477, 'description', 'http://learn.uci.edu/openchem.html'),
(106, 580, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(107, 581, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(108, 582, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(109, 583, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(110, 584, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(111, 585, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(112, 586, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(113, 587, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(114, 588, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(115, 589, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(116, 590, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(117, 591, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(118, 592, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(119, 593, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(120, 594, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(121, 595, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(122, 596, 'description', 'http://ocw.uci.edu/courses/mae_91_introduction_to_thermal_dynamics.html'),
(123, 624, 'description', 'http://learn.uci.edu/openchem'),
(125, 717, 'description', 'http://cast.nacs.uci.edu/ocw/collections/public_health/2010_UCI_Exercise_Objective.doc'),
(1573, 823, 'description', 'http://www.cgpacs.uci.edu/cgpacs_about'),
(126, 823, 'description', 'http://www.uci.edu/experts/video.php?format=mov&amp;res=low&amp;src=levine'),
(1577, 867, 'description', 'http://www.cgpacs.uci.edu/cgpacs_about'),
(128, 867, 'description', 'http://www.humanities.uci.edu/history/faculty_profile_millward.php'),
(135, 2176, 'description', 'http://learn.uci.edu/cms/course/view.php?id=2829'),
(155, 2544, 'description', 'http://learn.uci.edu/openchem'),
(156, 2548, 'description', 'http://learn.uci.edu/openchem'),
(157, 2550, 'description', 'http://learn.uci.edu/openchem'),
(174, 2681, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(176, 2825, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(178, 2826, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(180, 2827, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(182, 2828, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(184, 2829, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(186, 2830, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(188, 2831, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(190, 2864, 'description', 'http://ocw.uci.edu/courses/biosci_m121_immunology_with_hematology.html.'),
(195, 2874, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(197, 2875, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(200, 2876, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(202, 2877, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(205, 2878, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(207, 2879, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(210, 2881, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(213, 2882, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(215, 2883, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(217, 2884, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(219, 2885, 'description', 'http://ocw.uci.edu/courses/ess_23_air_pollution.html'),
(221, 2888, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(223, 2889, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(225, 2890, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(227, 2894, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(229, 2895, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(259, 2939, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(262, 2940, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(265, 2941, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(287, 2983, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(289, 2984, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(291, 2985, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(293, 3012, 'description', 'http://ocw.uci.edu/courses/math_1a1b_precalculus.html.'),
(322, 3126, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(324, 3127, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(326, 3128, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(328, 3129, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(330, 3130, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(332, 3131, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(334, 3132, 'description', 'http://ocw.uci.edu/courses/psych_9a_psychology_fundamentals.html'),
(354, 3257, 'description', 'http://learn.uci.edu/openchem'),
(357, 3398, 'description', 'http://ocw.uci.edu/courses/einsteins_general_relativity_and_gravitation.htm'),
(1856, 3625, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1858, 3627, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1859, 3629, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1862, 3644, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1864, 3646, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(1866, 3647, 'description', 'http://ocw.uci.edu/courses/chem_51a_organic_chemistry.html'),
(7545, 3666, 'description', 'http://ocw.uci.edu/courses/the_power_of_macroeconomics_economic_principles_in_the_real_world.html'),
(7547, 3667, 'description', 'http://ocw.uci.edu/courses/the_power_of_macroeconomics_economic_principles_in_the_real_world.html'),
(7549, 3668, 'description', 'http://ocw.uci.edu/courses/the_power_of_macroeconomics_economic_principles_in_the_real_world.html'),
(7782, 3708, 'description', 'http://ocw.uci.edu/courses/the_power_of_macroeconomics_economic_principles_in_the_real_world.htm'),
(129, 1381, 'File.ocwUrlFile', 'https://eee.uci.edu/08f/67270/home/Midterm+Essay+Question.doc'),
(130, 1438, 'File.ocwUrlFile', 'https://eee.uci.edu/08f/67270/home/Pool+of+IDs+for+Exam.doc'),
(131, 1515, 'File.ocwUrlFile', 'https://eee.uci.edu/11w/54520'),
(132, 1518, 'File.ocwUrlFile', 'https://eee.uci.edu/11w/54520'),
(133, 1519, 'File.ocwUrlFile', 'https://eee.uci.edu/11w/54520'),
(134, 1561, 'File.ocwUrlFile', 'https://eee.uci.edu/08f/67270/home/Paper+Topics.pdf'),
(136, 2270, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Ogunseitan+-+Environmental+Diseases+-+2007.pdf'),
(137, 2271, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/small+pox+article.pdf'),
(138, 2276, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Burden+of+Disease+Methods.pdf'),
(139, 2277, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Environmental+Health.mp3'),
(140, 2283, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/OCHCA+-+Public+Health+Bulletin.pdf'),
(141, 2284, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Orange+County+Reportable+Diseases.pdf'),
(142, 2285, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/2007-Substance-Expose-Baby.pdf'),
(143, 2286, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Orange+County+Cancer+Survey_2001_2005.pdf'),
(144, 2291, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Obesity+Virus.pdf'),
(145, 2292, 'File.ocwUrlFile', 'https://eee.uci.edu/09f/89300/homepage/Ethics+and+Utopia+-+Public+Health+Theory+and+Practice+in+the+16th+Century.pdf'),
(8154, 2300, 'File.ocwUrlFile', 'http://ehs.sph.berkeley.edu/krsmith/publications/2005%20pubs/ARER.pdf'),
(148, 2403, 'File.ocwUrlFile', 'https://eee.uci.edu/07f/67085/TAs.doc'),
(149, 2411, 'File.ocwUrlFile', 'https://eee.uci.edu/07f/67085/U.S.+Legal+Permanent+Residents+2006.pdf'),
(150, 2413, 'File.ocwUrlFile', 'https://eee.uci.edu/07f/67085/Race+and+Ethnicity+Questions++2000+Census.pdf'),
(151, 2425, 'File.ocwUrlFile', 'https://eee.uci.edu/07f/67085/home/Obituary+of+Augustus+Hawkins.doc'),
(380, 3482, 'File.ocwUrlFile', 'https://eee.uci.edu/14w/68080/home/Lecture011414.ppt'),
(124, 674, 'Lecture.ocwUrlLecture', 'http://cast.nacs.uci.edu/ocw/arts/music/crooks/crooks_intro_to_pitch_10_hd.mp4'),
(127, 834, 'Lecture.ocwUrlLecture', 'http://cast.nacs.uci.edu/ocw/collections/CUSA/20110414_sexton_sustainability_and_conflict.mp4');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Category`
--

DROP TABLE IF EXISTS `Category`;
CREATE TABLE IF NOT EXISTS `Category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catName` varchar(100) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `posicion` int(10) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=94 ;

--
-- Volcar la base de datos para la tabla `Category`
--

INSERT INTO `Category` (`id`, `catName`, `lft`, `rgt`, `posicion`) VALUES
(1, 'Arts', 1, 4, 0),
(4, 'Engineering', 9, 10, 5),
(8, 'Biological Sciences', 5, 8, 3),
(19, 'Music', 2, 3, 2),
(24, 'Developmental & Cell Biology', 6, 7, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Collection`
--

DROP TABLE IF EXISTS `Collection`;
CREATE TABLE IF NOT EXISTS `Collection` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idOCW` int(10) unsigned NOT NULL,
  `colfrequentlyQuest` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_collectionS_OCW` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

--
-- Volcar la base de datos para la tabla `Collection`
--

INSERT INTO `Collection` (`id`, `idOCW`, `colfrequentlyQuest`) VALUES
(49, 4185, '<p><br></p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Conference`
--

DROP TABLE IF EXISTS `Conference`;
CREATE TABLE IF NOT EXISTS `Conference` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idOCW` int(10) unsigned NOT NULL,
  `confrequentlyQuest` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_conference_OCW` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Volcar la base de datos para la tabla `Conference`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Contact`
--

DROP TABLE IF EXISTS `Contact`;
CREATE TABLE IF NOT EXISTS `Contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conFirstName` varchar(45) NOT NULL,
  `conLastName` varchar(45) NOT NULL,
  `conEmail` varchar(45) NOT NULL,
  `conRole` varchar(45) NOT NULL,
  `conCountry` varchar(100) NOT NULL,
  `conInquiriType` varchar(100) NOT NULL,
  `conComents` text,
  `conRead` tinyint(1) DEFAULT '0',
  `conDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `Contact`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ContactUSSettings`
--

DROP TABLE IF EXISTS `ContactUSSettings`;
CREATE TABLE IF NOT EXISTS `ContactUSSettings` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `educationalRole` text NOT NULL,
  `natureOfInquiry` text NOT NULL,
  `helpBlock` text NOT NULL,
  `contactInfo` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `ContactUSSettings`
--

INSERT INTO `ContactUSSettings` (`Id`, `title`, `description`, `educationalRole`, `natureOfInquiry`, `helpBlock`, `contactInfo`) VALUES
(1, 'Contact us', '<p>Please take a moment to complete a brief user survey below. &nbsp;Your feedback will be critical as we continue to make enhancements to our site. &nbsp;All information provided will be held confidential by UC Irvine staff. &nbsp;Thank you for your feedback.</p>', ',Independent Learner,UCI Alumnus/Alumna,Student - High School,Student - College/University,Student - Other,Educator - High School,Educator - College/University,Educator - Other,Parent - High School,Parent - College/University,Parent - Other', 'Technical,Course Content,Intellectual Property,Feedback,Accessibility,Other,', '<p>Please provide your comments on an OpenCourseWare lecture, course, conference or collection or general comments about UCI OpenCourseWare.</p>', '<p><strong>Address</strong>: 110 Theory, Suite 250, Irvine, California 5555</p><p><strong>Phone</strong>: 949-824-4119</p><p><strong>E-mai</strong>l: ocw@uci.edu</p>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Countries`
--

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE IF NOT EXISTS `Countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=242 ;

--
-- Volcar la base de datos para la tabla `Countries`
--

INSERT INTO `Countries` (`id`, `code`, `name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'AS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antarctica'),
(9, 'AG', 'Antigua and Barbuda'),
(10, 'AR', 'Argentina'),
(11, 'AM', 'Armenia'),
(12, 'AW', 'Aruba'),
(13, 'AU', 'Australia'),
(14, 'AT', 'Austria'),
(15, 'AZ', 'Azerbaijan'),
(16, 'BH', 'Bahrain'),
(17, 'BD', 'Bangladesh'),
(18, 'BB', 'Barbados'),
(19, 'BY', 'Belarus'),
(20, 'BE', 'Belgium'),
(21, 'BZ', 'Belize'),
(22, 'BJ', 'Benin'),
(23, 'BM', 'Bermuda'),
(24, 'BT', 'Bhutan'),
(25, 'BO', 'Bolivia'),
(26, 'BA', 'Bosnia and Herzegovina'),
(27, 'BW', 'Botswana'),
(28, 'BV', 'Bouvet Island'),
(29, 'BR', 'Brazil'),
(30, 'IO', 'British Indian Ocean Territory'),
(31, 'VG', 'British Virgin Islands'),
(32, 'BN', 'Brunei'),
(33, 'BG', 'Bulgaria'),
(34, 'BF', 'Burkina Faso'),
(35, 'BI', 'Burundi'),
(36, 'CI', 'Côte d''Ivoire'),
(37, 'KH', 'Cambodia'),
(38, 'CM', 'Cameroon'),
(39, 'CA', 'Canada'),
(40, 'CV', 'Cape Verde'),
(41, 'KY', 'Cayman Islands'),
(42, 'CF', 'Central African Republic'),
(43, 'TD', 'Chad'),
(44, 'CL', 'Chile'),
(45, 'CN', 'China'),
(46, 'CX', 'Christmas Island'),
(47, 'CC', 'Cocos (Keeling) Islands'),
(48, 'CO', 'Colombia'),
(49, 'KM', 'Comoros'),
(50, 'CG', 'Congo'),
(51, 'CK', 'Cook Islands'),
(52, 'CR', 'Costa Rica'),
(53, 'HR', 'Croatia'),
(54, 'CU', 'Cuba'),
(55, 'CY', 'Cyprus'),
(56, 'CZ', 'Czech Republic'),
(57, 'CD', 'Democratic Republic of the Congo'),
(58, 'DK', 'Denmark'),
(59, 'DJ', 'Djibouti'),
(60, 'DM', 'Dominica'),
(61, 'DO', 'Dominican Republic'),
(62, 'TP', 'East Timor'),
(63, 'EC', 'Ecuador'),
(64, 'EG', 'Egypt'),
(65, 'SV', 'El Salvador'),
(66, 'GQ', 'Equatorial Guinea'),
(67, 'ER', 'Eritrea'),
(68, 'EE', 'Estonia'),
(69, 'ET', 'Ethiopia'),
(70, 'FO', 'Faeroe Islands'),
(71, 'FK', 'Falkland Islands'),
(72, 'FJ', 'Fiji'),
(73, 'FI', 'Finland'),
(74, 'MK', 'Former Yugoslav Republic of Macedonia'),
(75, 'FR', 'France'),
(76, 'FX', 'France, Metropolitan'),
(77, 'GF', 'French Guiana'),
(78, 'PF', 'French Polynesia'),
(79, 'TF', 'French Southern Territories'),
(80, 'GA', 'Gabon'),
(81, 'GE', 'Georgia'),
(82, 'DE', 'Germany'),
(83, 'GH', 'Ghana'),
(84, 'GI', 'Gibraltar'),
(85, 'GR', 'Greece'),
(86, 'GL', 'Greenland'),
(87, 'GD', 'Grenada'),
(88, 'GP', 'Guadeloupe'),
(89, 'GU', 'Guam'),
(90, 'GT', 'Guatemala'),
(91, 'GN', 'Guinea'),
(92, 'GW', 'Guinea-Bissau'),
(93, 'GY', 'Guyana'),
(94, 'HT', 'Haiti'),
(95, 'HM', 'Heard and Mc Donald Islands'),
(96, 'HN', 'Honduras'),
(97, 'HK', 'Hong Kong'),
(98, 'HU', 'Hungary'),
(99, 'IS', 'Iceland'),
(100, 'IN', 'India'),
(101, 'ID', 'Indonesia'),
(102, 'IR', 'Iran'),
(103, 'IQ', 'Iraq'),
(104, 'IE', 'Ireland'),
(105, 'IL', 'Israel'),
(106, 'IT', 'Italy'),
(107, 'JM', 'Jamaica'),
(108, 'JP', 'Japan'),
(109, 'JO', 'Jordan'),
(110, 'KZ', 'Kazakhstan'),
(111, 'KE', 'Kenya'),
(112, 'KI', 'Kiribati'),
(113, 'KW', 'Kuwait'),
(114, 'KG', 'Kyrgyzstan'),
(115, 'LA', 'Laos'),
(116, 'LV', 'Latvia'),
(117, 'LB', 'Lebanon'),
(118, 'LS', 'Lesotho'),
(119, 'LR', 'Liberia'),
(120, 'LY', 'Libya'),
(121, 'LI', 'Liechtenstein'),
(122, 'LT', 'Lithuania'),
(123, 'LU', 'Luxembourg'),
(124, 'MO', 'Macau'),
(125, 'MG', 'Madagascar'),
(126, 'MW', 'Malawi'),
(127, 'MY', 'Malaysia'),
(128, 'MV', 'Maldives'),
(129, 'ML', 'Mali'),
(130, 'MT', 'Malta'),
(131, 'MH', 'Marshall Islands'),
(132, 'MQ', 'Martinique'),
(133, 'MR', 'Mauritania'),
(134, 'MU', 'Mauritius'),
(135, 'YT', 'Mayotte'),
(136, 'MX', 'Mexico'),
(137, 'FM', 'Micronesia'),
(138, 'MD', 'Moldova'),
(139, 'MC', 'Monaco'),
(140, 'MN', 'Mongolia'),
(141, 'ME', 'Montenegro'),
(142, 'MS', 'Montserrat'),
(143, 'MA', 'Morocco'),
(144, 'MZ', 'Mozambique'),
(145, 'MM', 'Myanmar'),
(146, 'NA', 'Namibia'),
(147, 'NR', 'Nauru'),
(148, 'NP', 'Nepal'),
(149, 'NL', 'Netherlands'),
(150, 'AN', 'Netherlands Antilles'),
(151, 'NC', 'New Caledonia'),
(152, 'NZ', 'New Zealand'),
(153, 'NI', 'Nicaragua'),
(154, 'NE', 'Niger'),
(155, 'NG', 'Nigeria'),
(156, 'NU', 'Niue'),
(157, 'NF', 'Norfolk Island'),
(158, 'KP', 'North Korea'),
(159, 'MP', 'Northern Marianas'),
(160, 'NO', 'Norway'),
(161, 'OM', 'Oman'),
(162, 'PK', 'Pakistan'),
(163, 'PW', 'Palau'),
(164, 'PS', 'Palestine'),
(165, 'PA', 'Panama'),
(166, 'PG', 'Papua New Guinea'),
(167, 'PY', 'Paraguay'),
(168, 'PE', 'Peru'),
(169, 'PH', 'Philippines'),
(170, 'PN', 'Pitcairn Islands'),
(171, 'PL', 'Poland'),
(172, 'PT', 'Portugal'),
(173, 'PR', 'Puerto Rico'),
(174, 'QA', 'Qatar'),
(175, 'RE', 'Reunion'),
(176, 'RO', 'Romania'),
(177, 'RU', 'Russia'),
(178, 'RW', 'Rwanda'),
(179, 'ST', 'São Tomé and Príncipe'),
(180, 'SH', 'Saint Helena'),
(181, 'PM', 'St. Pierre and Miquelon'),
(182, 'KN', 'Saint Kitts and Nevis'),
(183, 'LC', 'Saint Lucia'),
(184, 'VC', 'Saint Vincent and the Grenadines'),
(185, 'WS', 'Samoa'),
(186, 'SM', 'San Marino'),
(187, 'SA', 'Saudi Arabia'),
(188, 'SN', 'Senegal'),
(189, 'RS', 'Serbia'),
(190, 'SC', 'Seychelles'),
(191, 'SL', 'Sierra Leone'),
(192, 'SG', 'Singapore'),
(193, 'SK', 'Slovakia'),
(194, 'SI', 'Slovenia'),
(195, 'SB', 'Solomon Islands'),
(196, 'SO', 'Somalia'),
(197, 'ZA', 'South Africa'),
(198, 'GS', 'South Georgia and the South Sandwich Islands'),
(199, 'KR', 'South Korea'),
(200, 'ES', 'Spain'),
(201, 'LK', 'Sri Lanka'),
(202, 'SD', 'Sudan'),
(203, 'SR', 'Suriname'),
(204, 'SJ', 'Svalbard and Jan Mayen Islands'),
(205, 'SZ', 'Swaziland'),
(206, 'SE', 'Sweden'),
(207, 'CH', 'Switzerland'),
(208, 'SY', 'Syria'),
(209, 'TW', 'Taiwan'),
(210, 'TJ', 'Tajikistan'),
(211, 'TZ', 'Tanzania'),
(212, 'TH', 'Thailand'),
(213, 'BS', 'The Bahamas'),
(214, 'GM', 'The Gambia'),
(215, 'TG', 'Togo'),
(216, 'TK', 'Tokelau'),
(217, 'TO', 'Tonga'),
(218, 'TT', 'Trinidad and Tobago'),
(219, 'TN', 'Tunisia'),
(220, 'TR', 'Turkey'),
(221, 'TM', 'Turkmenistan'),
(222, 'TC', 'Turks and Caicos Islands'),
(223, 'TV', 'Tuvalu'),
(224, 'VI', 'US Virgin Islands'),
(225, 'UG', 'Uganda'),
(226, 'UA', 'Ukraine'),
(227, 'AE', 'United Arab Emirates'),
(228, 'GB', 'United Kingdom'),
(229, 'US', 'United States'),
(230, 'UM', 'United States Minor Outlying Islands'),
(231, 'UY', 'Uruguay'),
(232, 'UZ', 'Uzbekistan'),
(233, 'VU', 'Vanuatu'),
(234, 'VA', 'Vatican City'),
(235, 'VE', 'Venezuela'),
(236, 'VN', 'Vietnam'),
(237, 'WF', 'Wallis and Futuna Islands'),
(238, 'EH', 'Western Sahara'),
(239, 'YE', 'Yemen'),
(240, 'ZM', 'Zambia'),
(241, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Course`
--

DROP TABLE IF EXISTS `Course`;
CREATE TABLE IF NOT EXISTS `Course` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idOCW` int(10) unsigned NOT NULL,
  `ocwOpenstudy` tinyint(1) DEFAULT NULL,
  `ocwOpenstudyUrl` varchar(200) DEFAULT NULL,
  `ocwPartnerUrl` varchar(200) DEFAULT NULL,
  `ocwPartnerName` varchar(50) DEFAULT NULL,
  `creditCredits` int(10) DEFAULT NULL,
  `idCreditType` int(10) DEFAULT NULL,
  `creditUrl` int(10) DEFAULT NULL,
  `ocwUrlCourse` varchar(200) DEFAULT NULL,
  `ocwBypassUrlCourse` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_course_OCW` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=764 ;

--
-- Volcar la base de datos para la tabla `Course`
--

INSERT INTO `Course` (`id`, `idOCW`, `ocwOpenstudy`, `ocwOpenstudyUrl`, `ocwPartnerUrl`, `ocwPartnerName`, `creditCredits`, `idCreditType`, `creditUrl`, `ocwUrlCourse`, `ocwBypassUrlCourse`) VALUES
(763, 4183, 1, '', '', '', 0, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `CreditType`
--

DROP TABLE IF EXISTS `CreditType`;
CREATE TABLE IF NOT EXISTS `CreditType` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `CreditType`
--

INSERT INTO `CreditType` (`id`, `type`) VALUES
(1, 'Degree'),
(2, 'CEU'),
(3, 'CPE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Degree`
--

DROP TABLE IF EXISTS `Degree`;
CREATE TABLE IF NOT EXISTS `Degree` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `degDescription` varchar(100) NOT NULL,
  `degShortDescription` varchar(100) NOT NULL,
  `oldId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Volcar la base de datos para la tabla `Degree`
--

INSERT INTO `Degree` (`id`, `degDescription`, `degShortDescription`, `oldId`) VALUES
(2, 'M.B.A. ', 'Master of Business Administration', NULL),
(3, 'M.A. ', 'Master of Arts', NULL),
(10, 'MPH', 'Master of Public Health', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Department`
--

DROP TABLE IF EXISTS `Department`;
CREATE TABLE IF NOT EXISTS `Department` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idSchool` int(10) DEFAULT NULL,
  `depName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_department_school` (`idSchool`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=154 ;

--
-- Volcar la base de datos para la tabla `Department`
--

INSERT INTO `Department` (`id`, `idSchool`, `depName`) VALUES
(21, 6, 'Mechanical & Aerospace Engineering'),
(22, 1, 'Music'),
(34, 3, 'Business'),
(42, 1, 'Drama'),
(51, 6, 'Electrical Engineering and Computer Science'),
(121, 19, 'Pediatrics'),
(126, 19, 'Health Policy Research Institute'),
(128, 19, 'Psychiatry and Human Behavior'),
(132, 19, 'Medicine'),
(144, 68, 'Law');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_Footer`
--

DROP TABLE IF EXISTS `FE_Footer`;
CREATE TABLE IF NOT EXISTS `FE_Footer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idArea` int(11) DEFAULT NULL,
  `fooDescription` varchar(500) DEFAULT NULL,
  `fooSequence` int(11) DEFAULT NULL,
  `fooImageUrl` varchar(100) DEFAULT NULL,
  `fooUrl` varchar(500) DEFAULT NULL,
  `fooInclude` tinyint(1) DEFAULT NULL,
  `headerInclude` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Volcar la base de datos para la tabla `FE_Footer`
--

INSERT INTO `FE_Footer` (`id`, `idArea`, `fooDescription`, `fooSequence`, `fooImageUrl`, `fooUrl`, `fooInclude`, `headerInclude`) VALUES
(1, 2, 'Home', 1, NULL, 'http://ocw.uci.edu', 1, 0),
(2, 2, 'Collections', 2, NULL, 'http://ocw.uci.edu/collections', 1, 0),
(3, 2, 'Courses', 3, NULL, 'http://ocw.uci.edu/courses', 1, 0),
(4, 2, 'Lectures', 4, NULL, 'http://ocw.uci.edu/lectures', 1, 0),
(5, 2, 'About Us', 5, NULL, 'http://ocw.uci.edu/info', 1, 0),
(6, 4, 'UCI OCW Blog', 1, NULL, 'http://sites.uci.edu/opencourseware/', 1, 1),
(7, 4, 'Facebook', 2, 'http://jgutierrez-srv.aconcaguasf.com.ar/upload/images/facebook.png', 'https://www.facebook.com/UC-Irvine-OpenCourseWare-314697770646/', 1, 1),
(8, 4, 'Youtube Channel', 3, '/frontend/img/social/youtube.png', 'http://www.youtube.com/user/UCIrvineOCW', 1, 1),
(9, 4, 'Twitter', 4, 'http://jgutierrez-srv.aconcaguasf.com.ar/upload/images/twitter.png', 'https://twitter.com/UCI_OCW', 1, 1),
(10, 3, 'UC Irvine', 1, NULL, 'http://uci.edu', 0, 0),
(11, 3, 'UC Irvine Extension', 2, NULL, 'http://unex.uci.edu/?WT.mc_id=OCWWEB', 0, 0),
(12, 5, 'FAQ', 1, NULL, 'http://ocw.uci.edu/info#faq', 1, 0),
(13, 5, 'Contact Us', 2, NULL, 'http://ocw.uci.edu/contact', 1, 0),
(14, 5, 'Search', 3, NULL, 'http://ocw.uci.edu/search/results', 1, 0),
(15, 5, 'Terms of Use', 4, NULL, 'http://ocw.uci.edu/info#termsofuse', 1, 0),
(16, 5, 'Rss', 5, '/frontend/img/social/rss.png', '/rss/', 1, 1),
(18, 1, 'UCI OpenCourseWare is an open education project supporting the needs of learners and educators everywhere: on our campus, within California, and the rest of the United States and the world.', 1, '/frontend/img/donate/donate-OCW-sm.png', 'http://www.uadv.uci.edu/OpenCourseWare', 1, 1),
(19, 1, 'Unless otherwise noted.', 2, 'http://i.creativecommons.org/l/by-sa/4.0/88x31.png', 'http://creativecommons.org/licenses/by-sa/4.0/', 1, 0),
(20, 1, '', 3, 'http://www.oeconsortium.org/wp-content/themes/ocwc-mainsite/images/logo-oe.png', 'http://www.openedconsortium.org/', 1, 0),
(21, 1, '', 4, 'http://www.uci.edu/img/uci-wordmark.png', 'http://uci.edu', 1, 0),
(22, 2, 'LOGO', 1, 'http://jgutierrez-srv.aconcaguasf.com.ar/upload/images/uciocwlogo_horizontal.png', '/', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_FooterAndHeaderArea`
--

DROP TABLE IF EXISTS `FE_FooterAndHeaderArea`;
CREATE TABLE IF NOT EXISTS `FE_FooterAndHeaderArea` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `areaCode` varchar(6) DEFAULT NULL,
  `areaDescription` varchar(50) DEFAULT NULL,
  `areaSequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Volcar la base de datos para la tabla `FE_FooterAndHeaderArea`
--

INSERT INTO `FE_FooterAndHeaderArea` (`id`, `areaCode`, `areaDescription`, `areaSequence`) VALUES
(1, 'ADATA', 'Arbitrary Data', 5),
(2, 'NAV', 'Navigation', 1),
(3, 'RLINK', 'Related Links', 3),
(4, 'SOCIAL', 'Social Media', 2),
(5, 'UINFO', 'Useful Information', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_Home`
--

DROP TABLE IF EXISTS `FE_Home`;
CREATE TABLE IF NOT EXISTS `FE_Home` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `homeSquare` int(11) NOT NULL,
  `homeType` varchar(7) DEFAULT NULL,
  `homeTitle` varchar(200) DEFAULT NULL,
  `homeText` text,
  `homeOrder` int(11) DEFAULT NULL,
  `homeUrl` varchar(600) DEFAULT NULL,
  `homeImageUrl` varchar(600) DEFAULT NULL,
  `homeActive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Volcar la base de datos para la tabla `FE_Home`
--

INSERT INTO `FE_Home` (`id`, `homeSquare`, `homeType`, `homeTitle`, `homeText`, `homeOrder`, `homeUrl`, `homeImageUrl`, `homeActive`) VALUES
(1, 3, 'text', 'Spotlight on Open Education Week', '<p>for Open Education Week, UCI OpenCourseWare features Professor Michael Dennin, Interim Dean for Undergraduate Education profiled&nbsp;<em>Einstein''s General Relativity and Gravity</em></p><p><img data-cke-saved-src="http://ocw.uci.edu/upload/images/physics_50_dennin.png.PNG" src="http://ocw.uci.edu/upload/images/physics_50_dennin.png.PNG" alt="Michael Dennin, Professor of Physics and Astronomym, Interim Dean of Undergraduate Education" style="height:auto; max-width:98%; vertical-align:middle; width:152px"></p><p><strong>Why it''s important:</strong>&nbsp;UCI has been at the forefront of open education for a number of years, particularly with its&nbsp;<a data-cke-saved-href="http://ocw.uci.edu/openchem/" href="http://ocw.uci.edu/openchem/">OpenChem</a>&nbsp;initiative, which allows both our own students as well as learners everywhere to view the lecture classes offered in general, organic, and physical chemistry as well as select courses at the undergraduate and graduate level.</p><p>Professor Michael Dennin (Physics and Astronomy) is the interim dean for undergraduate education. But he is also a pioneer at our campus in the publication of OpenCourseWare. To his initial course (to login, choose guest access),&nbsp;<a data-cke-saved-href="http://learn.uci.edu/course/view.php?id=1647" href="http://learn.uci.edu/course/view.php?id=1647">Science from Superheroes to Global Warming</a>, he has added Physics 3A,&nbsp;<a data-cke-saved-href="http://ocw.uci.edu/courses/physics_3a_basic_physics.html" href="http://ocw.uci.edu/courses/physics_3a_basic_physics.html">Basic Physics</a>, and Physics 50,&nbsp;<a data-cke-saved-href="http://ocw.uci.edu/courses/physics_50_math_methods.html" href="http://ocw.uci.edu/courses/physics_50_math_methods.html">Math Methods</a>.</p><p>Professor Dennin follows former Chancellor Michael Drake and Professor Ken Janda, Dean of the School of Physical Sciences as a spokesperson for the campus on the importance of fulfilling our public mission through projects such as open education. His video, like theirs is available on our site (see below) as well as on the global<a data-cke-saved-href="http://www.openeducationweek.org/" href="http://www.openeducationweek.org/">Open Education Week</a>&nbsp;website.</p><p><strong>Associated information:</strong>&nbsp;UCI is a member of the&nbsp;<a data-cke-saved-href="http://www.openedconsortium.org/" href="http://www.openedconsortium.org/">Open Education Consortium</a>&nbsp;since 2007. Gary Matkin, dean of continuing education, distance learning and summer session, was its founding treasurer. Associate Dean Larry Cooperman serves as the president of the 300+ member consortium. UCI''s UCIrvineOCW YouTube channel is the university''s largest, with nearly 20,000 subscribers and 160,000 visits per month.</p><p><strong>Watch:</strong>&nbsp;Professor Dennin''s Open Education Week video can be seen below:</p><p><iframe width="410" height="225" src="https://www.youtube.com/embed/EjuPR7k3WYQ" frameborder="0" allowfullscreen=""></iframe></p>', 0, '', '', 1),
(2, 4, 'text', 'How Do I Use This Site?', '<p><strong>Keyword search and filtered search</strong></p><p><img data-cke-saved-src="http://jgutierrez-srv.aconcaguasf.com.ar/frontend/img/ocw-keyword-search.JPG" src="http://jgutierrez-srv.aconcaguasf.com.ar/frontend/img/ocw-keyword-search.JPG" style="height:auto; max-width:98%; vertical-align:middle"></p><p>After entering a term in the search field in the upper right side of any page, you will get a results screen. Here you will be provided the opportunity to further filter your results, including by type of resource (video, course, etc...) or by school and department.</p><p><strong>Hierarchical search</strong></p><p>The below image shows the step-by-step process for searching courses and lectures, but not conferences or collections, which are described in the next section. To search hierarchically, choose either Courses or Lectures (Step 1), select the appropriate school or program (Step 2), select the department or subcategory (Step 3) and then select the course or lecture (Step 4).</p><p><img data-cke-saved-src="http://jgutierrez-srv.aconcaguasf.com.ar/frontend/img/ocw-hierarchical-search.JPG" src="http://jgutierrez-srv.aconcaguasf.com.ar/frontend/img/ocw-hierarchical-search.JPG" style="height:auto; max-width:98%; vertical-align:middle"></p><p><a data-cke-saved-href="frontend/img/ocw-hierarchical-search.JPG" href="frontend/img/ocw-hierarchical-search.JPG"><em>Click here to see larger image...</em></a></p><p><br></p><p><strong>Searching Conferences and Collections</strong></p><p>Conferences and Collections are available simply through the pull-down menus. As we add more, we will introduce sub-menus to accommodate the expansion.</p>', 0, '', '', 1),
(3, 5, 'text', 'The UCI OCW Blog', '<ul><li><p><a data-cke-saved-href="http://sites.uci.edu/opencourseware/blog/2016/01/19/where-do-our-visitors-come-from/" href="http://sites.uci.edu/opencourseware/blog/2016/01/19/where-do-our-visitors-come-from/" target="_self">Where do our visitors come from?</a></p><p>19/1/2016 21:56:07</p><p>Google Analytics provides a ton of interesting data about our website (uci.open.edu) visitors. Some of it can be quite ...</p></li><li><p><a data-cke-saved-href="http://sites.uci.edu/opencourseware/blog/2016/01/08/the-role-of-open-education-resources-in-peer-learning/" href="http://sites.uci.edu/opencourseware/blog/2016/01/08/the-role-of-open-education-resources-in-peer-learning/" target="_self">The Role of Open Education Resources in Peer Learning</a></p><p>8/1/2016 14:05:30</p><p>Peer learning refers to a pedagogical practice in which learners (students) come together formally or informally in order to ...</p></li><li><p><a data-cke-saved-href="http://sites.uci.edu/opencourseware/blog/2015/12/15/announcing-uci-open-httpopen-uci-edu/" href="http://sites.uci.edu/opencourseware/blog/2015/12/15/announcing-uci-open-httpopen-uci-edu/" target="_self">Announcing UCI OPEN – http://open.uci.edu</a></p><p>15/12/2015 14:49:48</p><p>&nbsp; When MIT OpenCourseWare began as a project, the announcement that their “materials organized as courses” ...</p></li></ul>', 0, '', '', 1),
(4, 2, 'slide_v', 'Professor Michael Dennin explains the significance of open education and the impact it has on the general public as well as local students at UCI.', '', 2, 'https://www.youtube.com/embed/EjuPR7k3WYQ?enablejsapi=1', '', 1),
(5, 2, 'slide_i', '', '', 1, '', 'frontend/img/OpenEducationPoster.jpg', 1),
(6, 2, 'slide_v', 'UCI student Keerthana Jeeva discusses her use of OpenChem as a study resource.', '', 3, 'http://www.youtube.com/embed/a53OU6rkX-k?enablejsapi=1', '', 1),
(7, 2, 'slide_i', 'UCI Open Chemistry</b>:  Click the ', '', 4, '', '/frontend/img/carousel/openchem.png', 1),
(8, 2, 'slide_i', 'OCW has received multiple awards for our content. Check out the ', '', 5, '', '/frontend/img/carousel/nutn.png', 1),
(9, 2, 'slide_i', 'Former U.S. Undersecretary of Education Martha Kanter receives Open Education Consortium''s President''s Award.', '', 6, '', '/frontend/img/carousel/kanter.jpg', 1),
(13, 1, 'banner', '', '', 1, '', 'http://jgutierrez-srv.aconcaguasf.com.ar/upload/images/wordsbanner.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_SectionTemplate`
--

DROP TABLE IF EXISTS `FE_SectionTemplate`;
CREATE TABLE IF NOT EXISTS `FE_SectionTemplate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `secDescription` varchar(50) DEFAULT NULL,
  `secCode` varchar(4) DEFAULT NULL,
  `secTemplate` int(11) DEFAULT NULL,
  `secCustomfile` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Volcar la base de datos para la tabla `FE_SectionTemplate`
--

INSERT INTO `FE_SectionTemplate` (`id`, `secDescription`, `secCode`, `secTemplate`, `secCustomfile`) VALUES
(9, 'Header', 'HEAD', 1, NULL),
(10, 'Footer', 'FOO', 1, NULL),
(12, 'Home', 'HOME', 1, 'home.html');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `FE_SectionText`
--

DROP TABLE IF EXISTS `FE_SectionText`;
CREATE TABLE IF NOT EXISTS `FE_SectionText` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(20) NOT NULL,
  `secTitle` varchar(100) DEFAULT NULL,
  `secText` text,
  `secActive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Volcar la base de datos para la tabla `FE_SectionText`
--

INSERT INTO `FE_SectionText` (`id`, `section`, `secTitle`, `secText`, `secActive`) VALUES
(1, '1', 'About UCI OpenCourseWare', '<p>The University of California, Irvine is a sustaining member of the&nbsp;<a data-cke-saved-href="http://www.oeconsortium.org/" href="http://www.oeconsortium.org/">Open Education Consortium</a>&nbsp;(formerly the OpenCourseWare Consortium).</p><p>The University launched its OpenCourseWare initiative in November 2006. Since then, it has rapidly grown to become one of the premiere sites in the United States. Today, its&nbsp;<a data-cke-saved-href="http://www.youtube.com/UCIrvineOCW" href="http://www.youtube.com/UCIrvineOCW">YouTube channel</a>, is viewed more than a million minutes per month. Its&nbsp;<a data-cke-saved-href="http://ocw.uci.edu/collections/open_chemistry.html" href="http://ocw.uci.edu/collections/open_chemistry.html">OpenChem</a>&nbsp;project comprises video lectures and more equivalent to the lectures in an undergraduate chemistry degree.</p>', 1),
(2, '2', 'Frequently Asked Questions', '<p>What is OCW?</p><p>An OpenCourseWare (OCW) is an open educational resource organized as typical components of course materials, often including syllabi, lecture notes, assignments and assessments. While OCW initiatives typically do not provide a degree, credit, certification, or access to instructors, the materials are made available, for free, under open licenses for use and adaption by educators and learners around the world.</p><p>What is UC Irvine''s Open Education project?</p><p>The University of California, Irvine’s OCW is a web-based repository of various UC Irvine courses and video lectures from UC Irvine faculty, seminar participants, and instructional staff. While the great majority of courses are drawn from graduate, undergraduate, and continuing education programs, some were originally produced under grant funding to serve specific needs in California and elsewhere. UC Irvine’s OCW is open and available to the world for free.</p><p>What is UC Irvine''s Contribution to the OCW Movement?</p><p>The University of California, Irvine has a long history of social engagement. As a leading public research University, an important part of its mission is to showcase and disseminate the research and scholarship of the University to the public. Open educational content is a concept that will advance human knowledge, creativity, lifelong learning, and the social welfare of educators, students, and self-learners across the globe. As a proud member of the OpenCourseWare Consortium, UC Irvine strives to meet the following goals:</p><ul><li>To meet the University of California’s desire to play a significant role in the contribution to the social welfare of the state, the nation and the world</li><li>To showcase the University’s top instructional efforts and make those courses and course materials free on a global scale to educators, students, and self-learners</li><li>To create educational assets that are discoverable, searchable, and modifiable under Creative Commons licenses</li></ul><p>How do I register to use UC Irvine''s OCW?</p><p>There is no registration or enrollment process because UC Irvine’s OCW is not a credit-bearing or degree-granting initiative. In addition, UC Irvine’s OCW courses do not feature an instructor and we do not provide access to faculty members for instruction. There also are no prerequisites for users who want to use UC Irvine’s OCW course materials in their own learning.</p><p>Can I enroll in for-credit online courses?</p><p>In some cases, yes. Credit can be gained through one of several mechanisms: taking for-credit coursework (fees apply) offered at the University''s continuing education unit,<a data-cke-saved-href="http://unex.uci.edu/" href="http://unex.uci.edu/">UC Irvine Extension</a>, through&nbsp;<a data-cke-saved-href="http://unex.uci.edu/courses/access_uci/" href="http://unex.uci.edu/courses/access_uci/">Access UCI</a>,&nbsp;<a data-cke-saved-href="http://summer.uci.edu/" href="http://summer.uci.edu/">Summer Session</a>, or through the normal admissions process, such as the online&nbsp;<a data-cke-saved-href="http://clsmas.soceco.uci.edu/" href="http://clsmas.soceco.uci.edu/">Master of Advanced Studies in Criminology</a>, Law and Society degree program. For more information, please contact Kathy Tam at&nbsp;<a data-cke-saved-href="mailto:kstam@uci.edu" href="mailto:kstam@uci.edu">kstam@uci.edu</a>&nbsp;or visit&nbsp;<a data-cke-saved-href="http://unex.uci.edu/" href="http://unex.uci.edu/">UC Irvine Extension''s Web site</a>.</p><p>Can I get a certificate of completion fron UC Irvine''s OpenCourseWare?</p><p>No. UC Irvine’s OCW does not offer certificates of completion at this time. You should work through the courses at your own pace, and in whatever manner you desire.</p><p>How do I contribute my course materials?</p><p>If you are a member of either the UC Irvine faculty or instructional staff, the UC Irvine OCW staff provides licenses and a publication release form. Please contact us at 824-6260 or email&nbsp;<a data-cke-saved-href="mailto:ocw@uci.edu" href="mailto:ocw@uci.edu">ocw@uci.edu</a>.</p><p>Where can I find information about Admissions?</p><p>All information about admissions to graduate and undergraduate programs can be accessed here:&nbsp;<a data-cke-saved-href="http://uci.edu/admissions/index.php" href="http://uci.edu/admissions/index.php">http://uci.edu/admissions/index.php</a>. If you are an international student, please see the resources on this page for Undergraduate International Admissions and Graduate International Admissions.</p><p>How can I contact a professor?</p><p>On most course and lecture pages, the author’s name is typically linked to their UCI faculty page. On this page, you may find a listed email. Please contact them for appropriate professional questions. They cannot provide support for our freely provided resources. If you wish to leave a comment about the course and its value to you, please use the&nbsp;<a data-cke-saved-href="http://test.ocw.uci.edu/ocw.uci.edu/contact" href="http://test.ocw.uci.edu/ocw.uci.edu/contact">"contact us"</a>form.</p><p>How do I access the files I downloaded from a course page?</p><p>The download on course pages provides the opportunity to download the associated files and pages of a course with one big exception: videos are referenced only by URL. The download file has a .imscc suffix, which stands for IMS Common Cartridge format. However, it can be opened with any .zip program, such as 7-Zip or the Windows File Manager. We are still testing it for export/import capability with LMS systems, including Moodle and Canvas. For now, you can treat it like a zipped archive and get at the resources. Remember that the Creative Commons licenses apply to appropriate use – and enjoy!</p>', 1),
(3, '3', 'Term of Use', '<p><strong>How can I use UC Irvine’s OCW course materials?</strong></p><p>UC Irvine’s OCW is a Web-based publication of the courses and course materials that support higher education. Educators are encouraged to use the materials for curriculum development, while students can augment their current learning by making use of the materials offered, and self-learners are encouraged to draw upon the material for self-study or supplementary use. Course materials offered on the UC Irvine Web site typically may be used, copied, distributed, translated and modified, but only for non-commercial educational purposes that are made freely available to other users. Each course shows its own license provisions, so please check carefully. All licenses permit reuse, but the following restrictions may apply:</p><ul><li><strong>Non-commercial:</strong>&nbsp;Use of UC Irvine OCW materials is open to all, except for profit-making entities who charge a fee for access to educational materials. If you would like to use UC Irvine OCW course materials, but are unsure whether your intended use qualifies as non-commercial use, please contact Kathy Tam at&nbsp;<a data-cke-saved-href="mailto:kstam@uci.edu" href="mailto:kstam@uci.edu">kstam@uci.edu</a>&nbsp;for clarification.</li><li><strong>Attribution&nbsp;</strong>(always required)<strong>:</strong>&nbsp;Any and all use or reuse of the material, including use of derivative works (new materials that incorporate or draw on the original materials), must be attributed to the University of California, Irvine and, if a member of the faculty or instructional staff is associated with the material, to that person’s name and title as well.</li><li><strong>Sharealike:</strong>&nbsp;Any publication or distribution of original or derivative works, including production of electronic or printed Web site materials or placement of materials on a website, must offer the works freely and openly to others under the same terms that UC Irvine OCW first made the works available to the user.</li><li><strong>No derivatives:</strong>&nbsp;No modifications can be made to the materials themselves.</li></ul><p><a data-cke-saved-name="requiredattribution" name="requiredattribution">How do I properly attribute or cite a work found on the UCI OCW website?</a></p><p>The core attribution must include:</p><ul><li>Author. This is typically the professor or lecturer of a course. However, it may include multiple people, as in the case of panelists or team-taught courses.</li><li>Title of course or lecture.</li><li>Link to source URL on OCW website.</li><li>License</li></ul><p>Attribution example:</p><p>Dennin, Michael. Physics 3A: Basic Physics. (UCI OpenCourseWare: University of California, Irvine), http://ocw.uci.edu/courses/physics_3a_basic_physics.html. (Accessed 22 May, 2014). License: Creative Commons BY-NC-SA.</p><p>It is optional, but appreciated to link back to our website - http://ocw.uci.edu.</p>', 1),
(4, '6', 'Information for Faculty', '<p>UCI OpenCourseWare is always looking for great new content to make available to our users. Faculty, please use the tabs below to learn about how to share your course materials, record your lectures or post other academic content. UCI Professors with OCW content have told us they have been pleased by the feedback they have received from students around the world who have benefited from their content.</p><p><strong>Contributing Existing Course Materials</strong></p><p>We gladly accept the submission of course slides, lecture notes, syllabi and other copyright free materials that could constitute a course.</p><p><strong>Licenses</strong></p><p>We use Creative Commons copyright licenses for our materials. Creative Commons was developed to increase the body of work available to the public to use and share. To learn more about Creative Commons, click&nbsp;<a data-cke-saved-href="http://creativecommons.org/licenses/" href="http://creativecommons.org/licenses/">here</a>.</p><p><strong>Updating</strong></p><p>Faculty members are responsible for updating course material. If you would like to make changes or updates to your OCW materials, please contact us.</p><p><strong>Filming Classes</strong></p><p>If you are interested in having your class filmed for inclusion on the UCI OCW website, please contact us in advance of the start of the quarter. Unless your course has a sponsor, we cannot necessarily guarantee we will be able to film your course, but interested faculty are always encouraged to contact us!&nbsp;<br>We utilize a two camera set-up to capture the professor and the slides or blackboard from the back of the classroom. Instructors are asked to wear a microphone. Most instructors have found that the video taping is not disruptive to the class and the students in your class benefit from reviewing the lectures.</p>', 1),
(16, '4', 'What is a Collection?', '<p>Our collections are curated sets of instructional materials and videos from seminars, lecture series and classes. To view a collection, click on dropdown in the navigation bar or navigate below. Links to the source of a course, lecture or material take you typically to a department, institute, or center''s website. Click on the title of the course, lecture or material to view it.<br></p>', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `File`
--

DROP TABLE IF EXISTS `File`;
CREATE TABLE IF NOT EXISTS `File` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idOCW` int(10) unsigned NOT NULL,
  `ocwUrlFile` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_file_OCW` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1794 ;

--
-- Volcar la base de datos para la tabla `File`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Language`
--

DROP TABLE IF EXISTS `Language`;
CREATE TABLE IF NOT EXISTS `Language` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `lanName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Volcar la base de datos para la tabla `Language`
--

INSERT INTO `Language` (`id`, `lanName`) VALUES
(1, 'English'),
(2, 'Español'),
(3, 'Português');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Lecture`
--

DROP TABLE IF EXISTS `Lecture`;
CREATE TABLE IF NOT EXISTS `Lecture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idOCW` int(10) unsigned NOT NULL,
  `lecControls` tinyint(1) DEFAULT NULL,
  `lecAutoplay` tinyint(1) DEFAULT NULL,
  `lecHeight` int(10) DEFAULT NULL,
  `lecWidth` int(10) DEFAULT NULL,
  `lecStart` int(10) DEFAULT NULL,
  `lecDuration` int(10) DEFAULT NULL,
  `lecEmbed` mediumtext,
  `lecVolume` int(10) DEFAULT NULL,
  `lecPublishedDate` date DEFAULT NULL,
  `ocwUrlLecture` varchar(200) DEFAULT NULL,
  `ocwBypassUrlLecture` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_lecture_OCW` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8496 ;

--
-- Volcar la base de datos para la tabla `Lecture`
--

INSERT INTO `Lecture` (`id`, `idOCW`, `lecControls`, `lecAutoplay`, `lecHeight`, `lecWidth`, `lecStart`, `lecDuration`, `lecEmbed`, `lecVolume`, `lecPublishedDate`, `ocwUrlLecture`, `ocwBypassUrlLecture`) VALUES
(8495, 4184, 1, 1, 0, 0, NULL, 0, '<iframe width="420" height="315" src="//www.youtube.com/embed/Ymib32GzReQ" frameborder="0" allowfullscreen></iframe>', 0, '2016-03-16', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `OCW`
--

DROP TABLE IF EXISTS `OCW`;
CREATE TABLE IF NOT EXISTS `OCW` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idUniversity` int(10) DEFAULT NULL,
  `idSchool` int(10) DEFAULT NULL,
  `idDepartment` int(10) DEFAULT NULL,
  `idType` int(10) NOT NULL,
  `idLanguage` int(10) NOT NULL,
  `ocwTitle` varchar(255) NOT NULL,
  `ocwTitleEncode` varchar(140) DEFAULT NULL,
  `ocwGolive` tinyint(1) NOT NULL DEFAULT '0',
  `ocwDescription` longtext,
  `ocwKeywords` varchar(200) DEFAULT 'opencourseware',
  `ocwLicense` longtext,
  `thumbnail` varchar(200) DEFAULT NULL,
  `ocwNotes` text,
  `ocwTemplate` text,
  `ocwDraft` text,
  `promoloc` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_OCW_OCWTypes` (`idType`),
  KEY `FK_OCW_Language` (`idLanguage`),
  KEY `ocwTitleEncode` (`ocwTitleEncode`),
  KEY `ocwTitle` (`ocwTitle`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4187 ;

--
-- Volcar la base de datos para la tabla `OCW`
--

INSERT INTO `OCW` (`id`, `idUniversity`, `idSchool`, `idDepartment`, `idType`, `idLanguage`, `ocwTitle`, `ocwTitleEncode`, `ocwGolive`, `ocwDescription`, `ocwKeywords`, `ocwLicense`, `thumbnail`, `ocwNotes`, `ocwTemplate`, `ocwDraft`, `promoloc`) VALUES
(4183, 1, 0, 0, 1, 1, 'Course 1', 'course_1', 1, '<p>Example Course 1</p>', '', '\n                        		\n                                                                                    	', '', '', '{"T":"1"}', NULL, 1),
(4184, 40, 0, 0, 3, 1, 'Lecture 1', 'lecture_1', 1, '<p>Example Lectures</p>', '', '\n                        		\n                                                                                    	', '', '', '{"T":"3"}', NULL, 1),
(4185, 1, 0, 0, 4, 2, 'Collection 1', 'collection_1', 1, '<p>Example Collection</p>', '', '\n                                                            ', '', '', '', NULL, 1),
(4186, NULL, NULL, NULL, 5, 1, 'Courses', NULL, 1, NULL, 'opencourseware', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `OCWCategory`
--

DROP TABLE IF EXISTS `OCWCategory`;
CREATE TABLE IF NOT EXISTS `OCWCategory` (
  `idOCW` int(11) unsigned NOT NULL,
  `idCat` int(11) unsigned NOT NULL,
  `occSequence` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idOCW`,`idCat`),
  KEY `FK_OCWCategory_OCW` (`idOCW`),
  KEY `FK_OCWCategory_Category_idx` (`idCat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `OCWCategory`
--

INSERT INTO `OCWCategory` (`idOCW`, `idCat`, `occSequence`) VALUES
(4183, 24, 0),
(4184, 4, 0),
(4185, 4, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `OCWJoin`
--

DROP TABLE IF EXISTS `OCWJoin`;
CREATE TABLE IF NOT EXISTS `OCWJoin` (
  `idOCWParent` int(11) unsigned NOT NULL,
  `idOCWChild` int(11) unsigned NOT NULL,
  `joiSequence` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idOCWParent`,`idOCWChild`),
  KEY `FK_OCWjoinP_OCW` (`idOCWParent`),
  KEY `FK_OCWjoinC_OCW` (`idOCWChild`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `OCWJoin`
--

INSERT INTO `OCWJoin` (`idOCWParent`, `idOCWChild`, `joiSequence`) VALUES
(4185, 4183, 1),
(4185, 4186, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `OCWTypes`
--

DROP TABLE IF EXISTS `OCWTypes`;
CREATE TABLE IF NOT EXISTS `OCWTypes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `typName` varchar(100) NOT NULL,
  `visibility` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Volcar la base de datos para la tabla `OCWTypes`
--

INSERT INTO `OCWTypes` (`id`, `typName`, `visibility`) VALUES
(1, 'Course', 1),
(2, 'File', 1),
(3, 'Lecture', 1),
(4, 'Collection', 1),
(5, 'Header', 0),
(6, 'Conference', 1),
(7, 'Label', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Person`
--

DROP TABLE IF EXISTS `Person`;
CREATE TABLE IF NOT EXISTS `Person` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `perFirstName` varchar(100) NOT NULL,
  `perLastName` varchar(100) NOT NULL,
  `perEmail` varchar(100) DEFAULT NULL,
  `perPhone` varchar(100) DEFAULT NULL,
  `perWebPage` varchar(250) DEFAULT NULL,
  `perAddress1` varchar(250) DEFAULT NULL,
  `perAddress2` varchar(250) DEFAULT NULL,
  `perCity` varchar(100) DEFAULT NULL,
  `perState` varchar(200) DEFAULT NULL,
  `perCountry` varchar(200) DEFAULT NULL,
  `oldId` int(11) DEFAULT NULL,
  `perEmail1` varchar(100) DEFAULT NULL,
  `perUrlPersonal` varchar(200) DEFAULT NULL,
  `perZipCode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=424 ;

--
-- Volcar la base de datos para la tabla `Person`
--

INSERT INTO `Person` (`id`, `perFirstName`, `perLastName`, `perEmail`, `perPhone`, `perWebPage`, `perAddress1`, `perAddress2`, `perCity`, `perState`, `perCountry`, `oldId`, `perEmail1`, `perUrlPersonal`, `perZipCode`) VALUES
(9, 'John', 'Doe', '', '', NULL, '', '', '', '', '', NULL, '', '', ''),
(135, 'Joan', 'Doe', 'admin@email.com', '', 'http://ocw.uci.edu', '', '', '', '', '', NULL, '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PersonDegree`
--

DROP TABLE IF EXISTS `PersonDegree`;
CREATE TABLE IF NOT EXISTS `PersonDegree` (
  `idPer` int(11) NOT NULL,
  `idDeg` int(11) NOT NULL,
  `pdeSequence` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idPer`,`idDeg`),
  KEY `FK_persondegree_degree` (`idDeg`),
  KEY `FK_persondegree_person` (`idPer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `PersonDegree`
--

INSERT INTO `PersonDegree` (`idPer`, `idDeg`, `pdeSequence`) VALUES
(135, 2, 0),
(135, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `PersonDepartment`
--

DROP TABLE IF EXISTS `PersonDepartment`;
CREATE TABLE IF NOT EXISTS `PersonDepartment` (
  `idPer` int(11) NOT NULL,
  `idDep` int(11) NOT NULL,
  `pedSequence` int(10) unsigned NOT NULL,
  `pedTitle` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idPer`,`idDep`),
  KEY `FK_persondepartment_department` (`idDep`),
  KEY `FK_persondepartment_person` (`idPer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `PersonDepartment`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Resource`
--

DROP TABLE IF EXISTS `Resource`;
CREATE TABLE IF NOT EXISTS `Resource` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `resourceName` varchar(45) NOT NULL,
  `nickName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=109 ;

--
-- Volcar la base de datos para la tabla `Resource`
--

INSERT INTO `Resource` (`id`, `resourceName`, `nickName`) VALUES
(1, 'admin_ocw_index', 'View ocw'),
(2, 'admin_ocw_joins', 'Join ocw'),
(3, 'admin_ocw_savepublish', 'Publish ocw'),
(4, 'admin_ocw_delete', 'Delete ocw'),
(5, 'admin_ocw_editocw', 'New OCW'),
(6, 'admin_ocw_ocwgrid', 'Load ocw'),
(7, 'admin_ocw_getocwjoin', 'Get ocw join'),
(8, 'admin_ocw_saveocwjoin', 'Save ocw join'),
(9, 'admin_ocw_save', 'Save ocw'),
(10, 'admin_ocw_getschoolajax', 'Get schools'),
(11, 'admin_ocw_getdepartmentajax', 'Get departments'),
(12, 'admin_ocw_uploadfile', 'Upload files'),
(13, 'admin_ocw_addocwheader', 'Add ocw header'),
(14, 'admin_ocw_removeocwjoin', 'Remove ocw Label/Header'),
(15, 'admin_category_index', 'View category'),
(16, 'admin_category_editcategory', 'Edit category'),
(17, 'admin_category_savecategory', 'Save category'),
(18, 'admin_category_savetree', 'Save tree category'),
(19, 'admin_category_delcategory', 'Delete category'),
(20, 'admin_degree_index', 'View degree'),
(21, 'admin_degree_editdegree', 'Edit degree'),
(22, 'admin_degree_delete', 'Delete degree'),
(23, 'admin_degree_savedegree', 'Save degree'),
(24, 'admin_degree_degreegrid', 'Load degrees'),
(25, 'admin_person_index', 'View person'),
(26, 'admin_person_editperson', 'Edit person'),
(27, 'admin_person_delete', 'Delete person'),
(28, 'admin_person_saveperson', 'Save person'),
(29, 'admin_person_persongrid', 'Load persons'),
(30, 'admin_role_index', 'View roles'),
(31, 'admin_role_edit', 'Edit role'),
(32, 'admin_role_saverole', 'Save role'),
(33, 'admin_role_del', 'Delete role'),
(34, 'admin_role_ajaxsource', 'Load roles'),
(35, 'admin_testimonial_index', 'View testimonial'),
(36, 'admin_testimonial_testimonialgrid', 'Load testimonials'),
(37, 'admin_testimonial_delete', 'Delete testimonial'),
(38, 'admin_testimonial_savevisible', 'Save testimonial'),
(39, 'admin_university_index', 'View university'),
(40, 'admin_university_saveschool', 'Save school'),
(41, 'admin_university_savedeppartment', 'Save department'),
(42, 'admin_university_edituniversity', 'Edit university'),
(43, 'admin_university_deleteuni', 'Delete university'),
(44, 'admin_university_deleteschool', 'Delete school'),
(45, 'admin_university_deletedepp', 'Delete department'),
(46, 'admin_university_saveuniversity', 'Save university'),
(47, 'admin_user_index', 'View users'),
(48, 'admin_user_edit', 'Edit user'),
(49, 'admin_user_saveusuario', 'Save user'),
(50, 'admin_user_del', 'Delete user'),
(51, 'admin_user_ajaxsource', 'Load users'),
(52, 'admin_cache_refresh', 'Refresh ocw cache'),
(53, 'admin_cache_index', 'View Cache'),
(54, 'admin_cache_clean', 'Clean cache'),
(55, 'admin_cache_remove', 'Remove cache'),
(56, 'admin_contact_index', 'View contact request'),
(57, 'admin_contact_contactrequestsgrid', 'Load contact request'),
(58, 'admin_contact_updatestatus', 'Mark as read contact request'),
(59, 'admin_ocw_addocwlabel', 'Add ocw label'),
(60, 'admin_ocw_edit', 'Edit OCW'),
(61, 'admin_blc_index', 'Broken Link Checker List'),
(62, 'admin_blc_delete', 'Broken Link Delete'),
(63, 'admin_blc_blcgrid', 'Broken Link datagrid'),
(64, 'admin_ads_delete', 'Delete ocw'),
(65, 'admin_ads_getpages', 'Get schools'),
(66, 'admin_ads_index', 'View ocw'),
(67, 'admin_ads_save', 'Save ocw'),
(68, 'admin_ads_savepublish', 'Publish ocw'),
(69, 'admin_ads_uploadfile', 'Upload files'),
(70, 'admin_ads_adsgrid', 'Load ocw'),
(71, 'admin_ads_newads', 'New ads'),
(72, 'admin_ads_getcategoryfortype', 'Get Categories for OCWType'),
(73, 'admin_ocw_editac', 'Edit OCW AC'),
(74, 'admin_stylefile_editstylefile', 'Edit stylefile'),
(75, 'admin_stylefile_restore', 'Delete stylefile'),
(76, 'admin_stylefile_savedegree', 'Save stylefile'),
(77, 'admin_stylefile_degreegrid', 'Load stylefile'),
(78, 'admin_stylefile_index', 'View stylefile'),
(79, 'admin_stylefile_getchangeslog', 'View changes stylefile'),
(80, 'admin_frontend_index', 'Frontend Dashboard'),
(81, 'admin_fsectiontext_index', 'View Section Text'),
(82, 'admin_ffooter_index', 'View Footer & Social Networks'),
(83, 'admin_fsectiontext_sectiongrid', 'Load Section Text'),
(84, 'admin_fsectiontext_delete', 'Delete Section Text'),
(85, 'admin_fsectiontext_edittext', 'Edit Section Text'),
(86, 'admin_fsectiontext_savetext', 'Save Section Text'),
(87, 'admin_ffooter_footergrid', 'Load Footer & Social Networks'),
(88, 'admin_ffooter_delete', 'Delete Footer & Social Networks'),
(89, 'admin_ffooter_editfooter', 'Edit Footer & Social Networks'),
(90, 'admin_ffooter_savefooter', 'Save Footer & Social Networks'),
(91, 'admin_ffooter_getnextsequence', 'View Next Sequence'),
(92, 'admin_ffooter_designfooter', 'Design the footer'),
(93, 'admin_ffooter_getfooterdesign', 'Get footer design'),
(94, 'admin_ffooter_getfooterdata', 'Get footer data'),
(95, 'admin_fcontactus_index', 'Contact Us'),
(96, 'admin_ffooter_savedesign', 'Save Footer Design'),
(97, 'admin_ffooter_designheader', 'Design the header'),
(98, 'admin_fhome_homegrid', 'Load Home'),
(99, 'admin_fhome_delete', 'Delete Home'),
(100, 'admin_fhome_edithome', 'Edit Home'),
(101, 'admin_fhome_savehome', 'Save Home'),
(102, 'admin_fhome_savedesign', 'Save Home Design'),
(103, 'admin_fhome_gethomedesign', 'Get Home design'),
(104, 'admin_fhome_index', 'View Home List'),
(105, 'admin_fhome_designhome', 'Design the Home'),
(106, 'admin_fcontactus_savecontactus', 'Save Contact Us Content'),
(107, 'admin_fhome_customize', 'Custom Design'),
(108, 'admin_fhome_restorefile', 'Restore original Home Page file');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Role`
--

DROP TABLE IF EXISTS `Role`;
CREATE TABLE IF NOT EXISTS `Role` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcar la base de datos para la tabla `Role`
--

INSERT INTO `Role` (`id`, `roleName`) VALUES
(1, 'Site Admin'),
(2, 'Course Creator'),
(3, 'Lecture Creator'),
(4, 'Course Author');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `RoleResource`
--

DROP TABLE IF EXISTS `RoleResource`;
CREATE TABLE IF NOT EXISTS `RoleResource` (
  `idRole` int(10) NOT NULL,
  `idResource` int(10) NOT NULL,
  `access` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `RoleResource`
--

INSERT INTO `RoleResource` (`idRole`, `idResource`, `access`) VALUES
(3, 13, 1),
(3, 5, 1),
(3, 11, 1),
(3, 7, 1),
(3, 10, 1),
(3, 2, 1),
(3, 24, 1),
(3, 6, 1),
(3, 29, 1),
(3, 34, 1),
(3, 36, 1),
(3, 3, 1),
(3, 14, 1),
(3, 9, 1),
(3, 8, 1),
(3, 1, 1),
(4, 13, 1),
(4, 4, 1),
(4, 5, 1),
(4, 11, 1),
(4, 7, 1),
(4, 10, 1),
(4, 2, 1),
(4, 24, 1),
(4, 6, 1),
(4, 29, 1),
(4, 34, 1),
(4, 36, 1),
(4, 51, 1),
(4, 3, 1),
(4, 14, 1),
(4, 9, 1),
(4, 8, 1),
(4, 12, 1),
(4, 1, 1),
(2, 13, 1),
(2, 4, 1),
(2, 5, 1),
(2, 11, 1),
(2, 7, 1),
(2, 10, 1),
(2, 2, 1),
(2, 24, 1),
(2, 6, 1),
(2, 29, 1),
(2, 34, 1),
(2, 36, 1),
(2, 51, 1),
(2, 3, 1),
(2, 52, 1),
(2, 14, 1),
(2, 9, 1),
(2, 8, 1),
(2, 12, 1),
(2, 1, 1),
(2, 59, 1),
(3, 59, 1),
(4, 59, 1),
(2, 60, 1),
(3, 60, 1),
(4, 60, 1),
(2, 73, 1),
(3, 73, 1),
(4, 73, 1),
(1, 13, 1),
(1, 59, 1),
(1, 61, 1),
(1, 63, 1),
(1, 62, 1),
(1, 54, 1),
(1, 95, 1),
(1, 107, 1),
(1, 19, 1),
(1, 22, 1),
(1, 45, 1),
(1, 88, 1),
(1, 99, 1),
(1, 64, 1),
(1, 4, 1),
(1, 27, 1),
(1, 33, 1),
(1, 44, 1),
(1, 84, 1),
(1, 75, 1),
(1, 37, 1),
(1, 43, 1),
(1, 50, 1),
(1, 92, 1),
(1, 97, 1),
(1, 105, 1),
(1, 16, 1),
(1, 21, 1),
(1, 89, 1),
(1, 100, 1),
(1, 60, 1),
(1, 73, 1),
(1, 26, 1),
(1, 31, 1),
(1, 85, 1),
(1, 74, 1),
(1, 42, 1),
(1, 48, 1),
(1, 80, 1),
(1, 72, 1),
(1, 11, 1),
(1, 94, 1),
(1, 93, 1),
(1, 103, 1),
(1, 7, 1),
(1, 65, 1),
(1, 10, 1),
(1, 2, 1),
(1, 57, 1),
(1, 24, 1),
(1, 87, 1),
(1, 98, 1),
(1, 70, 1),
(1, 6, 1),
(1, 29, 1),
(1, 34, 1),
(1, 83, 1),
(1, 77, 1),
(1, 36, 1),
(1, 51, 1),
(1, 58, 1),
(1, 71, 1),
(1, 5, 1),
(1, 3, 1),
(1, 68, 1),
(1, 52, 1),
(1, 55, 1),
(1, 14, 1),
(1, 108, 1),
(1, 17, 1),
(1, 106, 1),
(1, 23, 1),
(1, 41, 1),
(1, 90, 1),
(1, 96, 1),
(1, 101, 1),
(1, 102, 1),
(1, 67, 1),
(1, 9, 1),
(1, 8, 1),
(1, 28, 1),
(1, 32, 1),
(1, 40, 1),
(1, 86, 1),
(1, 76, 1),
(1, 38, 1),
(1, 18, 1),
(1, 46, 1),
(1, 49, 1),
(1, 69, 1),
(1, 12, 1),
(1, 53, 1),
(1, 15, 1),
(1, 79, 1),
(1, 56, 1),
(1, 20, 1),
(1, 82, 1),
(1, 104, 1),
(1, 91, 1),
(1, 1, 1),
(1, 66, 1),
(1, 25, 1),
(1, 30, 1),
(1, 81, 1),
(1, 78, 1),
(1, 35, 1),
(1, 39, 1),
(1, 47, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `School`
--

DROP TABLE IF EXISTS `School`;
CREATE TABLE IF NOT EXISTS `School` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idUniversity` int(10) DEFAULT NULL,
  `schName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_school_university` (`idUniversity`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

--
-- Volcar la base de datos para la tabla `School`
--

INSERT INTO `School` (`id`, `idUniversity`, `schName`) VALUES
(1, 1, 'Arts'),
(3, 1, 'Business'),
(6, 1, 'Engineering'),
(19, 1, 'Medicine'),
(68, 40, 'Law');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Sponsor`
--

DROP TABLE IF EXISTS `Sponsor`;
CREATE TABLE IF NOT EXISTS `Sponsor` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `personId` int(10) DEFAULT NULL,
  `spoIsOrganization` tinyint(1) DEFAULT NULL COMMENT 'A field to tell whether the person is the sponsor or his/her company is. ',
  `spoCompanyName` varchar(100) NOT NULL,
  `spoCompanyURL` varchar(100) DEFAULT NULL,
  `spoImageURL` varchar(100) DEFAULT NULL,
  `spoEmail` varchar(100) DEFAULT NULL,
  `spoPhone` varchar(100) DEFAULT NULL,
  `spoAddress` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_sponsor_person` (`personId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `Sponsor`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `StyleFile`
--

DROP TABLE IF EXISTS `StyleFile`;
CREATE TABLE IF NOT EXISTS `StyleFile` (
  `id` int(10) NOT NULL,
  `styleDescription` varchar(100) NOT NULL,
  `styleShortDescription` varchar(100) NOT NULL,
  `stylefile` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `StyleFile`
--

INSERT INTO `StyleFile` (`id`, `styleDescription`, `styleShortDescription`, `stylefile`) VALUES
(1, 'style', 'file style.css main', 'style.css'),
(2, 'bootstrap', 'bootstrap', 'bootstrap.css'),
(3, 'bootstrap-responsive', 'bootstrap-responsive', 'bootstrap-responsive.css'),
(4, 'bootstrap-formhelpers', 'bootstrap-formhelpers', 'bootstrap-formhelpers.css'),
(5, 'bootstrap-formhelpers-countries.flags', 'bootstrap-formhelpers-countries.flags', 'bootstrap-formhelpers-countries.flags.css'),
(6, 'bootstrap-formhelpers-currencies.flags', 'bootstrap-formhelpers-currencies.flags', 'bootstrap-formhelpers-currencies.flags.css'),
(7, 'ovp', 'ovp', 'ovp.css'),
(8, 'prettify', 'prettify', 'prettify.css'),
(9, 'pace-theme-center-atom', 'pace-theme-center-atom', 'pace-theme-center-atom.css');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `StyleFileLog`
--

DROP TABLE IF EXISTS `StyleFileLog`;
CREATE TABLE IF NOT EXISTS `StyleFileLog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `stylefile` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

--
-- Volcar la base de datos para la tabla `StyleFileLog`
--

INSERT INTO `StyleFileLog` (`id`, `userid`, `action`, `stylefile`, `date`) VALUES
(1, '16', 'edit', 'home.html', '2016-02-26 14:35:02'),
(2, '16', 'edit', 'home.html', '2016-02-26 14:43:53'),
(3, '16', 'edit', 'home.html', '2016-02-26 14:44:14'),
(4, '16', 'edit', 'home.html', '2016-02-26 14:46:36'),
(5, '16', 'edit', 'home.html', '2016-02-26 14:52:43'),
(6, '16', 'edit', 'home.html', '2016-02-26 14:56:54'),
(7, '16', 'edit', 'home.html', '2016-02-26 14:57:21'),
(8, '16', 'edit', 'home.html', '2016-02-26 14:58:38'),
(9, '16', 'edit', 'home.html', '2016-02-26 14:59:28'),
(10, '16', 'edit', 'home.html', '2016-02-26 15:10:33'),
(11, '16', 'edit', 'home.html', '2016-02-26 15:48:30'),
(12, '16', 'edit', 'home.html', '2016-02-26 16:14:11'),
(13, '16', 'edit', 'home.html', '2016-02-26 16:15:47'),
(14, '16', 'edit', 'home.html', '2016-02-26 16:23:32'),
(15, '16', 'edit', 'home.html', '2016-02-26 17:17:40'),
(16, '16', 'edit', 'home.html', '2016-02-26 17:17:59'),
(17, '16', 'edit', 'home.html', '2016-02-26 17:18:15'),
(18, '16', 'edit', 'home.html', '2016-02-26 17:31:45'),
(19, '16', 'edit', 'home.html', '2016-02-26 17:32:24'),
(20, '16', 'edit', 'home.html', '2016-02-26 17:34:50'),
(21, '16', 'edit', 'home.html', '2016-02-26 17:35:13'),
(22, '16', 'edit', 'home.html', '2016-02-26 17:35:50'),
(23, '16', 'edit', 'home.html', '2016-02-26 17:36:52'),
(24, '16', 'edit', 'home.html', '2016-02-26 17:37:07'),
(25, '16', 'edit', 'home.html', '2016-02-26 17:37:33'),
(26, '16', 'edit', 'home.html', '2016-02-29 11:11:24'),
(27, '16', 'edit', 'home.html', '2016-02-29 11:15:15'),
(28, '16', 'edit', 'home.html', '2016-02-29 11:17:15'),
(29, '16', 'edit', 'home.html', '2016-02-29 11:17:33'),
(30, '16', 'edit', 'home.html', '2016-02-29 11:17:53'),
(31, '16', 'edit', 'home.html', '2016-02-29 11:18:31'),
(32, '16', 'edit', 'home.html', '2016-02-29 11:19:39'),
(33, '16', 'edit', 'home.html', '2016-02-29 11:20:10'),
(34, '16', 'edit', 'home.html', '2016-02-29 11:23:01'),
(35, '16', 'edit', 'home.html', '2016-02-29 11:23:28'),
(36, '16', 'edit', 'home.html', '2016-02-29 11:25:23'),
(37, '16', 'edit', 'home.html', '2016-02-29 11:25:33'),
(38, '16', 'edit', 'home.html', '2016-02-29 11:26:01'),
(39, '16', 'edit', 'home.html', '2016-02-29 11:26:16'),
(40, '16', 'edit', 'home.html', '2016-02-29 11:26:27'),
(41, '16', 'edit', 'home.html', '2016-02-29 11:26:46'),
(42, '16', 'edit', 'home.html', '2016-02-29 11:27:18'),
(43, '16', 'edit', 'home.html', '2016-02-29 11:28:08'),
(44, '16', 'edit', 'home.html', '2016-02-29 11:28:17'),
(45, '16', 'edit', 'home.html', '2016-02-29 11:28:36'),
(46, '16', 'edit', 'home.html', '2016-02-29 11:54:47'),
(47, '16', 'edit', 'home.html', '2016-02-29 11:55:50'),
(48, '16', 'edit', 'home.html', '2016-02-29 11:56:01'),
(49, '16', 'edit', 'home.html', '2016-02-29 12:09:10'),
(50, '16', 'edit', 'home.html', '2016-02-29 12:09:39'),
(51, '16', 'edit', 'home.html', '2016-02-29 12:12:55'),
(52, '16', 'edit', 'home.html', '2016-02-29 12:15:21'),
(53, '16', 'edit', 'home.html', '2016-02-29 12:16:04'),
(54, '16', 'edit', 'home.html', '2016-02-29 12:16:10'),
(55, '16', 'edit', 'home.html', '2016-02-29 12:16:47'),
(56, '16', 'edit', 'home.html', '2016-02-29 12:17:01'),
(57, '16', 'edit', 'home.html', '2016-02-29 12:17:13'),
(58, '16', 'edit', 'home.html', '2016-02-29 12:17:24'),
(59, '16', 'edit', 'home.html', '2016-02-29 12:18:23'),
(60, '16', 'edit', 'home.html', '2016-02-29 12:26:27'),
(61, '16', 'edit', 'home.html', '2016-02-29 12:27:38'),
(62, '16', 'edit', 'home.html', '2016-02-29 12:27:52'),
(63, '16', 'edit', 'home.html', '2016-02-29 12:28:46'),
(64, '16', 'edit', 'home.html', '2016-02-29 12:29:22'),
(65, '16', 'edit', 'home.html', '2016-02-29 12:29:32'),
(66, '16', 'edit', 'home.html', '2016-02-29 12:29:43'),
(67, '16', 'edit', 'home.html', '2016-02-29 14:06:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `TestimonialOptions`
--

DROP TABLE IF EXISTS `TestimonialOptions`;
CREATE TABLE IF NOT EXISTS `TestimonialOptions` (
  `groupId` int(11) NOT NULL,
  `tesOption` varchar(100) NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`groupId`,`tesOption`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcar la base de datos para la tabla `TestimonialOptions`
--

INSERT INTO `TestimonialOptions` (`groupId`, `tesOption`, `sequence`) VALUES
(1, 'Educator', 1),
(1, 'Other', 4),
(1, 'Self-Learner', 3),
(1, 'Student', 2),
(2, 'Option 1', 1),
(2, 'Option 2', 2),
(2, 'Option 3', 3),
(2, 'Option 4', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Testimonials`
--

DROP TABLE IF EXISTS `Testimonials`;
CREATE TABLE IF NOT EXISTS `Testimonials` (
  `idTes` int(11) NOT NULL AUTO_INCREMENT,
  `tesName` varchar(100) DEFAULT 'Anonymous',
  `tesCountry` varchar(100) DEFAULT 'Anonymous',
  `tesEmail` varchar(100) DEFAULT 'Anonymous',
  `tesQuestion1` varchar(100) DEFAULT NULL,
  `tesQuestion2` varchar(100) DEFAULT NULL,
  `tesQuestion3` longtext,
  `tesTestimonial` longtext NOT NULL,
  `tesMarketing` int(10) unsigned DEFAULT '0',
  `tesContact` int(10) unsigned DEFAULT '0',
  `idOCW` int(11) unsigned NOT NULL,
  `tesVisible` int(1) unsigned DEFAULT '0',
  `tesDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idTes`),
  KEY `fk_Testimonials_1_idx` (`idOCW`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=320 ;

--
-- Volcar la base de datos para la tabla `Testimonials`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `University`
--

DROP TABLE IF EXISTS `University`;
CREATE TABLE IF NOT EXISTS `University` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uniName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- Volcar la base de datos para la tabla `University`
--

INSERT INTO `University` (`id`, `uniName`) VALUES
(1, 'University 1'),
(40, 'University 2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `User`
--

DROP TABLE IF EXISTS `User`;
CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usrName` varchar(45) NOT NULL,
  `usrPassword` varchar(45) DEFAULT NULL,
  `idPer` int(11) NOT NULL,
  `usrActive` tinyint(1) DEFAULT '1',
  `idRole` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usrName_UNIQUE` (`usrName`),
  UNIQUE KEY `idPer_UNIQUE` (`idPer`),
  KEY `fk_User_Person_idx` (`idPer`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Volcar la base de datos para la tabla `User`
--

INSERT INTO `User` (`id`, `usrName`, `usrPassword`, `idPer`, `usrActive`, `idRole`) VALUES
(16, 'admin', '0192023a7bbd73250516f069df18b500', 135, 1, 1),
(17, 'user1', '6ad14ba9986e3615423dfca256d04e3f', 9, 1, 4);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `UserInfo`
--
DROP VIEW IF EXISTS `UserInfo`;
CREATE TABLE IF NOT EXISTS `UserInfo` (
`id` int(11)
,`usrName` varchar(45)
,`usrPassword` varchar(45)
,`idPer` int(11)
,`usrActive` tinyint(1)
,`perName` varchar(202)
,`idRole` int(10)
,`roleName` varchar(45)
);
-- --------------------------------------------------------

--
-- Estructura para la vista `UserInfo`
--
DROP TABLE IF EXISTS `UserInfo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `UserInfo` AS select `r0`.`id` AS `id`,`r0`.`usrName` AS `usrName`,`r0`.`usrPassword` AS `usrPassword`,`r0`.`idPer` AS `idPer`,`r0`.`usrActive` AS `usrActive`,concat_ws(', ',`r1`.`perFirstName`,`r1`.`perLastName`) AS `perName`,`r2`.`id` AS `idRole`,`r2`.`roleName` AS `roleName` from ((`User` `r0` join `Person` `r1` on((`r1`.`id` = `r0`.`idPer`))) join `Role` `r2` on((`r2`.`id` = `r0`.`idRole`)));

--
-- Filtros para las tablas descargadas (dump)
--

--
-- Filtros para la tabla `Ads`
--
ALTER TABLE `Ads`
  ADD CONSTRAINT `fk_Ads_AdsType1` FOREIGN KEY (`idAdsType`) REFERENCES `AdsType` (`idAdsType`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `AdsCategory`
--
ALTER TABLE `AdsCategory`
  ADD CONSTRAINT `fk_AdsCategory_Ads1` FOREIGN KEY (`idAds`) REFERENCES `Ads` (`idAds`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_AdsCategory_Category1` FOREIGN KEY (`idCat`) REFERENCES `Category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `AdsOCW`
--
ALTER TABLE `AdsOCW`
  ADD CONSTRAINT `fk_AdsOCW_Ads1` FOREIGN KEY (`idAds`) REFERENCES `Ads` (`idAds`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_AdsOCW_OCW1` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Author`
--
ALTER TABLE `Author`
  ADD CONSTRAINT `FK_author_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_author_person` FOREIGN KEY (`idPer`) REFERENCES `Person` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Collection`
--
ALTER TABLE `Collection`
  ADD CONSTRAINT `FK_collection_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Conference`
--
ALTER TABLE `Conference`
  ADD CONSTRAINT `FK_conference_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Course`
--
ALTER TABLE `Course`
  ADD CONSTRAINT `FK_course_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Department`
--
ALTER TABLE `Department`
  ADD CONSTRAINT `FK_department_school` FOREIGN KEY (`idSchool`) REFERENCES `School` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `File`
--
ALTER TABLE `File`
  ADD CONSTRAINT `FK_file_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Lecture`
--
ALTER TABLE `Lecture`
  ADD CONSTRAINT `FK_lecture_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `OCW`
--
ALTER TABLE `OCW`
  ADD CONSTRAINT `fk_OCW_1` FOREIGN KEY (`idLanguage`) REFERENCES `Language` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_OCW_OCWTypes` FOREIGN KEY (`idType`) REFERENCES `OCWTypes` (`id`);

--
-- Filtros para la tabla `OCWCategory`
--
ALTER TABLE `OCWCategory`
  ADD CONSTRAINT `FK_OCWCategory_Category` FOREIGN KEY (`idCat`) REFERENCES `Category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_OCWCategory_OCW` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `OCWJoin`
--
ALTER TABLE `OCWJoin`
  ADD CONSTRAINT `FK_OCWjoinC_OCW` FOREIGN KEY (`idOCWChild`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_OCWjoinP_OCW` FOREIGN KEY (`idOCWParent`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `PersonDegree`
--
ALTER TABLE `PersonDegree`
  ADD CONSTRAINT `FK_persondegree_degree` FOREIGN KEY (`idDeg`) REFERENCES `Degree` (`id`),
  ADD CONSTRAINT `FK_persondegree_person` FOREIGN KEY (`idPer`) REFERENCES `Person` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `PersonDepartment`
--
ALTER TABLE `PersonDepartment`
  ADD CONSTRAINT `FK_persondepartment_department` FOREIGN KEY (`idDep`) REFERENCES `Department` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_persondepartment_person` FOREIGN KEY (`idPer`) REFERENCES `Person` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `School`
--
ALTER TABLE `School`
  ADD CONSTRAINT `FK_school_university` FOREIGN KEY (`idUniversity`) REFERENCES `University` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Sponsor`
--
ALTER TABLE `Sponsor`
  ADD CONSTRAINT `FK_sponsor_person` FOREIGN KEY (`personId`) REFERENCES `Person` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `Testimonials`
--
ALTER TABLE `Testimonials`
  ADD CONSTRAINT `fk_Testimonials_1` FOREIGN KEY (`idOCW`) REFERENCES `OCW` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `SPAddCategory`$$
CREATE DEFINER=`root`@`%` PROCEDURE `SPAddCategory`(
	IN idCat INTEGER(11),
	IN nameCat VARCHAR(100)
)
BEGIN

DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;
-- DECLARE EXIT HANDLER FOR SQLWARNING ROLLBACK;

START TRANSACTION;

IF idCat = 0 THEN
	SELECT @myRight := max(rgt) FROM Category ;
	INSERT INTO Category(CatName, lft, rgt) VALUES(nameCat, @myRight + 1, @myRight + 2);
	
ELSE

	SELECT @myLeft := lft FROM Category

	WHERE id = idCat; 

	UPDATE Category SET rgt = rgt + 2 WHERE rgt > @myLeft;
	UPDATE Category SET lft = lft + 2 WHERE lft > @myLeft;

	INSERT INTO Category(CatName, lft, rgt) VALUES(nameCat, @myLeft + 1, @myLeft + 2) ;
	
END IF;
	
COMMIT;


END$$

DROP PROCEDURE IF EXISTS `SPDelCategory`$$
CREATE DEFINER=`root`@`%` PROCEDURE `SPDelCategory`(IN idCat INTEGER(11))
BEGIN


SELECT @myLeft := lft, @myRight := rgt, @myWidth := rgt - lft + 1
FROM Category
WHERE id = idCat;

DELETE FROM Category WHERE lft BETWEEN @myLeft AND @myRight;

UPDATE Category SET rgt = rgt - @myWidth WHERE rgt > @myRight;
UPDATE Category SET lft = lft - @myWidth WHERE lft > @myRight;

END$$

DROP PROCEDURE IF EXISTS `SPFiltersJoins`$$
CREATE DEFINER=`root`@`%` PROCEDURE `SPFiltersJoins`()
BEGIN
	DROP TABLE IF EXISTS Filters;
	CREATE TABLE Filters(
		`filterGroup` varchar(100) NOT NULL,
		`filterName`  varchar(500) NOT NULL,
		`idFilter` int(11) NOT NULL
	);
	DROP TABLE IF EXISTS Filters2;
	/*INSERT INTO Filters (filterGroup, filterName, idFilter )
		SELECT 
			 'University' as filterGroup
			, r0.uniName filterName
			, r0.id as idFilter
		FROM University r0 ; */

/*	INSERT INTO Filters (filterGroup, filterName, idFilter )
	SELECT * FROM (SELECT 
		'Language' as filterGroup
		, r0.lanName
		, 0 as idFilter
	FROM Language r0 
	INNER JOIN OCW r1 ON r1.idLanguage = r0.id
	GROUP BY r0.id
 	ORDER BY r0.lanName
	) tabla;*/

	/*INSERT INTO Filters (filterGroup, filterName, idFilter )
	SELECT * FROM(
	SELECT 
		'Author' as filterGroup
		, CONCAT_WS(', ', perFirstName, perLastName , CONCAT('(', degDescription,')')) as filterName
		, r0.id as idFilter
	FROM Person r0
	LEFT JOIN PersonDegree r2 ON r2.idPer = r0.id
	LEFT JOIN Degree r3 ON r3.id = r2.idDeg
	INNER JOIN Author r1 ON r1.idPer = r0.id) as ttatata
	;*/



INSERT INTO Filters (filterGroup, filterName, idFilter )
	SELECT 'Category', path, id FROM(SELECT    
					  n.id
					, n.catName
					, COUNT(*)-1 AS level
					, (SELECT GROUP_CONCAT(parent.catName SEPARATOR ' ► ')
						FROM Category AS node,Category AS parent
							WHERE node.lft BETWEEN parent.lft AND parent.rgt
			          AND node.id = n.id group by node.id ORDER BY node.posicion) AS path
				FROM Category AS n, Category AS p
			WHERE n.lft BETWEEN p.lft AND p.rgt  
			GROUP BY n.lft 
			ORDER BY n.posicion) tt
	;

SELECT
`Filters`.`filterGroup`,
`Filters`.`filterName`,
`Filters`.`idFilter`
FROM Filters;

/*SELECT
`Filters`.`filterGroup`,
`Filters`.`filterName`,
`Filters`.`idFilter`
FROM `UCIOCW`.`Filters`
UNION
	(
	SELECT 
		'Author' as filterGroup
		, CONCAT_WS(', ', perFirstName, perLastName , CONCAT('(', degDescription,')')) as filterName
		, r0.id as idFilter
	FROM Person r0
	LEFT JOIN PersonDegree r2 ON r2.idPer = r0.id
	LEFT JOIN Degree r3 ON r3.id = r2.idDeg
	INNER JOIN Author r1 ON r1.idPer = r0.id);*/

END$$

DROP PROCEDURE IF EXISTS `SPMoveCategory`$$
CREATE DEFINER=`root`@`%` PROCEDURE `SPMoveCategory`(
	IN idCat INTEGER(11),
	IN idParent INTEGER(11),
    IN pos INTEGER(11)
)
BEGIN


DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;
-- DECLARE EXIT HANDLER FOR SQLWARNING ROLLBACK;

START TRANSACTION;

IF idParent = 0 THEN
SELECT @myRight := (CASE WHEN max(rgt) is null THEN 0 ELSE max(rgt) END) FROM Category ;
UPDATE Category SET lft = @myRight + 1, rgt = @myRight + 2 , posicion = pos WHERE id = idCat;
-- SELECT @tempid; 
ELSE

SELECT @myLeft := lft FROM Category
WHERE id = idParent;

UPDATE Category SET rgt = rgt + 2 WHERE rgt > @myLeft;
UPDATE Category SET lft = lft + 2 WHERE lft > @myLeft;
UPDATE Category SET lft = @myLeft + 1, rgt = @myLeft + 2, posicion = pos WHERE id = idCat;
-- SELECT @tempid; 


END IF;
COMMIT;

END$$

DELIMITER ;
