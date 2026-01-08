-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 01:28 PM
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
-- Database: `crashpoint_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generate_report_number` (OUT `new_report_number` VARCHAR(50))   BEGIN
    DECLARE last_number INT;
    DECLARE year_prefix VARCHAR(4);
    
    SET year_prefix = YEAR(CURDATE());
    
    SELECT COALESCE(MAX(CAST(SUBSTRING(report_number, 11) AS UNSIGNED)), 0) INTO last_number
    FROM crash_reports
    WHERE report_number LIKE CONCAT('CR', year_prefix, '%');
    
    SET new_report_number = CONCAT('CR', year_prefix, LPAD(last_number + 1, 6, '0'));
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `authorities`
--

CREATE TABLE `authorities` (
  `authority_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `badge_number` varchar(50) NOT NULL,
  `rank_position` varchar(100) NOT NULL,
  `station` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT 'Traffic Department',
  `password` varchar(255) NOT NULL,
  `role` enum('admin','officer','supervisor') DEFAULT 'officer',
  `profile_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `authorities`
--

INSERT INTO `authorities` (`authority_id`, `full_name`, `email`, `phone`, `badge_number`, `rank_position`, `station`, `department`, `password`, `role`, `profile_image`, `is_active`, `created_at`, `updated_at`, `last_login`, `approved_by`, `approved_at`) VALUES
(1, 'admin', 'admin@crashpoint.co.ke', '+254700000000', 'ADMIN001', 'Chief Inspector', 'National Headquarters', 'IT & Systems', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, 1, '2026-01-08 12:15:16', '2026-01-08 12:15:16', NULL, NULL, NULL),
(2, 'Jane Wanjiru', 'jane.wanjiru@police.go.ke', '+254722334455', 'OFC001234', 'Inspector', 'Central Police Station', 'Traffic Department', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'officer', NULL, 1, '2026-01-08 12:20:40', '2026-01-08 12:20:40', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `crash_reports`
--

CREATE TABLE `crash_reports` (
  `report_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `authority_id` int(11) DEFAULT NULL,
  `report_number` varchar(50) NOT NULL,
  `crash_date` datetime NOT NULL,
  `location` varchar(500) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `severity` enum('minor','moderate','severe','fatal') NOT NULL,
  `crash_type` enum('collision','overturn','pedestrian','single_vehicle','other') DEFAULT 'collision',
  `weather_condition` varchar(100) DEFAULT NULL,
  `road_condition` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `injuries` int(11) DEFAULT 0,
  `fatalities` int(11) DEFAULT 0,
  `vehicles_involved` int(11) DEFAULT 1,
  `status` enum('pending','investigating','resolved','closed') DEFAULT 'pending',
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `photos` text DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crash_reports`
--

INSERT INTO `crash_reports` (`report_id`, `driver_id`, `authority_id`, `report_number`, `crash_date`, `location`, `latitude`, `longitude`, `severity`, `crash_type`, `weather_condition`, `road_condition`, `description`, `injuries`, `fatalities`, `vehicles_involved`, `status`, `priority`, `photos`, `documents`, `created_at`, `updated_at`, `resolved_at`) VALUES
(1, 1, NULL, 'CR2026010001', '2026-01-05 14:30:00', 'Mombasa Road, Near Syokimau', -1.31830000, 36.91170000, 'moderate', 'collision', NULL, NULL, 'Two vehicle collision at intersection. Minor injuries reported.', 2, 0, 2, 'pending', 'medium', NULL, NULL, '2026-01-07 07:43:03', '2026-01-07 07:43:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `full_name`, `email`, `phone`, `license_number`, `password`, `profile_image`, `address`, `is_active`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'John Kamau', 'john.kamau@example.com', '+254712345678', 'DL123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, 1, '2026-01-07 07:43:03', '2026-01-07 07:43:03', NULL);

--
-- Triggers `drivers`
--
DELIMITER $$
CREATE TRIGGER `after_driver_login` BEFORE UPDATE ON `drivers` FOR EACH ROW BEGIN
    IF NEW.last_login != OLD.last_login THEN
        SET NEW.updated_at = CURRENT_TIMESTAMP;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_type` enum('driver','authority') NOT NULL,
  `user_id` int(11) NOT NULL,
  `report_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_updates`
--

CREATE TABLE `report_updates` (
  `update_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `authority_id` int(11) DEFAULT NULL,
  `update_type` enum('comment','status_change','assignment') NOT NULL,
  `comment` text NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_type` enum('driver','authority','system') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_active_reports`
-- (See below for the actual view)
--
CREATE TABLE `vw_active_reports` (
`report_id` int(11)
,`report_number` varchar(50)
,`crash_date` datetime
,`location` varchar(500)
,`severity` enum('minor','moderate','severe','fatal')
,`status` enum('pending','investigating','resolved','closed')
,`priority` enum('low','medium','high','critical')
,`driver_name` varchar(255)
,`driver_phone` varchar(20)
,`investigating_officer` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `vw_dashboard_stats` (
`total_reports` bigint(21)
,`pending_reports` decimal(22,0)
,`investigating_reports` decimal(22,0)
,`resolved_reports` decimal(22,0)
,`fatal_crashes` decimal(22,0)
,`total_injuries` decimal(32,0)
,`total_fatalities` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_active_reports`
--
DROP TABLE IF EXISTS `vw_active_reports`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_active_reports`  AS SELECT `cr`.`report_id` AS `report_id`, `cr`.`report_number` AS `report_number`, `cr`.`crash_date` AS `crash_date`, `cr`.`location` AS `location`, `cr`.`severity` AS `severity`, `cr`.`status` AS `status`, `cr`.`priority` AS `priority`, `d`.`full_name` AS `driver_name`, `d`.`phone` AS `driver_phone`, `a`.`full_name` AS `investigating_officer`, `cr`.`created_at` AS `created_at` FROM ((`crash_reports` `cr` left join `drivers` `d` on(`cr`.`driver_id` = `d`.`driver_id`)) left join `authorities` `a` on(`cr`.`authority_id` = `a`.`authority_id`)) WHERE `cr`.`status` <> 'closed' ORDER BY `cr`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_dashboard_stats`
--
DROP TABLE IF EXISTS `vw_dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_dashboard_stats`  AS SELECT count(0) AS `total_reports`, sum(case when `crash_reports`.`status` = 'pending' then 1 else 0 end) AS `pending_reports`, sum(case when `crash_reports`.`status` = 'investigating' then 1 else 0 end) AS `investigating_reports`, sum(case when `crash_reports`.`status` = 'resolved' then 1 else 0 end) AS `resolved_reports`, sum(case when `crash_reports`.`severity` = 'fatal' then 1 else 0 end) AS `fatal_crashes`, sum(`crash_reports`.`injuries`) AS `total_injuries`, sum(`crash_reports`.`fatalities`) AS `total_fatalities` FROM `crash_reports` WHERE year(`crash_reports`.`crash_date`) = year(curdate()) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authorities`
--
ALTER TABLE `authorities`
  ADD PRIMARY KEY (`authority_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `badge_number` (`badge_number`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_badge` (`badge_number`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `crash_reports`
--
ALTER TABLE `crash_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `report_number` (`report_number`),
  ADD KEY `idx_report_number` (`report_number`),
  ADD KEY `idx_driver` (`driver_id`),
  ADD KEY `idx_authority` (`authority_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_crash_date` (`crash_date`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_license` (`license_number`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `report_updates`
--
ALTER TABLE `report_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `authority_id` (`authority_id`),
  ADD KEY `idx_report` (`report_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authorities`
--
ALTER TABLE `authorities`
  MODIFY `authority_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `crash_reports`
--
ALTER TABLE `crash_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_updates`
--
ALTER TABLE `report_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crash_reports`
--
ALTER TABLE `crash_reports`
  ADD CONSTRAINT `crash_reports_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crash_reports_ibfk_2` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`authority_id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `crash_reports` (`report_id`) ON DELETE CASCADE;

--
-- Constraints for table `report_updates`
--
ALTER TABLE `report_updates`
  ADD CONSTRAINT `report_updates_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `crash_reports` (`report_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_updates_ibfk_2` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`authority_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
