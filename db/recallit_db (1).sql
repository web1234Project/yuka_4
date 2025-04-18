-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 08:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
(1, 'aasiya', 'admin01@gmail.com', 'aasiya');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 'bhavya', 'bhavya@gmail.com', '$2y$10$exBRnSyl./GDiusZGvhE1O1nvLLmN.boPsOPsv29L18UEePEjrbQ.', '2025-04-17 10:23:26'),
(12, 'bhavyaa', 'bhavyaa@gmail.com', '$2y$10$moxX6qK/Tq6gKOKs78ddYe5Wvr16pFScpniegysVKkctyBebJqMFO', '2025-04-17 10:30:47'),
(13, 'afssana', 'afssana@gmail.com', '$2y$10$rMZIvFZGHMX6BM/ANhisUeLvYJRHNrTwjz3xpbnW2gu7uDaKWOYUm', '2025-04-17 10:36:02'),
(14, 'akshaya', 'akshaya@gmail.com', '$2y$10$8EDXalldlduS1oS6mE/tK.6Bprju4H2nu.2kNqsK1ooakl1JeCNK2', '2025-04-17 10:37:44'),
(16, 'txt', 'txt@gmail.com', '$2y$10$99gJAAQt3m9QfmxKGnXdOOFdiL2UeJVRo5UGLVbq4m1WtZi7kAsUq', '2025-04-17 10:44:02'),
(17, 'txt304', 'txt304@gmail.com', '$2y$10$6Rz9zJVnsZfBu7h1sZ1FgutijhGll5wSfQI1iW9eEdDEYdRbryMoa', '2025-04-17 10:44:48'),
(20, 'yuka', 'yuka@gmail.com', '$2y$10$gFYrhMe76jXzMFs3d1Y64uaLvEBomiYejW/NZqDKJ7yZv/CqVcIzS', '2025-04-17 17:33:44'),
(21, 'yuka77', 'yuka77@gmail.com', '$2y$10$RE3zXas8KVUeV9cuROd9yuDeLNIPOCO5tZenjvMuCS86JW/d.bnui', '2025-04-17 17:34:34'),
(23, 'yuka304', 'yuka304@gmail.com', '$2y$10$uPat6ceSCimlqFRRUMy9veihztF1ezwstsfPqg7vUQOQ9BmIw.lnm', '2025-04-17 23:13:07'),
(24, 'aasiya304', 'aasiya304@gmail.com', '$2y$10$VpfXFsf2X6O3xBHoNHukIeJkJbcJFj.s/Qk/zjS0h17sALTIrtehm', '2025-04-17 23:21:19'),
(26, 'hello', 'hello@gmail.com', '$2y$10$ouU.L41ijn.cRpI9/jvtyOVrW1.HVTziCTl7jSqDa26ybZi3w1Hw.', '2025-04-17 23:22:07');

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
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flashcards`
--
ALTER TABLE `flashcards`
  ADD CONSTRAINT `flashcards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
