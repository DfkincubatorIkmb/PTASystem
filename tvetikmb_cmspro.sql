-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 08:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tvetikmb_cmspro`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `AdminName` varchar(200) NOT NULL,
  `EmailId` varchar(150) NOT NULL,
  `ContactNumber` bigint(12) NOT NULL,
  `password` varchar(250) NOT NULL,
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `AdminName`, `EmailId`, `ContactNumber`, `password`, `updationDate`) VALUES
(1, 'admin', 'Admin', 'admin@gmail.com', 1234567890, 'f925916e2754e5e03f75dd58a5733251', '18-10-2016 04:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `brandname`
--

CREATE TABLE `brandname` (
  `id` int(11) NOT NULL,
  `categoryid` int(11) NOT NULL,
  `brandname` varchar(255) NOT NULL,
  `creationDate` datetime NOT NULL,
  `updationDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brandname`
--

INSERT INTO `brandname` (`id`, `categoryid`, `brandname`, `creationDate`, `updationDate`) VALUES
(18, 1, 'ACSON', '2025-08-18 12:51:00', NULL),
(19, 1, 'AUX', '2025-08-18 12:51:00', NULL),
(20, 1, 'BLACK SPIDER', '2025-08-18 12:51:00', NULL),
(21, 1, 'CORNELL', '2025-08-18 12:51:00', NULL),
(22, 1, 'DAIKIN', '2025-08-18 12:51:00', NULL),
(23, 1, 'DAEWOOD', '2025-08-18 12:51:00', NULL),
(24, 1, 'DEKA', '2025-08-18 12:51:00', NULL),
(25, 1, 'DAHUA', '2025-08-18 12:51:00', NULL),
(26, 1, 'ELECTROLUX', '2025-08-18 12:51:00', NULL),
(27, 1, 'ELBA', '2025-08-18 12:51:00', NULL),
(28, 1, 'EPAY', '2025-08-18 12:51:00', NULL),
(29, 1, 'FABER', '2025-08-18 12:51:00', NULL),
(30, 1, 'HITEC', '2025-08-18 12:51:00', NULL),
(31, 1, 'HAIER', '2025-08-18 12:51:00', NULL),
(32, 1, 'HISENSE', '2025-08-18 12:51:00', NULL),
(33, 1, 'HITACHI', '2025-08-18 12:51:00', NULL),
(34, 1, 'HIKVISION', '2025-08-18 12:51:00', NULL),
(35, 1, 'HESSTAR', '2025-08-18 12:51:00', NULL),
(36, 1, 'ISONIC', '2025-08-18 12:51:00', NULL),
(37, 1, 'I SLIDE', '2025-08-18 12:51:00', NULL),
(38, 1, 'JOVEN', '2025-08-18 12:51:00', NULL),
(39, 1, 'JASMA', '2025-08-18 12:51:00', NULL),
(40, 1, 'KHIND', '2025-08-18 12:51:00', NULL),
(41, 1, 'KDK', '2025-08-18 12:51:00', NULL),
(42, 1, 'KARCHER', '2025-08-18 12:51:00', NULL),
(43, 1, 'LG', '2025-08-18 12:51:00', NULL),
(44, 1, 'MIDEA', '2025-08-18 12:51:00', NULL),
(45, 1, 'MORGAN', '2025-08-18 12:51:00', NULL),
(46, 1, 'MECK', '2025-08-18 12:51:00', NULL),
(47, 1, 'MILUX', '2025-08-18 12:51:00', NULL),
(48, 1, 'MITSUBISHI', '2025-08-18 12:51:00', NULL),
(49, 1, 'MAHITA', '2025-08-18 12:51:00', NULL),
(50, 1, 'MAYER', '2025-08-18 12:51:00', NULL),
(51, 1, 'MI', '2025-08-18 12:51:00', NULL),
(52, 1, 'NOXXA', '2025-08-18 12:51:00', NULL),
(53, 1, 'NATIONAL', '2025-08-18 12:51:00', NULL),
(54, 1, 'NEW BUTTERFLY', '2025-08-18 12:51:00', NULL),
(55, 1, 'PHILIPS', '2025-08-18 12:51:00', NULL),
(56, 1, 'PENSONIC', '2025-08-18 12:51:00', NULL),
(57, 1, 'PTIME', '2025-08-18 12:51:00', NULL),
(58, 1, 'PROMAS', '2025-08-18 12:51:00', NULL),
(59, 1, 'TOPAIRE', '2025-08-18 12:51:00', NULL),
(60, 1, 'PRIMADA', '2025-08-18 12:51:00', NULL),
(61, 1, 'PHISON', '2025-08-18 12:51:00', NULL),
(62, 1, 'PANASONIC', '2025-08-18 12:51:00', NULL),
(63, 1, 'RUIJIE', '2025-08-18 12:51:00', NULL),
(64, 1, 'REGAIR', '2025-08-18 12:51:00', NULL),
(65, 1, 'SHARP', '2025-08-18 12:51:00', NULL),
(66, 1, 'TELEFUNKEN', '2025-08-18 12:51:00', NULL),
(67, 1, 'SONY', '2025-08-18 12:51:00', NULL),
(68, 1, 'AIWA', '2025-08-18 12:51:00', NULL),
(69, 1, 'SINGER', '2025-08-18 12:51:00', NULL),
(70, 1, 'SAMSUNG', '2025-08-18 12:51:00', NULL),
(71, 1, 'SKYWORTH', '2025-08-18 12:51:00', NULL),
(72, 1, 'STANLEY', '2025-08-18 12:51:00', NULL),
(73, 1, 'SNOW', '2025-08-18 12:51:00', NULL),
(74, 1, 'SANKYO', '2025-08-18 12:51:00', NULL),
(75, 1, 'SANDEN', '2025-08-18 12:51:00', NULL),
(76, 1, 'TOSHIBA', '2025-08-18 12:51:00', NULL),
(77, 1, 'TRIO', '2025-08-18 12:51:00', NULL),
(78, 1, 'THE BAKER', '2025-08-18 12:51:00', NULL),
(79, 1, 'TOKAI', '2025-08-18 12:51:00', NULL),
(80, 1, 'TCL', '2025-08-18 12:51:00', NULL),
(81, 1, 'UNIVERSAL', '2025-08-18 12:51:00', NULL),
(82, 1, 'ZANUSSI', '2025-08-18 12:51:00', NULL),
(83, 1, 'ASTRO', '2025-08-18 12:51:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `categoryName` varchar(255) DEFAULT NULL,
  `categoryDescription` longtext DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `categoryName`, `categoryDescription`, `creationDate`, `updationDate`) VALUES
(1, 'LAPORAN KEROSAKAN PELANGGAN', 'JENIS KEROSAKAN', '2020-06-21 07:06:04', '2025-02-15 08:18:32'),
(2, 'Other', 'Other', '2020-06-22 18:30:00', '2020-06-27 18:59:53'),
(4, 'SERVIS AIRCOND', '', '2025-02-15 08:21:42', NULL),
(5, 'SERVIS CUCIAN', '', '2025-02-15 08:21:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaintremark`
--

