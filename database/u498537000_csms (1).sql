-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 17, 2026 at 04:19 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u498537000_csms`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendars`
--

CREATE TABLE `academic_calendars` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `semester` enum('1st','2nd') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_calendars`
--

INSERT INTO `academic_calendars` (`id`, `academic_year`, `semester`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, '2025-2026', '1st', '2025-06-30', '2025-10-30', '2026-03-16 02:25:32', '2026-03-16 02:25:32'),
(2, '2025-2026', '2nd', '2025-12-01', '2026-04-16', '2026-03-16 02:25:32', '2026-03-16 02:25:32');

-- --------------------------------------------------------

--
-- Table structure for table `academic_calendar_events`
--

CREATE TABLE `academic_calendar_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_calendar_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('holiday','exam','break','non_teaching','other') NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_calendar_events`
--

INSERT INTO `academic_calendar_events` (`id`, `academic_calendar_id`, `type`, `name`, `date`, `created_at`, `updated_at`) VALUES
(1, 2, 'other', 'Lantern (D1)', '2025-12-17', '2026-03-16 02:45:24', '2026-03-16 02:45:24'),
(2, 2, 'other', 'Lantern (D2)', '2025-12-18', '2026-03-16 02:46:06', '2026-03-16 02:46:18'),
(3, 2, 'other', 'Lantern (D3)', '2025-12-19', '2026-03-16 02:46:40', '2026-03-16 02:46:40'),
(4, 2, 'break', 'Christmas Vacation', '2025-12-22', '2026-03-16 02:47:25', '2026-03-16 02:47:25'),
(5, 2, 'break', 'Christmas Vacation', '2026-01-03', '2026-03-16 02:47:52', '2026-03-16 02:47:52'),
(6, 2, 'exam', 'First Term Exam (D1)', '2026-01-14', '2026-03-16 03:03:08', '2026-03-16 03:03:08'),
(7, 2, 'exam', 'First Term Exam (D2)', '2026-01-15', '2026-03-16 03:03:33', '2026-03-16 03:04:01'),
(8, 2, 'exam', 'First Term Exam (D3)', '2026-01-16', '2026-03-16 03:03:47', '2026-03-16 03:04:20'),
(9, 2, 'exam', 'Second Term Exam (D1)', '2026-02-25', '2026-03-16 03:05:22', '2026-03-16 03:05:22'),
(11, 2, 'exam', 'Second Term Exam (D3)', '2026-02-27', '2026-03-16 03:06:07', '2026-03-16 03:06:07'),
(12, 2, 'exam', 'Final Term Exam (D1)', '2026-04-13', '2026-03-16 03:07:38', '2026-03-16 03:07:38'),
(13, 2, 'exam', 'Final Term Exam (D2)', '2026-04-14', '2026-03-16 03:07:57', '2026-03-16 03:07:57'),
(14, 2, 'exam', 'Final Term Exam (D3)', '2026-04-15', '2026-03-16 03:08:27', '2026-03-16 03:08:27'),
(15, 2, 'other', 'Intramurals (D1)', '2026-02-05', '2026-03-16 03:09:43', '2026-03-16 03:09:43'),
(16, 2, 'other', 'Intramurals (D2)', '2026-02-06', '2026-03-16 03:09:59', '2026-03-16 03:09:59'),
(17, 2, 'other', 'Intramurals (D3)', '2026-02-07', '2026-03-16 03:10:11', '2026-03-16 03:10:11'),
(18, 2, 'holiday', 'Chinese New Year', '2026-02-17', '2026-03-16 03:12:05', '2026-03-16 03:12:05'),
(19, 2, 'non_teaching', 'Testing of Whole Week No Class', '2026-01-08', '2026-03-19 02:59:03', '2026-03-27 19:48:05'),
(22, 2, 'exam', 'Second Term Exam (D2)', '2026-02-26', '2026-03-23 06:37:14', '2026-03-23 06:37:14'),
(24, 2, 'holiday', 'Valentine\'s Day ', '2026-02-14', '2026-04-07 15:12:24', '2026-04-07 15:12:24');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `module`, `reference_id`, `description`, `timestamp`, `created_at`, `updated_at`) VALUES
(205, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-26 14:58:35', '2026-03-26 14:58:35', '2026-03-26 14:58:35'),
(206, 1, 'created', 'Course', 3, 'Created course AQUA 1135 (Fisheries Biotechnology) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-26 15:02:14', '2026-03-26 15:02:14', '2026-03-26 15:02:14'),
(207, 16, 'registered', 'Authentication', 16, 'New user registered: Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph).', '2026-03-26 15:20:54', '2026-03-26 15:20:54', '2026-03-26 15:20:54'),
(208, 16, 'login', 'Authentication', 16, 'User Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph) logged in.', '2026-03-26 15:24:04', '2026-03-26 15:24:04', '2026-03-26 15:24:04'),
(209, 1, 'approved_by_cleared', 'Syllabus', 2, 'Cleared approved-by on syllabus #2.', '2026-03-26 15:58:17', '2026-03-26 15:58:17', '2026-03-26 15:58:17'),
(210, 1, 'approved_by_cleared', 'Syllabus', 2, 'Cleared approved-by on syllabus #2.', '2026-03-26 15:59:02', '2026-03-26 15:59:02', '2026-03-26 15:59:02'),
(211, 16, 'login', 'Authentication', 16, 'User Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph) logged in.', '2026-03-27 13:44:33', '2026-03-27 13:44:33', '2026-03-27 13:44:33'),
(212, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-27 18:45:39', '2026-03-27 18:45:39', '2026-03-27 18:45:39'),
(213, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-03-27 18:46:09', '2026-03-27 18:46:09', '2026-03-27 18:46:09'),
(214, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-27 19:42:14', '2026-03-27 19:42:14', '2026-03-27 19:42:14'),
(215, 1, 'updated', 'Academic Calendar Event', 19, 'Updated non_teaching event \'Testing of Whole Week No Class\' on 2026-01-08 for 2025-2026 2nd semester.', '2026-03-27 19:48:05', '2026-03-27 19:48:05', '2026-03-27 19:48:05'),
(216, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-30 08:41:17', '2026-03-30 08:41:17', '2026-03-30 08:41:17'),
(217, 17, 'registered', 'Authentication', 17, 'New user registered: Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph).', '2026-03-30 09:15:44', '2026-03-30 09:15:44', '2026-03-30 09:15:44'),
(218, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-03-30 09:17:00', '2026-03-30 09:17:00', '2026-03-30 09:17:00'),
(219, 1, 'approved', 'Account Approval', 17, 'Approved user Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph).', '2026-03-30 09:17:40', '2026-03-30 09:17:40', '2026-03-30 09:17:40'),
(220, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-03-30 09:18:08', '2026-03-30 09:18:08', '2026-03-30 09:18:08'),
(221, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-30 09:19:06', '2026-03-30 09:19:06', '2026-03-30 09:19:06'),
(222, 1, 'roles_updated', 'Account Approval', 17, 'Updated roles for Kiel Leonard Palaad. New roles: chair, faculty.', '2026-03-30 09:31:18', '2026-03-30 09:31:18', '2026-03-30 09:31:18'),
(223, 1, 'deleted', 'Course', 3, 'Deleted course AQUA 1135 (Fisheries Biotechnology) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 09:32:41', '2026-03-30 09:32:41', '2026-03-30 09:32:41'),
(224, 1, 'created', 'Course', 4, 'Created course AREM 1205 (Ichthyology) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 09:51:06', '2026-03-30 09:51:06', '2026-03-30 09:51:06'),
(225, 1, 'created', 'Course', 5, 'Created course AREM 2215 (Aquatic Ecology and Resources) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 09:54:05', '2026-03-30 09:54:05', '2026-03-30 09:54:05'),
(226, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-03-30 09:57:51', '2026-03-30 09:57:51', '2026-03-30 09:57:51'),
(227, 1, 'assigned', 'Organizational Hierarchy', 18, 'Assigned Kiel Leonard Palaad as chair of Department of Aquaculture.', '2026-03-30 09:58:10', '2026-03-30 09:58:10', '2026-03-30 09:58:10'),
(228, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-03-30 09:58:47', '2026-03-30 09:58:47', '2026-03-30 09:58:47'),
(229, 17, 'created', 'Course', 6, 'Created course AREM 2235 (Fisheries Meteorology) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 09:59:37', '2026-03-30 09:59:37', '2026-03-30 09:59:37'),
(230, 17, 'created', 'Course', 7, 'Created course AREM 2230 (Oceanography) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 10:01:21', '2026-03-30 10:01:21', '2026-03-30 10:01:21'),
(231, 17, 'created', 'Course', 8, 'Created course AREM 2200 (Capture Fisheries) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 10:05:36', '2026-03-30 10:05:36', '2026-03-30 10:05:36'),
(232, 17, 'created', 'Course', 9, 'Created course AREM 2265 (Fisheries Laws, Policies and Institutions) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 10:07:41', '2026-03-30 10:07:41', '2026-03-30 10:07:41'),
(233, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-03-30 13:11:52', '2026-03-30 13:11:52', '2026-03-30 13:11:52'),
(234, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-30 13:34:23', '2026-03-30 13:34:23', '2026-03-30 13:34:23'),
(235, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-03-30 13:34:34', '2026-03-30 13:34:34', '2026-03-30 13:34:34'),
(236, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-03-30 13:34:47', '2026-03-30 13:34:47', '2026-03-30 13:34:47'),
(237, 17, 'created', 'Course', 10, 'Created course AQUA 3115 (Aquaculture) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:37:30', '2026-03-30 13:37:30', '2026-03-30 13:37:30'),
(238, 17, 'created', 'Course', 11, 'Created course AREM 3225 (Research Design and Methodologies) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:39:51', '2026-03-30 13:39:51', '2026-03-30 13:39:51'),
(239, 17, 'created', 'Course', 12, 'Created course AQUA 3120 (Fish Nutrition) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:41:12', '2026-03-30 13:41:12', '2026-03-30 13:41:12'),
(240, 17, 'created', 'Course', 13, 'Created course AQUA 3125 (Fish Health Management) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:44:08', '2026-03-30 13:44:08', '2026-03-30 13:44:08'),
(241, 17, 'created', 'Course', 14, 'Created course AREM 4220 (Fisheries Entrepreneurship) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:45:31', '2026-03-30 13:45:31', '2026-03-30 13:45:31'),
(242, 17, 'created', 'Course', 15, 'Created course AQUA 4130 (Fish Breeding and Hatchery Management) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:46:39', '2026-03-30 13:46:39', '2026-03-30 13:46:39'),
(243, 17, 'created', 'Course', 16, 'Created course AQPH 4315 (Post-harvest Handling and Low Temperature Preservation) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:47:36', '2026-03-30 13:47:36', '2026-03-30 13:47:36'),
(244, 17, 'created', 'Course', 17, 'Created course AQPH 4320 (Fish Processing Plant Management) for program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-03-30 13:48:37', '2026-03-30 13:48:37', '2026-03-30 13:48:37'),
(245, 1, 'approved', 'Account Approval', 16, 'Approved user Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph).', '2026-03-30 15:02:25', '2026-03-30 15:02:25', '2026-03-30 15:02:25'),
(246, 1, 'roles_updated', 'Account Approval', 17, 'Updated roles for Kiel Leonard Palaad. New roles: faculty.', '2026-03-30 15:02:33', '2026-03-30 15:02:33', '2026-03-30 15:02:33'),
(247, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-30 21:08:57', '2026-03-30 21:08:57', '2026-03-30 21:08:57'),
(248, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-31 08:33:02', '2026-03-31 08:33:02', '2026-03-31 08:33:02'),
(249, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-31 08:37:51', '2026-03-31 08:37:51', '2026-03-31 08:37:51'),
(250, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-03-31 08:42:32', '2026-03-31 08:42:32', '2026-03-31 08:42:32'),
(251, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-31 08:43:32', '2026-03-31 08:43:32', '2026-03-31 08:43:32'),
(252, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-31 18:13:26', '2026-03-31 18:13:26', '2026-03-31 18:13:26'),
(253, 1, 'disabled', 'Account Approval', 4, 'Disabled user Faculty User (faculty@csms.local).', '2026-03-31 18:18:50', '2026-03-31 18:18:50', '2026-03-31 18:18:50'),
(254, 1, 'updated', 'Account Approval', 4, 'Admin edited user Faculty User (faculty@csms.local).', '2026-03-31 18:19:23', '2026-03-31 18:19:23', '2026-03-31 18:19:23'),
(255, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-03-31 18:28:36', '2026-03-31 18:28:36', '2026-03-31 18:28:36'),
(256, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-03-31 18:30:13', '2026-03-31 18:30:13', '2026-03-31 18:30:13'),
(257, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-03-31 18:33:29', '2026-03-31 18:33:29', '2026-03-31 18:33:29'),
(258, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-03-31 18:34:13', '2026-03-31 18:34:13', '2026-03-31 18:34:13'),
(259, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-03-31 18:36:53', '2026-03-31 18:36:53', '2026-03-31 18:36:53'),
(260, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-03-31 18:38:17', '2026-03-31 18:38:17', '2026-03-31 18:38:17'),
(261, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-03-31 18:41:28', '2026-03-31 18:41:28', '2026-03-31 18:41:28'),
(262, 18, 'logout', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged out.', '2026-03-31 18:44:01', '2026-03-31 18:44:01', '2026-03-31 18:44:01'),
(263, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-04 18:04:30', '2026-04-04 18:04:30', '2026-04-04 18:04:30'),
(264, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-04-04 18:05:35', '2026-04-04 18:05:35', '2026-04-04 18:05:35'),
(265, 16, 'login', 'Authentication', 16, 'User Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph) logged in.', '2026-04-06 18:08:42', '2026-04-06 18:08:42', '2026-04-06 18:08:42'),
(266, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-06 18:21:08', '2026-04-06 18:21:08', '2026-04-06 18:21:08'),
(267, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-04-06 18:45:35', '2026-04-06 18:45:35', '2026-04-06 18:45:35'),
(268, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-07 07:41:09', '2026-04-07 07:41:09', '2026-04-07 07:41:09'),
(269, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-04-07 08:26:45', '2026-04-07 08:26:45', '2026-04-07 08:26:45'),
(270, 1, 'saved_version', 'Syllabus', 1, 'Saved syllabus version v3 for course COMSCI 1100 (2025-2026 2nd).', '2026-04-07 09:08:27', '2026-04-07 09:08:27', '2026-04-07 09:08:27'),
(271, 1, 'saved_version', 'Syllabus', 1, 'Saved syllabus version v4 for course COMSCI 1100 (2025-2026 2nd).', '2026-04-07 09:10:36', '2026-04-07 09:10:36', '2026-04-07 09:10:36'),
(272, 1, 'saved_version', 'Syllabus', 1, 'Saved syllabus version v5 for course COMSCI 1100 (2025-2026 2nd).', '2026-04-07 09:21:27', '2026-04-07 09:21:27', '2026-04-07 09:21:27'),
(273, 17, 'approved_by_cleared', 'Syllabus', 4, 'Cleared approved-by on syllabus #4.', '2026-04-07 10:41:09', '2026-04-07 10:41:09', '2026-04-07 10:41:09'),
(274, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-04-07 13:03:01', '2026-04-07 13:03:01', '2026-04-07 13:03:01'),
(275, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-04-07 13:43:59', '2026-04-07 13:43:59', '2026-04-07 13:43:59'),
(276, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-07 13:46:10', '2026-04-07 13:46:10', '2026-04-07 13:46:10'),
(277, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-04-07 13:46:52', '2026-04-07 13:46:52', '2026-04-07 13:46:52'),
(278, 3, 'login', 'Authentication', 3, 'User College Dean (dean@csms.local) logged in.', '2026-04-07 13:47:47', '2026-04-07 13:47:47', '2026-04-07 13:47:47'),
(279, 2, 'login', 'Authentication', 2, 'User Department Chair (chair@csms.local) logged in.', '2026-04-07 13:51:08', '2026-04-07 13:51:08', '2026-04-07 13:51:08'),
(280, 1, 'removed', 'Organizational Hierarchy', 16, 'Removed Department Chair as faculty from Department of Information Technology.', '2026-04-07 13:53:29', '2026-04-07 13:53:29', '2026-04-07 13:53:29'),
(281, 1, 'assigned', 'Organizational Hierarchy', 18, 'Assigned Department Chair as chair of Department of Aquaculture.', '2026-04-07 13:54:01', '2026-04-07 13:54:01', '2026-04-07 13:54:01'),
(282, 18, 'created', 'Academic Calendar Event', 23, 'Created holiday event \'Valentine\'s Day\' on 2026-02-14 for 2025-2026 2nd semester.', '2026-04-07 14:00:06', '2026-04-07 14:00:06', '2026-04-07 14:00:06'),
(283, 18, 'deleted', 'Academic Calendar Event', 23, 'Deleted holiday event \'Valentine\'s Day\' on 2026-02-14 for 2025-2026 2nd semester.', '2026-04-07 14:00:15', '2026-04-07 14:00:15', '2026-04-07 14:00:15'),
(284, 3, 'created', 'Goal', 10, 'Created goal d for college College of Engineering.', '2026-04-07 14:02:01', '2026-04-07 14:02:01', '2026-04-07 14:02:01'),
(285, 3, 'deleted', 'Goal', 10, 'Deleted goal d for college College of Engineering.', '2026-04-07 14:02:13', '2026-04-07 14:02:13', '2026-04-07 14:02:13'),
(286, 2, 'saved', 'PEO', 2, 'Saved PEOs for Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-04-07 14:23:33', '2026-04-07 14:23:33', '2026-04-07 14:23:33'),
(287, 2, 'deleted', 'PEO', 10, 'Deleted d from Bachelor of Science in Fisheries (BSFi) and re-sequenced PEO codes.', '2026-04-07 14:23:43', '2026-04-07 14:23:43', '2026-04-07 14:23:43'),
(288, 2, 'updated', 'Course', 16, 'Updated course AQPH 4315 (Post-harvest Handling and Low Temperature Preservation); program Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-04-07 14:27:25', '2026-04-07 14:27:25', '2026-04-07 14:27:25'),
(289, 18, 'created', 'Academic Calendar Event', 24, 'Created holiday event \'Valentine\'s Day \' on 2026-02-14 for 2025-2026 2nd semester.', '2026-04-07 15:12:24', '2026-04-07 15:12:24', '2026-04-07 15:12:24'),
(290, 3, 'created', 'Goal', 11, 'Created goal d for college College of Engineering.', '2026-04-07 15:14:48', '2026-04-07 15:14:48', '2026-04-07 15:14:48'),
(291, 3, 'updated', 'Goal', 11, 'Updated goal d for college College of Engineering.', '2026-04-07 15:14:58', '2026-04-07 15:14:58', '2026-04-07 15:14:58'),
(292, 3, 'updated', 'Goal', 11, 'Updated goal d for college College of Engineering.', '2026-04-07 15:14:58', '2026-04-07 15:14:58', '2026-04-07 15:14:58'),
(293, 3, 'deleted', 'Goal', 11, 'Deleted goal d for college College of Engineering.', '2026-04-07 15:15:05', '2026-04-07 15:15:05', '2026-04-07 15:15:05'),
(294, 2, 'created', 'Objective', 10, 'Created objective d for department Department of Aquaculture, college College of Fisheries.', '2026-04-07 15:17:11', '2026-04-07 15:17:11', '2026-04-07 15:17:11'),
(295, 2, 'deleted', 'Objective', 10, 'Deleted objective d for department Department of Aquaculture, college College of Fisheries.', '2026-04-07 15:17:20', '2026-04-07 15:17:20', '2026-04-07 15:17:20'),
(296, 2, 'saved', 'PO', 2, 'Saved POs for Bachelor of Science in Fisheries (BSFi); college: College of Fisheries; department: Department of Aquaculture.', '2026-04-07 15:25:15', '2026-04-07 15:25:15', '2026-04-07 15:25:15'),
(297, 17, 'logout', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged out.', '2026-04-07 16:29:59', '2026-04-07 16:29:59', '2026-04-07 16:29:59'),
(298, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-04-08 13:18:39', '2026-04-08 13:18:39', '2026-04-08 13:18:39'),
(299, 2, 'login', 'Authentication', 2, 'User Department Chair (chair@csms.local) logged in.', '2026-04-08 13:21:52', '2026-04-08 13:21:52', '2026-04-08 13:21:52'),
(300, 3, 'login', 'Authentication', 3, 'User College Dean (dean@csms.local) logged in.', '2026-04-08 13:22:51', '2026-04-08 13:22:51', '2026-04-08 13:22:51'),
(301, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-04-08 13:23:07', '2026-04-08 13:23:07', '2026-04-08 13:23:07'),
(302, 3, 'login', 'Authentication', 3, 'User College Dean (dean@csms.local) logged in.', '2026-04-08 13:57:43', '2026-04-08 13:57:43', '2026-04-08 13:57:43'),
(303, 3, 'logout', 'Authentication', 3, 'User College Dean (dean@csms.local) logged out.', '2026-04-08 13:57:58', '2026-04-08 13:57:58', '2026-04-08 13:57:58'),
(304, 3, 'created', 'Goal', 12, 'Created goal d for college College of Engineering.', '2026-04-08 14:23:23', '2026-04-08 14:23:23', '2026-04-08 14:23:23'),
(305, 3, 'updated', 'Goal', 12, 'Updated goal d for college College of Engineering.', '2026-04-08 14:23:34', '2026-04-08 14:23:34', '2026-04-08 14:23:34'),
(306, 3, 'deleted', 'Goal', 12, 'Deleted goal d for college College of Engineering.', '2026-04-08 14:23:39', '2026-04-08 14:23:39', '2026-04-08 14:23:39'),
(307, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-08 21:30:56', '2026-04-08 21:30:56', '2026-04-08 21:30:56'),
(308, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-10 19:22:52', '2026-04-10 19:22:52', '2026-04-10 19:22:52'),
(309, 16, 'login', 'Authentication', 16, 'User Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph) logged in.', '2026-04-10 19:41:50', '2026-04-10 19:41:50', '2026-04-10 19:41:50'),
(310, 16, 'logout', 'Authentication', 16, 'User Jeric J Dela Cruz (jeric.delacruz@clsu2.edu.ph) logged out.', '2026-04-10 19:42:20', '2026-04-10 19:42:20', '2026-04-10 19:42:20'),
(311, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-10 19:42:27', '2026-04-10 19:42:27', '2026-04-10 19:42:27'),
(312, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-14 18:27:54', '2026-04-14 18:27:54', '2026-04-14 18:27:54'),
(313, 1, 'logout', 'Authentication', 1, 'User Admin User (admin@csms.local) logged out.', '2026-04-14 19:04:14', '2026-04-14 19:04:14', '2026-04-14 19:04:14'),
(314, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-15 12:26:37', '2026-04-15 12:26:37', '2026-04-15 12:26:37'),
(315, 3, 'login', 'Authentication', 3, 'User College Dean (dean@csms.local) logged in.', '2026-04-15 12:28:14', '2026-04-15 12:28:14', '2026-04-15 12:28:14'),
(316, 2, 'login', 'Authentication', 2, 'User Department Chair (chair@csms.local) logged in.', '2026-04-15 12:28:30', '2026-04-15 12:28:30', '2026-04-15 12:28:30'),
(317, 18, 'login', 'Authentication', 18, 'User OVPAA User (ovpaa@csms.local) logged in.', '2026-04-15 12:28:50', '2026-04-15 12:28:50', '2026-04-15 12:28:50'),
(318, 17, 'login', 'Authentication', 17, 'User Kiel Leonard Palaad (kiel.palaad@clsu2.edu.ph) logged in.', '2026-04-15 12:29:00', '2026-04-15 12:29:00', '2026-04-15 12:29:00'),
(319, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-15 16:41:45', '2026-04-15 16:41:45', '2026-04-15 16:41:45'),
(320, 1, 'login', 'Authentication', 1, 'User Admin User (admin@csms.local) logged in.', '2026-04-17 11:22:20', '2026-04-17 11:22:20', '2026-04-17 11:22:20');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'College of Agriculture', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(2, 'College of Arts and Social Sciences', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(3, 'College of Business and Accountancy', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(4, 'College of Education', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(5, 'College of Engineering', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(6, 'College of Fisheries', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(7, 'College of Home Science and Insdustry', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(8, 'College of Veterinary Science and Medicine', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(9, 'College of Science', '2026-03-16 01:32:30', '2026-03-16 01:32:30');

-- --------------------------------------------------------

--
-- Table structure for table `college_goals`
--

CREATE TABLE `college_goals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `college_id` bigint(20) UNSIGNED NOT NULL,
  `college_goals_code` varchar(255) NOT NULL,
  `goal_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `college_goals`
--

INSERT INTO `college_goals` (`id`, `college_id`, `college_goals_code`, `goal_text`, `created_at`, `updated_at`) VALUES
(1, 5, 'a', 'To provide quality education and apply engineering and technology knowledge that anticipates and addresses future and relevant agro-industrial needs, preparing students for global practice.', '2026-03-16 01:35:16', '2026-03-16 01:41:25'),
(2, 5, 'b', 'To engage in research with significant socio-economic impact, contributing to sustainable development, and disseminate the results of these research efforts.', '2026-03-16 01:35:34', '2026-03-16 01:41:42'),
(3, 5, 'c', 'To share the vast store of knowledge and technology with the community and stakeholders to make the Philippines and the world a better place.', '2026-03-16 01:35:47', '2026-03-16 01:41:55'),
(5, 6, 'a', 'Produce globally competitive fisheries graduates who are knowledgeable and skilled in sustainable aquaculture, capture fisheries, aquatic resource management, and post-harvest technologies aligned with national and ASEAN standards.', '2026-03-26 09:28:36', '2026-03-26 09:39:01'),
(6, 6, 'b', 'Develop professionals with strong ethical values and integrity who demonstrate nationalism, responsibility, and commitment to sustainable fisheries development and environmental stewardship.', '2026-03-26 09:28:50', '2026-03-26 09:28:50'),
(7, 6, 'c', 'Prepare graduates to become leaders and innovators in fisheries research, education, community development, and industry practices that support national and regional fisheries advancement.', '2026-03-26 09:28:57', '2026-03-26 09:28:57'),
(8, 6, 'd', 'Strengthen students’ competencies in research, communication, and resource management through outcomes-based education, enhanced general education courses, and practical training aligned with CHED policies and curriculum standards.', '2026-03-26 09:29:05', '2026-03-26 09:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `complete_syllabi`
--

CREATE TABLE `complete_syllabi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `semester` varchar(255) NOT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `evaluation_path` varchar(255) DEFAULT NULL,
  `abridged_path` varchar(255) DEFAULT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `checksum` varchar(255) DEFAULT NULL,
  `checksum_evaluation` varchar(64) DEFAULT NULL,
  `checksum_abridged` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complete_syllabi`
--

INSERT INTO `complete_syllabi` (`id`, `syllabus_id`, `course_id`, `academic_year`, `semester`, `pdf_path`, `evaluation_path`, `abridged_path`, `version`, `approved_at`, `approved_by`, `checksum`, `checksum_evaluation`, `checksum_abridged`, `created_at`, `updated_at`) VALUES
(31, 1, 1, '2025-2026', '2nd', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v1 (2025-2026 2nd)/Complete - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v1 (2025-2026 2nd)/Assessment - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v1 (2025-2026 2nd)/Abridged - COMSCI 1100.html', 1, NULL, NULL, 'cdf64cc0997ab9e4e9aab572b3fc6af01248516919c9f817598ad2241b434369', 'cf74599a2393144e8cfcbdd73ac8bec495da172cf68dc35b7050d1802e17e317', '49c8c62f995360e1d24253df69d3ddf663d6c2add1b643e1b027a1e58ef95018', '2026-03-24 13:00:06', '2026-03-24 13:00:06'),
(32, 1, 1, '2025-2026', '2nd', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v2 (2025-2026 2nd)/Complete - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v2 (2025-2026 2nd)/Assessment - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v2 (2025-2026 2nd)/Abridged - COMSCI 1100.html', 2, NULL, NULL, 'cdf64cc0997ab9e4e9aab572b3fc6af01248516919c9f817598ad2241b434369', 'cf74599a2393144e8cfcbdd73ac8bec495da172cf68dc35b7050d1802e17e317', '49c8c62f995360e1d24253df69d3ddf663d6c2add1b643e1b027a1e58ef95018', '2026-03-25 15:43:58', '2026-03-25 15:43:58'),
(33, 1, 1, '2025-2026', '2nd', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v3 (2025-2026 2nd)/Complete - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v3 (2025-2026 2nd)/Assessment - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v3 (2025-2026 2nd)/Abridged - COMSCI 1100.html', 3, NULL, NULL, '33e1ced4c40c707ded49ffbd9e8b624301499edb6aa06a81a865f37cc03db03f', '887f46b5424f0cd6f0f357284da51c0608807df7560a517f0fbb691c5fce999d', '636a76da6844171e9dcd3a83ddae3e9c4b176e4d1beed531e9681132c0871e87', '2026-04-07 09:08:27', '2026-04-07 09:08:27'),
(34, 1, 1, '2025-2026', '2nd', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v4 (2025-2026 2nd)/Complete - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v4 (2025-2026 2nd)/Assessment - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v4 (2025-2026 2nd)/Abridged - COMSCI 1100.html', 4, NULL, NULL, '33e1ced4c40c707ded49ffbd9e8b624301499edb6aa06a81a865f37cc03db03f', '887f46b5424f0cd6f0f357284da51c0608807df7560a517f0fbb691c5fce999d', '636a76da6844171e9dcd3a83ddae3e9c4b176e4d1beed531e9681132c0871e87', '2026-04-07 09:10:36', '2026-04-07 09:10:36'),
(35, 1, 1, '2025-2026', '2nd', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v5 (2025-2026 2nd)/Complete - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v5 (2025-2026 2nd)/Assessment - COMSCI 1100.html', 'Syllabus Snapshots/College of Engineering/Department of Information Technology/Bachelor of Science in Information Technology (BSIT)/Admin User/COMSCI 1100/v5 (2025-2026 2nd)/Abridged - COMSCI 1100.html', 5, NULL, NULL, '33e1ced4c40c707ded49ffbd9e8b624301499edb6aa06a81a865f37cc03db03f', '887f46b5424f0cd6f0f357284da51c0608807df7560a517f0fbb691c5fce999d', '636a76da6844171e9dcd3a83ddae3e9c4b176e4d1beed531e9681132c0871e87', '2026-04-07 09:21:27', '2026-04-07 09:21:27');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `course_title` varchar(255) NOT NULL,
  `course_description` text DEFAULT NULL,
  `credit_units` tinyint(3) UNSIGNED NOT NULL,
  `has_lec_lab` tinyint(1) NOT NULL DEFAULT 0,
  `year_level` tinyint(3) UNSIGNED NOT NULL,
  `semester` tinyint(3) UNSIGNED NOT NULL,
  `prerequisite` varchar(255) DEFAULT NULL,
  `corequisite` varchar(255) DEFAULT NULL,
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `version` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `program_id`, `course_code`, `course_title`, `course_description`, `credit_units`, `has_lec_lab`, `year_level`, `semester`, `prerequisite`, `corequisite`, `status`, `version`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'COMSCI 1100', 'Introduction to Computing', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', 3, 1, 1, 1, 'None', 'None', 'active', 1, 1, '2026-03-16 03:36:57', '2026-03-19 00:36:34'),
(4, 2, 'AREM 1205', 'Ichthyology', 'Biology, classification and life histories of commercially important fishes.', 5, 1, 1, 1, 'Zoology', 'None', 'active', 1, 1, '2026-03-30 09:51:06', '2026-03-30 09:51:06'),
(5, 2, 'AREM 2215', 'Aquatic Ecology and Resources', 'Basic ecological principles in freshwater, estuarine and marine environments. Physico-chemical and biological characteristics of the three environments governing the association, distribution and adaptation of aquatic organisms. Knowledge of techniques in water quality sampling and analysis.', 5, 1, 2, 1, 'Botany and Zoology', 'none', 'active', 1, 1, '2026-03-30 09:54:05', '2026-03-30 09:54:05'),
(6, 2, 'AREM 2235', 'Fisheries Meteorology', 'Elements of weather–forecasting and techniques as applied to fisheries investigation.', 3, 0, 2, 1, 'None', 'None', 'active', 1, 17, '2026-03-30 09:59:37', '2026-03-30 09:59:37'),
(7, 2, 'AREM 2230', 'Oceanography', 'Geological, physical, chemical and biological properties of ocean waters to include pertinent fishery and environmental laws.', 3, 0, 2, 1, 'Organic Chemistry and Inorganic Analytical Chemistry', 'None', 'active', 1, 17, '2026-03-30 10:01:21', '2026-03-30 10:01:21'),
(8, 2, 'AREM 2200', 'Capture Fisheries', 'The principles and fundamentals of commercial fisheries and its fish biology and ecology, with emphasis on fisheries, management and conservation to include pertinent fishery laws and Fisheries Administrative Orders (FAOs). General survey of Philippine fishing grounds including the status of fisheries resources and their utilization.', 3, 0, 2, 1, 'None', 'None', 'active', 1, 17, '2026-03-30 10:05:36', '2026-03-30 10:05:36'),
(9, 2, 'AREM 2265', 'Fisheries Laws, Policies and Institutions', 'Laws, regulations, policies and orders affecting fisheries.', 3, 0, 2, 2, 'None', 'None', 'active', 1, 17, '2026-03-30 10:07:41', '2026-03-30 10:07:41'),
(10, 2, 'AQUA 3115', 'Aquaculture', 'Application of farming systems of selected species, project and case studies; and aquaculture planning and development.', 5, 1, 3, 1, 'Ichthyology and Physiology of Aquatic Organisms', 'None', 'active', 1, 17, '2026-03-30 13:37:30', '2026-03-30 13:37:30'),
(11, 2, 'AREM 3225', 'Research Design and Methodologies', 'Statistical designs, approaches, data analysis and case examples of researches in fisheries.', 3, 0, 3, 1, 'None', 'None', 'active', 1, 17, '2026-03-30 13:39:51', '2026-03-30 13:39:51'),
(12, 2, 'AQUA 3120', 'Fish Nutrition', 'Principles of nutrition, nutrient requirements, feed formulation and preparation, and feeding management applied to finfishes and crustaceans.', 5, 1, 3, 1, 'Ichthyology and Organic Chemistry', 'None', 'active', 1, 17, '2026-03-30 13:41:12', '2026-03-30 13:41:12'),
(13, 2, 'AQUA 3125', 'Fish Health Management', 'Identification of organisms and other factors causing diseases in commercially important finfishes and invertebrates; principles and measures of prevention and control.', 5, 1, 3, 1, 'Microbiology and Ichthyology', 'None', 'active', 1, 17, '2026-03-30 13:44:08', '2026-03-30 13:44:08'),
(14, 2, 'AREM 4220', 'Fisheries Entrepreneurship', 'Principles and practices of managing fisheries and aquaculture enterprises including case and feasibility studies, and introduction to fisheries cooperative principles and practices.', 3, 0, 4, 1, 'Ichthyology and Aquaculture', 'None', 'active', 1, 17, '2026-03-30 13:45:31', '2026-03-30 13:45:31'),
(15, 2, 'AQUA 4130', 'Fish Breeding and Hatchery Management', 'Fundamentals and techniques of fish breeding and propagation, hatchery and nursery operations of commercially important finfishes and invertebrates.', 5, 1, 4, 1, 'Ichthyology, Biochemistry and Aquaculture', 'None', 'active', 1, 17, '2026-03-30 13:46:39', '2026-03-30 13:46:39'),
(16, 2, 'AQPH 4315', 'Post-harvest Handling and Low Temperature Preservation', 'Principles and techniques of handling and refrigeration of processing fish and other fishery products.', 5, 1, 4, 1, 'Post-harvest fisheries &Fisheries Product, Safety and  Quality', 'None', 'active', 1, 17, '2026-03-30 13:47:36', '2026-04-07 14:27:25'),
(17, 2, 'AQPH 4320', 'Fish Processing Plant Management', 'Principles of plant sanitation and personal hygiene; good manufacturing practice.', 5, 1, 4, 1, 'Post-harvest fisheries &Fisheries Product, Safety and Quality', 'None', 'active', 1, 17, '2026-03-30 13:48:37', '2026-03-30 13:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `course_components`
--

CREATE TABLE `course_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('LEC','LAB') NOT NULL,
  `class_hours` varchar(255) NOT NULL,
  `schedule` text DEFAULT NULL,
  `instructor_name` varchar(255) NOT NULL,
  `instructor_email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `office` varchar(255) DEFAULT NULL,
  `consultation_hours` varchar(255) DEFAULT NULL,
  `performance_standard` decimal(5,2) NOT NULL DEFAULT 50.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_components`
