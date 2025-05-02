-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 11:36 AM
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
-- Database: `recallit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin01', 'admin01@gmail.com', '$2y$10$0wHJ91yJhrvcH4Bi97pWKuOBTZJ7q6ah7CF3w5WSzcH2Ib.avRfSS');

-- --------------------------------------------------------

--
-- Table structure for table `flashcards`
--

CREATE TABLE `flashcards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flashcards`
--

INSERT INTO `flashcards` (`id`, `user_id`, `subject`, `notes`, `difficulty`, `question`, `answer`, `image_path`, `pdf_path`, `created_at`, `subject_id`) VALUES
(1, 29, 'web', '', 'easy', 'what is php', 'server side scripting language', 'uploads/images/6807edd6290ef.png', '', '2025-04-18 19:46:39', 5),
(2, 30, 'Biology', '', 'easy', 'what is cell', 'cell is the smallest functional unit in human body ', 'uploads/images/1745006893-6802b12dd6768.png', 'uploads/files/68138b6baf3b4.pptx', '2025-04-18 20:08:13', 10),
(3, 10, 'Biology', 'photosynthesis', 'easy', 'what is photosynthesis', 'Photosynthesis is the process for making food in the plants', 'uploads/images/68109f8a7cb00.jpeg', 'uploads/files/68138a2b3b3c4.pptx', '2025-04-29 09:43:30', 10),
(11, 10, 'Maths', '', 'easy', '2+2', '4', '', '', '2025-04-30 16:30:43', 20),
(13, 10, 'R', '', 'medium', 'what is R', 'R is a programming language', '', '', '2025-04-30 16:37:32', 27),
(14, 10, 'R', '', 'easy', 'how to plot graph in R', 'use plot() function', 'uploads/images/681413e64eb64.png', '', '2025-04-30 16:38:21', 27),
(15, 30, 'web', '', 'easy', 'explain HTML', 'HyperTextMarkupLanguage', '', '', '2025-05-01 13:04:49', 5),
(16, 30, 'web', '', 'easy', 'what is php', 'For backup', '', '', '2025-05-01 13:06:32', 5),
(17, 30, 'web', '', 'medium', 'CSS', 'Cascading style sheets', '', '', '2025-05-01 13:07:06', 5),
(18, 30, 'web', '', 'medium', 'Database', 'Store Data', 'uploads/images/681417a66c10d.jpeg', '', '2025-05-01 13:07:27', 5),
(19, 30, 'web', '', 'medium', 'types of database', 'Mysql,Nomysql', 'uploads/images/68138fb9ebdc2.png', 'uploads/files/6813903b7ad72.pptx', '2025-05-01 13:08:09', 5),
(20, 30, 'web', '', 'medium', 'why use css', 'For styling', 'uploads/images/681490dd70aef.png', '', '2025-05-01 13:09:11', 5),
(25, 10, 'Biology', NULL, NULL, 'what is photosynthesis', 'Photosynthesis is the process for making food in the plants', 'uploads/images/68109f8a7cb00.jpeg', 'uploads/files/68138a2b3b3c4.pptx', '2025-05-01 18:21:17', 31),
(26, 10, 'web', NULL, NULL, 'Database', 'Store Data', '', '', '2025-05-01 18:35:36', 32),
(27, 10, 'web', NULL, NULL, 'what is php', 'For backup', 'uploads/images/681406c986962.png', '', '2025-05-01 23:41:38', 32),
(28, 10, 'web', NULL, NULL, 'CSS', 'Cascading style sheets', 'uploads/images/68140703782ad.jpeg', '', '2025-05-01 23:42:33', 32),
(29, 30, 'R', NULL, NULL, 'how to plot graph in R', 'use plot() function', '', '', '2025-05-02 00:10:43', 6),
(30, 10, 'Biology', NULL, NULL, 'what is cell', 'cell is the smallest functional unit in human body ', 'uploads/images/681410c22bc13.png', 'uploads/files/68138b6baf3b4.pptx', '2025-05-02 00:24:13', 31),
(31, 10, 'R', NULL, NULL, 'how to plot graph in R', 'use plot() function', '', '', '2025-05-02 00:37:18', 27),
(32, 10, 'web', NULL, NULL, 'explain HTML', 'HyperTextMarkupLanguage', 'uploads/images/68141498a4801.png', '', '2025-05-02 00:40:35', 32),
(33, 10, 'web', NULL, NULL, 'Database', 'Store Data', 'uploads/images/681417a66c10d.jpeg', 'uploads/files/681417d26dafd.pdf', '2025-05-02 00:54:21', 32),
(34, 10, 'web', NULL, NULL, 'what is php', 'For backup', '', '', '2025-05-02 00:57:35', 32),
(35, 30, 'hindi', '', 'easy', 'a e i o u ', 'd e f', '', '', '2025-05-02 01:14:26', 33),
(36, 10, 'hindi', NULL, NULL, 'a b e', 'd e f', 'uploads/images/6814217c5cd8f.png', '', '2025-05-02 01:14:59', 34),
(37, 10, 'hindi', NULL, NULL, 'a b e', 'd e f', '', '', '2025-05-02 01:35:28', 34),
(38, 10, 'hindi', NULL, NULL, 'a e i o u ', 'd e f', 'uploads/images/6814244a526cc.png', '', '2025-05-02 01:47:41', 34),
(39, 30, 'English', '', 'medium', 'characters', 'characters', '', '', '2025-05-02 01:53:15', 35),
(40, 10, 'English', NULL, NULL, 'characters', 'characters', '', '', '2025-05-02 01:53:37', 21),
(41, 30, 'xxxxxx', '', 'medium', '111111', '22222234', '', '', '2025-05-02 02:04:44', 36),
(49, 10, 'xxxxxx', NULL, NULL, '111111', '222222345', NULL, NULL, '2025-05-02 02:40:33', 44),
(50, 30, 'stat', '', 'medium', 'aaa', 'bbb', '', '', '2025-05-02 03:45:02', 45),
(51, 10, 'stat', NULL, NULL, 'aaa', 'bbb', NULL, NULL, '2025-05-02 03:46:16', 46),
(52, 10, 'ffff', '', 'medium', 'aaaaa', 'aaaaaa', '', '', '2025-05-02 03:46:53', 47),
(53, 30, 'ffff', NULL, NULL, 'aaaaa', 'aaaaaa', 'uploads/images/6814484fceb9d.jpeg', NULL, '2025-05-02 03:47:11', 48),
(54, 10, 'bbbbbbbbb', '', 'medium', 'mmmmmm', 'mmmmmmmmmmm', '', '', '2025-05-02 04:36:55', 49),
(55, 30, 'bbbbbbbbb', NULL, NULL, 'mmmmmm', 'mmmmmmmmmmm', NULL, NULL, '2025-05-02 04:38:18', 50),
(56, 30, 'dbms', '', 'medium', 'aaaa', 'bbbb', '', '', '2025-05-02 05:51:32', 11),
(57, 30, 'stat', '', 'medium', 'graphs', 'graphs', '', '', '2025-05-02 06:33:05', 45),
(58, 10, 'stat', NULL, NULL, 'graphs', 'graphs', NULL, NULL, '2025-05-02 06:33:20', 46),
(59, 30, 'stat', '', 'medium', 'plouy', 'plouy', '', '', '2025-05-02 06:40:21', 45),
(60, 10, 'stat', NULL, NULL, 'plouy', 'plouy', NULL, NULL, '2025-05-02 06:40:41', 46),
(61, 10, 'Maths', '', 'medium', '3+2', '5', '', '', '2025-05-02 07:20:36', 20),
(62, 30, 'Maths', '', 'medium', '4+4', '9', '', '', '2025-05-02 07:27:05', 51),
(63, 10, 'Maths', '', 'medium', '5+5', '10', '', '', '2025-05-02 07:28:26', 20),
(64, 30, 'Maths', NULL, NULL, '5+5', '10', NULL, NULL, '2025-05-02 07:28:52', 51),
(65, 10, 'Maths', NULL, NULL, '4+4', '8', NULL, NULL, '2025-05-02 07:30:35', 20),
(66, 10, 'Maths', NULL, NULL, '5+5', '10', NULL, NULL, '2025-05-02 07:35:53', 20),
(67, 30, 'Maths', '', 'medium', '7+7', '14', '', '', '2025-05-02 07:36:29', 51),
(68, 10, 'Maths', NULL, NULL, '7+7', '15', NULL, NULL, '2025-05-02 07:36:51', 20),
(69, 30, 'Maths', '', 'medium', '8+8', '16', '', '', '2025-05-02 07:52:05', 51),
(70, 10, 'Maths', NULL, NULL, '8+8', '16', NULL, NULL, '2025-05-02 07:52:26', 20),
(71, 30, 'Maths', '', 'medium', '10+10', '20', '', '', '2025-05-02 08:03:10', 51),
(72, 10, 'Maths', NULL, NULL, '10+10', '20', NULL, NULL, '2025-05-02 08:03:33', 20),
(73, 30, 'Maths', '', 'medium', '1+1', '2', '', '', '2025-05-02 08:09:47', 51),
(74, 10, 'Maths', NULL, NULL, '1+1', '2', NULL, NULL, '2025-05-02 08:10:09', 20),
(75, 30, 'Maths', '', 'medium', '6+6', '12', '', '', '2025-05-02 08:21:50', 51),
(76, 10, 'Maths', NULL, NULL, '6+6', '12', NULL, NULL, '2025-05-02 08:22:15', 20),
(77, 30, 'econo', '', 'medium', 'abcd', 'efgh', '', '', '2025-05-02 08:25:29', 52),
(78, 10, 'econo', NULL, NULL, 'abcd', 'efgh', NULL, NULL, '2025-05-02 08:25:51', 53),
(79, 10, 'java', '', 'medium', 'oops', 'oops', '', '', '2025-05-02 08:27:24', 54),
(80, 10, 'bbbbbbbbb', NULL, NULL, 'mmmmmm', 'mmmmmmmmmmm', NULL, NULL, '2025-05-02 09:31:59', 49),
(81, 30, 'docker', '', 'medium', 'abc', 'def', '', '', '2025-05-02 09:34:29', 56),
(82, 10, 'docker', NULL, NULL, 'abc', 'def', NULL, NULL, '2025-05-02 09:34:53', 57);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `sender_id` varchar(10) NOT NULL,
  `receiver_id` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `msg_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `msg_status`) VALUES