CREATE TABLE `complaintremark` (
  `id` int(11) NOT NULL,
  `complaintNumber` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `notetransport` mediumtext DEFAULT NULL,
  `checking` mediumtext DEFAULT NULL,
  `remark` mediumtext DEFAULT NULL,
  `remarkDate` timestamp NULL DEFAULT current_timestamp(),
  `remarkBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `complaintremark`
--

INSERT INTO `complaintremark` (`id`, `complaintNumber`, `status`, `notetransport`, `checking`, `remark`, `remarkDate`, `remarkBy`) VALUES
(11, 13, 'in process', 'transport cas rm50', 'baik', 'item ordered', '2025-03-06 06:51:40', NULL),
(12, 13, 'in process', 'up rm200', 'good', 'baik', '2025-03-06 06:53:12', NULL),
(13, 13, 'in process', '', '123', '123', '2025-03-06 08:17:44', NULL),
(14, 3, 'in process', 'fghfgh', 'gfhfggf', 'ghfhfgh', '2025-03-18 14:05:30', NULL),
(15, 3, 'in process', 'bfcghfthtr', 'tghfthfghfgh', 'fghfghfghfgh', '2025-03-18 14:26:45', NULL),
(16, 3, 'in process', 'hghjghj', 'ghjghjghjgh', 'ghjghjghjghjgh', '2025-03-18 14:31:28', NULL),
(17, 3, 'in process', 'hghjghj', 'ghjghjghjgh', 'ghjghjghjghjgh', '2025-03-18 14:31:28', 3),
(18, 3, 'in process', 'nvnvn', 'gfhfghfghfg', 'hfghfghfgh', '2025-03-18 14:34:46', 3),
(19, 3, 'in process', 'cnbgfchbfg', 'fhfghgfhfg', 'hfghfghfghfghfg', '2025-03-18 14:39:04', 3),
(20, 3, 'in process', 'fgdfgdfg', 'dfgdfgdfg', 'dgdfgdfg', '2025-03-18 14:43:42', 3),
(21, 3, 'in process', 'hgjghj', 'hjgrtyd', 'dhyfhbdf', '2025-03-18 14:47:41', 3),
(22, 3, 'in process', 'fcdvxcv', 'cxvcvxcvxc', 'vcxvxcvxc', '2025-03-18 14:53:52', 3),
(23, 3, 'in process', 'vcxvcxvxc', 'xvcxcvxcvcxv', 'cxvxcvcxv', '2025-03-18 14:55:23', 3),
(24, 3, 'in process', 'bcvnbvn', 'vbnbnmbm', 'bmnbmnbmb', '2025-03-18 15:00:39', 3),
(25, 3, 'in process', 'bcfbnb', 'nbvnvbnvbn', 'vbnvbnvbn', '2025-03-18 15:03:05', 3),
(26, 3, 'in process', 'bcvbvcbc', 'vbcvbcvbc', 'bvcbcvbvc', '2025-03-18 15:05:16', 3),
(27, 3, 'in process', 'bcvbcvb', 'bvcbvc', 'vbcvbcv', '2025-03-18 15:06:19', 3),
(28, 3, 'in process', 'hfghfgh', 'fghfghfg', 'fghfghfg', '2025-03-18 15:10:42', 3),
(29, 3, 'in process', 'm,nm,', 'nm,nm,', 'gdgfgdf', '2025-03-18 15:13:39', 3),
(30, 3, 'in process', 'hfgh', 'gfhfgh', 'hgfhfgh', '2025-03-18 15:16:36', 3),
(31, 14, 'in process', 'rm50', 'semak', 'motor belum sampai', '2025-03-25 17:57:15', NULL),
(32, 14, 'in process', 'rm50 note by afnan', 'check by afnan', 'remark by afnan', '2025-03-25 18:10:40', 3),
(33, 14, 'in process', 'sds', 'sds', 'sds', '2025-03-25 18:29:31', 3),
(34, 14, 'in process', 'scsd', 'sdsd', 'sds', '2025-03-25 18:42:33', 3),
(35, 14, 'in process', 'dfsdf', 'sfs', 'sf2134', '2025-03-25 18:48:39', 3),
(36, 14, 'in process', 'sdsadmin', 'admin', 'admin', '2025-03-25 18:58:45', NULL),
(37, 14, 'in process', 'zxczcadmin', 'zczcadmin', 'zczadmin', '2025-03-25 19:00:24', NULL),
(38, 14, 'in process', 'asa', 'asa', 'asas', '2025-03-25 19:51:34', NULL),
(39, 14, 'in process', 'note admin', 'admin', 'admin', '2025-03-25 19:52:19', NULL),
(40, 14, 'in process', 'admin', 'admin', 'admin', '2025-03-25 19:53:15', NULL),
(41, 14, 'in process', 'rm 100', 'tengh test peti sejuk ', 'belum sampai', '2025-03-25 19:59:43', NULL),
(42, 15, 'in process', 'rm 100 minyak van ', 'check battery', 'oda battery', '2025-03-25 20:19:03', NULL),
(43, 17, 'in process', 'asasa', 'asas', 'asasasasbla asbdsds', '2025-08-25 05:53:08', NULL),
(44, 4, 'in process', 'sd', 'sd', 'sd', '2025-09-06 13:32:46', NULL),
(45, 5, 'in process', '10 km', 'check bare', 'lapan hari', '2025-09-10 01:25:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `otp`, `expires_at`, `created_at`) VALUES
(9, 1, '194698', '2025-08-27 12:15:36', '2025-08-27 10:05:36');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int(11) NOT NULL,
  `stateName` varchar(255) DEFAULT NULL,
  `stateDescription` tinytext DEFAULT NULL,
  `postingDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`id`, `stateName`, `stateDescription`, `postingDate`, `updationDate`) VALUES
