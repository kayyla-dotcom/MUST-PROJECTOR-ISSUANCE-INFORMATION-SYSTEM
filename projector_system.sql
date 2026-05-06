-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 11:16 PM
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
-- Database: `projector_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_borrowers`
--

CREATE TABLE `tbl_borrowers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `department` varchar(150) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_borrowers`
--

INSERT INTO `tbl_borrowers` (`id`, `full_name`, `department`, `contact`, `email`) VALUES
(12, 'Mr Chongwe', 'CSIT', '0897263097', 'alanchongwe@gmail.com'),
(13, 'Mr Joabe', 'Applied scieces', '0892921119', 'joabe@gmail.com'),
(14, 'Dr Maliwichi', 'CSIT', '0888853525', 'maliwichi@must.ac.mw'),
(15, 'Mr Chirwa', 'CSIT', '0985559490', 'kondwani@must.ac.mw'),
(16, 'Dr Mwantobwe', 'Applied Sciences', '0983281183', 'amwantobwe@gmail.com'),
(17, 'Mr Morgan', 'Language and culture', '0991081020', 'morgan@must.ac.mw'),
(18, 'Mss Chisomo', 'Medical Sciences', '0980730168', 'chisomo@gmail.com'),
(19, 'Mss Banda', 'BAM', '0988464534', 'bandac@gmail.com'),
(20, 'Professor Khumbo Kamwachale', 'Applied Sciences', '0887616839', 'khamwachale@must.ac.mw'),
(21, 'Ms Nyirongo', 'Language and culture', '0892921645', 'elludahkhaid@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_projectors`
--

CREATE TABLE `tbl_projectors` (
  `id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(150) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `condition_note` varchar(200) DEFAULT 'Good',
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_projectors`
--

INSERT INTO `tbl_projectors` (`id`, `serial_number`, `brand`, `model`, `status`, `condition_note`, `added_at`) VALUES
(3, 'PRJ-01', 'Epson', 'EB-X01', 'issued', 'Good', '2026-05-03 21:00:24'),
(4, 'PRJ-02', 'Epson', 'EB-X02', 'issued', 'Good', '2026-05-03 21:01:02'),
(8, 'PRJ-03', 'Epson', 'EB-03', 'issued', 'Good', '2026-05-06 20:04:47'),
(9, 'EX-014564', 'Sony', 'VPL SERIES', 'issued', 'Good', '2026-05-06 20:08:13'),
(10, 'EX-0145645', 'Epson', 'EB-3436', 'issued', 'Good', '2026-05-06 20:08:54'),
(11, 'EX-0145745', 'Epson', 'EB-34364', 'issued', 'Good', '2026-05-06 20:09:48'),
(12, 'EX-01456', 'Epson', 'EB-3222', 'available', 'Good', '2026-05-06 20:10:08'),
(13, 'VX-014532', 'Sony', 'VPL SERIES', 'issued', 'Good', '2026-05-06 20:10:43'),
(14, 'BH-654656', 'BenQ', 'VC705i 4K', 'issued', 'Good', '2026-05-06 20:11:50'),
(17, 'PRJ-5642', 'BenQ', 'W5850 4K', 'issued', 'Good', '2026-05-06 20:14:28'),
(18, 'TX-01424', 'EPSON', 'RX56001', 'issued', 'Good', '2026-05-06 20:15:23'),
(19, 'EY3542', 'BenQ', 'VPL SERIES', 'issued', 'Good', '2026-05-06 20:16:11'),
(20, 'HG5418', 'Sony', 'WE3435', 'issued', 'Good', '2026-05-06 20:16:38'),
(21, 'PRJ-453', 'BenQ', 'EB-3433', 'issued', 'Good', '2026-05-06 20:17:05'),
(22, 'QX-0145373', 'EPSON', 'YT3435', 'issued', 'Good', '2026-05-06 20:17:36'),
(23, 'PRJ-03122', 'Epson', 'W5850 4KW', 'issued', 'Good', '2026-05-06 20:18:28'),
(24, 'PRJ-4555', 'EPSON', 'EB-343643', 'issued', 'Good', '2026-05-06 20:18:45'),
(25, 'EX-014003', 'BenQ', 'DF7643', 'issued', 'Good', '2026-05-06 20:19:25'),
(26, 'HVD6454', 'EPSON', 'UJK2234', 'issued', 'Good', '2026-05-06 20:20:04'),
(27, 'EX-01116', 'Sony', 'X200P', 'issued', 'Good', '2026-05-06 20:20:31');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transactions`
--

CREATE TABLE `tbl_transactions` (
  `id` int(11) NOT NULL,
  `projector_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `issued_by` int(11) NOT NULL,
  `purpose` text DEFAULT NULL,
  `date_issued` date NOT NULL,
  `expected_return` date NOT NULL,
  `date_returned` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'issued',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_transactions`
--

INSERT INTO `tbl_transactions` (`id`, `projector_id`, `borrower_id`, `issued_by`, `purpose`, `date_issued`, `expected_return`, `date_returned`, `status`, `notes`, `created_at`) VALUES
(7, 14, 16, 16, 'systems analysis lecture', '2026-05-06', '2026-05-09', '2026-05-06', 'returned', '', '2026-05-06 20:45:57'),
(8, 20, 12, 16, 'information security lecture', '2026-05-04', '2026-05-11', NULL, 'issued', NULL, '2026-05-06 20:46:58'),
(9, 9, 19, 16, 'music and audio production', '2026-05-06', '2026-05-12', NULL, 'issued', NULL, '2026-05-06 20:48:05'),
(10, 24, 17, 16, 'proposal presentation', '2026-05-04', '2026-05-07', '2026-05-06', 'returned', '', '2026-05-06 20:48:56'),
(11, 18, 18, 16, 'presentation on anatomy', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:49:39'),
(12, 23, 20, 16, 'lecture on accounting errors', '2026-05-05', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:50:40'),
(13, 19, 16, 16, 'physics lecture', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:52:39'),
(14, 12, 14, 16, 'SYAD lecture', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:53:45'),
(15, 10, 12, 16, 'ISEC lecture', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:54:16'),
(16, 3, 21, 16, 'Presentations', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:54:57'),
(17, 4, 18, 16, 'Presentations', '2026-05-06', '2026-05-11', NULL, 'issued', NULL, '2026-05-06 20:55:33'),
(18, 27, 20, 16, 'FOAC lecture', '2026-05-06', '2026-05-09', '2026-05-06', 'returned', '', '2026-05-06 20:56:32'),
(19, 21, 19, 16, 'Presentations', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 20:57:14'),
(20, 25, 13, 16, 'MPPR Lecture', '2026-05-01', '2026-05-10', NULL, 'issued', NULL, '2026-05-06 20:59:38'),
(21, 17, 14, 16, 'DSYS Lecture', '2026-05-06', '2026-05-12', NULL, 'issued', NULL, '2026-05-06 21:01:12'),
(22, 22, 13, 16, 'MPPR lecture', '2026-05-06', '2026-05-08', '2026-05-06', 'returned', '', '2026-05-06 21:04:17'),
(23, 26, 12, 16, 'ISEC lecture', '2026-05-06', '2026-05-14', NULL, 'issued', NULL, '2026-05-06 21:05:23'),
(24, 8, 20, 16, 'FOAC presentation', '2026-05-06', '2026-05-11', NULL, 'issued', NULL, '2026-05-06 21:06:22'),
(25, 11, 19, 16, 'lecture', '2026-05-06', '2026-05-12', NULL, 'issued', NULL, '2026-05-06 21:07:00'),
(26, 13, 15, 16, 'Computer programming lecture', '2026-05-06', '2026-05-13', NULL, 'issued', NULL, '2026-05-06 21:09:30'),
(27, 14, 13, 16, 'Presentation', '2026-04-28', '2026-05-22', NULL, 'issued', NULL, '2026-05-06 21:11:25'),
(28, 10, 19, 16, 'Lecture', '2026-05-01', '2026-05-28', NULL, 'issued', NULL, '2026-05-06 21:11:48'),
(29, 21, 20, 16, '', '2026-05-06', '2026-05-29', NULL, 'issued', NULL, '2026-05-06 21:12:16'),
(30, 19, 15, 16, '', '2026-05-06', '2026-05-30', NULL, 'issued', NULL, '2026-05-06 21:12:39'),
(31, 3, 16, 16, '', '2026-05-06', '2026-05-21', NULL, 'issued', NULL, '2026-05-06 21:12:58'),
(32, 22, 14, 16, 'Presentation', '2026-05-06', '2026-05-21', NULL, 'issued', NULL, '2026-05-06 21:13:39'),
(33, 27, 19, 16, 'Movie Night', '2026-05-01', '2026-05-22', NULL, 'issued', NULL, '2026-05-06 21:14:17'),
(34, 23, 18, 16, 'Lecture', '2026-05-06', '2026-05-29', NULL, 'issued', NULL, '2026-05-06 21:14:41'),
(35, 24, 21, 16, 'Culture day', '2026-04-30', '2026-05-26', NULL, 'issued', NULL, '2026-05-06 21:15:18'),
(36, 18, 17, 16, 'Lecture', '2026-04-30', '2026-05-29', NULL, 'issued', NULL, '2026-05-06 21:15:48');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(10) NOT NULL DEFAULT 'teller',
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','active','inactive') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `status`) VALUES
(4, 'admin', 'admin@gmail.com', '$2y$10$ak829RLylkvf9GxAkS/a5ulXQfCWLyRONkueyEdIhqCy9rNN5ekFO', 'admin', '2026-04-30 22:07:50', 'active'),
(16, 'Aaron Kingsley', 'aaronkingsley@gmail.com', '$2y$10$EVtLm0FN8lReiJcAkWhgZOPyLIwXHh.DFHHjThJt74pXAApZSRD7a', 'user', '2026-05-07 05:43:32', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_borrowers`
--
ALTER TABLE `tbl_borrowers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_projectors`
--
ALTER TABLE `tbl_projectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`);

--
-- Indexes for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projector_id` (`projector_id`),
  ADD KEY `borrower_id` (`borrower_id`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_borrowers`
--
ALTER TABLE `tbl_borrowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_projectors`
--
ALTER TABLE `tbl_projectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  ADD CONSTRAINT `tbl_transactions_ibfk_1` FOREIGN KEY (`projector_id`) REFERENCES `tbl_projectors` (`id`),
  ADD CONSTRAINT `tbl_transactions_ibfk_2` FOREIGN KEY (`borrower_id`) REFERENCES `tbl_borrowers` (`id`),
  ADD CONSTRAINT `tbl_transactions_ibfk_3` FOREIGN KEY (`issued_by`) REFERENCES `tbl_users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