--

INSERT INTO `course_components` (`id`, `syllabus_id`, `type`, `class_hours`, `schedule`, `instructor_name`, `instructor_email`, `phone`, `office`, `consultation_hours`, `performance_standard`, `created_at`, `updated_at`) VALUES
(1, 1, 'LEC', '1 hr and 30 min', 'TBA', 'Admin User', 'admin@csms.local', NULL, 'CLIRDEC', 'TBA', 60.00, '2026-03-16 05:51:21', '2026-04-10 19:24:16'),
(2, 1, 'LAB', '3 hr', 'TBA', 'Kiel Palaad', 'kiel@gmail.com', NULL, 'CLIRDEC', 'TBA', 60.00, '2026-03-16 05:51:22', '2026-04-10 19:24:16'),
(4, 3, 'LEC', '1 hr', 'M - 1-2pm | TH 3-4pm', 'Kiel Leonard Palaad', 'kiel.palaad@clsu2.edu.ph', '09697886767', 'Office for Learning Outcomes and Innovation', 'TH, F - 1pm to 4pm', 60.00, '2026-03-30 15:04:54', '2026-04-07 09:06:40'),
(5, 3, 'LAB', '1 hr', 'T - 1-2pm | F - 3-4pm', 'Jeric J. Dela Cruz', 'sample@clsu2.edu.ph', NULL, 'Office for Learning Outcomes and Innovation', 'TH, F - 1pm to 4pm', 60.00, '2026-04-07 08:52:51', '2026-04-07 09:06:40'),
(6, 4, 'LEC', '1 hr', NULL, 'Kiel Leonard Palaad', 'kiel.palaad@clsu2.edu.ph', '09697886767', 'Office for Learning Outcomes and Innovation', NULL, 60.00, '2026-04-07 09:50:04', '2026-04-15 13:23:29'),
(7, 5, 'LEC', '1 hr', 'M - 1pm - 4pm | TH - 1pm - 4pm', 'Kiel Leonard Palaad', 'kiel.palaad@clsu2.edu.ph', '09697886767', 'Office for Learning Outcomes and Innovation', 'TH, F - 1pm - 4pm', 60.00, '2026-04-07 15:38:40', '2026-04-08 14:30:02'),
(8, 6, 'LEC', '1 hr', NULL, 'Admin User', 'admin@csms.local', NULL, NULL, NULL, 60.00, '2026-04-10 19:43:06', '2026-04-17 11:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `course_curriculum_maps`
--

CREATE TABLE `course_curriculum_maps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `program_outcome_id` bigint(20) UNSIGNED NOT NULL,
  `ied` enum('I','E','D') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_curriculum_maps`
--

INSERT INTO `course_curriculum_maps` (`id`, `course_id`, `program_outcome_id`, `ied`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'I', NULL, NULL),
(2, 1, 3, 'I', NULL, NULL),
(3, 1, 5, 'I', NULL, NULL),
(4, 1, 6, 'E', NULL, NULL),
(5, 1, 8, 'I', NULL, NULL),
(18, 4, 11, 'I', NULL, NULL),
(19, 4, 12, 'I', NULL, NULL),
(20, 4, 13, 'I', NULL, NULL),
(21, 4, 15, 'I', NULL, NULL),
(22, 4, 17, 'I', NULL, NULL),
(23, 4, 20, 'I', NULL, NULL),
(24, 5, 10, 'E', NULL, NULL),
(25, 5, 11, 'E', NULL, NULL),
(26, 5, 12, 'E', NULL, NULL),
(27, 5, 13, 'E', NULL, NULL),
(28, 5, 14, 'E', NULL, NULL),
(29, 5, 15, 'E', NULL, NULL),
(30, 5, 16, 'E', NULL, NULL),
(31, 5, 17, 'E', NULL, NULL),
(32, 5, 18, 'E', NULL, NULL),
(33, 5, 20, 'E', NULL, NULL),
(34, 6, 11, 'E', NULL, NULL),
(35, 6, 12, 'E', NULL, NULL),
(36, 6, 18, 'E', NULL, NULL),
(37, 6, 20, 'E', NULL, NULL),
(38, 7, 11, 'E', NULL, NULL),
(39, 7, 12, 'E', NULL, NULL),
(40, 7, 18, 'E', NULL, NULL),
(41, 7, 20, 'E', NULL, NULL),
(42, 8, 10, 'E', NULL, NULL),
(43, 8, 11, 'E', NULL, NULL),
(44, 8, 12, 'E', NULL, NULL),
(45, 8, 13, 'E', NULL, NULL),
(46, 8, 14, 'E', NULL, NULL),
(47, 8, 15, 'E', NULL, NULL),
(48, 8, 16, 'E', NULL, NULL),
(49, 8, 17, 'E', NULL, NULL),
(50, 8, 18, 'E', NULL, NULL),
(51, 8, 20, 'E', NULL, NULL),
(52, 9, 10, 'E', NULL, NULL),
(53, 9, 11, 'E', NULL, NULL),
(54, 9, 12, 'E', NULL, NULL),
(55, 9, 13, 'E', NULL, NULL),
(56, 9, 14, 'E', NULL, NULL),
(57, 9, 15, 'E', NULL, NULL),
(58, 9, 16, 'E', NULL, NULL),
(59, 9, 17, 'E', NULL, NULL),
(60, 9, 18, 'E', NULL, NULL),
(61, 9, 20, 'E', NULL, NULL),
(62, 10, 10, 'E', NULL, NULL),
(63, 10, 11, 'E', NULL, NULL),
(64, 10, 12, 'E', NULL, NULL),
(65, 10, 13, 'E', NULL, NULL),
(66, 10, 14, 'E', NULL, NULL),
(67, 10, 15, 'E', NULL, NULL),
(68, 10, 16, 'E', NULL, NULL),
(69, 10, 17, 'E', NULL, NULL),
(70, 10, 18, 'E', NULL, NULL),
(71, 10, 20, 'E', NULL, NULL),
(72, 11, 11, 'E', NULL, NULL),
(73, 11, 12, 'E', NULL, NULL),
(74, 12, 10, 'E', NULL, NULL),
(75, 12, 11, 'E', NULL, NULL),
(76, 12, 12, 'E', NULL, NULL),
(77, 12, 13, 'E', NULL, NULL),
(78, 12, 14, 'E', NULL, NULL),
(79, 12, 15, 'E', NULL, NULL),
(80, 12, 16, 'E', NULL, NULL),
(81, 12, 17, 'E', NULL, NULL),
(82, 12, 18, 'E', NULL, NULL),
(83, 12, 20, 'E', NULL, NULL),
(84, 14, 10, 'D', NULL, NULL),
(85, 14, 11, 'D', NULL, NULL),
(86, 14, 12, 'D', NULL, NULL),
(87, 14, 13, 'D', NULL, NULL),
(88, 14, 14, 'D', NULL, NULL),
(89, 14, 15, 'D', NULL, NULL),
(90, 14, 16, 'D', NULL, NULL),
(91, 14, 17, 'D', NULL, NULL),
(92, 14, 18, 'D', NULL, NULL),
(93, 14, 19, 'D', NULL, NULL),
(94, 15, 10, 'D', NULL, NULL),
(95, 15, 11, 'D', NULL, NULL),
(96, 15, 12, 'D', NULL, NULL),
(97, 15, 13, 'D', NULL, NULL),
(98, 15, 14, 'D', NULL, NULL),
(99, 15, 15, 'D', NULL, NULL),
(100, 15, 16, 'D', NULL, NULL),
(101, 15, 17, 'D', NULL, NULL),
(102, 15, 18, 'D', NULL, NULL),
(103, 15, 20, 'D', NULL, NULL),
(104, 16, 10, 'D', NULL, NULL),
(105, 16, 11, 'D', NULL, NULL),
(106, 16, 12, 'D', NULL, NULL),
(107, 16, 13, 'D', NULL, NULL),
(108, 16, 16, 'D', NULL, NULL),
(109, 16, 19, 'D', NULL, NULL),
(110, 17, 10, 'D', NULL, NULL),
(111, 17, 11, 'D', NULL, NULL),
(112, 17, 12, 'D', NULL, NULL),
(113, 17, 13, 'D', NULL, NULL),
(114, 17, 16, 'D', NULL, NULL),
(115, 17, 19, 'D', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_outcomes`
--

CREATE TABLE `course_outcomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `co_code` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_outcomes`
--

INSERT INTO `course_outcomes` (`id`, `syllabus_id`, `co_code`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'CO1', 'Nulla est distinctio Nulla est distinctioNulla est distinctio Nulla est distinctio Nulla est distinctio v Nulla est distinctio Nulla est distinctio vNulla est distinctio', '2026-03-16 05:29:55', '2026-03-16 05:30:23'),
(2, 1, 'CO2', 'Nesciunt minim ad d Nobis id aspernaturNobis id aspernatur', '2026-03-16 05:30:02', '2026-03-16 05:30:43'),
(3, 1, 'CO3', 'Nobis id aspernatur Nobis id aspernatur Nobis id aspernatur Nobis id aspernatur Nobis id aspernatur', '2026-03-16 05:30:10', '2026-03-16 05:30:37'),
(4, 1, 'CO4', 'Culpa sunt sit sint Culpa sunt sit sint Culpa sunt sit sint  Nobis id aspernatur Nobis id aspernatur Nobis id aspernatur Nobis id aspernatur', '2026-03-18 01:27:55', '2026-03-20 05:33:50'),
(22, 3, 'CO1', 'Define and explain the basic concepts, principles, and scope of capture fisheries.', '2026-04-07 10:13:24', '2026-04-07 10:13:24'),
(23, 3, 'CO2', 'Describe fish biology and ecology relevant to capture fisheries operations.', '2026-04-07 10:13:35', '2026-04-07 10:13:35'),
(24, 3, 'CO3', 'Explain the different types of fishing gears, vessels, and commercial fishing methods.', '2026-04-07 10:13:45', '2026-04-07 10:13:45'),
(25, 3, 'CO4', 'Discuss the major Philippine fishing grounds, including their characteristics and resource status.', '2026-04-07 10:13:53', '2026-04-07 10:13:53'),
(26, 3, 'CO5', 'Analyze the current status, utilization, and challenges of fisheries resources in the Philippines.', '2026-04-07 10:14:02', '2026-04-07 10:14:02'),
(27, 3, 'CO6', 'Explain and interpret fisheries management principles, conservation strategies, and sustainability practices.', '2026-04-07 10:14:13', '2026-04-07 10:14:13'),
(28, 3, 'CO7', 'Identify and evaluate key fishery laws, regulations, and Fisheries Administrative Orders (FAOs).', '2026-04-07 10:14:25', '2026-04-07 10:14:25'),
(29, 3, 'CO8', 'Apply knowledge of fisheries management, laws, and ecological principles in proposing sustainable fisheries solutions.', '2026-04-07 10:14:36', '2026-04-07 10:14:36'),
(30, 4, 'CO1', 'Define and explain the basic concepts, principles, and scope of capture fisher', '2026-04-07 10:16:14', '2026-04-07 10:16:14'),
(31, 4, 'CO2', 'Describe fish biology and ecology relevant to capture fisheries operations.', '2026-04-07 10:16:21', '2026-04-07 10:16:21'),
(32, 4, 'CO3', 'Explain the different types of fishing gears, vessels, and commercial fishing methods.', '2026-04-07 10:16:30', '2026-04-07 10:16:30'),
(33, 4, 'CO4', 'Discuss the major Philippine fishing grounds, including their characteristics and resource status.', '2026-04-07 10:16:37', '2026-04-07 10:16:37'),
(34, 4, 'CO5', 'Analyze the current status, utilization, and challenges of fisheries resources in the Philippines.', '2026-04-07 10:16:45', '2026-04-07 10:16:45'),
(35, 4, 'CO6', 'Explain and interpret fisheries management principles, conservation strategies, and sustainability practices.', '2026-04-07 10:16:52', '2026-04-07 10:16:52'),
(36, 4, 'CO7', 'Identify and evaluate key fishery laws, regulations, and Fisheries Administrative Orders (FAOs).', '2026-04-07 10:17:02', '2026-04-07 10:17:02'),
(37, 4, 'CO8', 'Apply knowledge of fisheries management, laws, and ecological principles in proposing sustainable fisheries solutions.', '2026-04-07 10:17:11', '2026-04-07 10:17:11'),
(38, 5, 'CO1', 'Define and explain the basic concepts, principles, and scope of capture fisheries.', '2026-04-07 15:39:42', '2026-04-07 15:39:42'),
(39, 5, 'CO2', 'Describe fish biology and ecology relevant to capture fisheries operations.', '2026-04-07 15:41:29', '2026-04-07 15:41:29'),
(40, 6, 'CO1', 'gasgsaga', '2026-04-14 18:30:18', '2026-04-14 18:30:18');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `college_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `college_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'Department of Agricultural Extension Education', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(2, 1, 'Department of Agri-Management', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(3, 2, 'Department of Communication and Development Studies', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(4, 2, 'Department of English and Humanities', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(5, 2, 'Department of Filipino', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(6, 2, 'Department of Global Studies', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(7, 2, 'Department of Social Science', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(8, 2, 'Department of Psychology', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(9, 3, 'Department of Accountancy', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(10, 3, 'Department of Business', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(11, 4, 'Department of Early Childhood Education', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(12, 4, 'Department of Language, Culture, and Arts Education', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(13, 4, 'Department of Technology, Livelihood, and Life Skills Education', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(14, 4, 'Department of Science Education', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(15, 5, 'Department of Agricultural and Biosystems Engineering', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(16, 5, 'Department of Information Technology', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(17, 5, 'Department of Civil Engineering', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(18, 6, 'Department of Aquaculture', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(19, 6, 'Department of Aquatic Resources, Ecology, and Management', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(20, 7, 'Department of Food Science and Technology', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(21, 7, 'Department of Hospitality and Tourism', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(22, 7, 'Department of Textile and Garment Technology', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(23, 9, 'Department of Biological Sciences', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(24, 9, 'Department of Chemistry', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(25, 9, 'Department of Environmental Science and Meteorology', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(26, 9, 'Department of Mathemathics and Physics', '2026-03-16 01:32:30', '2026-03-16 01:32:30'),
(27, 9, 'Department of Statistics', '2026-03-16 01:32:30', '2026-03-16 01:32:30');

-- --------------------------------------------------------

--
-- Table structure for table `department_objectives`
--

CREATE TABLE `department_objectives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `dept_obj_code` varchar(255) NOT NULL,
  `objective_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_objectives`
--

INSERT INTO `department_objectives` (`id`, `department_id`, `dept_obj_code`, `objective_text`, `created_at`, `updated_at`) VALUES
(1, 16, 'a', 'To provide relevant and quality education in information and communications technology.', '2026-03-16 01:40:01', '2026-03-16 01:40:01'),
(2, 16, 'b', 'To generate innovative technologies and systems that upholds the global and national initiatives of bridging the digital divide in the diverse range of human experiences, in every sector of the society and the economy.', '2026-03-16 01:40:24', '2026-03-16 01:40:24'),
(3, 16, 'c', 'To provide technological expertise in the field of information technology for the country, the region and beyond.', '2026-03-16 01:40:35', '2026-03-16 01:40:35'),
(4, 18, 'a', 'To provide fisheries education that will enable the students and other stakeholders knowledgeable in global trends in fisheries and aquaculture.', '2026-03-26 09:54:26', '2026-03-26 09:55:38'),
(7, 18, 'b', 'To develop well rounded students with competencies in fisheries education, research, development, management and entrepreneurship as future active partners in national and international development.', '2026-03-26 09:56:06', '2026-03-26 09:56:06'),
(8, 18, 'c', 'To produce fisheries technologist and professionals who will develop, generate and manage aquaculture production systems, postharvest, aquatic resources and ecology.', '2026-03-26 09:56:16', '2026-03-26 09:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_19_000001_create_roles_table', 1),
(5, '2026_01_19_000002_create_user_roles_table', 1),
(6, '2026_01_21_000001_create_colleges_table', 1),
(7, '2026_01_21_000002_create_departments_table', 1),
(8, '2026_01_21_000003_create_college_goals_table', 1),
(9, '2026_01_21_000004_create_department_objectives_table', 1),
(10, '2026_01_21_000005_create_programs_table', 1),
(11, '2026_01_21_000006_create_program_departments_table', 1),
(12, '2026_01_27_000001_create_program_eos_table', 1),
(13, '2026_01_27_000002_create_program_outcomes_table', 1),
(14, '2026_01_27_000003_create_program_outcome_peo_table', 1),
(15, '2026_01_28_000001_create_academic_calendars_table', 1),
(16, '2026_01_28_000002_create_academic_calendar_events_table', 1),
(17, '2026_02_02_000001_create_courses_table', 1),
(18, '2026_02_02_000002_create_course_curriculum_maps_table', 1),
(19, '2026_02_04_000001_create_syllabi_table', 1),
(20, '2026_02_04_000002_create_syllabus_revisions_table', 1),
(21, '2026_02_04_000003_create_course_components_table', 1),
(22, '2026_02_05_000001_create_course_outcomes_table', 1),
(23, '2026_02_06_000001_create_user_assignments_table', 1),
(24, '2026_02_10_000001_create_syllabus_weeks_table', 1),
(25, '2026_02_20_000001_create_audit_logs_table', 1),
(26, '2026_02_23_000001_create_user_otps_table', 1),
(27, '2026_02_24_000001_create_syllabus_references_table', 1),
(28, '2026_02_24_000002_create_syllabus_materials_table', 1),
(29, '2026_02_25_000001_create_week_contents_table', 1),
(30, '2026_03_02_150000_create_syllabus_evaluation_items_table', 1),
(31, '2026_03_09_000001_create_complete_syllabi_table', 1),
(32, '2026_03_10_145552_create_syllabus_reviewers_table', 1),
(33, '2026_03_17_144432_add_abridged_columns_to_complete_syllabi', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `bor_approval_no` varchar(255) DEFAULT NULL,
  `bor_approval_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `bor_approval_no`, `bor_approval_date`, `created_at`, `updated_at`) VALUES
(1, 'Bachelor of Science in Information Technology (BSIT)', 'BOR Resolution No. 42-2018', '2018-09-13', '2026-03-16 01:44:30', '2026-03-16 01:44:30'),
(2, 'Bachelor of Science in Fisheries (BSFi)', 'BOR RESOLUTION NO.1604', '1976-01-01', '2026-03-26 10:04:10', '2026-03-26 10:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `program_departments`
--

CREATE TABLE `program_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('primary','supporting') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_departments`
--

INSERT INTO `program_departments` (`id`, `program_id`, `department_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 16, 'primary', '2026-03-16 01:44:30', '2026-03-16 01:44:30'),
(2, 2, 18, 'primary', '2026-03-26 10:04:10', '2026-03-26 10:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `program_eos`
--

CREATE TABLE `program_eos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `peo_code` varchar(255) DEFAULT NULL,
  `peo_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_eos`
--

INSERT INTO `program_eos` (`id`, `program_id`, `peo_code`, `peo_text`, `created_at`, `updated_at`) VALUES
(1, 1, 'a', 'Extensively trained professionals equipped with analytical, technical, interpersonal, leadership, and entrepreneurial competence, providing them with maximum flexibility to assume appropriate ICT leadership roles and to function effectively in multidisciplinary and diverse teams,', '2026-03-16 01:46:16', '2026-03-16 01:46:16'),
(2, 1, 'b', 'Cognizant of opportunities for personal and professional growth, participating in lifelong learning processes, whether on-the-job or in graduate school.', '2026-03-16 01:46:16', '2026-03-16 01:46:16'),
(3, 1, 'c', 'Adaptive to the changing work environment, staying abreast of technology trends and developments.', '2026-03-16 01:46:16', '2026-03-16 01:46:16'),
(4, 1, 'd', 'Capable of leading and conducting ICT-based solutions, taking into account the environmental, legal, ethical, moral, cultural, and societal implications of ICT projects and research.', '2026-03-16 01:46:16', '2026-03-16 01:46:16'),
(5, 1, 'e', 'Adept with a sense of professionalism and personal discipline, prepared to succeed in their chosen careers and become productive, responsible, and contributing global citizens.', '2026-03-16 01:46:16', '2026-03-16 01:46:16'),
(6, 2, 'a', 'Professionals that are able to apply the fundamental concept of fisheries and aquaculture sciences in their chosen career with strong sense of concern for environment and civic consciousness', '2026-03-26 10:24:37', '2026-03-26 10:24:37'),
(7, 2, 'b', 'Practicing professionals who are qualified and proficient in the development and application of appropriate fisheries technologies required to successfully perform their tasks within the accepted norms in the society', '2026-03-26 10:25:20', '2026-03-26 10:25:20'),
(9, 2, 'c', 'World-class human resources that utilize appropriate knowledge and competencies in fisheries, research, management and entrepreneurship and serve as active partners in national and international development in fisheries and aquaculture', '2026-03-26 10:25:46', '2026-03-26 10:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `program_outcomes`
--

CREATE TABLE `program_outcomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `po_code` varchar(255) DEFAULT NULL,
  `po_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_outcomes`
--

INSERT INTO `program_outcomes` (`id`, `program_id`, `po_code`, `po_text`, `created_at`, `updated_at`) VALUES
(1, 1, 'a', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(2, 1, 'b', 'Unde blanditiis et o Unde blanditiis et o  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(3, 1, 'c', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(4, 1, 'd', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blandiet o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(5, 1, 'e', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(6, 1, 'f', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan  Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(7, 1, 'g', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(8, 1, 'h', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(9, 1, 'i', 'Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blanditiis et o Unde blan 531 bfa', '2026-03-19 00:33:37', '2026-03-19 00:33:37'),
(10, 2, 'a', 'Articulate and discuss latest developments in fisheries and aquaculture.', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(11, 2, 'b', 'Effectively communicate orally and in writing.', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(12, 2, 'c', 'Work effectively and independently in multi-disciplinary and multiculture teams.', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(13, 2, 'd', 'Act in recognition of professional, social, and ethical responsibility.', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(14, 2, 'e', 'Preserve and promote “Filipino historical and cultural heritage”', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(15, 2, 'f', 'Create, generate and share knowledge as it apply to specific problems in fisheries and aquaculture', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(16, 2, 'g', 'Formulate sustainable and productive fisheries and aquaculture development plans', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(17, 2, 'h', 'Develop, operate and manage fisheries and aquaculture production systems', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(18, 2, 'i', 'Explore and utilize wisely fisheries resources using appropriate and innovative fisheries technologies and fishing methods', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(19, 2, 'j', 'Apply post-harvest practices that are compliant to international standards for food safety and quality', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(20, 2, 'k', 'Manage and protect the integrity and quality of aquatic ecosystems and resources', '2026-03-26 10:29:14', '2026-03-26 10:29:14'),
(21, 2, 'l', 'Manage and protect the integrity and quality of aquatic ecosystems and resources', '2026-04-07 15:25:15', '2026-04-07 15:25:15');

-- --------------------------------------------------------

--
-- Table structure for table `program_outcome_peo`
--

CREATE TABLE `program_outcome_peo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_outcome_id` bigint(20) UNSIGNED NOT NULL,
  `program_eo_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_outcome_peo`
--

INSERT INTO `program_outcome_peo` (`id`, `program_outcome_id`, `program_eo_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, NULL, NULL),
(2, 2, 3, NULL, NULL),
(3, 3, 5, NULL, NULL),
(4, 4, 1, NULL, NULL),
(5, 5, 3, NULL, NULL),
(6, 6, 5, NULL, NULL),
(7, 7, 3, NULL, NULL),
(8, 8, 1, NULL, NULL),
(9, 9, 3, NULL, NULL),
(10, 9, 2, NULL, NULL),
(11, 9, 1, NULL, NULL),
(13, 10, 6, NULL, NULL),
(14, 11, 6, NULL, NULL),
(15, 12, 7, NULL, NULL),
(16, 13, 7, NULL, NULL),
(17, 14, 7, NULL, NULL),
(18, 14, 9, NULL, NULL),
(19, 15, 9, NULL, NULL),
(20, 16, 7, NULL, NULL),
(21, 16, 6, NULL, NULL),
(22, 17, 7, NULL, NULL),
(23, 17, 9, NULL, NULL),
(24, 18, 7, NULL, NULL),
(25, 18, 9, NULL, NULL),
(26, 19, 9, NULL, NULL),
(27, 20, 9, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2026-03-16 01:32:28', '2026-03-16 01:32:28'),
(2, 'chair', '2026-03-16 01:32:28', '2026-03-16 01:32:28'),
(3, 'dean', '2026-03-16 01:32:28', '2026-03-16 01:32:28'),
(4, 'faculty', '2026-03-16 01:32:28', '2026-03-16 01:32:28'),
(5, 'ovpaa', '2026-03-31 10:27:51', '2026-03-31 10:27:51');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0nTgXqEg4WPN3TMrMhyO2JJmn5WgO21F3MSprted', 1, '136.239.247.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTHJDNzVLSnFPWVAxbm5saUJ3Q1RKc3JsVFNQWUJERDEzbzk1RTh6SSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cz90YWI9ZHJhZnQiO3M6NToicm91dGUiO3M6MTQ6InN5bGxhYnVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1776242505),
('0rBvyvc7FZzaNyqH55KP8AgIXsK2vCuDhUGNP8jr', NULL, '1.37.67.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicmwyaklPUmJQMTE0S010TmV2U0ZrSnh3R0ZZSzlBdXl1VktwWlRxeSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3MzoiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vYWNjb3VudC1hcHByb3ZhbCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjYxOiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9hdXRoIjtzOjU6InJvdXRlIjtzOjk6ImF1dGguc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776231206),
('7EipUHACL07l7dklbeKboAQ1mcD4PtMg28SdHDGo', NULL, '1.37.67.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.7559.97 Spotify/1.2.86.502 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQnhCdjJ4cWVaUzIwZUZhd2ZqWlBNVm13NkYweHF5MW1vM2RmY1IxOCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3ODoiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vZGVwYXJ0bWVudC9vYmplY3RpdmVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHBzOi8vbWVkaXVtc2xhdGVibHVlLXBlYWZvd2wtNDEyNDA4Lmhvc3RpbmdlcnNpdGUuY29tL2F1dGgiO3M6NToicm91dGUiO3M6OToiYXV0aC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776230981),
('8flzahW31BD2sQuFezD7PJre1fYgSNStxKQ7MqOs', NULL, '202.90.128.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiekhwR3RkTFVhM2NkY3RFVVJTN3pid3dtVnZZeWV0Y0hoY2dtMHYzOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3NToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vc3lsbGFidXM/dGFiPWRyYWZ0Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzU6Imh0dHBzOi8vbWVkaXVtc2xhdGVibHVlLXBlYWZvd2wtNDEyNDA4Lmhvc3RpbmdlcnNpdGUuY29tL3N5bGxhYnVzP3RhYj1kcmFmdCI7czo1OiJyb3V0ZSI7czoxNDoic3lsbGFidXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776306109),
('EBN12WCR7qrYFPFDJpeXp0QMZ3zVHEnQEv4Hj9R9', 2, '136.239.247.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVUNtejVWdzJHZmJmOURIR0M1Q2xYWWlBcWxOT2ozajFBenYySzQxSiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cz90YWI9ZHJhZnQiO3M6NToicm91dGUiO3M6MTQ6InN5bGxhYnVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1776227311),
('FpaPUQTGZLPq3spdO74OCsjmVQdsI9fr1ifS3TBq', 1, '202.90.128.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidVJ0bkVuYVVQeEJhWThiTnJyOFVwb1ZhVFc1REpSS05wQVc1dDNvVCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9hY2FkZW1pYy1zdHJ1Y3R1cmUiO3M6NToicm91dGUiO3M6MjQ6ImFjYWRlbWljLnN0cnVjdHVyZS5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1776231217),
('H1n4nkPr7kObuhQE2ZpTw2zv4vOvl46wngPFjDpY', NULL, '1.37.67.132', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV1JDOXl3NXJzZGFiTnlib3hvQUhsVzZrVFdDaGhSdmQ1eDU4Zks5OSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo4NToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vc3lsbGFidXMvd2l6YXJkP3N5bGxhYnVzSWQ9NyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjYxOiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9hdXRoIjtzOjU6InJvdXRlIjtzOjk6ImF1dGguc2hvdyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1776396334),
('IBIByBVsKUUkyNqRlktnGSAF0stccSd6mvmn0Icc', NULL, '136.239.247.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicjNWRHM3R3NtSUV6NEtTdTJGM0lVdDJXUGRJYTFHdUZMRVBwV3BJcSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo4NToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vc3lsbGFidXMvd2l6YXJkP3N5bGxhYnVzSWQ9NCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjg1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cy93aXphcmQ/c3lsbGFidXNJZD00IjtzOjU6InJvdXRlIjtzOjE1OiJzeWxsYWJ1cy53aXphcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776227242),
('JnesZf9ZQ2aKFL0vOPbiR21HtIMb7HFeIOxvkYnI', 1, '2001:4452:28a:2e00:2c66:b0e:9e57:f959', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQ2c0eHpqVnRGVEliOHpMT3hmRjRZSWR2OGttWHpYVW13ZGJuZ2VadSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjg1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cy93aXphcmQ/c3lsbGFidXNJZD02IjtzOjU6InJvdXRlIjtzOjE1OiJzeWxsYWJ1cy53aXphcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1776397218),
('KhKqzPftMTsi4NnEyoOEcA46S1gLEC93upIK9BsU', NULL, '1.37.67.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRnRBejlWSUJVMFlBcFQzWXRsRjBMNHBUU0ZwVmIzdjNEQ1pHQmVBOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo4MDoiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vb3JnYW5pemF0aW9uYWwvY29sbGVnZXMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo4MDoiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vb3JnYW5pemF0aW9uYWwvY29sbGVnZXMiO3M6NToicm91dGUiO3M6Mjk6Im9yZ2FuaXphdGlvbmFsLmNvbGxlZ2VzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776230938),
('lUDcjdjIkWFLbac7aR7O2ysLTJba0izgMejlTKGR', NULL, '1.37.67.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiT1c3VFhiMWRTT2pQNzZEbVVUemNqSmNJdDlZMGhlVVBvN0VQZ0hyOSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3NToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vYWNhZGVtaWMtc3RydWN0dXJlIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHBzOi8vbWVkaXVtc2xhdGVibHVlLXBlYWZvd2wtNDEyNDA4Lmhvc3RpbmdlcnNpdGUuY29tL2F1dGgiO3M6NToicm91dGUiO3M6OToiYXV0aC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776231225),
('NNEbXISUn4uyCNbxP1Tcd2C8ONR1XoNHiE2eEyNj', 18, '136.239.247.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSFRDR1h3THA4NWhGS2xiOWoxTmZOV21TTWhzUHI3ZUVER1R2NXJTdyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cz90YWI9ZHJhZnQiO3M6NToicm91dGUiO3M6MTQ6InN5bGxhYnVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTg7fQ==', 1776227330),
('pb9E8XVNlJDcvjlBv6MESNQXjQjFffFxnAJeVsOp', 3, '136.239.247.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicGo2YXVxYkV2WHV6ZnVXR0dMMmVFdVdOZXNYZnVuN21YWWphTDZqbSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cz90YWI9ZHJhZnQiO3M6NToicm91dGUiO3M6MTQ6InN5bGxhYnVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1776227295),
('rRjbKvF6ivkSRKQegdEzPCocx4tBCrWeDQw9rtF2', NULL, '136.158.124.132', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUzFXZk9TZElEZHFIWXlRTFk3VklKSXhkdHhvUjBMNGFQVVJwVjZRSCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo3NToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vc3lsbGFidXM/dGFiPWRyYWZ0Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjE6Imh0dHBzOi8vbWVkaXVtc2xhdGVibHVlLXBlYWZvd2wtNDEyNDA4Lmhvc3RpbmdlcnNpdGUuY29tL2F1dGgiO3M6NToicm91dGUiO3M6OToiYXV0aC5zaG93Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776399530),
('ti3eTeCDdGqtVCFQ9QYmvMfVklXuMEjnEGQ4cOy9', 17, '202.90.128.130', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicDhHYTVPZGE0VjA2ekxsTE02N3BibG5XMEpDQTV6Tk9TMlZRc3B4YiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY1OiJodHRwczovL21lZGl1bXNsYXRlYmx1ZS1wZWFmb3dsLTQxMjQwOC5ob3N0aW5nZXJzaXRlLmNvbS9zeWxsYWJ1cyI7czo1OiJyb3V0ZSI7czoxNDoic3lsbGFidXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNzt9', 1776230858),
('Yr5B4kud6x2s8SUfWfrtMuJHoJs5gjKSd08AN6nZ', NULL, '203.177.59.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTzVMU2tCZXRXdmZ0dkdVeUdNYlNpSmFYRmIyM1VuQlhnZm82MTVXUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo2NToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vcHJvZ3JhbXMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo2MToiaHR0cHM6Ly9tZWRpdW1zbGF0ZWJsdWUtcGVhZm93bC00MTI0MDguaG9zdGluZ2Vyc2l0ZS5jb20vYXV0aCI7czo1OiJyb3V0ZSI7czo5OiJhdXRoLnNob3ciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1776230993);

-- --------------------------------------------------------

--
-- Table structure for table `syllabi`
--

CREATE TABLE `syllabi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `academic_calendar_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','under_review','for_revision','approved') NOT NULL DEFAULT 'draft',
  `current_step` varchar(255) NOT NULL DEFAULT 'academic_calendar',
  `prepared_by` bigint(20) UNSIGNED NOT NULL,
  `concurred_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabi`
--

INSERT INTO `syllabi` (`id`, `course_id`, `academic_calendar_id`, `status`, `current_step`, `prepared_by`, `concurred_by`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'under_review', 'course_components', 1, NULL, 3, NULL, '2026-03-16 05:10:11', '2026-04-10 19:24:35'),
(3, 4, 2, 'draft', 'weekly_coverage', 17, NULL, NULL, NULL, '2026-03-30 15:04:02', '2026-04-07 15:49:56'),
(4, 8, 2, 'draft', 'review', 17, NULL, NULL, NULL, '2026-04-07 09:49:39', '2026-04-15 13:26:50'),
(5, 5, 2, 'draft', 'weekly_coverage', 17, NULL, NULL, NULL, '2026-04-07 14:45:59', '2026-04-08 14:30:56'),
(6, 14, 2, 'draft', 'review', 1, NULL, NULL, NULL, '2026-04-10 19:42:52', '2026-04-17 11:40:17');

-- --------------------------------------------------------

--
-- Table structure for table `syllabus_evaluation_items`
--

CREATE TABLE `syllabus_evaluation_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `week_content_id` bigint(20) UNSIGNED NOT NULL,
  `outcome_label` varchar(255) DEFAULT NULL,
  `kind` enum('activity','quiz','exam') DEFAULT NULL,
  `exam_type` enum('first_term','second_term','final_term') DEFAULT NULL,
  `weight` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabus_evaluation_items`
--

INSERT INTO `syllabus_evaluation_items` (`id`, `syllabus_id`, `course_id`, `week_content_id`, `outcome_label`, `kind`, `exam_type`, `weight`, `created_at`, `updated_at`) VALUES
(29, 1, 1, 37, 'MVGO', 'activity', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(30, 1, 1, 39, NULL, 'quiz', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(31, 1, 1, 40, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(32, 1, 1, 41, NULL, 'quiz', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(33, 1, 1, 42, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(34, 1, 1, 45, 'CO1', 'exam', 'first_term', 9, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(35, 1, 1, 46, 'CO1', 'exam', 'first_term', 6, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(36, 1, 1, 47, NULL, 'activity', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(37, 1, 1, 48, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(38, 1, 1, 49, NULL, 'quiz', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(39, 1, 1, 50, NULL, 'activity', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(40, 1, 1, 51, NULL, 'activity', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(41, 1, 1, 52, NULL, 'activity', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(42, 1, 1, 53, NULL, 'quiz', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(43, 1, 1, 54, NULL, 'activity', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(44, 1, 1, 55, NULL, 'quiz', NULL, 2, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(45, 1, 1, 56, NULL, 'activity', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(46, 1, 1, 57, 'CO3', 'exam', 'second_term', 9, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(47, 1, 1, 58, 'CO3', 'exam', 'second_term', 6, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(48, 1, 1, 59, NULL, 'activity', NULL, 3, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(49, 1, 1, 60, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(50, 1, 1, 61, NULL, 'quiz', NULL, 3, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(51, 1, 1, 62, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(52, 1, 1, 63, NULL, 'quiz', NULL, 4, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(53, 1, 1, 64, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(54, 1, 1, 65, NULL, 'activity', NULL, 4, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(55, 1, 1, 66, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(56, 1, 1, 67, NULL, 'activity', NULL, 5, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(57, 1, 1, 68, NULL, 'quiz', NULL, 1, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(58, 1, 1, 69, NULL, 'activity', NULL, 5, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(59, 1, 1, 70, NULL, 'activity', NULL, 2, '2026-03-19 03:07:33', '2026-03-20 05:37:40'),
(60, 1, 1, 71, 'CO4', 'exam', 'final_term', 10, '2026-03-19 03:07:33', '2026-03-19 03:56:42'),
(61, 1, 1, 72, 'CO4', 'exam', 'final_term', 7, '2026-03-19 03:07:33', '2026-03-20 05:37:40'),
(68, 4, 8, 195, 'MVGO', 'activity', NULL, 2, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(69, 4, 8, 196, NULL, 'quiz', NULL, 3, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(70, 4, 8, 197, NULL, 'quiz', NULL, 3, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(71, 4, 8, 199, 'CO2', 'exam', 'first_term', 20, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(72, 4, 8, 200, NULL, 'activity', NULL, 2, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(73, 4, 8, 201, NULL, 'quiz', NULL, 3, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(74, 4, 8, 202, NULL, 'activity', NULL, 2, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(75, 4, 8, 203, NULL, 'quiz', NULL, 3, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(76, 4, 8, 204, NULL, 'activity', NULL, 2, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(77, 4, 8, 205, 'CO4', 'exam', 'second_term', 20, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(78, 4, 8, 206, NULL, 'activity', NULL, 1, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(79, 4, 8, 207, NULL, 'activity', NULL, 1, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(80, 4, 8, 208, NULL, 'quiz', NULL, 2, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(81, 4, 8, 209, NULL, 'activity', NULL, 1, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(82, 4, 8, 210, NULL, 'activity', NULL, 3, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(83, 4, 8, 211, NULL, 'activity', NULL, 2, '2026-04-07 10:40:11', '2026-04-07 10:40:11'),
(84, 4, 8, 212, 'CO8', 'exam', 'final_term', 30, '2026-04-07 10:40:11', '2026-04-07 10:48:55'),
(85, 3, 4, 167, NULL, 'exam', 'first_term', NULL, '2026-04-07 15:49:57', '2026-04-07 15:49:57'),
(86, 3, 4, 168, NULL, 'exam', 'first_term', NULL, '2026-04-07 15:49:57', '2026-04-07 15:49:57'),
(87, 3, 4, 179, NULL, 'exam', 'second_term', NULL, '2026-04-07 15:49:57', '2026-04-07 15:49:57'),
(88, 3, 4, 180, NULL, 'exam', 'second_term', NULL, '2026-04-07 15:49:57', '2026-04-07 15:49:57'),
(89, 3, 4, 193, NULL, 'exam', 'final_term', NULL, '2026-04-07 15:49:57', '2026-04-07 15:49:57'),
(90, 3, 4, 194, NULL, 'exam', 'final_term', NULL, '2026-04-07 15:49:57', '2026-04-07 15:49:57'),
(91, 6, 14, 235, NULL, 'exam', 'first_term', NULL, '2026-04-14 18:30:49', '2026-04-14 18:30:49'),
(92, 6, 14, 241, NULL, 'exam', 'second_term', NULL, '2026-04-14 18:30:49', '2026-04-14 18:30:49'),
(93, 6, 14, 248, NULL, 'exam', 'final_term', NULL, '2026-04-14 18:30:49', '2026-04-14 18:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `syllabus_materials`
--

CREATE TABLE `syllabus_materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_week_id` bigint(20) UNSIGNED DEFAULT NULL,
  `material_name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabus_materials`
--

INSERT INTO `syllabus_materials` (`id`, `syllabus_id`, `syllabus_week_id`, `material_name`, `url`, `created_at`, `updated_at`) VALUES
(23, 1, 19, 'Quaerat veniam pari', 'https://www.xyxelid.mobi', '2026-03-19 03:00:19', '2026-03-19 03:00:19'),
(44, 1, 34, 'Placeat eius illo s', 'https://www.qopotixomorupu.info', '2026-03-19 03:04:48', '2026-03-19 03:04:48'),
(46, 1, 20, 'Online Material', 'https://www.bbc.com/news/articles/c7474004g01o', '2026-03-19 03:09:49', '2026-03-19 03:09:49'),
(47, 1, 21, 'Online Material', 'https://www.bbc.com/news/articles/cvg1gr5v333o', '2026-03-19 03:09:52', '2026-03-19 03:09:52'),
(48, 1, 26, 'Online Material', 'https://www.technewsworld.com/story/ai-glasses-shift-into-momentum-mode-shipments-grow-322-in-2025-180215.html', '2026-03-19 03:10:26', '2026-03-19 03:10:26'),
(52, 4, 127, 'What is Capture Fisheries', 'https://www.fao.org/fisheries', '2026-04-07 10:19:28', '2026-04-07 10:19:28'),
(53, 4, 128, 'Fisheries and Aquaculture Overview', 'https://www.fao.org', '2026-04-07 10:20:22', '2026-04-07 10:20:22'),
(54, 4, 129, 'Fish Anatomy Basics', 'https://www.britannica.com', '2026-04-07 10:21:27', '2026-04-07 10:21:27'),
(55, 4, 132, 'Marine Ecosystems', 'https://oceanservice.noaa.gov', '2026-04-07 10:22:55', '2026-04-07 10:22:55'),
(56, 4, 133, 'Fishing Methods Guide', 'https://www.fao.org', '2026-04-07 10:23:45', '2026-04-07 10:23:45'),
(57, 4, 134, 'Types of Fishing Vessels', 'https://www.marineinsight.com', '2026-04-07 10:24:43', '2026-04-07 10:24:43'),
(58, 4, 135, 'Philippine Fisheries Resources', 'https://www.bfar.da.gov.ph', '2026-04-07 10:25:58', '2026-04-07 10:25:58'),
(59, 4, 136, 'Ocean Productivity', 'https://earthobservatory.nasa.gov', '2026-04-07 10:26:59', '2026-04-07 10:26:59'),
(60, 4, 138, 'Fisheries Statistics', 'https://psa.gov.ph', '2026-04-07 10:30:25', '2026-04-07 10:30:25'),
(61, 4, 139, 'Overfishing Explained', 'https://www.worldwildlife.org', '2026-04-07 10:31:24', '2026-04-07 10:31:24'),
(62, 4, 140, 'Fisheries Management Basics', 'https://www.fao.org', '2026-04-07 10:33:19', '2026-04-07 10:33:19'),
(63, 4, 141, 'Sustainable Fisheries', 'https://www.un.org', '2026-04-07 10:34:10', '2026-04-07 10:34:10'),
(64, 4, 142, 'Law Enforcement in Fisheries', 'https://www.fao.org', '2026-04-07 10:35:00', '2026-04-07 10:35:00'),
(65, 4, 143, 'Fisheries Solutions', 'https://www.worldbank.org', '2026-04-07 10:36:30', '2026-04-07 10:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `syllabus_references`
--

CREATE TABLE `syllabus_references` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_week_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabus_references`
--

INSERT INTO `syllabus_references` (`id`, `syllabus_id`, `syllabus_week_id`, `reference_text`, `created_at`, `updated_at`) VALUES
(23, 1, 19, 'John W. Creswell, J. W. (2018). Research design: Qualitative, quantitative, and mixed methods approaches (5th ed.). SAGE Publications.', '2026-03-19 03:00:19', '2026-03-19 03:00:19'),
(43, 1, 34, 'Quia quae ea veritat', '2026-03-19 03:04:48', '2026-03-19 03:04:48'),
(45, 1, 20, 'Thomas H. Cormen, T. H., Charles E. Leiserson, C. E., Ronald L. Rivest, R. L., & Clifford Stein, C. (2009). Introduction to algorithms (3rd ed.). MIT Press.', '2026-03-19 03:08:56', '2026-03-19 03:08:56'),
(46, 1, 21, 'Stuart Russell, S., & Peter Norvig, P. (2021). Artificial intelligence: A modern approach (4th ed.). Pearson.', '2026-03-19 03:09:04', '2026-03-19 03:09:04'),
(50, 4, 127, 'FAO Fisheries Overview Handbook', '2026-04-07 10:19:28', '2026-04-07 10:19:28'),
(51, 4, 128, 'Introduction to Fisheries Science by Michael King', '2026-04-07 10:20:22', '2026-04-07 10:20:22'),
(52, 4, 129, 'Fish Biology by Bone & Moore', '2026-04-07 10:21:27', '2026-04-07 10:21:27'),
(53, 4, 132, 'Marine Ecology Processes by Valiela', '2026-04-07 10:22:55', '2026-04-07 10:22:55'),
(54, 4, 133, 'Fishing Gear Technology by FAO', '2026-04-07 10:23:45', '2026-04-07 10:23:45'),
(55, 4, 134, 'Commercial Fishing Methods by King', '2026-04-07 10:24:43', '2026-04-07 10:24:43'),
(56, 4, 135, 'BFAR Fisheries Profile', '2026-04-07 10:25:58', '2026-04-07 10:25:58'),
(57, 4, 136, 'Fisheries Oceanography by Bakun', '2026-04-07 10:26:59', '2026-04-07 10:26:59'),
(58, 4, 138, 'BFAR Annual Report', '2026-04-07 10:30:25', '2026-04-07 10:30:25'),
(59, 4, 139, 'Fisheries Management Textbook', '2026-04-07 10:31:24', '2026-04-07 10:31:24'),
(60, 4, 140, 'Fisheries Management by Hilborn', '2026-04-07 10:33:19', '2026-04-07 10:33:19'),
(61, 4, 141, 'Marine Conservation Biology', '2026-04-07 10:34:10', '2026-04-07 10:34:10'),
(62, 4, 142, 'Fisheries Law Enforcement Manual', '2026-04-07 10:35:00', '2026-04-07 10:35:00'),
(63, 4, 143, 'Sustainable Fisheries Case Studies', '2026-04-07 10:36:30', '2026-04-07 10:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `syllabus_reviewers`
--

CREATE TABLE `syllabus_reviewers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabus_reviewers`
--

INSERT INTO `syllabus_reviewers` (`id`, `syllabus_id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'approved', '2026-03-18 00:29:23', '2026-03-18 00:29:23'),
(2, 4, 16, 'approved', '2026-04-07 15:54:55', '2026-04-07 15:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `syllabus_revisions`
--

CREATE TABLE `syllabus_revisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `revision_no` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `revision_date` date NOT NULL,
  `implementation_semester` varchar(255) NOT NULL,
  `highlights` text DEFAULT NULL,
  `contributors` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabus_revisions`
--

INSERT INTO `syllabus_revisions` (`id`, `syllabus_id`, `revision_no`, `revision_date`, `implementation_semester`, `highlights`, `contributors`, `created_at`, `updated_at`) VALUES
(1, 1, 0, '2026-03-18', 'fsafa', 'fsafa', 'fsafa', '2026-03-18 01:24:01', '2026-03-18 01:24:01'),
(2, 1, 1, '1982-11-18', 'Quia praesentium aut', 'Laudantium nesciunt', 'Expedita velit libe', '2026-03-20 05:55:44', '2026-03-20 05:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `syllabus_weeks`
--

CREATE TABLE `syllabus_weeks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_id` bigint(20) UNSIGNED NOT NULL,
  `week_no` tinyint(3) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_exam_week` tinyint(1) NOT NULL DEFAULT 0,
  `exam_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `syllabus_weeks`
--

INSERT INTO `syllabus_weeks` (`id`, `syllabus_id`, `week_no`, `start_date`, `end_date`, `is_exam_week`, `exam_type`, `created_at`, `updated_at`) VALUES
(19, 1, 1, '2025-12-01', '2025-12-07', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(20, 1, 2, '2025-12-08', '2025-12-14', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(21, 1, 3, '2025-12-15', '2025-12-21', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(22, 1, 4, '2026-01-05', '2026-01-11', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(23, 1, 5, '2026-01-12', '2026-01-18', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(24, 1, 6, '2026-01-19', '2026-01-25', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(25, 1, 7, '2026-01-26', '2026-02-01', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(26, 1, 8, '2026-02-02', '2026-02-08', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(27, 1, 9, '2026-02-09', '2026-02-15', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(28, 1, 10, '2026-02-16', '2026-02-22', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(29, 1, 11, '2026-02-23', '2026-03-01', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(30, 1, 12, '2026-03-02', '2026-03-08', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(31, 1, 13, '2026-03-09', '2026-03-15', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(32, 1, 14, '2026-03-16', '2026-03-22', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(33, 1, 15, '2026-03-23', '2026-03-29', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(34, 1, 16, '2026-03-30', '2026-04-05', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(35, 1, 17, '2026-04-06', '2026-04-12', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(36, 1, 18, '2026-04-13', '2026-04-16', 0, NULL, '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(109, 3, 1, '2025-12-01', '2025-12-07', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(110, 3, 2, '2025-12-08', '2025-12-14', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(111, 3, 3, '2025-12-15', '2025-12-21', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(112, 3, 4, '2026-01-05', '2026-01-11', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(113, 3, 5, '2026-01-12', '2026-01-18', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(114, 3, 6, '2026-01-19', '2026-01-25', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(115, 3, 7, '2026-01-26', '2026-02-01', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(116, 3, 8, '2026-02-02', '2026-02-08', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(117, 3, 9, '2026-02-09', '2026-02-15', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(118, 3, 10, '2026-02-16', '2026-02-22', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(119, 3, 11, '2026-02-23', '2026-03-01', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(120, 3, 12, '2026-03-02', '2026-03-08', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(121, 3, 13, '2026-03-09', '2026-03-15', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(122, 3, 14, '2026-03-16', '2026-03-22', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(123, 3, 15, '2026-03-23', '2026-03-29', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(124, 3, 16, '2026-03-30', '2026-04-05', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(125, 3, 17, '2026-04-06', '2026-04-12', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(126, 3, 18, '2026-04-13', '2026-04-16', 0, NULL, '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(127, 4, 1, '2025-12-01', '2025-12-07', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(128, 4, 2, '2025-12-08', '2025-12-14', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(129, 4, 3, '2025-12-15', '2025-12-21', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(130, 4, 4, '2026-01-05', '2026-01-11', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(131, 4, 5, '2026-01-12', '2026-01-18', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(132, 4, 6, '2026-01-19', '2026-01-25', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(133, 4, 7, '2026-01-26', '2026-02-01', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(134, 4, 8, '2026-02-02', '2026-02-08', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(135, 4, 9, '2026-02-09', '2026-02-15', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(136, 4, 10, '2026-02-16', '2026-02-22', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(137, 4, 11, '2026-02-23', '2026-03-01', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(138, 4, 12, '2026-03-02', '2026-03-08', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(139, 4, 13, '2026-03-09', '2026-03-15', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(140, 4, 14, '2026-03-16', '2026-03-22', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(141, 4, 15, '2026-03-23', '2026-03-29', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(142, 4, 16, '2026-03-30', '2026-04-05', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(143, 4, 17, '2026-04-06', '2026-04-12', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(144, 4, 18, '2026-04-13', '2026-04-16', 0, NULL, '2026-04-07 10:17:45', '2026-04-07 10:17:45'),
(145, 5, 1, '2025-12-01', '2025-12-07', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(146, 5, 2, '2025-12-08', '2025-12-14', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(147, 5, 3, '2025-12-15', '2025-12-21', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(148, 5, 4, '2026-01-05', '2026-01-11', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(149, 5, 5, '2026-01-12', '2026-01-18', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(150, 5, 6, '2026-01-19', '2026-01-25', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(151, 5, 7, '2026-01-26', '2026-02-01', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(152, 5, 8, '2026-02-02', '2026-02-08', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(153, 5, 9, '2026-02-09', '2026-02-15', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(154, 5, 10, '2026-02-16', '2026-02-22', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(155, 5, 11, '2026-02-23', '2026-03-01', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(156, 5, 12, '2026-03-02', '2026-03-08', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(157, 5, 13, '2026-03-09', '2026-03-15', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(158, 5, 14, '2026-03-16', '2026-03-22', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(159, 5, 15, '2026-03-23', '2026-03-29', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(160, 5, 16, '2026-03-30', '2026-04-05', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(161, 5, 17, '2026-04-06', '2026-04-12', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(162, 5, 18, '2026-04-13', '2026-04-16', 0, NULL, '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(163, 6, 1, '2025-12-01', '2025-12-07', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(164, 6, 2, '2025-12-08', '2025-12-14', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(165, 6, 3, '2025-12-15', '2025-12-21', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(166, 6, 4, '2026-01-05', '2026-01-11', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(167, 6, 5, '2026-01-12', '2026-01-18', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(168, 6, 6, '2026-01-19', '2026-01-25', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(169, 6, 7, '2026-01-26', '2026-02-01', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(170, 6, 8, '2026-02-02', '2026-02-08', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(171, 6, 9, '2026-02-09', '2026-02-15', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(172, 6, 10, '2026-02-16', '2026-02-22', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(173, 6, 11, '2026-02-23', '2026-03-01', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(174, 6, 12, '2026-03-02', '2026-03-08', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(175, 6, 13, '2026-03-09', '2026-03-15', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(176, 6, 14, '2026-03-16', '2026-03-22', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(177, 6, 15, '2026-03-23', '2026-03-29', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(178, 6, 16, '2026-03-30', '2026-04-05', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(179, 6, 17, '2026-04-06', '2026-04-12', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(180, 6, 18, '2026-04-13', '2026-04-16', 0, NULL, '2026-04-14 18:30:23', '2026-04-14 18:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `account_status` varchar(255) NOT NULL DEFAULT 'pending',
  `phone_number` varchar(255) DEFAULT NULL,
  `office` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `account_status`, `phone_number`, `office`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@csms.local', '2026-03-16 01:32:28', '$2y$12$u2VEvwdYkpwR72xj8gZ4jOk0WvUoFh7XztG9e9IgMde/Cu.KxeE.e', 'active', NULL, NULL, 'C6EomWj1zz220665RJrQNjASl46g7QDpVoOyhnluQM3oIojjZKr7vm4glVVA', '2026-03-16 01:32:28', '2026-03-16 01:32:28'),
(2, 'Department Chair', 'chair@csms.local', '2026-03-16 01:32:29', '$2y$12$QVKNyKkPWaxFTrPdRtmQKef0H7UIZmlHnvP3ojqkWt4NScJF3GQlK', 'active', NULL, NULL, NULL, '2026-03-16 01:32:29', '2026-03-23 06:03:13'),
(3, 'College Dean', 'dean@csms.local', '2026-03-16 01:32:29', '$2y$12$9fdFZNx3ag52agEqy.fINeboItWDPmgDWz3K78qChVPO2Vi9W9x5W', 'active', NULL, NULL, NULL, '2026-03-16 01:32:29', '2026-03-16 01:32:29'),
(4, 'Faculty User', 'faculty@csms.local', '2026-03-16 01:32:30', '$2y$12$jdFEHAjYB7d68fe5/cpnVeqYyawMVhu2o92H4krNvyVRg9.VG.Maq', 'disabled', '0000', NULL, NULL, '2026-03-16 01:32:30', '2026-03-31 18:19:23'),
(16, 'Jeric J Dela Cruz', 'jeric.delacruz@clsu2.edu.ph', '2026-04-06 18:20:55', '$2y$12$FALQfMNQCynOxmSE6NSwvOK6wNQldGHFP7zZwlCGGlovY5h2lmBEi', 'active', '09758806128', 'CLIRDEC', NULL, '2026-03-26 15:20:54', '2026-04-06 18:20:55'),
(17, 'Kiel Leonard Palaad', 'kiel.palaad@clsu2.edu.ph', '2026-03-30 09:16:35', '$2y$12$uRxutbXtuMqtAgeZSNbGJutORV5vpDSG/YENUvWafM.tj90skI59m', 'active', '09697886767', 'Office for Learning Outcomes and Innovation', NULL, '2026-03-30 09:15:44', '2026-03-30 09:17:37'),
(18, 'OVPAA User', 'ovpaa@csms.local', '2026-03-31 10:32:57', '$2y$12$u2VEvwdYkpwR72xj8gZ4jOk0WvUoFh7XztG9e9IgMde/Cu.KxeE.e', 'active', NULL, NULL, NULL, '2026-03-31 10:32:57', '2026-03-31 10:32:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_assignments`
--

CREATE TABLE `user_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `college_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `context` enum('faculty','chair','dean') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_assignments`
--

INSERT INTO `user_assignments` (`id`, `user_id`, `college_id`, `department_id`, `context`, `created_at`, `updated_at`) VALUES
(1, 3, 5, NULL, 'dean', '2026-03-23 02:27:41', '2026-03-23 02:27:41'),
(2, 3, 5, NULL, 'faculty', '2026-03-23 02:27:41', '2026-03-23 02:27:41'),
(15, 17, NULL, 18, 'faculty', '2026-03-30 09:58:10', '2026-03-30 09:58:10'),
(16, 2, NULL, 18, 'chair', '2026-04-07 13:54:01', '2026-04-07 13:54:01'),
(17, 2, NULL, 18, 'faculty', '2026-04-07 13:54:01', '2026-04-07 13:54:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_otps`
--

CREATE TABLE `user_otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `purpose` varchar(50) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL),
(3, 3, 3, NULL, NULL),
(4, 4, 4, NULL, NULL),
(5, 3, 4, NULL, NULL),
(6, 2, 4, NULL, NULL),
(17, 17, 4, NULL, NULL),
(19, 16, 4, NULL, NULL),
(20, 18, 5, '2026-03-31 10:33:12', '2026-03-31 10:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `week_contents`
--

CREATE TABLE `week_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `syllabus_week_id` bigint(20) UNSIGNED NOT NULL,
  `component_type` enum('LEC','LAB') NOT NULL,
  `course_outcome_id` bigint(20) UNSIGNED DEFAULT NULL,
  `learning_outcomes` text NOT NULL,
  `topics` text NOT NULL,
  `assessment_task` text DEFAULT NULL,
  `tla` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `week_contents`
--

INSERT INTO `week_contents` (`id`, `syllabus_week_id`, `component_type`, `course_outcome_id`, `learning_outcomes`, `topics`, `assessment_task`, `tla`, `created_at`, `updated_at`) VALUES
(37, 19, 'LEC', NULL, 'Reprehenderit ea nu', 'Vel qui fugiat at en', 'Iste commodi digniss', 'Corrupti velit et', '2026-03-19 02:59:21', '2026-03-19 03:00:19'),
(38, 19, 'LAB', NULL, '', '', '', '', '2026-03-19 02:59:21', '2026-03-19 02:59:21'),
(39, 20, 'LEC', 1, 'Nulla officia rerum', 'Vero aliquid enim au', 'Eum ea id consequatu', 'Aute laborum Nisi a', '2026-03-19 02:59:21', '2026-03-19 03:00:30'),
(40, 20, 'LAB', 1, 'Aut aut hic harum ni', 'Omnis error minima v', 'Provident eos sed', 'Laborum facere dolor', '2026-03-19 02:59:21', '2026-03-19 03:02:43'),
(41, 21, 'LEC', 1, 'Sed dignissimos sunt', 'Enim fugiat lorem s', 'Nisi hic quod volupt', 'Quidem adipisci sunt', '2026-03-19 02:59:21', '2026-03-19 03:00:44'),
(42, 21, 'LAB', 1, 'Qui autem amet eos', 'Nisi expedita tempor', 'Voluptates quo sed q', 'Ea itaque dolor unde', '2026-03-19 02:59:21', '2026-03-19 03:02:53'),
(43, 22, 'LEC', NULL, '', '', 'Non-Teaching Week', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(44, 22, 'LAB', NULL, '', '', 'Non-Teaching Week', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(45, 23, 'LEC', NULL, '', '', '1st Term Exam', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(46, 23, 'LAB', NULL, '', '', '1st Term Practical Exam', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(47, 24, 'LEC', 2, 'In cum accusantium e', 'Quia et magna aspern', 'Et magna quod eiusmo', 'Voluptatem neque fu', '2026-03-19 02:59:21', '2026-03-19 03:01:11'),
(48, 24, 'LAB', 2, 'Laboris maxime moles', 'Fuga Modi praesenti', 'Neque est iste eu mo', 'Corrupti sed praese', '2026-03-19 02:59:21', '2026-03-19 03:03:03'),
(49, 25, 'LEC', 2, 'Incididunt iure cumq', 'Architecto voluptate', 'Aute rerum quis illo', 'Molestias iusto sequ', '2026-03-19 02:59:21', '2026-03-19 03:01:17'),
(50, 25, 'LAB', 2, 'Sit sunt sint sit fu', 'Autem id ut eligend', 'Exercitationem in au', 'Magni nobis voluptas', '2026-03-19 02:59:21', '2026-03-19 03:03:09'),
(51, 26, 'LEC', 2, 'Eaque accusamus quis', 'Impedit exercitatio', 'Elit in id odit ut', 'Qui cum eveniet et', '2026-03-19 02:59:21', '2026-03-19 03:01:07'),
(52, 26, 'LAB', 2, 'Quisquam optio dict', 'Optio alias sed dol', 'Rem et suscipit qui', 'Necessitatibus esse', '2026-03-19 02:59:21', '2026-03-19 03:03:17'),
(53, 27, 'LEC', 3, 'At reprehenderit et', 'Nobis praesentium eu', 'Irure sequi ea paria', 'Excepteur harum ad d', '2026-03-19 02:59:21', '2026-03-19 03:01:22'),
(54, 27, 'LAB', 3, 'Tempora quae accusam', 'Fugiat est aliquid', 'Dignissimos laborum', 'Aspernatur ut rem la', '2026-03-19 02:59:21', '2026-03-19 03:03:22'),
(55, 28, 'LEC', 3, 'Alias iusto id autem', 'Aperiam ullam commod', 'Voluptatem placeat', 'Assumenda laborum do', '2026-03-19 02:59:21', '2026-03-19 03:01:34'),
(56, 28, 'LAB', 3, 'Et porro exercitatio', 'Dolores reiciendis c', 'Excepteur voluptas v', 'Rerum sint adipisici', '2026-03-19 02:59:21', '2026-03-19 03:04:26'),
(57, 29, 'LEC', NULL, '', '', '2nd Term Exam', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(58, 29, 'LAB', NULL, '', '', '2nd Term Practical Exam', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(59, 30, 'LEC', 4, 'Nisi obcaecati rerum', 'Nulla excepteur qui', 'Mollitia culpa id in', 'Esse dicta id est m', '2026-03-19 02:59:21', '2026-03-19 03:01:45'),
(60, 30, 'LAB', 4, 'Repudiandae dolorem', 'Voluptatem quo dist', 'Aspernatur in invent', 'Aspernatur dicta qui', '2026-03-19 02:59:21', '2026-03-19 03:04:30'),
(61, 31, 'LEC', 4, 'Duis molestiae obcae', 'Ex unde odit aliquip', 'Ut sit est dolore c', 'Nisi ipsum qui sed d', '2026-03-19 02:59:21', '2026-03-19 03:01:51'),
(62, 31, 'LAB', 4, 'Dolor ut enim sed si', 'Est nisi distinctio', 'Eiusmod iste qui con', 'Rerum veritatis et s', '2026-03-19 02:59:21', '2026-03-19 03:04:34'),
(63, 32, 'LEC', 4, 'Tempore est commodo', 'Hic corrupti volupt', 'Quo unde quo eum vol', 'Non assumenda deleni', '2026-03-19 02:59:21', '2026-03-19 03:02:07'),
(64, 32, 'LAB', 4, 'Nesciunt consectetu', 'Quis aute voluptas q', 'Quis atque labore fu', 'Qui deserunt dolore', '2026-03-19 02:59:21', '2026-03-19 03:04:38'),
(65, 33, 'LEC', 4, 'Anim aut esse id pla', 'Enim voluptatem dele', 'Est laudantium corr', 'Maiores enim a elige', '2026-03-19 02:59:21', '2026-03-19 03:02:02'),
(66, 33, 'LAB', 4, 'Beatae quaerat volup', 'Ea unde labore ea pa', 'Nobis sunt aut sed d', 'Sint fugiat duis ut', '2026-03-19 02:59:21', '2026-03-19 03:04:42'),
(67, 34, 'LEC', 4, 'Est cum quaerat moll', 'Enim quis cumque et', 'Minim excepturi aute', 'Reprehenderit et an', '2026-03-19 02:59:21', '2026-03-19 03:02:09'),
(68, 34, 'LAB', 4, 'Esse quia maxime co', 'Adipisicing quidem o', 'Ea enim voluptatem', 'Deleniti est volupta', '2026-03-19 02:59:21', '2026-03-19 03:04:48'),
(69, 35, 'LEC', 4, 'Qui eum cillum cupid', 'Esse corporis deleni', 'Exercitationem conse', 'Possimus soluta qui', '2026-03-19 02:59:21', '2026-03-19 03:02:16'),
(70, 35, 'LAB', 4, 'Id blanditiis et pa', 'Voluptatem deleniti', 'Corrupti aut repell', 'Autem quae magni pra', '2026-03-19 02:59:21', '2026-03-19 03:05:00'),
(71, 36, 'LEC', NULL, '', '', 'Final Term Exam', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(72, 36, 'LAB', NULL, '', '', 'Final Term Practical Exam', '', '2026-03-19 02:59:21', '2026-04-10 19:24:18'),
(159, 109, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(160, 109, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(161, 110, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(162, 110, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(163, 111, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(164, 111, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(165, 112, 'LEC', NULL, '', '', 'Non-Teaching Week', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(166, 112, 'LAB', NULL, '', '', 'Non-Teaching Week', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(167, 113, 'LEC', NULL, '', '', '1st Term Exam', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(168, 113, 'LAB', NULL, '', '', '1st Term Practical Exam', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(169, 114, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(170, 114, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(171, 115, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(172, 115, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(173, 116, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(174, 116, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(175, 117, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(176, 117, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(177, 118, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(178, 118, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(179, 119, 'LEC', NULL, '', '', '2nd Term Exam', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(180, 119, 'LAB', NULL, '', '', '2nd Term Practical Exam', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(181, 120, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(182, 120, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(183, 121, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(184, 121, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(185, 122, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(186, 122, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(187, 123, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(188, 123, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(189, 124, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(190, 124, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(191, 125, 'LEC', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(192, 125, 'LAB', NULL, '', '', '', '', '2026-04-07 10:14:59', '2026-04-07 10:14:59'),
(193, 126, 'LEC', NULL, '', '', 'Final Term Exam', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(194, 126, 'LAB', NULL, '', '', 'Final Term Practical Exam', '', '2026-04-07 10:14:59', '2026-04-08 13:18:39'),
(195, 127, 'LEC', NULL, 'Define capture fisheries and explain its importance', 'Introduction to Capture Fisheries', 'Write a short reflection on the role of fisheries in food security', 'PPT discussion, class orientation, GClass materials', '2026-04-07 10:17:45', '2026-04-07 10:42:43'),
(196, 128, 'LEC', 30, 'Explain the scope and importance of capture fisheries in the Philippines', 'Scope and Importance of Capture Fisheries', 'Concept mapping of fisheries sectors', 'Lecture, group discussion', '2026-04-07 10:17:45', '2026-04-07 10:42:51'),
(197, 129, 'LEC', 31, 'Describe basic fish anatomy and functions', 'Fish Biology (Anatomy & Physiology)', 'Label parts of a fish diagram', 'PPT, diagrams, interactive discussion', '2026-04-07 10:17:45', '2026-04-07 10:43:00'),
(198, 130, 'LEC', NULL, '', '', 'Non-Teaching Week', '', '2026-04-07 10:17:45', '2026-04-15 13:24:33'),
(199, 131, 'LEC', NULL, '', '', '1st Term Exam', '', '2026-04-07 10:17:45', '2026-04-15 13:24:33'),
(200, 132, 'LEC', 31, 'Explain fish ecology and habitat relationships', 'Fish Ecology', 'Short essay on fish habitat importance', 'Lecture, case discussion', '2026-04-07 10:17:45', '2026-04-07 10:43:06'),
(201, 133, 'LEC', 32, 'Identify different fishing gears and their uses', 'Fishing Gears and Equipment', 'Matching type of gear to fishing method', 'PPT, visual aids', '2026-04-07 10:17:45', '2026-04-07 10:43:15'),
(202, 134, 'LEC', 32, 'Explain commercial fishing methods and vessel types', 'Fishing Methods and Vessels', 'Compare traditional vs commercial fishing', 'Lecture, group reporting', '2026-04-07 10:17:45', '2026-04-07 10:43:33'),
(203, 135, 'LEC', 33, 'Identify major fishing grounds in the Philippines', 'Philippine Fishing Grounds', 'Map labeling of fishing areas', 'Map analysis, PPT', '2026-04-07 10:17:45', '2026-04-07 10:43:29'),
(204, 136, 'LEC', 33, 'Describe characteristics and productivity of fishing grounds', 'Characteristics of Fishing Grounds', 'Short report on assigned fishing area', 'Lecture, reporting', '2026-04-07 10:17:45', '2026-04-07 10:43:25'),
(205, 137, 'LEC', NULL, '', '', '2nd Term Exam', '', '2026-04-07 10:17:45', '2026-04-15 13:24:33'),
(206, 138, 'LEC', 34, 'Analyze fisheries resource status in the Philippines', 'Status of Fisheries Resources', 'Data interpretation exercise', 'Case study, data analysis', '2026-04-07 10:17:45', '2026-04-07 10:43:41'),
(207, 139, 'LEC', 34, 'Discuss issues and challenges in fisheries utilization', 'Fisheries Issues and Challenges', 'Reflection on overfishing issues', 'Group discussion', '2026-04-07 10:17:45', '2026-04-07 10:43:45'),
(208, 140, 'LEC', 35, 'Explain fisheries management concepts and principles', 'Fisheries Management Principles', 'Quiz on management concepts', 'Lecture, quiz', '2026-04-07 10:17:45', '2026-04-07 10:43:50'),
(209, 141, 'LEC', 36, 'Describe conservation strategies and sustainability practices', 'Conservation and Sustainability', 'Create a conservation plan outline', 'Workshop, group activity', '2026-04-07 10:17:45', '2026-04-07 10:43:57'),
(210, 142, 'LEC', 36, 'Evaluate the implementation of fisheries regulations', 'Implementation of Fisheries Laws', 'Case analysis of law enforcement', 'Case study discussion', '2026-04-07 10:17:45', '2026-04-07 10:44:01'),
(211, 143, 'LEC', 37, 'Apply fisheries knowledge in solving real-world problems', 'Application of Fisheries Management', 'Group project proposal (sustainable solution)', 'Project-based learning', '2026-04-07 10:17:45', '2026-04-07 10:47:09'),
(212, 144, 'LEC', NULL, '', '', 'Final Term Exam', '', '2026-04-07 10:17:45', '2026-04-15 13:24:33'),
(213, 145, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(214, 146, 'LEC', 38, '', '', '', '', '2026-04-07 15:41:46', '2026-04-08 14:31:37'),
(215, 147, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(216, 148, 'LEC', NULL, '', '', 'Non-Teaching Week', '', '2026-04-07 15:41:46', '2026-04-15 12:29:01'),
(217, 149, 'LEC', NULL, '', '', '1st Term Exam', '', '2026-04-07 15:41:46', '2026-04-15 12:29:01'),
(218, 150, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(219, 151, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(220, 152, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(221, 153, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(222, 154, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(223, 155, 'LEC', NULL, '', '', '2nd Term Exam', '', '2026-04-07 15:41:46', '2026-04-15 12:29:01'),
(224, 156, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(225, 157, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(226, 158, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(227, 159, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(228, 160, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(229, 161, 'LEC', NULL, '', '', '', '', '2026-04-07 15:41:46', '2026-04-07 15:41:46'),
(230, 162, 'LEC', NULL, '', '', 'Final Term Exam', '', '2026-04-07 15:41:46', '2026-04-15 12:29:01'),
(231, 163, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(232, 164, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(233, 165, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(234, 166, 'LEC', NULL, '', '', 'Non-Teaching Week', '', '2026-04-14 18:30:23', '2026-04-17 11:25:34'),
(235, 167, 'LEC', NULL, '', '', '1st Term Exam', '', '2026-04-14 18:30:23', '2026-04-17 11:25:34'),
(236, 168, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(237, 169, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(238, 170, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(239, 171, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(240, 172, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(241, 173, 'LEC', NULL, '', '', '2nd Term Exam', '', '2026-04-14 18:30:23', '2026-04-17 11:25:34'),
(242, 174, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(243, 175, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(244, 176, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(245, 177, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(246, 178, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(247, 179, 'LEC', NULL, '', '', '', '', '2026-04-14 18:30:23', '2026-04-14 18:30:23'),
(248, 180, 'LEC', NULL, '', '', 'Final Term Exam', '', '2026-04-14 18:30:23', '2026-04-17 11:25:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_calendars`
--
ALTER TABLE `academic_calendars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `academic_calendar_events`
--
ALTER TABLE `academic_calendar_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_calendar_events_academic_calendar_id_foreign` (`academic_calendar_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_index` (`user_id`),
  ADD KEY `audit_logs_module_index` (`module`),
  ADD KEY `audit_logs_reference_id_index` (`reference_id`),
  ADD KEY `audit_logs_timestamp_index` (`timestamp`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `colleges_name_unique` (`name`);

--
-- Indexes for table `college_goals`
--
ALTER TABLE `college_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `college_goals_college_id_foreign` (`college_id`);

--
-- Indexes for table `complete_syllabi`
--
ALTER TABLE `complete_syllabi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `complete_syllabi_syllabus_id_version_unique` (`syllabus_id`,`version`),
  ADD KEY `complete_syllabi_approved_by_foreign` (`approved_by`),
  ADD KEY `complete_syllabi_course_id_academic_year_semester_index` (`course_id`,`academic_year`,`semester`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `courses_program_id_course_code_unique` (`program_id`,`course_code`),
  ADD KEY `courses_created_by_foreign` (`created_by`);

--
-- Indexes for table `course_components`
--
ALTER TABLE `course_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_components_syllabus_id_foreign` (`syllabus_id`);

--
-- Indexes for table `course_curriculum_maps`
--
ALTER TABLE `course_curriculum_maps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_curriculum_maps_course_id_program_outcome_id_unique` (`course_id`,`program_outcome_id`),
  ADD KEY `course_curriculum_maps_program_outcome_id_foreign` (`program_outcome_id`);

--
-- Indexes for table `course_outcomes`
--
ALTER TABLE `course_outcomes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_outcomes_syllabus_id_co_code_unique` (`syllabus_id`,`co_code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `departments_college_id_foreign` (`college_id`);

--
-- Indexes for table `department_objectives`
--
ALTER TABLE `department_objectives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_objectives_department_id_foreign` (`department_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_departments`
--
ALTER TABLE `program_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_departments_program_id_foreign` (`program_id`),
  ADD KEY `program_departments_department_id_foreign` (`department_id`);

--
-- Indexes for table `program_eos`
--
ALTER TABLE `program_eos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_eos_program_id_peo_code_unique` (`program_id`,`peo_code`);

--
-- Indexes for table `program_outcomes`
--
ALTER TABLE `program_outcomes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_outcomes_program_id_po_code_unique` (`program_id`,`po_code`);

--
-- Indexes for table `program_outcome_peo`
--
ALTER TABLE `program_outcome_peo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_outcome_peo_program_outcome_id_program_eo_id_unique` (`program_outcome_id`,`program_eo_id`),
  ADD KEY `program_outcome_peo_program_eo_id_foreign` (`program_eo_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `syllabi`
--
ALTER TABLE `syllabi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `syllabi_course_id_academic_calendar_id_unique` (`course_id`,`academic_calendar_id`),
  ADD KEY `syllabi_academic_calendar_id_foreign` (`academic_calendar_id`),
  ADD KEY `syllabi_prepared_by_foreign` (`prepared_by`),
  ADD KEY `syllabi_concurred_by_foreign` (`concurred_by`),
  ADD KEY `syllabi_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `syllabus_evaluation_items`
--
ALTER TABLE `syllabus_evaluation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `syllabus_evaluation_items_course_id_foreign` (`course_id`),
  ADD KEY `syllabus_evaluation_items_week_content_id_foreign` (`week_content_id`),
  ADD KEY `syllabus_evaluation_items_syllabus_id_course_id_index` (`syllabus_id`,`course_id`);

--
-- Indexes for table `syllabus_materials`
--
ALTER TABLE `syllabus_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `syllabus_materials_syllabus_id_foreign` (`syllabus_id`),
  ADD KEY `syllabus_materials_syllabus_week_id_foreign` (`syllabus_week_id`);

--
-- Indexes for table `syllabus_references`
--
ALTER TABLE `syllabus_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `syllabus_references_syllabus_id_foreign` (`syllabus_id`),
  ADD KEY `syllabus_references_syllabus_week_id_foreign` (`syllabus_week_id`);

--
-- Indexes for table `syllabus_reviewers`
--
ALTER TABLE `syllabus_reviewers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `syllabus_reviewers_syllabus_id_user_id_unique` (`syllabus_id`,`user_id`),
  ADD KEY `syllabus_reviewers_user_id_foreign` (`user_id`);

--
-- Indexes for table `syllabus_revisions`
--
ALTER TABLE `syllabus_revisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `syllabus_revisions_syllabus_id_revision_no_unique` (`syllabus_id`,`revision_no`);

--
-- Indexes for table `syllabus_weeks`
--
ALTER TABLE `syllabus_weeks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `syllabus_weeks_syllabus_id_week_no_unique` (`syllabus_id`,`week_no`),
  ADD UNIQUE KEY `syllabus_weeks_syllabus_id_exam_type_unique` (`syllabus_id`,`exam_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_assignments`
--
ALTER TABLE `user_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_assignments_unique_scope` (`user_id`,`college_id`,`department_id`,`context`),
  ADD KEY `user_assignments_college_id_foreign` (`college_id`),
  ADD KEY `user_assignments_department_id_foreign` (`department_id`);

--
-- Indexes for table `user_otps`
--
ALTER TABLE `user_otps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_otps_user_id_purpose_unique` (`user_id`,`purpose`),
  ADD KEY `user_otps_purpose_index` (`purpose`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `week_contents`
--
ALTER TABLE `week_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `week_contents_syllabus_week_id_foreign` (`syllabus_week_id`),
  ADD KEY `week_contents_course_outcome_id_foreign` (`course_outcome_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_calendars`
--
ALTER TABLE `academic_calendars`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `academic_calendar_events`
--
ALTER TABLE `academic_calendar_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `college_goals`
--
ALTER TABLE `college_goals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `complete_syllabi`
--
ALTER TABLE `complete_syllabi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `course_components`
--
ALTER TABLE `course_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_curriculum_maps`
--
ALTER TABLE `course_curriculum_maps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `course_outcomes`
--
ALTER TABLE `course_outcomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `department_objectives`
--
ALTER TABLE `department_objectives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `program_departments`
--
ALTER TABLE `program_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `program_eos`
--
ALTER TABLE `program_eos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `program_outcomes`
--
ALTER TABLE `program_outcomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `program_outcome_peo`
--
ALTER TABLE `program_outcome_peo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `syllabi`
--
ALTER TABLE `syllabi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `syllabus_evaluation_items`
--
ALTER TABLE `syllabus_evaluation_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `syllabus_materials`
--
ALTER TABLE `syllabus_materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `syllabus_references`
--
ALTER TABLE `syllabus_references`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `syllabus_reviewers`
--
ALTER TABLE `syllabus_reviewers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `syllabus_revisions`
--
ALTER TABLE `syllabus_revisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `syllabus_weeks`
--
ALTER TABLE `syllabus_weeks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_assignments`
--
ALTER TABLE `user_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_otps`
--
ALTER TABLE `user_otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `week_contents`
--
ALTER TABLE `week_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_calendar_events`
--
ALTER TABLE `academic_calendar_events`
  ADD CONSTRAINT `academic_calendar_events_academic_calendar_id_foreign` FOREIGN KEY (`academic_calendar_id`) REFERENCES `academic_calendars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `college_goals`
--
ALTER TABLE `college_goals`
  ADD CONSTRAINT `college_goals_college_id_foreign` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complete_syllabi`
--
ALTER TABLE `complete_syllabi`
  ADD CONSTRAINT `complete_syllabi_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complete_syllabi_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complete_syllabi_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_components`
--
ALTER TABLE `course_components`
  ADD CONSTRAINT `course_components_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_curriculum_maps`
--
ALTER TABLE `course_curriculum_maps`
  ADD CONSTRAINT `course_curriculum_maps_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_curriculum_maps_program_outcome_id_foreign` FOREIGN KEY (`program_outcome_id`) REFERENCES `program_outcomes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_outcomes`
--
ALTER TABLE `course_outcomes`
  ADD CONSTRAINT `course_outcomes_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_college_id_foreign` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `department_objectives`
--
ALTER TABLE `department_objectives`
  ADD CONSTRAINT `department_objectives_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_departments`
--
ALTER TABLE `program_departments`
  ADD CONSTRAINT `program_departments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `program_departments_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_eos`
--
ALTER TABLE `program_eos`
  ADD CONSTRAINT `program_eos_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_outcomes`
--
ALTER TABLE `program_outcomes`
  ADD CONSTRAINT `program_outcomes_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `program_outcome_peo`
--
ALTER TABLE `program_outcome_peo`
  ADD CONSTRAINT `program_outcome_peo_program_eo_id_foreign` FOREIGN KEY (`program_eo_id`) REFERENCES `program_eos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `program_outcome_peo_program_outcome_id_foreign` FOREIGN KEY (`program_outcome_id`) REFERENCES `program_outcomes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `syllabi`
--
ALTER TABLE `syllabi`
  ADD CONSTRAINT `syllabi_academic_calendar_id_foreign` FOREIGN KEY (`academic_calendar_id`) REFERENCES `academic_calendars` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `syllabi_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `syllabi_concurred_by_foreign` FOREIGN KEY (`concurred_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `syllabi_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `syllabi_prepared_by_foreign` FOREIGN KEY (`prepared_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `syllabus_evaluation_items`
--
ALTER TABLE `syllabus_evaluation_items`
  ADD CONSTRAINT `syllabus_evaluation_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `syllabus_evaluation_items_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `syllabus_evaluation_items_week_content_id_foreign` FOREIGN KEY (`week_content_id`) REFERENCES `week_contents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `syllabus_materials`
--
ALTER TABLE `syllabus_materials`
  ADD CONSTRAINT `syllabus_materials_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `syllabus_materials_syllabus_week_id_foreign` FOREIGN KEY (`syllabus_week_id`) REFERENCES `syllabus_weeks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `syllabus_references`
--
ALTER TABLE `syllabus_references`
  ADD CONSTRAINT `syllabus_references_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `syllabus_references_syllabus_week_id_foreign` FOREIGN KEY (`syllabus_week_id`) REFERENCES `syllabus_weeks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `syllabus_reviewers`
--
ALTER TABLE `syllabus_reviewers`
  ADD CONSTRAINT `syllabus_reviewers_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `syllabus_reviewers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `syllabus_revisions`
--
ALTER TABLE `syllabus_revisions`
  ADD CONSTRAINT `syllabus_revisions_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `syllabus_weeks`
--
ALTER TABLE `syllabus_weeks`
  ADD CONSTRAINT `syllabus_weeks_syllabus_id_foreign` FOREIGN KEY (`syllabus_id`) REFERENCES `syllabi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_assignments`
--
ALTER TABLE `user_assignments`
  ADD CONSTRAINT `user_assignments_college_id_foreign` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_assignments_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_otps`
--
ALTER TABLE `user_otps`
  ADD CONSTRAINT `user_otps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `week_contents`
--
ALTER TABLE `week_contents`
  ADD CONSTRAINT `week_contents_course_outcome_id_foreign` FOREIGN KEY (`course_outcome_id`) REFERENCES `course_outcomes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `week_contents_syllabus_week_id_foreign` FOREIGN KEY (`syllabus_week_id`) REFERENCES `syllabus_weeks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
