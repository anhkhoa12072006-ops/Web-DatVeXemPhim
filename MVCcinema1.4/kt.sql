-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 01, 2026 at 08:17 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kt`
--
CREATE DATABASE IF NOT EXISTS `kt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kt`;

-- --------------------------------------------------------

--
-- Table structure for table `binhluan`
--

CREATE TABLE `binhluan` (
  `mabl` int NOT NULL,
  `tendn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `maphim` int NOT NULL,
  `noidung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `diemdanhgia` tinyint NOT NULL,
  `ngaytao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `binhluan`
--

INSERT INTO `binhluan` (`mabl`, `tendn`, `maphim`, `noidung`, `diemdanhgia`, `ngaytao`) VALUES
(1, 'dan', 1, 'Phim rất hay và cảm động!', 5, '2026-04-28 03:23:43'),
(2, 'khoa', 2, 'Cũng khá sợ hãi, nhưng nội dung chưa đặc sắc lắm', 3, '2026-04-28 03:23:43'),
(3, 'dan', 8, 'Kỹ xảo Avatar 2 quá đỉnh, đáng đồng tiền bát gạo.', 5, '2026-04-28 03:23:44'),
(4, 'khoa', 5, 'Phim Conan phần này plot twist khá dễ đoán.', 4, '2026-04-28 03:23:44'),
(5, 'dan', 9, 'Luca dễ thương xỉu, thông điệp gia đình tuyệt vời.', 5, '2026-04-28 03:23:44'),
(6, 'khoa', 1, 'Mẹ con diễn cảm động, mình đã khóc rất nhiều.', 5, '2026-04-28 03:23:44'),
(7, 'dan', 3, 'Phim hài hước, xả stress tốt dịp cuối tuần.', 4, '2026-04-28 03:23:44'),
(9, 'dan', 7, 'Ý tưởng hay nhưng hoạt hình chưa mượt lắm.', 3, '2026-04-28 03:23:44'),
(10, 'khoa', 8, 'Coi IMAX phê thực sự, 10 điểm không có nhưng.', 5, '2026-04-28 03:23:44'),
(11, 'dan', 2, 'Góc máy đẹp nhưng âm thanh chèn nhiều jumpscare quá.', 3, '2026-04-28 03:23:44'),
(12, 'khoa', 9, 'Nhạc phim hay, màu sắc tươi sáng.', 4, '2026-04-28 03:23:44'),
(13, 'thuyen', 7, 'phim bình thường', 3, '2026-04-30 16:17:20');

-- --------------------------------------------------------

--
-- Table structure for table `cauhinh`
--

