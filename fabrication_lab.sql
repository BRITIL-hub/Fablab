-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 11:06 AM
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
-- Database: `fabrication_lab`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `machine_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `status` enum('pending','approved','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `machine_id`, `appointment_date`, `status`) VALUES
(4, 4, 1, '2024-10-14 09:00:00', 'pending'),
(5, 4, 3, '2024-10-16 10:00:00', 'pending'),
(6, 3, 3, '2024-10-16 10:00:00', 'pending'),
(7, 3, 3, '2024-10-16 10:00:00', 'pending'),
(10, 3, 3, '2024-10-16 10:00:00', 'pending'),
(11, 4, 3, '2024-10-16 10:00:00', 'pending'),
(14, 3, 1, '2024-10-14 10:00:00', 'pending'),
(16, 10, 5, '2024-10-22 07:30:00', 'approved'),
(17, 1, 6, '2024-10-16 09:40:00', 'pending'),
(18, 10, 6, '2024-10-16 09:40:00', 'approved'),
(20, 10, 2, '2024-11-19 09:00:00', 'approved'),
(22, 10, 1, '2024-11-21 08:00:00', 'approved'),
(23, 4, 2, '2024-11-19 09:00:00', 'pending'),
(24, 11, 2, '2024-11-19 09:00:00', 'completed'),
(25, 12, 4, '2024-12-19 09:00:00', 'pending'),
(26, 12, 2, '2024-11-29 06:39:00', 'pending'),
(28, 4, 2, '2024-11-29 06:39:00', 'pending'),
(29, 11, 2, '2024-11-29 06:39:00', 'pending'),
(30, 3, 2, '2024-11-29 06:39:00', 'pending'),
(31, 3, 2, '2024-11-20 16:00:00', 'pending'),
(32, 11, 2, '2024-11-20 16:00:00', 'pending'),
(33, 4, 2, '2024-11-20 16:00:00', 'pending'),
(34, 12, 2, '2024-11-20 16:00:00', 'pending'),
(35, 10, 2, '2024-11-20 16:00:00', 'pending'),
(37, 20, 1, '2024-11-29 13:00:00', 'approved'),
(38, 21, 1, '2024-11-29 13:00:00', 'pending'),
(39, 21, 4, '2024-12-19 09:00:00', 'pending'),
(40, 21, 4, '2024-11-26 18:00:00', 'pending'),
(41, 21, 6, '2024-11-27 18:05:00', 'pending'),
(42, 10, 1, '2024-11-29 13:00:00', 'approved'),
(43, 65, 1, '2024-11-29 13:00:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `machine_id` int(11) NOT NULL,
  `machine_name` varchar(100) NOT NULL,
  `machine_photo` varchar(255) NOT NULL,
  `availability` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`machine_id`, `machine_name`, `machine_photo`, `availability`) VALUES
(1, 'Roland (Print & Cut)', 'Machine 1.jpg', 1),
(2, 'CNC Laser', '../images/Machine 2.jpg', 0),
(3, 'Embroideryyy', '../images/Machine 3.jpg', 1),
(4, 'CNC Router', '../images/Machine 4.jpg', 0),
(5, '3D Scanning', '../images/Machine 5.jpg', 1),
(6, '3D Printing', '../images/Machine 6.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `slots_available` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `machine_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `date`, `slots_available`, `created_at`, `machine_id`) VALUES
(3, '2024-10-14 09:00:00', 1, '2024-10-12 06:35:45', 1),
(4, '2024-10-14 10:00:00', 1, '2024-10-12 06:36:42', 1),
(5, '2024-10-16 10:00:00', 0, '2024-10-14 10:32:39', 3),
(6, '2024-10-15 08:00:00', 3, '2024-10-14 10:43:19', 4),
(7, '2024-10-22 07:30:00', 2, '2024-10-14 11:29:23', 5),
(8, '2024-10-16 09:40:00', 1, '2024-10-14 11:37:22', 6),
(9, '2024-11-21 08:00:00', 0, '2024-10-28 07:16:29', 1),
(10, '2024-11-19 09:00:00', 0, '2024-10-28 07:44:24', 2),
(11, '2024-12-19 09:00:00', 3, '2024-11-08 04:47:23', 4),
(12, '2024-11-29 13:00:00', 0, '2024-11-08 04:47:58', 1),
(13, '2024-11-28 14:14:00', 5, '2024-11-08 06:14:59', 3),
(14, '2024-11-28 08:15:00', 5, '2024-11-08 06:15:36', 3),
(15, '2024-11-29 06:39:00', 0, '2024-11-11 10:39:45', 2),
(16, '2024-11-20 16:00:00', 0, '2024-11-11 10:53:38', 2),
(17, '2024-11-26 18:00:00', 1, '2024-11-14 10:00:22', 4),
(18, '2024-11-27 18:05:00', 1, '2024-11-14 10:04:33', 6),
(19, '2024-11-27 22:14:00', 2, '2024-11-14 14:14:40', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `verification_code_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `profile_picture`, `role`, `verification_code`, `is_verified`, `reset_token`, `verification_code_expires_at`) VALUES
(1, 'Abby', 'abby@gmail.com', '$2y$10$9L68CaZaHckt2Hq4bI629uLWZEkeA1lThhVtU79JlUhvutCdcYlCu', NULL, 'admin', NULL, 0, NULL, NULL),
(3, 'itsybitsyabbyyy', 'itsy@gmail.com', '$2y$10$.rmtghyIVBXGBP8gsPCA2e36t79pqpN3NNJRe.ouOKOlQwGxoKcfe', '6731ea2af3496_Screenshot (4).png', 'customer', NULL, 0, NULL, NULL),
(4, 'Jane Doe', 'jane@gmail.com', '$2y$10$hRazrZgfCNbPly6nlYZCVeiHIfvXisJCZuRXN5WOLMwiHkGIqoR4O', 'uploads/1728624015_Abyang.jpg', 'customer', NULL, 0, NULL, NULL),
(10, 'Abegail', 'basan.abegail@gmail.com', '$2y$10$TtzmFdwl.VGyG38aWzDhjOA/YwJL/FMbvDZSa0JcClxXCRCoQnyZ2', '6731d99bc45a9_Jane.jpg', 'customer', '263177', 1, 'be8e2e754e7cddfc5cd701b5445223d8554cb14889d84e1fab597a892f4910117fd917c055d1a64e0ce210f693d115274f63', NULL),
(11, 'Nick', 'nconrod.12345@gmail.com', '$2y$10$tNDiF5K9euVT4NQqkRoviOKyb7xREuaVlAUipifXRJAKpPh.jyE/C', '../uploads/1731038965_Jane.jpg', 'customer', '512214', 1, NULL, NULL),
(12, 'Anastasia Steele', 'jhonarcher913@gmail.com', '$2y$10$.2/Zvlu63XAPRlLUSDBKuu4LD5WfrbFAeC2SCDRD6Tn2ivJ2rKigC', '../uploads/1731321082_profile.jpg', 'customer', '641237', 1, NULL, NULL),
(19, 'Dhalia Mazekeen', 'mazekeen.dhalia@gmail.com', '$2y$10$atMEaM69fdLQSJqRDUS4ROwefA2Mc5m.S2/deg6wP9fcVIEJ1aajW', '67321527818c6_Jane.jpg', 'customer', NULL, 1, NULL, NULL),
(20, 'FabLab CTU Danao', 'fabricationlab.ctudanao@gmail.com', '$2y$10$uRAMGdrfwolSqgh8UhDAIuoH23T..5XF35cBJ85lk9exgDLfMlOiu', '6732c1fae99ca_profile.jpg', 'customer', NULL, 1, NULL, NULL),
(21, 'Wasp Stingers', 'makeitdrop.thatsawasp@gmail.com', '$2y$10$yr15ZF2tDGmayba9fSxiQOMzMXpKMIEZgpGRiX56wmiRmmj3GMU1y', '67346aa3db049_FABLAB.drawio.png', 'customer', NULL, 1, NULL, NULL),
(65, 'Hanie', 'hanie.einahhh@gmail.com', '$2y$10$yVq/vY2nGAOB2b5GjEkFSuYHUjTJWLsD8U.Hk9vdWnQPL4R/Cgrrq', '../uploads/1731922441_Abyang.jpg', 'customer', '277048', 1, NULL, '2024-11-18 17:39:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `machine_id` (`machine_id`);

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`machine_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `fk_machine` (`machine_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `machine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`machine_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_machine` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`machine_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
