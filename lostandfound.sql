-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2025 at 12:06 PM
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
-- Database: `lostandfound`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `created`) VALUES
(1, 'Administrator', 'ns685276@gmail.com', '12345678', '2025-09-18 09:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `actor` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target` varchar(255) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `timestamp`, `actor`, `action`, `target`, `result`) VALUES
(1, '2025-09-02 10:12:00', 'Admin2', 'Status change', 'User: sam@example.com', 'Active → Suspended'),
(2, '2025-09-02 09:48:00', 'Admin1', 'Override', 'Match L#245', 'Overridden'),
(3, '2025-09-01 18:21:00', 'System', 'Report generated', 'Daily Summary', 'CSV');

-- --------------------------------------------------------

--
-- Table structure for table `found_items`
--

CREATE TABLE `found_items` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `found_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `found_items`
--

INSERT INTO `found_items` (`id`, `item_id`, `found_at`, `location`) VALUES
(1, 1, '2025-09-10 10:00:13', 'Library'),
(2, 2, '2025-09-13 10:00:13', 'Cafeteria'),
(3, 4, '2025-09-16 10:00:13', 'Parking Lot');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('lost','found','returned') DEFAULT 'lost',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `category`, `location`, `status`, `reported_at`) VALUES
(1, 'Black Wallet', 'Wallet', 'Library', 'lost', '2025-09-08 09:59:53'),
(2, 'Blue Umbrella', 'Umbrella', 'Cafeteria', 'lost', '2025-09-11 09:59:53'),
(3, 'Red Backpack', 'Bag', 'Lecture Hall', 'returned', '2025-09-03 09:59:53'),
(4, 'Car Keys', 'Keys', 'Parking Lot', 'found', '2025-09-15 09:59:53');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` varchar(20) NOT NULL,
  `item_title` varchar(255) NOT NULL,
  `claimant_name` varchar(100) NOT NULL,
  `similarity_percentage` int(11) NOT NULL CHECK (`similarity_percentage` between 0 and 100),
  `status` enum('pending','approved','overridden') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `item_title`, `claimant_name`, `similarity_percentage`, `status`, `created_at`) VALUES
('L#245', 'Black Wallet • Library', 'Alex Morgan', 86, 'pending', '2025-09-18 10:00:48'),
('L#246', 'Blue Umbrella • Cafeteria', 'Sam Lee', 62, 'pending', '2025-09-18 10:00:48'),
('L#247', 'Red Backpack • Lecture Hall', 'Mia Wong', 74, 'approved', '2025-09-18 10:00:48'),
('L#248', 'Car Keys • Parking Lot', 'John Smith', 58, 'overridden', '2025-09-18 10:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `audience` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `audience`, `type`, `subject`, `message`, `created_at`) VALUES
(1, 'All Users', 'Announcement', 'Welcome Back', 'We are happy to announce new features coming soon!', '2025-09-18 10:01:50'),
(2, 'Only Finders', 'Security', 'Be Vigilant', 'Please report any suspicious behavior immediately.', '2025-09-18 10:01:50'),
(3, 'Only Claimants', 'Service Update', 'Claim Process Change', 'Claim pickup hours have been extended.', '2025-09-18 10:01:50');

-- --------------------------------------------------------

--
-- Table structure for table `returned_items`
--

CREATE TABLE `returned_items` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `returned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `received_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returned_items`
--

INSERT INTO `returned_items` (`id`, `item_id`, `returned_at`, `received_by`) VALUES
(1, 3, '2025-09-17 10:00:29', 'Alex Morgan'),
(2, 1, '2025-09-16 10:00:29', 'Mia Wong');

-- --------------------------------------------------------

--
-- Table structure for table `security_events`
--

CREATE TABLE `security_events` (
  `id` int(11) NOT NULL,
  `event_date` date NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_events`
--

INSERT INTO `security_events` (`id`, `event_date`, `event_type`, `count`, `notes`) VALUES
(1, '2025-09-18', 'Failed Logins', 5, 'Investigate'),
(2, '2025-09-17', 'Admin Overrides', 2, 'Reviewed'),
(3, '2025-09-16', 'Password Reset Requests', 3, 'Info'),
(4, '2025-09-15', 'Multiple Failed Attempts', 7, 'Investigate');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first` varchar(100) NOT NULL,
  `last` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first`, `last`, `email`, `password`, `status`) VALUES
(1, 'Alex', 'Morgan', 'alex@example.com', 'password123', 'Inactive'),
(2, 'Sam', 'Lee', 'sam@example.com', 'password123', 'Active'),
(3, 'Mia', 'Wong', 'mia@example.com', 'password123', 'Active'),
(4, 'Ali', 'abdul', 'aa@example.com', '9876', 'Active'),
(5, 'Khan', 'Limon', 'kl@example.com', '1234', 'Active'),
(6, 'Asha', 'Mim', 'Am@example.com', '4567', 'Active'),
(7, 'shadman', 'abrar', '12@gmail.com', '12345678', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `found_items`
--
ALTER TABLE `found_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returned_items`
--
ALTER TABLE `returned_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `security_events`
--
ALTER TABLE `security_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `found_items`
--
ALTER TABLE `found_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `returned_items`
--
ALTER TABLE `returned_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `security_events`
--
ALTER TABLE `security_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `found_items`
--
ALTER TABLE `found_items`
  ADD CONSTRAINT `found_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `returned_items`
--
ALTER TABLE `returned_items`
  ADD CONSTRAINT `returned_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