(131, 'R001', '30', 'hello', '2025-04-28 11:35:36', 0),
(132, '30', 'R001', 'Hi', '2025-04-28 15:05:53', 0),
(133, 'R001', '30', 'how are you..?', '2025-04-28 11:36:18', 0),
(134, 'R001', '30', 'hai', '2025-04-28 11:38:36', 0),
(135, '30', 'R001', 'hello', '2025-04-28 15:08:56', 0),
(136, 'R001', '30', 'hello', '2025-04-28 11:39:13', 0),
(137, '30', 'R001', 'hello', '2025-04-28 15:16:27', 0),
(138, 'R001', '30', 'bye', '2025-04-28 11:46:39', 0),
(139, '30', 'R001', 'hello', '2025-04-28 15:16:51', 0),
(140, '30', 'R001', 'hello', '2025-04-28 15:17:14', 0),
(141, '30', 'R001', 'hello', '2025-04-28 15:21:09', 0),
(142, '30', 'R001', 'hi', '2025-04-28 15:21:23', 0),
(143, 'R001', '30', 'ji', '2025-04-28 11:51:32', 0),
(144, 'R001', '30', 'hai csk', '2025-04-28 11:52:11', 0),
(145, '30', 'R001', 'jai csk', '2025-04-28 15:22:20', 0),
(146, 'R001', '30', 'ee sala no cup', '2025-04-28 11:52:30', 0),
(147, '10', '30', 'hi', '2025-04-29 09:48:36', 0),
(148, '30', '10', 'hello', '2025-04-29 09:48:48', 0),
(149, '10', '30', 'how are you', '2025-04-29 09:48:59', 0),
(150, '30', '10', 'im fine', '2025-04-29 09:49:06', 0),
(151, 'R007', '10', 'Hi', '2025-04-29 06:21:25', 0),
(152, '10', 'R007', 'hello', '2025-04-29 09:51:33', 0),
(153, 'R007', '10', 'Hi', '2025-04-29 06:21:38', 0),
(154, 'R007', '10', 'what do you want', '2025-04-29 06:22:00', 0),
(155, '30', 'R001', 'hi', '2025-05-01 15:23:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `flashcard_id` int(11) NOT NULL,
  `status` enum('Unread','Read') DEFAULT 'Unread',
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `flashcard_id`, `status`, `read_at`, `created_at`) VALUES
(1, 30, 'You\'ve received a flashcard from bhavya', 3, 'Read', NULL, '2025-05-01 17:41:36'),
(2, 30, 'You\'ve received a flashcard from bhavya', 11, 'Read', NULL, '2025-05-01 17:43:30'),
(3, 29, 'You\'ve received a flashcard from Gowtam', 2, 'Unread', NULL, '2025-05-01 17:47:33'),
(4, 10, 'You\'ve received a shared flashcard', 3, 'Read', NULL, '2025-05-01 17:52:34'),
(5, 10, 'You\'ve received a shared flashcard from Gowtam', 19, 'Read', NULL, '2025-05-01 18:25:41'),
(6, 10, 'You\'ve received a shared flashcard from Gowtam', 18, 'Read', NULL, '2025-05-01 18:30:06'),
(7, 30, 'You\'ve received a shared flashcard from bhavya', 25, 'Read', NULL, '2025-05-01 18:38:08'),
(8, 30, 'You\'ve received a shared flashcard from bhavya', 14, 'Read', NULL, '2025-05-01 18:39:42'),
(9, 10, 'You\'ve received a shared flashcard from Gowtam', 16, 'Read', NULL, '2025-05-01 23:41:03'),
(10, 10, 'You\'ve received a shared flashcard from Gowtam', 17, 'Read', NULL, '2025-05-01 23:41:11'),
(11, 10, 'You\'ve received a shared flashcard from Gowtam', 2, 'Read', NULL, '2025-05-02 00:23:47'),
(12, 10, 'You\'ve received a shared flashcard from Gowtam', 29, 'Read', NULL, '2025-05-02 00:37:05'),
(13, 10, 'You\'ve received a shared flashcard from Gowtam', 15, 'Read', NULL, '2025-05-02 00:40:19'),
(14, 10, 'You\'ve received a shared flashcard from Gowtam', 18, 'Read', NULL, '2025-05-02 00:54:08'),
(15, 10, 'You\'ve received a shared flashcard from Gowtam', 16, 'Read', NULL, '2025-05-02 00:57:18'),
(16, 10, 'You\'ve received a shared flashcard from Gowtam (Edit permissions)', 35, 'Read', NULL, '2025-05-02 01:14:37'),
(17, 30, 'Your shared flashcard was updated by ', 35, 'Unread', NULL, '2025-05-02 01:34:46'),
(18, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 35, 'Read', NULL, '2025-05-02 01:35:15'),
(19, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 35, 'Read', NULL, '2025-05-02 01:47:27'),
(20, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 39, 'Read', NULL, '2025-05-02 01:53:20'),
(21, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 41, 'Read', NULL, '2025-05-02 02:22:28'),
(22, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 50, 'Read', NULL, '2025-05-02 03:45:58'),
(23, 30, 'You\'ve received a shared flashcard from bhavya (View permissions)', 52, 'Read', NULL, '2025-05-02 03:47:02'),
(24, 30, 'You\'ve received a shared flashcard from bhavya (View permissions)', 54, 'Read', NULL, '2025-05-02 04:38:05'),
(25, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 56, 'Read', '2025-05-02 11:32:55', '2025-05-02 05:51:49'),
(26, 30, ' accepted your shared flashcard: aaaa...', 56, 'Unread', NULL, '2025-05-02 06:02:55'),
(27, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 57, 'Read', NULL, '2025-05-02 06:33:14'),
(28, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 59, 'Read', NULL, '2025-05-02 06:40:30'),
(29, 30, 'You\'ve received a shared flashcard from bhavya (View permissions)', 61, 'Read', NULL, '2025-05-02 07:27:45'),
(30, 30, 'You\'ve received a shared flashcard from bhavya (View permissions)', 63, 'Read', NULL, '2025-05-02 07:28:35'),
(31, 10, 'You\'ve received a shared flashcard from Gowtam (Edit permissions)', 62, 'Read', NULL, '2025-05-02 07:30:26'),
(32, 10, 'You\'ve received a shared flashcard from Gowtam (Edit permissions)', 64, 'Read', NULL, '2025-05-02 07:35:43'),
(33, 10, 'You\'ve received a shared flashcard from Gowtam (Edit permissions)', 67, 'Read', NULL, '2025-05-02 07:36:45'),
(34, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 69, 'Read', NULL, '2025-05-02 07:52:13'),
(35, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 71, 'Read', NULL, '2025-05-02 08:03:22'),
(36, 10, 'You\'ve received a shared flashcard from Gowtam (Edit permissions)', 73, 'Read', NULL, '2025-05-02 08:09:59'),
(37, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 75, 'Read', NULL, '2025-05-02 08:22:04'),
(38, 10, 'You\'ve received a shared flashcard from Gowtam (View permissions)', 77, 'Read', NULL, '2025-05-02 08:25:38'),
(39, 30, 'You\'ve received a shared flashcard from bhavya (Edit permissions)', 79, 'Read', NULL, '2025-05-02 08:27:32'),
(40, 10, 'You\'ve received a shared flashcard from Gowtam1 (View permissions)', 55, 'Read', NULL, '2025-05-02 09:31:46'),
(41, 10, 'You\'ve received a shared flashcard from Gowtam1 (View permissions)', 81, 'Read', NULL, '2025-05-02 09:34:40');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `user_id`, `score`, `created_at`, `subject_id`) VALUES
(15, 10, 45, '2025-04-30 17:44:07', 20),
(16, 10, 50, '2025-04-30 17:44:29', 20),
(17, 10, 50, '2025-04-30 18:02:52', 21),
(18, 10, 35, '2025-04-30 18:04:30', 20),
(19, 30, 40, '2025-05-01 12:51:41', 10),
(20, 30, 50, '2025-05-01 12:57:11', 10),
(21, 30, 50, '2025-05-01 13:05:57', 5),
(22, 30, 40, '2025-05-01 13:10:26', 5),
(23, 30, 50, '2025-05-01 13:11:44', 10),
(24, 30, 10, '2025-05-01 13:11:55', 10),
(25, 30, 50, '2025-05-01 13:22:40', 10),
(26, 10, 50, '2025-05-01 13:33:10', 21),
(27, 10, 45, '2025-05-01 13:34:01', 20),
(28, 10, 10, '2025-05-01 13:37:02', 21),
(29, 10, 20, '2025-05-01 14:09:26', 21),
(30, 10, 20, '2025-05-01 14:39:16', 27),
(31, 10, 30, '2025-05-01 14:48:01', 20),
(32, 30, 32, '2025-05-02 03:43:50', 5);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` varchar(255) NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `report_desc` text NOT NULL,
  `report_created` varchar(255) NOT NULL,
  `report_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`report_id`, `report_title`, `report_desc`, `report_created`, `report_status`) VALUES