(1, 'KAMPUNG RAJA', 'BESUT', '2020-06-27 19:18:02', '2025-02-15 08:24:40'),
(5, 'SETIU', 'TERENGGANU', '2025-02-15 08:25:01', NULL),
(7, 'JERTEH', 'TERENGGANU', '2025-02-15 08:25:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

CREATE TABLE `subcategory` (
  `id` int(11) NOT NULL,
  `categoryid` int(11) DEFAULT NULL,
  `subcategory` varchar(255) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subcategory`
--

INSERT INTO `subcategory` (`id`, `categoryid`, `subcategory`, `creationDate`, `updationDate`) VALUES
(3, 2, 'other', '2020-06-24 07:06:44', '2020-06-24 07:21:38'),
(5, 1, 'Mesin Basuh', '2025-02-15 08:19:30', NULL),
(6, 1, 'PETI', '2025-02-15 08:19:39', NULL),
(7, 1, 'DRYER', '2025-02-15 08:19:51', NULL),
(8, 1, 'FREEZER', '2025-02-15 08:19:59', NULL),
(9, 1, 'JAM AZAN MASJID', '2025-02-15 08:20:10', NULL),
(10, 1, 'WATER ( HEATER )', '2025-02-15 08:20:27', NULL),
(11, 1, 'TV (UNTUK 50\" KE ATAS SAHAJA)', '2025-02-15 08:20:45', NULL),
(12, 1, 'AIRCOND', '2025-02-15 08:20:54', NULL),
(13, 1, 'KIPAS SILING / DINDING', '2025-02-15 08:21:08', NULL),
(14, 4, 'AIRCOND SILING CASSETE', '2025-02-15 08:22:35', NULL),
(15, 4, 'AIRCOND WALL MOUNTED (BIASA)', '2025-02-15 08:22:47', NULL),
(16, 5, 'MESIN BASUH', '2025-02-15 08:23:44', NULL),
(17, 5, 'MESIN PENGERING', '2025-02-15 08:24:00', NULL),
(18, 1, 'VACUUM', '2025-08-18 04:44:48', NULL),
(19, 1, 'AIR COOLER', '2025-08-18 04:44:55', NULL),
(20, 1, 'SERVICE', '2025-08-18 04:45:03', NULL),
(21, 1, 'WIRING', '2025-08-18 04:45:18', NULL),
(22, 1, 'JUICER', '2025-08-18 04:45:26', NULL),
(23, 1, 'WATER JET', '2025-08-18 04:45:33', NULL),
(24, 1, 'AIR FRYER', '2025-08-18 04:45:41', NULL),
(25, 1, 'HAIR DRYER', '2025-08-18 04:45:58', NULL),
(26, 1, 'BREADMAKER', '2025-08-18 04:46:03', NULL),
(27, 1, 'THERMOPOT', '2025-08-18 04:46:14', NULL),
(28, 1, 'DRYER', '2025-08-18 04:46:26', NULL),
(29, 1, 'WATER DISPENSER', '2025-08-18 04:46:33', NULL),
(30, 1, 'WATER PUMP', '2025-08-18 04:46:40', NULL),
(31, 1, 'KETTLE JUG', '2025-08-18 04:46:48', NULL),
(32, 1, 'STEAMER', '2025-08-18 04:46:58', NULL),
(33, 1, 'ANDROID BOX', '2025-08-18 04:47:07', NULL),
(34, 1, 'HAND MIXER', '2025-08-18 04:47:13', NULL),
(35, 1, 'AIR PURIFIER', '2025-08-18 04:47:20', NULL),
(36, 1, 'SEALER', '2025-08-18 04:47:27', NULL),
(37, 1, 'SPEAKER', '2025-08-18 04:47:34', NULL),
(38, 1, 'JAM', '2025-08-18 04:47:41', NULL),
(39, 1, 'HOOD', '2025-08-18 04:47:46', NULL),
(40, 1, 'HOME THEATER', '2025-08-18 04:47:57', NULL),
(41, 1, 'INSECT KILLER', '2025-08-18 04:48:03', NULL),
(42, 1, 'GRILL PAN', '2025-08-18 04:48:09', NULL),
(43, 1, 'CCTV', '2025-08-18 04:48:16', NULL),
(44, 1, 'LAMPU', '2025-08-18 04:48:24', NULL),
(45, 1, 'HOOD', '2025-08-18 04:48:32', NULL),
(46, 1, 'AUTOGATE', '2025-08-18 04:48:42', NULL),
(47, 1, 'CHILLER', '2025-08-18 04:48:50', NULL),
(48, 1, 'EKZOS FAN', '2025-08-18 04:48:56', NULL),
(49, 1, 'AIRCOND', '2025-08-18 04:49:01', NULL),
(50, 1, 'NETWORK', '2025-08-18 04:49:07', NULL),
(51, 1, 'TRANSPORT', '2025-08-18 04:49:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcomplaints`
--

CREATE TABLE `tblcomplaints` (
  `complaintNumber` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `subcategory` varchar(255) DEFAULT NULL,
  `complaintType` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `brandname` varchar(100) NOT NULL,
  `modelNo` varchar(255) DEFAULT NULL,
  `complaintDetails` mediumtext DEFAULT NULL,
  `warrantyFile` varchar(255) DEFAULT NULL,
  `receiptFile` varchar(255) DEFAULT NULL,
  `regDate` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `lastUpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `reportNumber` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcomplaints`
--

INSERT INTO `tblcomplaints` (`complaintNumber`, `userId`, `category`, `subcategory`, `complaintType`, `state`, `brandname`, `modelNo`, `complaintDetails`, `warrantyFile`, `receiptFile`, `regDate`, `status`, `lastUpdationDate`, `reportNumber`) VALUES
(1, 1, 1, 'Mesin Basuh', 'Over Warranty', 'KAMPUNG RAJA', 'ACSON', '12323', 'easqdas', NULL, NULL, '2025-09-06 12:31:12', NULL, '2025-09-16 18:21:43', 'A00001'),
(2, 1, 1, 'Mesin Basuh', 'Over Warranty', 'KAMPUNG RAJA', 'ACSON', '12323', 'xcaxc', NULL, NULL, '2025-09-06 12:31:42', NULL, '2025-09-16 18:21:43', 'A00002'),
(3, 10, 1, 'AUTOGATE', 'Under Warranty', 'KAMPUNG RAJA', 'AIWA', '123123', 'sdsd', 'premium_photo-1673448760651-7e1e6fd79e40.jpg', 'forest-3119826_1280.jpg', '2025-09-06 13:30:10', NULL, '2025-09-16 18:21:43', 'A00003'),
(4, 10, 1, 'AUTOGATE', 'Under Warranty', 'KAMPUNG RAJA', 'AIWA', '123123', 'sdsd', 'premium_photo-1673448760651-7e1e6fd79e40.jpg', 'forest-3119826_1280.jpg', '2025-09-06 13:31:58', 'in process', '2025-09-16 18:21:43', 'A00004'),
(5, 11, 1, 'PETI', 'Under Warranty', 'KAMPUNG RAJA', 'AIWA', '', 'xcxczxzxz', 'MainBefore.jpg', 'MainBefore.jpg', '2025-09-09 08:57:04', 'in process', '2025-09-16 18:21:43', 'A00005'),
(6, 11, 1, 'Mesin Basuh', 'Under Warranty', 'KAMPUNG RAJA', 'AIWA', 'DFG3022', 'enjin rosok pastu netup', 'logo-01_1693205416.png', 'WhatsApp Image 2025-09-10 at 11.29.14 AM.jpeg', '2025-09-10 07:07:49', 'closed', '2025-09-16 18:21:43', 'A00006'),
(7, 11, 1, 'KETTLE JUG', 'Under Warranty', 'KAMPUNG RAJA', 'HESSTAR', '2421424', 'jug air x masak', '68c9ab64e04b6_7c0e67454daa4a269f2e44beab68e68b.jpeg', '68c9ab64e050f_WhatsAppImage20250507at15.21.02_6f9adfd5.jpg', '2025-09-16 18:24:36', NULL, NULL, 'A00007');

-- --------------------------------------------------------

--
-- Table structure for table `tblforwardhistory`
--

CREATE TABLE `tblforwardhistory` (
  `id` int(11) NOT NULL,
  `ComplaintNumber` bigint(12) DEFAULT NULL,
  `ForwardFrom` int(6) DEFAULT NULL,
  `ForwardTo` int(6) DEFAULT NULL,
  `ForwadDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblforwardhistory`
--

INSERT INTO `tblforwardhistory` (`id`, `ComplaintNumber`, `ForwardFrom`, `ForwardTo`, `ForwadDate`) VALUES
(1, 1, 1, 2, '2020-06-28 13:22:58'),
(2, 2, 1, 3, '2025-02-15 08:30:26'),
(3, 13, 1, 3, '2025-03-06 06:52:34'),
(4, 3, 1, 3, '2025-03-18 14:09:26'),
(5, 14, 1, 3, '2025-03-25 18:01:17'),
(6, 15, 1, 3, '2025-03-25 20:19:24'),
(7, 17, 1, 3, '2025-08-25 05:45:58'),
(8, 4, 10, 4, '2025-09-06 13:37:47'),
(9, 5, 11, 4, '2025-09-10 01:23:24'),
(10, 6, 1, 4, '2025-09-10 07:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `tblsubadmin`
--

CREATE TABLE `tblsubadmin` (
  `id` int(11) NOT NULL,
  `SubAdminName` varchar(150) DEFAULT NULL,
  `Department` varchar(150) DEFAULT NULL,
  `EmailId` varchar(150) DEFAULT NULL,
  `ContactNumber` bigint(12) DEFAULT NULL,
  `UserName` varchar(12) DEFAULT NULL,
  `Password` varchar(150) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `LastUpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `IsActive` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblsubadmin`
--

INSERT INTO `tblsubadmin` (`id`, `SubAdminName`, `Department`, `EmailId`, `ContactNumber`, `UserName`, `Password`, `RegDate`, `LastUpdationDate`, `IsActive`) VALUES
(3, 'Afnan', 'It technician', 'nanibos@gmail.com', 199904162, 'afnan', 'f925916e2754e5e03f75dd58a5733251', '2025-02-15 08:15:22', NULL, 1),
(4, 'Ali', 'Aircond', 'ali@gmail.con', 112930921, 'ali', 'f925916e2754e5e03f75dd58a5733251', '2025-08-25 13:05:22', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblsubadminremark`
--

CREATE TABLE `tblsubadminremark` (
  `id` int(11) NOT NULL,
  `ComplainNumber` varchar(50) NOT NULL,
  `ComplainRemark` text DEFAULT NULL,
  `ComplainStatus` varchar(50) DEFAULT NULL,
  `PostingDate` datetime DEFAULT NULL,
  `notetransport` text DEFAULT NULL,
  `checking` text DEFAULT NULL,
  `RemarkBy` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tblsubadminremark`
--

INSERT INTO `tblsubadminremark` (`id`, `ComplainNumber`, `ComplainRemark`, `ComplainStatus`, `PostingDate`, `notetransport`, `checking`, `RemarkBy`) VALUES
(1, '13', 'as', 'in process', '2025-08-25 13:44:43', 'asas', 'asas', 3),
(2, '5', '8 hari siap', 'in process', '2025-09-10 09:35:44', '6 km', 'masih check item', 4),
(3, '6', 'closed case at 15/9 at 3.00 pm', 'closed', '2025-09-10 15:15:32', '10', '10', 4);

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `userip` varchar(45) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `loginTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `uid`, `username`, `userip`, `status`, `loginTime`) VALUES
(1, NULL, '020116110323', '183.171.99.234', 0, '2025-08-25 02:34:17'),
(2, 1, '020116110323', '183.171.99.234', 1, '2025-08-25 03:05:45'),
(3, 1, '020116110323', '183.171.99.234', 1, '2025-08-25 03:09:34'),
(4, 1, '020116110323', '183.171.99.234', 1, '2025-08-25 03:17:36'),
(5, 1, '020116110323', '183.171.99.234', 1, '2025-08-25 03:20:48'),
(6, 1, '020116110323', '183.171.99.234', 1, '2025-08-25 03:21:00'),
(7, 7, 'sojo123@gmail.com', '::1', 1, '2025-08-27 04:10:36'),
(8, 1, '020116110323', '::1', 1, '2025-08-27 04:33:39'),
(9, 1, '020116110323', '::1', 1, '2025-08-27 04:47:47'),
(10, 1, '020116110323', '::1', 1, '2025-08-27 05:01:36'),
(11, 1, '020116110323', '::1', 1, '2025-08-27 09:03:01'),
(12, 1, '020116110323', '::1', 1, '2025-08-27 09:03:25'),
(13, 1, '020116110323', '::1', 1, '2025-09-05 01:59:05'),
(14, 9, '020116110390', '::1', 1, '2025-09-05 02:04:05'),
(15, 1, '020116110323', '::1', 1, '2025-09-05 16:05:24'),
(16, 1, '020116110323', '::1', 1, '2025-09-05 16:06:27'),
(17, 1, '020116110323', '::1', 1, '2025-09-05 16:08:24'),
(18, 1, '020116110323', '::1', 1, '2025-09-05 17:29:09'),
(19, 1, '020116110323', '::1', 1, '2025-09-06 10:50:33'),
(20, 4, '020116110326', '115.132.189.80', 1, '2025-09-09 01:27:42'),
(21, 11, '020116110323', '115.132.189.80', 1, '2025-09-09 01:59:40'),
(22, 11, '020116110323', '175.138.241.149', 1, '2025-09-09 08:53:17'),
(23, 11, '020116110323', '175.138.241.149', 1, '2025-09-09 08:54:12'),
(24, 11, '020116110323', '175.138.241.149', 1, '2025-09-10 01:19:47'),
(25, 11, '020116110323', '175.138.241.149', 1, '2025-09-10 01:22:40'),
(26, 11, '020116110323', '175.138.241.149', 1, '2025-09-10 03:35:53'),
(27, 11, '020116110323', '183.171.104.110', 1, '2025-09-10 06:54:52'),
(28, 11, '020116110323', '27.125.240.24', 1, '2025-09-10 07:05:13'),
(29, 11, '020116110323', '27.125.240.26', 1, '2025-09-10 07:13:40'),
(30, 11, '020116110323', '27.125.240.26', 1, '2025-09-10 07:16:35'),
(31, 11, '020116110323', '27.125.240.26', 1, '2025-09-10 07:30:04'),
(32, 11, '020116110323', '::1', 1, '2025-09-16 18:00:41'),
(33, 11, 'ahmadzahid482@gmail.com', '::1', 1, '2025-09-16 18:04:04'),
(34, 11, 'ahmadzahid482@gmail.com', '::1', 1, '2025-09-16 18:23:36'),
(35, 11, 'ahmadzahid482@gmail.com', '::1', 1, '2025-09-16 18:26:26'),
(36, 11, 'ahmadzahid482@gmail.com', '::1', 1, '2025-09-16 18:43:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `userEmail` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `contactno` varchar(15) NOT NULL,
  `contactno2` varchar(15) DEFAULT NULL,
  `icnumber` varchar(12) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `State` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `userImage` varchar(255) DEFAULT NULL,
  `regDate` timestamp NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `updationDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullName`, `userEmail`, `password`, `contactno`, `contactno2`, `icnumber`, `address`, `status`, `State`, `country`, `pincode`, `userImage`, `regDate`, `reset_token`, `reset_expires`, `updationDate`) VALUES
(4, 'paknan hesem', NULL, '$2y$12$JEO2pOy1cQlrCizibqsZ/.7XpCSX1CWHQKgydUUknCLsEyRnpuZgG', '0199904162', '0157963632', '020116110326', 'azdxad', '1', NULL, NULL, NULL, NULL, '2025-08-25 03:44:21', NULL, NULL, NULL),
(5, 'paknan hesem', NULL, '$2y$12$U7a8EXYqJBmPYOk85bBOtufZCFhceguse/Qjswe1xUxAT.jhQL8AO', '12345678', '3266266160', '020116110324', 'ZXDXZXD', '1', NULL, NULL, NULL, NULL, '2025-08-25 03:45:07', NULL, NULL, NULL),
(6, 'alia shahirah', 'husnana408@gmail.com', '$2y$12$xLdEIsrUDPqLe8LGOtTIKusvmTeE5.TbJW5cytOj9uuFC/ef6fI8q', '0199904162', '0199904162', '730207386669', '2122,TAMAN SERI BUDI,\r\nPADANG MIDIN', '1', NULL, NULL, NULL, NULL, '2025-08-25 05:55:09', NULL, NULL, NULL),
(7, 'Hope Vinson', 'sojo123@gmail.com', '$2y$10$b3LRyjSGHVZDs5/LPVJWF.ALvCw/VGG7H6sDsMU0vmg5FXbKdB9.q', '16283735959', '19263652253', '470701146072', 'Ut sequi enim dicta', '1', NULL, NULL, NULL, NULL, '2025-08-27 04:09:55', NULL, NULL, NULL),
(8, 'Ori Dorsey', 'hatowi@mailinator.com', '$2y$10$ZkyoWdXJ3Higfu/1KBIsHOySchYv0YtsWDRghoY1XgpuFvf1hdPHm', '18935692544', '17677097186', '891231231212', 'Aut sunt rerum labozxzx', '1', NULL, NULL, NULL, NULL, '2025-08-27 09:38:10', NULL, NULL, NULL),
(9, 'yaya', NULL, '$2y$10$2IF4deMFevH9UndfmayxeOP1Zwh.2DW9vf2uy1.A3BClD8Af2.zI.', '014555555', '014555557', '020116110390', 'asdasaa', '1', NULL, NULL, NULL, NULL, '2025-09-05 02:03:07', NULL, NULL, NULL),
(11, 'AHMAD ZAHID BIN MOHD SOFI', 'ahmadzahid482@gmail.com', '$2y$10$kHAmvMFVikWT//cYpSNvyODvCoRbH/aveRzRXEZV7FzGBftlvoahG', '0147963532', '0147963456', '020116110323', '2122 taman seri budi padang midin,21400 kuala terengganu\r\nterengganu', '1', 'KAMPUNG RAJA', 'malaysia', '21400', NULL, '2025-09-09 01:36:11', NULL, NULL, '2025-09-17 02:43:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brandname`
--
ALTER TABLE `brandname`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryid` (`categoryid`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaintremark`
--
ALTER TABLE `complaintremark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcomplaints`
--
ALTER TABLE `tblcomplaints`
  ADD PRIMARY KEY (`complaintNumber`),
  ADD UNIQUE KEY `reportNumber` (`reportNumber`);

--
-- Indexes for table `tblforwardhistory`
--
ALTER TABLE `tblforwardhistory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblsubadmin`
--
ALTER TABLE `tblsubadmin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblsubadminremark`
--
ALTER TABLE `tblsubadminremark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `icnumber` (`icnumber`),
  ADD UNIQUE KEY `userEmail` (`userEmail`),
  ADD UNIQUE KEY `userEmail_2` (`userEmail`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brandname`
--
ALTER TABLE `brandname`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `complaintremark`
--
ALTER TABLE `complaintremark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tblcomplaints`
--
ALTER TABLE `tblcomplaints`
  MODIFY `complaintNumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblforwardhistory`
--
ALTER TABLE `tblforwardhistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tblsubadmin`
--
ALTER TABLE `tblsubadmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblsubadminremark`
--
ALTER TABLE `tblsubadminremark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `brandname`
--
ALTER TABLE `brandname`
  ADD CONSTRAINT `brandname_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `category` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