CREATE TABLE `cauhinh` (
  `id` int NOT NULL,
  `tukhoa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `giatri` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mota` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cauhinh`
--

INSERT INTO `cauhinh` (`id`, `tukhoa`, `giatri`, `mota`) VALUES
(1, 'momo_env', 'sandbox', 'Môi trường thanh toán: sandbox hoặc production'),
(2, 'momo_partner_code', 'MOMOBKUN20180529', 'Partner Code MoMo'),
(3, 'momo_access_key', 'klm05TvNBzhg7h7j', 'Access Key MoMo'),
(4, 'momo_secret_key', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa', 'Secret Key MoMo'),
(5, 'vietqr_bank', 'vietcombank', 'Mã Ngân hàng (VietQR)'),
(6, 'vietqr_account', '1050587264', 'Số tài khoản nhận tiền'),
(7, 'vietqr_name', 'NGUYEN NGOC MINH NHA', 'Tên chủ tài khoản ngân hàng');

-- --------------------------------------------------------

--
-- Table structure for table `chitietve`
--

CREATE TABLE `chitietve` (
  `mahd` int NOT NULL,
  `masuat` int NOT NULL,
  `maghe` int NOT NULL,
  `giave` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `danhmuc`
--

CREATE TABLE `danhmuc` (
  `madm` int NOT NULL,
  `tendm` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ghichu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danhmuc`
--

INSERT INTO `danhmuc` (`madm`, `tendm`, `logo`, `ghichu`) VALUES
(1, 'Hoạt hình', 'logoa', 'abc'),
(2, 'Phim ảnh', 'logob', 'bcd'),
(3, 'Hành động', 'logoc.png', 'Phim hành động kịch tính'),
(4, 'Viễn tưởng', 'logod.png', 'Phim khoa học viễn tưởng'),
(5, 'Kinh dị', 'logoe.png', 'Phim kinh dị giật gân'),
(6, 'Tài liệu', 'logof.png', 'Phim tài liệu thực tế'),
(7, 'Nhạc kịch', 'logog.png', 'Phim âm nhạc, vũ đạo'),
(8, 'Gia đình', 'logoh.png', 'Phim dành cho mọi lứa tuổi'),
(9, 'Cổ trang', 'logoi.png', 'Phim lịch sử, thần thoại'),
(10, 'Trinh thám', 'logok.png', 'Phim phá án, bí ẩn'),
(11, 'Hài hước', 'logol.png', 'Phim giải trí nhẹ nhàng'),
(12, 'Tâm lý', 'logom.png', 'Phim tâm lý xã hội sâu sắc');

-- --------------------------------------------------------

--
-- Table structure for table `ghengoi`
--

CREATE TABLE `ghengoi` (
  `maghe` int NOT NULL,
  `maphong` int NOT NULL,
  `tenghe` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `loaighe` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Thường'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ghengoi`
--

INSERT INTO `ghengoi` (`maghe`, `maphong`, `tenghe`, `loaighe`) VALUES
(18, 14, 'A1', 'Thường'),
(19, 14, 'A2', 'Thường'),
(20, 14, 'A3', 'Thường'),
(21, 14, 'A4', 'Thường'),
(22, 14, 'A5', 'Thường'),
(23, 14, 'A6', 'Thường'),
(24, 14, 'A7', 'Thường'),
(25, 14, 'A8', 'Thường'),
(26, 14, 'A9', 'Thường'),
(27, 14, 'A10', 'Thường'),
(28, 14, 'B1', 'Thường'),
(29, 14, 'B2', 'Thường'),
(30, 14, 'B3', 'Thường'),
(31, 14, 'B4', 'Thường'),
(32, 14, 'B5', 'Thường'),
(33, 14, 'B6', 'Thường'),
(34, 14, 'B7', 'Thường'),
(35, 14, 'B8', 'Thường'),
(36, 14, 'B9', 'Thường'),
(37, 14, 'B10', 'Thường'),
(38, 14, 'C1', 'Thường'),
(39, 14, 'C2', 'Thường'),
(40, 14, 'C3', 'Thường'),
(41, 14, 'C4', 'Thường'),
(42, 14, 'C5', 'Thường'),
(43, 14, 'C6', 'Thường'),
(44, 14, 'C7', 'Thường'),
(45, 14, 'C8', 'Thường'),
(46, 14, 'C9', 'Thường'),
(47, 14, 'C10', 'Thường'),
(48, 14, 'D1', 'Thường'),
(49, 14, 'D2', 'Thường'),
(50, 14, 'D3', 'Thường'),
(51, 14, 'D4', 'Thường'),
(52, 14, 'D5', 'Thường'),
(53, 14, 'D6', 'Thường'),
(54, 14, 'D7', 'Thường'),
(55, 14, 'D8', 'Thường'),
(56, 14, 'D9', 'Thường'),
(57, 14, 'D10', 'Thường'),
(58, 14, 'E1', 'Thường'),
(59, 14, 'E2', 'Thường'),
(60, 14, 'E3', 'Thường'),
(61, 14, 'E4', 'Thường'),
(62, 14, 'E5', 'Thường'),
(63, 14, 'E6', 'Thường'),
(64, 14, 'E7', 'Thường'),
(65, 14, 'E8', 'Thường'),
(66, 14, 'E9', 'Thường'),
(67, 14, 'E10', 'Thường'),
(118, 2, 'A1', 'VIP'),
(119, 2, 'A2', 'VIP'),
(120, 2, 'A3', 'VIP'),
(121, 2, 'A4', 'VIP'),
(122, 2, 'A5', 'VIP'),
(123, 2, 'A6', 'VIP'),
(124, 2, 'A7', 'VIP'),
(125, 2, 'A8', 'VIP'),
(126, 2, 'A9', 'VIP'),
(127, 2, 'A10', 'VIP'),
(128, 2, 'B1', 'VIP'),
(129, 2, 'B2', 'VIP'),
(130, 2, 'B3', 'VIP'),
(131, 2, 'B4', 'VIP'),
(132, 2, 'B5', 'VIP'),
(133, 2, 'B6', 'VIP'),
(134, 2, 'B7', 'VIP'),
(135, 2, 'B8', 'VIP'),
(136, 2, 'B9', 'VIP'),
(137, 2, 'B10', 'VIP'),
(138, 2, 'C1', 'VIP'),
(139, 2, 'C2', 'VIP'),
(140, 2, 'C3', 'VIP'),
(141, 2, 'C4', 'VIP'),
(142, 2, 'C5', 'VIP'),
(143, 2, 'C6', 'VIP'),
(144, 2, 'C7', 'VIP'),
(145, 2, 'C8', 'VIP'),
(146, 2, 'C9', 'VIP'),
(147, 2, 'C10', 'VIP'),
(148, 2, 'D1', 'VIP'),
(149, 2, 'D2', 'VIP'),
(150, 2, 'D3', 'VIP'),
(151, 2, 'D4', 'VIP'),
(152, 2, 'D5', 'VIP'),
(153, 2, 'D6', 'VIP'),
(154, 2, 'D7', 'VIP'),
(155, 2, 'D8', 'VIP'),
(156, 2, 'D9', 'VIP'),
(157, 2, 'D10', 'VIP'),
(158, 2, 'E1', 'VIP'),
(159, 2, 'E2', 'VIP'),
(160, 2, 'E3', 'VIP'),
(161, 2, 'E4', 'VIP'),
(162, 2, 'E5', 'VIP'),
(163, 2, 'E6', 'VIP'),
(164, 2, 'E7', 'VIP'),
(165, 2, 'E8', 'VIP'),
(166, 2, 'E9', 'VIP'),
(167, 2, 'E10', 'VIP'),
(168, 3, 'A1', 'Thường'),
(169, 3, 'A2', 'Thường'),
(170, 3, 'A3', 'Thường'),
(171, 3, 'A4', 'Thường'),
(172, 3, 'A5', 'Thường'),
(173, 3, 'A6', 'Thường'),
(174, 3, 'A7', 'Thường'),
(175, 3, 'A8', 'Thường'),
(176, 3, 'A9', 'Thường'),
(177, 3, 'A10', 'Thường'),
(178, 3, 'B1', 'Thường'),
(179, 3, 'B2', 'Thường'),
(180, 3, 'B3', 'Thường'),
(181, 3, 'B4', 'Thường'),
(182, 3, 'B5', 'Thường'),
(183, 3, 'B6', 'Thường'),
(184, 3, 'B7', 'Thường'),
(185, 3, 'B8', 'Thường'),
(186, 3, 'B9', 'Thường'),
(187, 3, 'B10', 'Thường'),
(188, 3, 'C1', 'Thường'),
(189, 3, 'C2', 'Thường'),
(190, 3, 'C3', 'Thường'),
(191, 3, 'C4', 'Thường'),
(192, 3, 'C5', 'Thường'),
(193, 3, 'C6', 'Thường'),
(194, 3, 'C7', 'Thường'),
(195, 3, 'C8', 'Thường'),
(196, 3, 'C9', 'Thường'),
(197, 3, 'C10', 'Thường'),
(198, 3, 'D1', 'Thường'),
(199, 3, 'D2', 'Thường'),
(200, 3, 'D3', 'Thường'),
(201, 3, 'D4', 'Thường'),
(202, 3, 'D5', 'Thường'),
(203, 3, 'D6', 'Thường'),
(204, 3, 'D7', 'Thường'),
(205, 3, 'D8', 'Thường'),
(206, 3, 'D9', 'Thường'),
(207, 3, 'D10', 'Thường'),
(208, 3, 'E1', 'Thường'),
(209, 3, 'E2', 'Thường'),
(210, 3, 'E3', 'Thường'),
(211, 3, 'E4', 'Thường'),
(212, 3, 'E5', 'Thường'),
(213, 3, 'E6', 'Thường'),
(214, 3, 'E7', 'Thường'),
(215, 3, 'E8', 'Thường'),
(216, 3, 'E9', 'Thường'),
(217, 3, 'E10', 'Thường'),
(218, 4, 'A1', 'Thường'),
(219, 4, 'A2', 'Thường'),
(220, 4, 'A3', 'Thường'),
(221, 4, 'A4', 'Thường'),
(222, 4, 'A5', 'Thường'),
(223, 4, 'A6', 'Thường'),
(224, 4, 'A7', 'Thường'),
(225, 4, 'A8', 'Thường'),
(226, 4, 'A9', 'Thường'),
(227, 4, 'A10', 'Thường'),
(228, 4, 'B1', 'Thường'),
(229, 4, 'B2', 'Thường'),
(230, 4, 'B3', 'Thường'),
(231, 4, 'B4', 'Thường'),
(232, 4, 'B5', 'Thường'),
(233, 4, 'B6', 'Thường'),
(234, 4, 'B7', 'Thường'),
(235, 4, 'B8', 'Thường'),
(236, 4, 'B9', 'Thường'),
(237, 4, 'B10', 'Thường'),
(238, 4, 'C1', 'Thường'),
(239, 4, 'C2', 'Thường'),
(240, 4, 'C3', 'Thường'),
(241, 4, 'C4', 'Thường'),
(242, 4, 'C5', 'Thường'),
(243, 4, 'C6', 'Thường'),
(244, 4, 'C7', 'Thường'),
(245, 4, 'C8', 'Thường'),
(246, 4, 'C9', 'Thường'),
(247, 4, 'C10', 'Thường'),
(248, 4, 'D1', 'Thường'),
(249, 4, 'D2', 'Thường'),
(250, 4, 'D3', 'Thường'),
(251, 4, 'D4', 'Thường'),
(252, 4, 'D5', 'Thường'),
(253, 4, 'D6', 'Thường'),
(254, 4, 'D7', 'Thường'),
(255, 4, 'D8', 'Thường'),
(256, 4, 'D9', 'Thường'),
(257, 4, 'D10', 'Thường'),
(258, 4, 'E1', 'Thường'),
(259, 4, 'E2', 'Thường'),
(260, 4, 'E3', 'Thường'),
(261, 4, 'E4', 'Thường'),
(262, 4, 'E5', 'Thường'),
(263, 4, 'E6', 'Thường'),
(264, 4, 'E7', 'Thường'),
(265, 4, 'E8', 'Thường'),
(266, 4, 'E9', 'Thường'),
(267, 4, 'E10', 'Thường'),
(268, 5, 'A1', 'Thường'),
(269, 5, 'A2', 'Thường'),
(270, 5, 'A3', 'Thường'),
(271, 5, 'A4', 'Thường'),
(272, 5, 'A5', 'Thường'),
(273, 5, 'A6', 'Thường'),
(274, 5, 'A7', 'Thường'),
(275, 5, 'A8', 'Thường'),
(276, 5, 'A9', 'Thường'),
(277, 5, 'A10', 'Thường'),
(278, 5, 'B1', 'Thường'),
(279, 5, 'B2', 'Thường'),
(280, 5, 'B3', 'Thường'),
(281, 5, 'B4', 'Thường'),
(282, 5, 'B5', 'Thường'),
(283, 5, 'B6', 'Thường'),
(284, 5, 'B7', 'Thường'),
(285, 5, 'B8', 'Thường'),
(286, 5, 'B9', 'Thường'),
(287, 5, 'B10', 'Thường'),
(288, 5, 'C1', 'Thường'),
(289, 5, 'C2', 'Thường'),
(290, 5, 'C3', 'Thường'),
(291, 5, 'C4', 'Thường'),
(292, 5, 'C5', 'Thường'),
(293, 5, 'C6', 'Thường'),
(294, 5, 'C7', 'Thường'),
(295, 5, 'C8', 'Thường'),
(296, 5, 'C9', 'Thường'),
(297, 5, 'C10', 'Thường'),
(298, 5, 'D1', 'Thường'),
(299, 5, 'D2', 'Thường'),
(300, 5, 'D3', 'Thường'),
(301, 5, 'D4', 'Thường'),
(302, 5, 'D5', 'Thường'),
(303, 5, 'D6', 'Thường'),
(304, 5, 'D7', 'Thường'),
(305, 5, 'D8', 'Thường'),
(306, 5, 'D9', 'Thường'),
(307, 5, 'D10', 'Thường'),
(308, 5, 'E1', 'Thường'),
(309, 5, 'E2', 'Thường'),
(310, 5, 'E3', 'Thường'),
(311, 5, 'E4', 'Thường'),
(312, 5, 'E5', 'Thường'),
(313, 5, 'E6', 'Thường'),
(314, 5, 'E7', 'Thường'),
(315, 5, 'E8', 'Thường'),
(316, 5, 'E9', 'Thường'),
(317, 5, 'E10', 'Thường'),
(318, 6, 'A1', 'Thường'),
(319, 6, 'A2', 'Thường'),
(320, 6, 'A3', 'Thường'),
(321, 6, 'A4', 'Thường'),
(322, 6, 'A5', 'Thường'),
(323, 6, 'A6', 'Thường'),
(324, 6, 'A7', 'Thường'),
(325, 6, 'A8', 'Thường'),
(326, 6, 'A9', 'Thường'),
(327, 6, 'A10', 'Thường'),
(328, 6, 'B1', 'Thường'),
(329, 6, 'B2', 'Thường'),
(330, 6, 'B3', 'Thường'),
(331, 6, 'B4', 'Thường'),
(332, 6, 'B5', 'Thường'),
(333, 6, 'B6', 'Thường'),
(334, 6, 'B7', 'Thường'),
(335, 6, 'B8', 'Thường'),
(336, 6, 'B9', 'Thường'),
(337, 6, 'B10', 'Thường'),
(338, 6, 'C1', 'Thường'),
(339, 6, 'C2', 'Thường'),
(340, 6, 'C3', 'Thường'),
(341, 6, 'C4', 'Thường'),
(342, 6, 'C5', 'Thường'),
(343, 6, 'C6', 'Thường'),
(344, 6, 'C7', 'Thường'),
(345, 6, 'C8', 'Thường'),
(346, 6, 'C9', 'Thường'),
(347, 6, 'C10', 'Thường'),
(348, 6, 'D1', 'Thường'),
(349, 6, 'D2', 'Thường'),
(350, 6, 'D3', 'Thường'),
(351, 6, 'D4', 'Thường'),
(352, 6, 'D5', 'Thường'),
(353, 6, 'D6', 'Thường'),
(354, 6, 'D7', 'Thường'),
(355, 6, 'D8', 'Thường'),
(356, 6, 'D9', 'Thường'),
(357, 6, 'D10', 'Thường'),
(358, 6, 'E1', 'Thường'),
(359, 6, 'E2', 'Thường'),
(360, 6, 'E3', 'Thường'),
(361, 6, 'E4', 'Thường'),
(362, 6, 'E5', 'Thường'),
(363, 6, 'E6', 'Thường'),
(364, 6, 'E7', 'Thường'),
(365, 6, 'E8', 'Thường'),
(366, 6, 'E9', 'Thường'),
(367, 6, 'E10', 'Thường'),
(368, 7, 'A1', 'Thường'),
(369, 7, 'A2', 'Thường'),
(370, 7, 'A3', 'Thường'),
(371, 7, 'A4', 'Thường'),
(372, 7, 'A5', 'Thường'),
(373, 7, 'A6', 'Thường'),
(374, 7, 'A7', 'Thường'),
(375, 7, 'A8', 'Thường'),
(376, 7, 'A9', 'Thường'),
(377, 7, 'A10', 'Thường'),
(378, 7, 'B1', 'Thường'),
(379, 7, 'B2', 'Thường'),
(380, 7, 'B3', 'Thường'),
(381, 7, 'B4', 'Thường'),
(382, 7, 'B5', 'Thường'),
(383, 7, 'B6', 'Thường'),
(384, 7, 'B7', 'Thường'),
(385, 7, 'B8', 'Thường'),
(386, 7, 'B9', 'Thường'),
(387, 7, 'B10', 'Thường'),
(388, 7, 'C1', 'Thường'),
(389, 7, 'C2', 'Thường'),
(390, 7, 'C3', 'Thường'),
(391, 7, 'C4', 'Thường'),
(392, 7, 'C5', 'Thường'),
(393, 7, 'C6', 'Thường'),
(394, 7, 'C7', 'Thường'),
(395, 7, 'C8', 'Thường'),
(396, 7, 'C9', 'Thường'),
(397, 7, 'C10', 'Thường'),
(398, 7, 'D1', 'Thường'),
(399, 7, 'D2', 'Thường'),
(400, 7, 'D3', 'Thường'),
(401, 7, 'D4', 'Thường'),
(402, 7, 'D5', 'Thường'),
(403, 7, 'D6', 'Thường'),
(404, 7, 'D7', 'Thường'),
(405, 7, 'D8', 'Thường'),
(406, 7, 'D9', 'Thường'),
(407, 7, 'D10', 'Thường'),
(408, 7, 'E1', 'Thường'),
(409, 7, 'E2', 'Thường'),
(410, 7, 'E3', 'Thường'),
(411, 7, 'E4', 'Thường'),
(412, 7, 'E5', 'Thường'),
(413, 7, 'E6', 'Thường'),
(414, 7, 'E7', 'Thường'),
(415, 7, 'E8', 'Thường'),
(416, 7, 'E9', 'Thường'),
(417, 7, 'E10', 'Thường'),
(418, 8, 'A1', 'Thường'),
(419, 8, 'A2', 'Thường'),
(420, 8, 'A3', 'Thường'),
(421, 8, 'A4', 'Thường'),
(422, 8, 'A5', 'Thường'),
(423, 8, 'A6', 'Thường'),
(424, 8, 'A7', 'Thường'),
(425, 8, 'A8', 'Thường'),
(426, 8, 'A9', 'Thường'),
(427, 8, 'A10', 'Thường'),
(428, 8, 'B1', 'Thường'),
(429, 8, 'B2', 'Thường'),
(430, 8, 'B3', 'Thường'),
(431, 8, 'B4', 'Thường'),
(432, 8, 'B5', 'Thường'),
(433, 8, 'B6', 'Thường'),
(434, 8, 'B7', 'Thường'),
(435, 8, 'B8', 'Thường'),
(436, 8, 'B9', 'Thường'),
(437, 8, 'B10', 'Thường'),
(438, 8, 'C1', 'Thường'),
(439, 8, 'C2', 'Thường'),
(440, 8, 'C3', 'Thường'),
(441, 8, 'C4', 'Thường'),
(442, 8, 'C5', 'Thường'),
(443, 8, 'C6', 'Thường'),
(444, 8, 'C7', 'Thường'),
(445, 8, 'C8', 'Thường'),
(446, 8, 'C9', 'Thường'),
(447, 8, 'C10', 'Thường'),
(448, 8, 'D1', 'Thường'),
(449, 8, 'D2', 'Thường'),
(450, 8, 'D3', 'Thường'),
(451, 8, 'D4', 'Thường'),
(452, 8, 'D5', 'Thường'),
(453, 8, 'D6', 'Thường'),
(454, 8, 'D7', 'Thường'),
(455, 8, 'D8', 'Thường'),
(456, 8, 'D9', 'Thường'),
(457, 8, 'D10', 'Thường'),
(458, 8, 'E1', 'Thường'),
(459, 8, 'E2', 'Thường'),
(460, 8, 'E3', 'Thường'),
(461, 8, 'E4', 'Thường'),
(462, 8, 'E5', 'Thường'),
(463, 8, 'E6', 'Thường'),
(464, 8, 'E7', 'Thường'),
(465, 8, 'E8', 'Thường'),
(466, 8, 'E9', 'Thường'),
(467, 8, 'E10', 'Thường'),
(468, 9, 'A1', 'Thường'),
(469, 9, 'A2', 'Thường'),
(470, 9, 'A3', 'Thường'),
(471, 9, 'A4', 'Thường'),
(472, 9, 'A5', 'Thường'),
(473, 9, 'A6', 'Thường'),
(474, 9, 'A7', 'Thường'),
(475, 9, 'A8', 'Thường'),
(476, 9, 'A9', 'Thường'),
(477, 9, 'A10', 'Thường'),
(478, 9, 'B1', 'Thường'),
(479, 9, 'B2', 'Thường'),
(480, 9, 'B3', 'Thường'),
(481, 9, 'B4', 'Thường'),
(482, 9, 'B5', 'Thường'),
(483, 9, 'B6', 'Thường'),
(484, 9, 'B7', 'Thường'),
(485, 9, 'B8', 'Thường'),
(486, 9, 'B9', 'Thường'),
(487, 9, 'B10', 'Thường'),
(488, 9, 'C1', 'Thường'),
(489, 9, 'C2', 'Thường'),
(490, 9, 'C3', 'Thường'),
(491, 9, 'C4', 'Thường'),
(492, 9, 'C5', 'Thường'),
(493, 9, 'C6', 'Thường'),
(494, 9, 'C7', 'Thường'),
(495, 9, 'C8', 'Thường'),
(496, 9, 'C9', 'Thường'),
(497, 9, 'C10', 'Thường'),
(498, 9, 'D1', 'Thường'),
(499, 9, 'D2', 'Thường'),
(500, 9, 'D3', 'Thường'),
(501, 9, 'D4', 'Thường'),
(502, 9, 'D5', 'Thường'),
(503, 9, 'D6', 'Thường'),
(504, 9, 'D7', 'Thường'),
(505, 9, 'D8', 'Thường'),
(506, 9, 'D9', 'Thường'),
(507, 9, 'D10', 'Thường'),
(508, 9, 'E1', 'Thường'),
(509, 9, 'E2', 'Thường'),
(510, 9, 'E3', 'Thường'),
(511, 9, 'E4', 'Thường'),
(512, 9, 'E5', 'Thường'),
(513, 9, 'E6', 'Thường'),
(514, 9, 'E7', 'Thường'),
(515, 9, 'E8', 'Thường'),
(516, 9, 'E9', 'Thường'),
(517, 9, 'E10', 'Thường'),
(518, 10, 'A1', 'Thường'),
(519, 10, 'A2', 'Thường'),
(520, 10, 'A3', 'Thường'),
(521, 10, 'A4', 'Thường'),
(522, 10, 'A5', 'Thường'),
(523, 10, 'A6', 'Thường'),
(524, 10, 'A7', 'Thường'),
(525, 10, 'A8', 'Thường'),
(526, 10, 'A9', 'Thường'),
(527, 10, 'A10', 'Thường'),
(528, 10, 'B1', 'Thường'),
(529, 10, 'B2', 'Thường'),
(530, 10, 'B3', 'Thường'),
(531, 10, 'B4', 'Thường'),
(532, 10, 'B5', 'Thường'),
(533, 10, 'B6', 'Thường'),
(534, 10, 'B7', 'Thường'),
(535, 10, 'B8', 'Thường'),
(536, 10, 'B9', 'Thường'),
(537, 10, 'B10', 'Thường'),
(538, 10, 'C1', 'Thường'),
(539, 10, 'C2', 'Thường'),
(540, 10, 'C3', 'Thường'),
(541, 10, 'C4', 'Thường'),
(542, 10, 'C5', 'Thường'),
(543, 10, 'C6', 'Thường'),
(544, 10, 'C7', 'Thường'),
(545, 10, 'C8', 'Thường'),
(546, 10, 'C9', 'Thường'),
(547, 10, 'C10', 'Thường'),
(548, 10, 'D1', 'Thường'),
(549, 10, 'D2', 'Thường'),
(550, 10, 'D3', 'Thường'),
(551, 10, 'D4', 'Thường'),
(552, 10, 'D5', 'Thường'),
(553, 10, 'D6', 'Thường'),
(554, 10, 'D7', 'Thường'),
(555, 10, 'D8', 'Thường'),
(556, 10, 'D9', 'Thường'),
(557, 10, 'D10', 'Thường'),
(558, 10, 'E1', 'Thường'),
(559, 10, 'E2', 'Thường'),
(560, 10, 'E3', 'Thường'),
(561, 10, 'E4', 'Thường'),
(562, 10, 'E5', 'Thường'),
(563, 10, 'E6', 'Thường'),
(564, 10, 'E7', 'Thường'),
(565, 10, 'E8', 'Thường'),
(566, 10, 'E9', 'Thường'),
(567, 10, 'E10', 'Thường'),
(568, 11, 'A1', 'Thường'),
(569, 11, 'A2', 'Thường'),
(570, 11, 'A3', 'Thường'),
(571, 11, 'A4', 'Thường'),
(572, 11, 'A5', 'Thường'),
(573, 11, 'A6', 'Thường'),
(574, 11, 'A7', 'Thường'),
(575, 11, 'A8', 'Thường'),
(576, 11, 'A9', 'Thường'),
(577, 11, 'A10', 'Thường'),
(578, 11, 'B1', 'Thường'),
(579, 11, 'B2', 'Thường'),
(580, 11, 'B3', 'Thường'),
(581, 11, 'B4', 'Thường'),
(582, 11, 'B5', 'Thường'),
(583, 11, 'B6', 'Thường'),
(584, 11, 'B7', 'Thường'),
(585, 11, 'B8', 'Thường'),
(586, 11, 'B9', 'Thường'),
(587, 11, 'B10', 'Thường'),
(588, 11, 'C1', 'Thường'),
(589, 11, 'C2', 'Thường'),
(590, 11, 'C3', 'Thường'),
(591, 11, 'C4', 'Thường'),
(592, 11, 'C5', 'Thường'),
(593, 11, 'C6', 'Thường'),
(594, 11, 'C7', 'Thường'),
(595, 11, 'C8', 'Thường'),
(596, 11, 'C9', 'Thường'),
(597, 11, 'C10', 'Thường'),
(598, 11, 'D1', 'Thường'),
(599, 11, 'D2', 'Thường'),
(600, 11, 'D3', 'Thường'),
(601, 11, 'D4', 'Thường'),
(602, 11, 'D5', 'Thường'),
(603, 11, 'D6', 'Thường'),
(604, 11, 'D7', 'Thường'),
(605, 11, 'D8', 'Thường'),
(606, 11, 'D9', 'Thường'),
(607, 11, 'D10', 'Thường'),
(608, 11, 'E1', 'Thường'),
(609, 11, 'E2', 'Thường'),
(610, 11, 'E3', 'Thường'),
(611, 11, 'E4', 'Thường'),
(612, 11, 'E5', 'Thường'),
(613, 11, 'E6', 'Thường'),
(614, 11, 'E7', 'Thường'),
(615, 11, 'E8', 'Thường'),
(616, 11, 'E9', 'Thường'),
(617, 11, 'E10', 'Thường'),
(618, 12, 'A1', 'Thường'),
(619, 12, 'A2', 'Thường'),
(620, 12, 'A3', 'Thường'),
(621, 12, 'A4', 'Thường'),
(622, 12, 'A5', 'Thường'),
(623, 12, 'A6', 'Thường'),
(624, 12, 'A7', 'Thường'),
(625, 12, 'A8', 'Thường'),
(626, 12, 'A9', 'Thường'),
(627, 12, 'A10', 'Thường'),
(628, 12, 'B1', 'Thường'),
(629, 12, 'B2', 'Thường'),
(630, 12, 'B3', 'Thường'),
(631, 12, 'B4', 'Thường'),
(632, 12, 'B5', 'Thường'),
(633, 12, 'B6', 'Thường'),
(634, 12, 'B7', 'Thường'),
(635, 12, 'B8', 'Thường'),
(636, 12, 'B9', 'Thường'),
(637, 12, 'B10', 'Thường'),
(638, 12, 'C1', 'Thường'),
(639, 12, 'C2', 'Thường'),
(640, 12, 'C3', 'Thường'),
(641, 12, 'C4', 'Thường'),
(642, 12, 'C5', 'Thường'),
(643, 12, 'C6', 'Thường'),
(644, 12, 'C7', 'Thường'),
(645, 12, 'C8', 'Thường'),
(646, 12, 'C9', 'Thường'),
(647, 12, 'C10', 'Thường'),
(648, 12, 'D1', 'Thường'),
(649, 12, 'D2', 'Thường'),
(650, 12, 'D3', 'Thường'),
(651, 12, 'D4', 'Thường'),
(652, 12, 'D5', 'Thường'),
(653, 12, 'D6', 'Thường'),
(654, 12, 'D7', 'Thường'),
(655, 12, 'D8', 'Thường'),
(656, 12, 'D9', 'Thường'),
(657, 12, 'D10', 'Thường'),
(658, 12, 'E1', 'Thường'),
(659, 12, 'E2', 'Thường'),
(660, 12, 'E3', 'Thường'),
(661, 12, 'E4', 'Thường'),
(662, 12, 'E5', 'Thường'),
(663, 12, 'E6', 'Thường'),
(664, 12, 'E7', 'Thường'),
(665, 12, 'E8', 'Thường'),
(666, 12, 'E9', 'Thường'),
(667, 12, 'E10', 'Thường'),
(668, 13, 'A1', 'Thường'),
(669, 13, 'A2', 'Thường'),
(670, 13, 'A3', 'Thường'),
(671, 13, 'A4', 'Thường'),
(672, 13, 'A5', 'Thường'),
(673, 13, 'A6', 'Thường'),
(674, 13, 'A7', 'Thường'),
(675, 13, 'A8', 'Thường'),
(676, 13, 'A9', 'Thường'),
(677, 13, 'A10', 'Thường'),
(678, 13, 'B1', 'Thường'),
(679, 13, 'B2', 'Thường'),
(680, 13, 'B3', 'Thường'),
(681, 13, 'B4', 'Thường'),
(682, 13, 'B5', 'Thường'),
(683, 13, 'B6', 'Thường'),
(684, 13, 'B7', 'Thường'),
(685, 13, 'B8', 'Thường'),
(686, 13, 'B9', 'Thường'),
(687, 13, 'B10', 'Thường'),
(688, 13, 'C1', 'Thường'),
(689, 13, 'C2', 'Thường'),
(690, 13, 'C3', 'Thường'),
(691, 13, 'C4', 'Thường'),
(692, 13, 'C5', 'Thường'),
(693, 13, 'C6', 'Thường'),
(694, 13, 'C7', 'Thường'),
(695, 13, 'C8', 'Thường'),
(696, 13, 'C9', 'Thường'),
(697, 13, 'C10', 'Thường'),
(698, 13, 'D1', 'Thường'),
(699, 13, 'D2', 'Thường'),
(700, 13, 'D3', 'Thường'),
(701, 13, 'D4', 'Thường'),
(702, 13, 'D5', 'Thường'),
(703, 13, 'D6', 'Thường'),
(704, 13, 'D7', 'Thường'),
(705, 13, 'D8', 'Thường'),
(706, 13, 'D9', 'Thường'),
(707, 13, 'D10', 'Thường'),
(708, 13, 'E1', 'Thường'),
(709, 13, 'E2', 'Thường'),
(710, 13, 'E3', 'Thường'),
(711, 13, 'E4', 'Thường'),
(712, 13, 'E5', 'Thường'),
(713, 13, 'E6', 'Thường'),
(714, 13, 'E7', 'Thường'),
(715, 13, 'E8', 'Thường'),
(716, 13, 'E9', 'Thường'),
(717, 13, 'E10', 'Thường'),
(769, 15, 'A1', 'VIP'),
(770, 15, 'A2', 'VIP'),
(771, 15, 'A3', 'VIP'),
(772, 15, 'A4', 'VIP'),
(773, 15, 'A5', 'VIP'),
(774, 15, 'A6', 'VIP'),
(775, 15, 'A7', 'VIP'),
(776, 15, 'A8', 'VIP'),
(777, 15, 'A9', 'VIP'),
(778, 15, 'A10', 'VIP'),
(779, 15, 'B1', 'VIP'),
(780, 15, 'B2', 'VIP'),
(781, 15, 'B3', 'VIP'),
(782, 15, 'B4', 'VIP'),
(783, 15, 'B5', 'VIP'),
(784, 15, 'B6', 'VIP'),
(785, 15, 'B7', 'VIP'),
(786, 15, 'B8', 'VIP'),
(787, 15, 'B9', 'VIP'),
(788, 15, 'B10', 'VIP'),
(789, 15, 'C1', 'VIP'),
(790, 15, 'C2', 'VIP'),
(791, 15, 'C3', 'VIP'),
(792, 15, 'C4', 'VIP'),
(793, 15, 'C5', 'VIP'),
(794, 15, 'C6', 'VIP'),
(795, 15, 'C7', 'VIP'),
(796, 15, 'C8', 'VIP'),
(797, 15, 'C9', 'VIP'),
(798, 15, 'C10', 'VIP'),
(799, 15, 'D1', 'VIP'),
(800, 15, 'D2', 'VIP'),
(801, 15, 'D3', 'VIP'),
(802, 15, 'D4', 'VIP'),
(803, 15, 'D5', 'VIP'),
(804, 15, 'D6', 'VIP'),
(805, 15, 'D7', 'VIP'),
(806, 15, 'D8', 'VIP'),
(807, 15, 'D9', 'VIP'),
(808, 15, 'D10', 'VIP'),
(809, 15, 'E1', 'VIP'),
(810, 15, 'E2', 'VIP'),
(811, 15, 'E3', 'VIP'),
(812, 15, 'E4', 'VIP'),
(813, 15, 'E5', 'VIP'),
(814, 15, 'E6', 'VIP'),
(815, 15, 'E7', 'VIP'),
(816, 15, 'E8', 'VIP'),
(817, 15, 'E9', 'VIP'),
(818, 15, 'E10', 'VIP'),
(1119, 1, 'A1', 'VIP'),
(1120, 1, 'A2', 'VIP'),
(1121, 1, 'A3', 'VIP'),
(1122, 1, 'A4', 'VIP'),
(1123, 1, 'A5', 'VIP'),
(1124, 1, 'A6', 'VIP'),
(1125, 1, 'A7', 'VIP'),
(1126, 1, 'A8', 'VIP'),
(1127, 1, 'A9', 'VIP'),
(1128, 1, 'A10', 'VIP'),
(1129, 1, 'B1', 'Thường'),
(1130, 1, 'B2', 'Thường'),
(1131, 1, 'B3', 'Thường'),
(1132, 1, 'B4', 'Thường'),
(1133, 1, 'B5', 'Thường'),
(1134, 1, 'B6', 'Thường'),
(1135, 1, 'B7', 'Thường'),
(1136, 1, 'B8', 'Thường'),
(1137, 1, 'B9', 'Thường'),
(1138, 1, 'B10', 'Thường'),
(1139, 1, 'C1', 'Thường'),
(1140, 1, 'C2', 'Thường'),
(1141, 1, 'C3', 'Thường'),
(1142, 1, 'C4', 'Thường'),
(1143, 1, 'C5', 'Thường'),
(1144, 1, 'C6', 'Thường'),
(1145, 1, 'C7', 'Thường'),
(1146, 1, 'C8', 'Thường'),
(1147, 1, 'C9', 'Thường'),
(1148, 1, 'C10', 'Thường'),
(1149, 1, 'D1', 'Thường'),
(1150, 1, 'D2', 'Thường'),
(1151, 1, 'D3', 'Thường'),
(1152, 1, 'D4', 'Thường'),
(1153, 1, 'D5', 'Thường'),
(1154, 1, 'D6', 'Thường'),
(1155, 1, 'D7', 'Thường'),
(1156, 1, 'D8', 'Thường'),
(1157, 1, 'D9', 'Thường'),
(1158, 1, 'D10', 'Thường'),
(1159, 1, 'E1', 'Thường'),
(1160, 1, 'E2', 'Thường'),
(1161, 1, 'E3', 'Thường'),
(1162, 1, 'E4', 'Thường'),
(1163, 1, 'E5', 'Thường'),
(1164, 1, 'E6', 'Thường'),
(1165, 1, 'E7', 'Thường'),
(1166, 1, 'E8', 'Thường'),
(1167, 1, 'E9', 'Thường'),
(1168, 1, 'E10', 'Thường');

-- --------------------------------------------------------

--
-- Table structure for table `hoadon`
--

CREATE TABLE `hoadon` (
  `mahd` int NOT NULL,
  `tendn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngaydat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tongtien` decimal(12,0) NOT NULL DEFAULT '0',
  `phuongthuctt` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Tiền mặt',
  `trangthai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Đã thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nguoidung`
--

CREATE TABLE `nguoidung` (
  `tendn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matkhau` int NOT NULL,
  `quyen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangthai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hoạt động',
  `ghichu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoidung`
--

INSERT INTO `nguoidung` (`tendn`, `matkhau`, `quyen`, `trangthai`, `ghichu`) VALUES
('dan', 123, 'user', 'Hoạt động', 'hihi'),
('dinh', 123, 'admin', 'Hoạt động', 'haha'),
('khoa', 123, 'admin', 'Hoạt động', 'uuuu'),
('nha', 123, 'admin', 'Hoạt động', ''),
('thuyen', 123, 'admin', 'Hoạt động', 'hehe');

-- --------------------------------------------------------

--
-- Table structure for table `phim`
--

CREATE TABLE `phim` (
  `maphim` int NOT NULL,
  `tenphim` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `theloai` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinh` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `daodien` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dienvien` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thoiluong` int DEFAULT '0',
  `ngonngu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kiemduyet` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trailer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mota` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trangthai` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Đang chiếu',
  `madm` int NOT NULL,
  `luotxem` int NOT NULL DEFAULT '0',
  `giave` decimal(10,0) NOT NULL DEFAULT '80000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phim`
--

INSERT INTO `phim` (`maphim`, `tenphim`, `theloai`, `hinh`, `daodien`, `dienvien`, `thoiluong`, `ngonngu`, `kiemduyet`, `trailer`, `mota`, `trangthai`, `madm`, `luotxem`, `giave`) VALUES
(1, 'Đếm ngày xa mẹ', 'Tâm lý / Tình cảm', 'movie_69f334c536495.jpg', 'Choi Ik-hwan', 'Chae Ro-hee, Kim Bo-ra', 115, 'Tiếng Hàn - Phụ đề Tiếng Việt', 'P', 'https://www.youtube.com/watch?v=Dp-3zxsfWbw', 'Đếm Ngày Xa Mẹ xoay quanh cặp mẹ con Eun-sil...', 'Đang chiếu', 2, 15000, 80000),
(2, 'Quỷ nhập tràng 2', 'Kinh dị / Giật gân', 'movie_69f334ba11bfb.jpg', 'Đang cập nhật', 'Đang cập nhật', 90, 'Tiếng Thái - Phụ đề Tiếng Việt', 'T18', 'https://www.youtube.com/watch?v=zO18gS2BDfw', 'Quỷ Nhập Tràng 2 thuộc thể loại kinh dị...', 'Đang chiếu', 2, 300000, 90000),
(3, 'Thỏ ơi', 'Hài / Tâm lý', 'ThoOi.jpg', 'Đang cập nhật', 'Đang cập nhật', 100, 'Tiếng Việt', 'T18', 'https://www.youtube.com/watch?v=XMv1Zhj5TQg', 'Phim “Thỏ ơi!!” dự kiến công chiếu...', 'Đang chiếu', 2, 5000, 85000),
(5, 'Conan', 'Hoạt hình', 'Conan.jpg', 'Yuzuru Tachikawa', 'Minami Takayama, Wakana Yamazaki', 110, 'Tiếng Nhật - Phụ đề Tiếng Việt', 'K', 'https://www.youtube.com/watch?v=HSow7Ep6l_4', 'Thám tử lừng danh Conan...', 'Đang chiếu', 1, 85000, 70000),
(7, 'Xứ sở số nguyên tố', 'Hoạt hình', 'XuSoCacSoNT.jpg', 'Peter Sohn', 'Leah Lewis, Mamoudou Athie', 101, 'Tiếng Anh - Phụ đề Tiếng Việt', 'P', 'https://www.youtube.com/watch?v=maq_YK88Xnw', 'Xứ sở các nguyên tố...', 'Đang chiếu', 1, 2000, 90000),
(8, 'Avatar 2', 'Hoạt hình', 'Avatar2.jpg', 'James Cameron', 'Sam Worthington, Zoe Saldana', 192, 'Tiếng Anh - Phụ đề Tiếng Việt', 'T13', 'https://www.youtube.com/watch?v=d9MyW72ELq0', 'Avatar 2: Dòng Chảy Của Nước...', 'Đang chiếu', 1, 500000, 150000),
(9, 'Luca', 'Hoạt hình', 'Luca.jpg', 'Enrico Casarosa', 'Jacob Tremblay, Jack Dylan Grazer', 95, 'Tiếng Anh - Phụ đề Tiếng Việt', 'P', 'https://www.youtube.com/watch?v=mYfJxlgR2jw', 'Mùa hè của Luca...', 'Đang chiếu', 1, 9000, 75000),
(10, 'Phí Phông', 'Kinh dị / Kịch tính', 'movie_69f0290113f1a.jpg', 'Đang cập nhật', 'Đang cập nhật', 105, 'Tiếng Thái - Phụ đề Tiếng Việt', 'T16', 'https://www.youtube.com/watch?v=AFkKZXbzHdI&t=2s', '“Phí Phông” là hình tượng ma/ quỷ trong truyền thuyết dân gian, đặc biệt ở một số vùng núi, được miêu tả giống ma cà rồng', 'Sắp chiếu', 5, 0, 80000),
(11, 'Lớp học ám sát', 'Hài hước ', 'movie_69f0e0e4f3149.jpg', 'Seiji Kishi', 'Jun Fukuyama, Mai Fuchigami', 110, 'Tiếng Nhật - Phụ đề Tiếng Việt', 'T13', 'https://www.youtube.com/watch?v=ODEAL3PnbMA', 'Một ngày nọ, mặt trăng bất thình lình bị một sinh vật siêu việt thổi bay 70%. Sinh vật ấy đã tự nhận mình là thủ phạm, và tuyên bố sẽ thổi ...', 'Đang chiếu', 1, 0, 80000);

-- --------------------------------------------------------

--
-- Table structure for table `phongchieu`
--

CREATE TABLE `phongchieu` (
  `maphong` int NOT NULL,
  `marap` int NOT NULL DEFAULT '1',
  `tenphong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tongghe` int NOT NULL,
  `tinhtrang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phongchieu`
--

INSERT INTO `phongchieu` (`maphong`, `marap`, `tenphong`, `tongghe`, `tinhtrang`) VALUES
(1, 1, 'Phòng 1 - 2D', 50, 'Hoạt động'),
(2, 1, 'Phòng 2 - 3D', 50, 'Hoạt động'),
(3, 2, 'Phòng 3 - VIP', 50, 'Hoạt động'),
(4, 3, 'Phòng 1 - IMAX', 50, 'Hoạt động'),
(5, 3, 'Phòng 2 - 4DX', 50, 'Hoạt động'),
(6, 4, 'Phòng 1 - STARMAX', 50, 'Hoạt động'),
(7, 5, 'Phòng 3 - 2D', 50, 'Bảo trì'),
(8, 6, 'Phòng C1 - Thường', 50, 'Hoạt động'),
(9, 7, 'Phòng 2 - VIP', 50, 'Hoạt động'),
(10, 8, 'Phòng 1 - Couple', 50, 'Hoạt động'),
(11, 9, 'Phòng Gold Class', 50, 'Hoạt động'),
(12, 10, 'Phòng 4 - 3D', 50, 'Hoạt động'),
(13, 11, 'Phòng 5 - 2D', 50, 'Hoạt động'),
(14, 13, 'Phòng 01', 50, 'Hoạt động'),
(15, 1, 'Phòng 01', 50, 'Hoạt động');

-- --------------------------------------------------------

--
-- Table structure for table `rapchieu`
--

CREATE TABLE `rapchieu` (
  `marap` int NOT NULL,
  `tenrap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `diachi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hotline` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rapchieu`
--

INSERT INTO `rapchieu` (`marap`, `tenrap`, `diachi`, `hotline`) VALUES
(1, 'Cinema Center', '123 Nguyễn Văn Cừ, Quận 5, TP.HCM', '19001234'),
(2, 'Cinema Lotte', '456 Lê Lợi, Quận 1, TP.HCM', '19005678'),
(3, 'CGV Vincom Đồng Khởi', '72 Lê Thánh Tôn, Quận 1, TP.HCM', '19006017'),
(4, 'BHD Star Thảo Điền', '159 Xa Lộ Hà Nội, Quận 2, TP.HCM', '19002099'),
(5, 'Galaxy Nguyễn Du', '116 Nguyễn Du, Quận 1, TP.HCM', '19002224'),
(6, 'Cinestar Quốc Thanh', '271 Nguyễn Trãi, Quận 1, TP.HCM', '19007300'),
(7, 'Mega GS Cao Thắng', '19 Cao Thắng, Quận 3, TP.HCM', '19005555'),
(8, 'Dcine Bến Thành', '6 Mạc Đĩnh Chi, Quận 1, TP.HCM', '19006666'),
(9, 'CGV Sư Vạn Hạnh', '11 Sư Vạn Hạnh, Quận 10, TP.HCM', '19006017'),
(10, 'BHD Star Quang Trung', '190 Quang Trung, Gò Vấp, TP.HCM', '19002099'),
(11, 'Galaxy Tân Bình', '246 Nguyễn Hồng Đào, Tân Bình, TP.HCM', '19002224'),
(12, 'Lotte Cinema Nam Sài Gòn', '469 Nguyễn Hữu Thọ, Quận 7, TP.HCM', '19005678'),
(13, 'CGV Thủ đức', '123 Thủ đức', '19002054');

-- --------------------------------------------------------

--
-- Table structure for table `suatchieu`
--

CREATE TABLE `suatchieu` (
  `masuat` int NOT NULL,
  `maphim` int NOT NULL,
  `maphong` int NOT NULL,
  `ngaychieu` date NOT NULL,
  `giochieu` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suatchieu`
--

INSERT INTO `suatchieu` (`masuat`, `maphim`, `maphong`, `ngaychieu`, `giochieu`) VALUES
(1, 1, 1, '2026-03-28', '18:00:00'),
(2, 2, 2, '2026-03-28', '20:30:00'),
(3, 5, 1, '2026-03-29', '09:00:00'),
(4, 3, 3, '2026-03-30', '19:00:00'),
(6, 7, 5, '2026-04-01', '09:30:00'),
(7, 8, 6, '2026-04-01', '14:00:00'),
(8, 9, 7, '2026-04-02', '16:45:00'),
(9, 1, 8, '2026-04-02', '18:30:00'),
(10, 2, 9, '2026-04-03', '20:00:00'),
(11, 5, 10, '2026-04-03', '10:00:00'),
(12, 8, 11, '2026-04-30', '19:30:00'),
(13, 9, 12, '2026-04-04', '15:00:00'),
(14, 11, 12, '2026-05-01', '10:33:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`mabl`),
  ADD KEY `fk_binhluan_nguoidung` (`tendn`),
  ADD KEY `fk_binhluan_phim` (`maphim`);

--
-- Indexes for table `cauhinh`
--
ALTER TABLE `cauhinh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_cauhinh_tukhoa` (`tukhoa`);

--
-- Indexes for table `chitietve`
--
ALTER TABLE `chitietve`
  ADD PRIMARY KEY (`mahd`,`masuat`,`maghe`),
  ADD KEY `fk_chitietve_suat` (`masuat`),
  ADD KEY `fk_chitietve_ghe` (`maghe`);

--
-- Indexes for table `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`madm`);

--
-- Indexes for table `ghengoi`
--
ALTER TABLE `ghengoi`
  ADD PRIMARY KEY (`maghe`),
  ADD KEY `fk_ghengoi_phong` (`maphong`);

--
-- Indexes for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`mahd`),
  ADD KEY `fk_hoadon_nguoidung` (`tendn`);

--
-- Indexes for table `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`tendn`);

--
-- Indexes for table `phim`
--
ALTER TABLE `phim`
  ADD PRIMARY KEY (`maphim`),
  ADD KEY `fk_phim_danhmuc` (`madm`);

--
-- Indexes for table `phongchieu`
--
ALTER TABLE `phongchieu`
  ADD PRIMARY KEY (`maphong`),
  ADD KEY `fk_phongchieu_rap` (`marap`);

--
-- Indexes for table `rapchieu`
--
ALTER TABLE `rapchieu`
  ADD PRIMARY KEY (`marap`);

--
-- Indexes for table `suatchieu`
--
ALTER TABLE `suatchieu`
  ADD PRIMARY KEY (`masuat`),
  ADD KEY `fk_suatchieu_phim` (`maphim`),
  ADD KEY `fk_suatchieu_phong` (`maphong`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `mabl` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cauhinh`
--
ALTER TABLE `cauhinh`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `madm` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `ghengoi`
--
ALTER TABLE `ghengoi`
  MODIFY `maghe` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1169;

--
-- AUTO_INCREMENT for table `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `mahd` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `phim`
--
ALTER TABLE `phim`
  MODIFY `maphim` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `phongchieu`
--
ALTER TABLE `phongchieu`
  MODIFY `maphong` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rapchieu`
--
ALTER TABLE `rapchieu`
  MODIFY `marap` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `suatchieu`
--
ALTER TABLE `suatchieu`
  MODIFY `masuat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `binhluan`
--
ALTER TABLE `binhluan`
  ADD CONSTRAINT `fk_binhluan_nguoidung` FOREIGN KEY (`tendn`) REFERENCES `nguoidung` (`tendn`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_binhluan_phim` FOREIGN KEY (`maphim`) REFERENCES `phim` (`maphim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chitietve`
--
ALTER TABLE `chitietve`
  ADD CONSTRAINT `fk_chitietve_ghe` FOREIGN KEY (`maghe`) REFERENCES `ghengoi` (`maghe`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chitietve_hd` FOREIGN KEY (`mahd`) REFERENCES `hoadon` (`mahd`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chitietve_suat` FOREIGN KEY (`masuat`) REFERENCES `suatchieu` (`masuat`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `ghengoi`
--
ALTER TABLE `ghengoi`
  ADD CONSTRAINT `fk_ghengoi_phong` FOREIGN KEY (`maphong`) REFERENCES `phongchieu` (`maphong`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `fk_hoadon_nguoidung` FOREIGN KEY (`tendn`) REFERENCES `nguoidung` (`tendn`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `phim`
--
ALTER TABLE `phim`
  ADD CONSTRAINT `fk_phim_danhmuc` FOREIGN KEY (`madm`) REFERENCES `danhmuc` (`madm`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `phongchieu`
--
ALTER TABLE `phongchieu`
  ADD CONSTRAINT `fk_phongchieu_rap` FOREIGN KEY (`marap`) REFERENCES `rapchieu` (`marap`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `suatchieu`
--
ALTER TABLE `suatchieu`
  ADD CONSTRAINT `fk_suatchieu_phim` FOREIGN KEY (`maphim`) REFERENCES `phim` (`maphim`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_suatchieu_phong` FOREIGN KEY (`maphong`) REFERENCES `phongchieu` (`maphong`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