('R001', 'Change theme', 'i need to change the theme', '30', 1),
('R002', 'theme', 'i need to change my theme', '10', 1),
('R003', 'theme', 'chnage themee', '10', 1),
('R004', '2 or more flashcard', 'flashcard', '30', 0),
('R005', '2 or more flashcard', 'flashcard', '30', 1),
('R006', '2 or more flashcard', 'flashcard', '30', 0),
('R007', 'Change theme i want it blue', 'blue', '10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shared_flashcards`
--

CREATE TABLE `shared_flashcards` (
  `share_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `flashcard_id` int(11) NOT NULL,
  `recipient_flashcard_id` int(11) DEFAULT NULL,
  `permissions` enum('view','edit') NOT NULL,
  `share_token` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Accepted') DEFAULT 'Pending',
  `accepted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shared_flashcards`
--

INSERT INTO `shared_flashcards` (`share_id`, `owner_id`, `recipient_id`, `flashcard_id`, `recipient_flashcard_id`, `permissions`, `share_token`, `status`, `accepted_at`) VALUES
(5, 10, 29, 3, NULL, 'view', 'db1f7e93c003210f752adfd0aa3fe970', 'Pending', NULL),
(6, 10, 30, 3, NULL, 'view', '84e73cdbb172116e5d2e9bd1f06a6317', 'Pending', NULL),
(7, 10, 30, 11, NULL, 'view', '52db484e20380567aac073bfc81dbff3', 'Pending', NULL),
(8, 30, 29, 2, NULL, 'edit', 'c1e45e23062f44b6b0b6be9d2f991ecb', 'Pending', NULL),
(9, 10, 10, 3, NULL, 'edit', NULL, 'Accepted', NULL),
(10, 30, 10, 19, NULL, 'edit', NULL, '', NULL),
(11, 30, 10, 18, 33, 'edit', NULL, 'Accepted', NULL),
(12, 10, 30, 25, NULL, 'edit', NULL, '', NULL),
(13, 10, 30, 14, NULL, 'view', NULL, 'Accepted', NULL),
(14, 30, 10, 16, 34, 'edit', NULL, 'Accepted', NULL),
(15, 30, 10, 17, NULL, 'view', NULL, 'Accepted', NULL),
(16, 30, 10, 2, NULL, 'edit', NULL, 'Accepted', NULL),
(17, 30, 10, 29, NULL, 'edit', NULL, 'Accepted', NULL),
(18, 30, 10, 15, NULL, 'view', NULL, 'Accepted', NULL),
(19, 30, 10, 18, 33, 'edit', NULL, 'Accepted', NULL),
(20, 30, 10, 16, 34, 'view', NULL, 'Accepted', NULL),
(21, 30, 10, 35, 38, 'view', NULL, 'Accepted', NULL),
(22, 30, 10, 35, 38, 'view', NULL, 'Accepted', NULL),
(23, 30, 10, 35, 38, 'view', NULL, 'Accepted', NULL),
(24, 30, 10, 39, 40, 'view', NULL, 'Accepted', NULL),
(25, 30, 10, 41, 49, 'view', NULL, 'Accepted', NULL),
(26, 30, 10, 50, 51, 'view', NULL, 'Accepted', NULL),
(27, 10, 30, 52, 53, 'view', NULL, 'Accepted', NULL),
(28, 10, 30, 54, 55, 'view', NULL, 'Accepted', NULL),
(29, 30, 10, 56, 56, 'view', NULL, 'Accepted', '2025-05-02 11:32:55'),
(30, 30, 10, 57, 58, 'view', NULL, 'Accepted', NULL),
(31, 30, 10, 59, 60, 'view', NULL, 'Accepted', NULL),
(32, 10, 30, 61, NULL, 'view', NULL, 'Pending', NULL),
(33, 10, 30, 63, 64, 'view', NULL, 'Accepted', NULL),
(34, 30, 10, 62, 65, 'edit', NULL, 'Accepted', NULL),
(35, 30, 10, 64, 66, 'edit', NULL, 'Accepted', NULL),
(36, 30, 10, 67, 68, 'edit', NULL, 'Accepted', NULL),
(37, 30, 10, 69, 70, 'view', NULL, 'Accepted', NULL),
(38, 30, 10, 71, 72, 'view', NULL, 'Accepted', NULL),
(39, 30, 10, 73, 74, 'edit', NULL, 'Accepted', NULL),
(40, 30, 10, 75, 76, 'view', NULL, 'Accepted', NULL),
(41, 30, 10, 77, 78, 'view', NULL, 'Accepted', NULL),
(42, 10, 30, 79, NULL, 'edit', NULL, 'Accepted', NULL),
(43, 30, 10, 55, 80, 'view', NULL, 'Accepted', NULL),
(44, 30, 10, 81, 82, 'view', NULL, 'Accepted', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `file_name`, `uploaded_at`, `user_id`) VALUES
(5, 'web', 'Screenshot 2023-09-06 130542.png', '2025-04-18 19:42:21', 30),
(6, 'R', 'Screenshot 2023-08-12 215305.png', '2025-04-18 19:44:08', 30),
(10, 'biology', 'Screenshot 2023-09-11 112451.png', '2025-04-18 20:09:32', 30),
(11, 'dbms', 'ADBMS_Record_Bhavya.docx', '2025-04-29 09:45:22', 30),
(20, 'Maths', '', '2025-04-30 14:49:28', 10),
(21, 'English', '', '2025-04-30 14:52:09', 10),
(26, 'dbms', 'java_record_final_merged.pdf', '2025-04-30 15:48:55', 10),
(27, 'R', 'ADBMS_Record_Bhavya (1).docx', '2025-04-30 15:49:21', 10),
(28, 'dbms', 'ADBMS_Record_Bhavya.docx', '2025-04-30 15:50:23', 10),
(30, 'R', 'WorkShopCloud.docx', '2025-05-01 12:52:49', 30),
(31, 'Biology', '', '2025-05-01 18:21:17', 10),
(32, 'web', '', '2025-05-01 18:35:36', 10),
(33, 'hindi', '', '2025-05-02 01:14:26', 30),
(34, 'hindi', '', '2025-05-02 01:14:59', 10),
(35, 'English', '', '2025-05-02 01:53:15', 30),
(36, 'xxxxxx', '', '2025-05-02 02:04:44', 30),
(44, 'xxxxxx', '', '2025-05-02 02:40:33', 10),
(45, 'stat', '', '2025-05-02 03:45:02', 30),
(46, 'stat', '', '2025-05-02 03:46:16', 10),
(47, 'ffff', '', '2025-05-02 03:46:53', 10),
(48, 'ffff', '', '2025-05-02 03:47:11', 30),
(49, 'bbbbbbbbb', '', '2025-05-02 04:36:55', 10),
(50, 'bbbbbbbbb', '', '2025-05-02 04:38:18', 30),
(51, 'Maths', '', '2025-05-02 07:27:05', 30),
(52, 'econo', '', '2025-05-02 08:25:29', 30),
(53, 'econo', '', '2025-05-02 08:25:51', 10),
(54, 'java', '', '2025-05-02 08:27:24', 10),
(55, 'java', '', '2025-05-02 08:27:50', 30),
(56, 'docker', '', '2025-05-02 09:34:29', 30),
(57, 'docker', '', '2025-05-02 09:34:53', 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `PASSWORD`, `created_at`) VALUES
(1, 'AASIYA', 'aasiyashafeek15@gmail.com', '$2y$10$QY.dcc/1ZCaDDQLeYTe8juCj7r516xz8.JcSOqCXIFMWb/48iFY.K', '2025-04-17 05:42:23'),
(7, 'aasiyaaa', 'aasiyaaa@gmail.com', '$2y$10$RZ46Nj2hZBt2veIBgxTLAOMcp2.OzkncaqliO0fq6lKtkezPreJwO', '2025-04-17 09:13:20'),
(8, 'aasy', 'aasy@gmail.com', '$2y$10$V5fWV3UslzXUNdflO.zonelsIceRQmi5ZQvIrIok9fYWCB.tUvvYS', '2025-04-17 10:19:14'),
(10, 'bhavya1', 'bhavya@gmail.com', '$2y$10$exBRnSyl./GDiusZGvhE1O1nvLLmN.boPsOPsv29L18UEePEjrbQ.', '2025-04-17 10:23:26'),
(12, 'bhavyaa', 'bhavyaa@gmail.com', '$2y$10$moxX6qK/Tq6gKOKs78ddYe5Wvr16pFScpniegysVKkctyBebJqMFO', '2025-04-17 10:30:47'),
(13, 'afssana', 'afssana@gmail.com', '$2y$10$rMZIvFZGHMX6BM/ANhisUeLvYJRHNrTwjz3xpbnW2gu7uDaKWOYUm', '2025-04-17 10:36:02'),
(14, 'akshaya', 'akshaya@gmail.com', '$2y$10$8EDXalldlduS1oS6mE/tK.6Bprju4H2nu.2kNqsK1ooakl1JeCNK2', '2025-04-17 10:37:44'),
(16, 'txt', 'txt@gmail.com', '$2y$10$99gJAAQt3m9QfmxKGnXdOOFdiL2UeJVRo5UGLVbq4m1WtZi7kAsUq', '2025-04-17 10:44:02'),
(17, 'txt304', 'txt304@gmail.com', '$2y$10$6Rz9zJVnsZfBu7h1sZ1FgutijhGll5wSfQI1iW9eEdDEYdRbryMoa', '2025-04-17 10:44:48'),
(20, 'yuka', 'yuka@gmail.com', '$2y$10$gFYrhMe76jXzMFs3d1Y64uaLvEBomiYejW/NZqDKJ7yZv/CqVcIzS', '2025-04-17 17:33:44'),
(21, 'yuka77', 'yuka77@gmail.com', '$2y$10$RE3zXas8KVUeV9cuROd9yuDeLNIPOCO5tZenjvMuCS86JW/d.bnui', '2025-04-17 17:34:34'),
(23, 'yuka304', 'yuka304@gmail.com', '$2y$10$uPat6ceSCimlqFRRUMy9veihztF1ezwstsfPqg7vUQOQ9BmIw.lnm', '2025-04-17 23:13:07'),
(24, 'aasiya304', 'aasiya304@gmail.com', '$2y$10$VpfXFsf2X6O3xBHoNHukIeJkJbcJFj.s/Qk/zjS0h17sALTIrtehm', '2025-04-17 23:21:19'),
(26, 'hello', 'hello@gmail.com', '$2y$10$ouU.L41ijn.cRpI9/jvtyOVrW1.HVTziCTl7jSqDa26ybZi3w1Hw.', '2025-04-17 23:22:07'),
(29, 'BhavyaUllas', 'bhavyag190616@gmail.com', '$2y$10$tCQJAj0.F3kA6unJykM82eGsPdCDg7MoU8M38Z49oo/4Xbz2GNGwO', '2025-04-18 19:46:01'),
(30, 'Gowtam1', 'gowtamcrux333@gmail.com', '$2y$10$dK1FEgXJDw4N0anINZFi4eNI8MGpCj2rQIXpiXnZVAFbSyScx7J6y', '2025-04-18 20:06:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `flashcards`
--
ALTER TABLE `flashcards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_subject_id` (`subject_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `flashcard_id` (`flashcard_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_quizzes_subject` (`subject_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `shared_flashcards`
--
ALTER TABLE `shared_flashcards`
  ADD PRIMARY KEY (`share_id`),
  ADD UNIQUE KEY `share_token` (`share_token`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `shared_with_user_id` (`recipient_id`),
  ADD KEY `flashcard_id` (`flashcard_id`),
  ADD KEY `recipient_flashcard_id` (`recipient_flashcard_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subjects_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `flashcards`
--
ALTER TABLE `flashcards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `shared_flashcards`
--
ALTER TABLE `shared_flashcards`
  MODIFY `share_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flashcards`
--
ALTER TABLE `flashcards`
  ADD CONSTRAINT `fk_flashcards_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `flashcards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`flashcard_id`) REFERENCES `flashcards` (`id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_quizzes_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shared_flashcards`
--
ALTER TABLE `shared_flashcards`
  ADD CONSTRAINT `shared_flashcards_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `shared_flashcards_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `shared_flashcards_ibfk_3` FOREIGN KEY (`flashcard_id`) REFERENCES `flashcards` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_subjects_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
