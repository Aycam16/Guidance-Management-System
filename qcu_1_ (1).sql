-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2025 at 03:41 PM
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
-- Database: `qcu(1)`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_info`
--

CREATE TABLE `admin_info` (
  `Employee_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_info`
--

INSERT INTO `admin_info` (`Employee_ID`, `User_ID`, `name`, `position`, `profile_pic`) VALUES
(2, 17, 'Ayra Peta', 'Admin', NULL),
(3, 23, 'yo yo', 'Admin', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `Announcement_ID` int(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image_path` text NOT NULL,
  `posted_by` int(10) NOT NULL,
  `post_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `edited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `is_removed` tinyint(1) NOT NULL DEFAULT 0,
  `is_important` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(10) DEFAULT 'Posted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`Announcement_ID`, `title`, `description`, `image_path`, `posted_by`, `post_date`, `edited_at`, `is_archived`, `is_removed`, `is_important`, `status`) VALUES
(1, 'title lang', 'gwenchana', '', 0, '2024-12-02 08:54:44', '2024-12-02 08:54:44', 0, 1, 0, 'Posted'),
(2, 'asd', 'asd', '', 0, '2024-12-02 09:28:47', '2024-12-02 09:28:47', 0, 1, 0, 'Posted'),
(3, 'zzz', 'zzz', '', 11, '2024-12-02 09:31:05', '2024-12-02 09:31:05', 0, 1, 0, 'Posted'),
(4, '4', '4', '', 0, '2024-12-02 10:09:10', '2024-12-02 10:09:10', 0, 1, 0, 'Posted'),
(5, '4', '4', '', 11, '2024-12-02 10:09:35', '2024-12-02 10:09:35', 0, 1, 0, 'Posted'),
(6, '5', '5', '', 11, '2024-12-02 10:10:03', '2024-12-02 10:10:03', 0, 1, 0, 'Posted'),
(7, '6', '6', '', 11, '2024-12-02 10:10:11', '2024-12-02 10:10:11', 0, 1, 0, 'Posted'),
(8, '7', '7', '', 11, '2024-12-02 10:10:18', '2024-12-02 10:10:18', 0, 1, 0, 'Posted'),
(9, 'xiangli yao', 'my babe^^', 'announcement_674e6881747596.31349336.jpg', 0, '2024-12-03 02:10:09', '2024-12-03 02:44:58', 0, 1, 1, NULL),
(10, '#WorldMentalHealthDay', '“My dark days made me stronger. Or maybe I already was strong, and they made me prove it. Dark days made me stronger. Or maybe I already was strong, and they made me prove it.” — Emery L-', 'announcement_674fb12c5ccd06.01098575.png', 0, '2024-12-04 01:31:47', '2024-12-04 09:43:50', 0, 0, 1, NULL),
(11, 'For announcements', 'announcement', 'announcement_67502444837556.28502335.png', 0, '2024-12-04 09:43:32', '2024-12-04 09:43:32', 0, 0, 0, 'Posted');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `section` varchar(50) NOT NULL,
  `course` varchar(50) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(50) NOT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `name`, `email`, `section`, `course`, `reason`, `appointment_date`, `appointment_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Jana shee', 'ayracamille2017@gmail.com', 'SBIT-2F', 'BSIT', 'Mental', '2024-12-27', '8:00 - 9:00 am', 'Pending', '2024-12-07 07:01:50', '2024-12-07 07:01:50'),
(2, 'Ayra', 'ayracamille2017@gmail.com', 'SBIT-2F', 'BSCS', 'School', '2024-12-20', '8:00 - 9:00 am', 'Pending', '2024-12-07 07:11:02', '2024-12-07 07:11:02'),
(3, 'Xiangli Yao', 'ayracamille2017@gmail.com', 'SBIT-2F', 'BSIS', 'School', '2024-12-17', '9:00 - 10:00 am', 'Pending', '2024-12-07 07:24:58', '2024-12-07 07:24:58'),
(4, 'jkjkj', '1@gmail.com', 'SBIT-2F', 'BSIT', 'Mental', '2024-12-21', '8:00 - 9:00 am', 'Pending', '2024-12-07 07:37:47', '2024-12-07 07:37:47'),
(5, 'Ayra Peta', 'counselor@gmail.com', 'SBIT2F', 'BSIT', 'Mental', '2025-04-30', '8:00 - 9:00 am', 'Pending', '2025-04-11 06:26:21', '2025-04-11 06:26:21');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_history`
--

CREATE TABLE `appointment_history` (
  `history_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `status` enum('Updated','Cancelled') NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_history`
--

INSERT INTO `appointment_history` (`history_id`, `appointment_id`, `status`, `updated_at`) VALUES
(1, 3, '', '2024-12-07 07:24:58'),
(2, 4, '', '2024-12-07 07:37:47'),
(3, 5, '', '2025-04-11 06:26:21');

-- --------------------------------------------------------

--
-- Table structure for table `assesment`
--

CREATE TABLE `assesment` (
  `Assesment_ID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `is_removed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assesment`
--

INSERT INTO `assesment` (`Assesment_ID`, `title`, `description`, `created_by`, `is_archived`, `is_removed`, `created_at`, `updated_at`) VALUES
(6, 'Mental Health', 'Recognize the mental health issues of the student', 0, 0, 0, '2024-12-04 08:46:48', '2024-12-04 08:46:48'),
(7, 'Career Guidance', 'this is for the student career', 0, 0, 0, '2024-12-07 05:44:59', '2024-12-07 05:44:59');

-- --------------------------------------------------------

--
-- Table structure for table `assesment_questions`
--

CREATE TABLE `assesment_questions` (
  `Question_ID` int(11) NOT NULL,
  `Assesment_ID` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `likert_scale` enum('Strongly Disagree','Disagree','Neutral','Agree','Strongly Agree') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response_option` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assesment_questions`
--

INSERT INTO `assesment_questions` (`Question_ID`, `Assesment_ID`, `question_text`, `likert_scale`, `created_at`, `response_option`) VALUES
(1, 5, 'dada', 'Strongly Disagree', '2024-12-03 16:08:44', 'Strongly Agree'),
(2, 5, 'tefsfsdfsdf', 'Strongly Disagree', '2024-12-03 16:08:44', 'Strongly Agree'),
(3, 6, 'Always Stress', 'Strongly Disagree', '2024-12-04 08:46:48', 'Strongly Agree'),
(4, 6, 'Are you always nervous?', 'Strongly Disagree', '2024-12-04 08:46:48', 'Neutral'),
(5, 7, 'I feel confident about making decisions about my career path.', 'Strongly Disagree', '2024-12-07 05:44:59', 'Strongly Agree'),
(6, 7, 'I have a clear understanding of my career goals.', 'Strongly Disagree', '2024-12-07 05:44:59', 'Neutral'),
(7, 7, 'I feel well-informed about my options for higher education or employment.', 'Strongly Disagree', '2024-12-07 05:44:59', 'Neutral'),
(8, 7, 'I receive adequate support from mentors or family members in career planning.', 'Strongly Disagree', '2024-12-07 05:44:59', 'Neutral'),
(9, 7, 'I know where to find resources to support career exploration.', 'Strongly Disagree', '2024-12-07 05:44:59', 'Neutral');

-- --------------------------------------------------------

--
-- Table structure for table `assesment_response`
--

CREATE TABLE `assesment_response` (
  `Response_ID` int(11) NOT NULL,
  `Question_ID` int(11) NOT NULL,
  `Student_ID` int(11) DEFAULT NULL,
  `response_data` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Assesment_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assesment_result`
--

CREATE TABLE `assesment_result` (
  `Result_ID` int(11) NOT NULL,
  `Assesment_ID` int(11) NOT NULL,
  `Student_ID` int(11) NOT NULL,
  `score` decimal(25,0) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counseling_appointment`
--

CREATE TABLE `counseling_appointment` (
  `Appointment_ID` int(11) NOT NULL,
  `Student_ID` int(11) NOT NULL,
  `Employee_ID` int(11) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `student_number` int(6) NOT NULL,
  `course` enum('BSIT','BSBA','BSENT','BSIE','BSECE') NOT NULL,
  `section` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL DEFAULT current_timestamp(),
  `appointment_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` enum('GOOD MORAL','','','') NOT NULL,
  `status` enum('PENDING','FOR PICK-UP','CLAIMED') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counseling_modules`
--

CREATE TABLE `counseling_modules` (
  `Module_ID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `file_path` text NOT NULL,
  `uploaded_by` varchar(100) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `edited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `is_removed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `counselor_info`
--

CREATE TABLE `counselor_info` (
  `Employee_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counselor_info`
--

INSERT INTO `counselor_info` (`Employee_ID`, `User_ID`, `name`, `department`) VALUES
(1, 18, 'Jana shee', 'CCS'),
(2, 19, 'Jana shee', 'CCS'),
(3, 21, 'Xiangli Yao', 'CCS');

-- --------------------------------------------------------

--
-- Table structure for table `employee_info`
--

CREATE TABLE `employee_info` (
  `Employee_ID` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`Employee_ID`, `User_ID`, `name`, `department`) VALUES
(1, 22, 'sha sha', 'ENGINEERING');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `Feedback_ID` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `statement` text NOT NULL DEFAULT current_timestamp(),
  `response` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`Feedback_ID`, `category`, `statement`, `response`) VALUES
(0, 'accessibility', 'Placeholder statement for accessibility', 2),
(0, 'professionalism', 'Placeholder statement for professionalism', 3),
(0, 'timeliness', 'Placeholder statement for timeliness', 4),
(0, 'effectiveness', 'Placeholder statement for effectiveness', 5),
(0, 'confidentiality', 'Placeholder statement for confidentiality', 4),
(0, 'accessibility', 'Placeholder statement for accessibility', 2),
(0, 'professionalism', 'Placeholder statement for professionalism', 3),
(0, 'timeliness', 'Placeholder statement for timeliness', 4),
(0, 'effectiveness', 'Placeholder statement for effectiveness', 5),
(0, 'confidentiality', 'Placeholder statement for confidentiality', 4),
(0, 'accessibility', 'Placeholder statement for accessibility', 1),
(0, 'professionalism', 'Placeholder statement for professionalism', 5),
(0, 'timeliness', 'Placeholder statement for timeliness', 4),
(0, 'effectiveness', 'Placeholder statement for effectiveness', 2),
(0, 'confidentiality', 'Placeholder statement for confidentiality', 3);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_result`
--

CREATE TABLE `feedback_result` (
  `Feedback_Result_ID` int(11) NOT NULL,
  `Feedback_ID` int(11) NOT NULL,
  `statement_text` varchar(255) NOT NULL,
  `total_response` int(11) NOT NULL,
  `strongly_disagree` int(11) NOT NULL DEFAULT 0,
  `disagree` int(11) NOT NULL DEFAULT 0,
  `neutral` int(11) NOT NULL DEFAULT 0,
  `agree` int(11) NOT NULL DEFAULT 0,
  `strongly agree` int(11) NOT NULL DEFAULT 0,
  `satisfaction_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmc_degree_course`
--

CREATE TABLE `gmc_degree_course` (
  `DegreeCourses_ID` int(11) NOT NULL,
  `Request_ID` int(11) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `middle_initial` varchar(5) NOT NULL,
  `suffix` varchar(4) NOT NULL,
  `student_number` varchar(7) NOT NULL,
  `course` enum('BSIT') NOT NULL,
  `sy_started` enum('2024-2025') NOT NULL,
  `semester_started` enum('2024-2025') NOT NULL,
  `last_sy_attended` enum('2024-2025') NOT NULL,
  `last_semester_attended` enum('2024-2025') NOT NULL,
  `is_graduate` enum('YES','NO') NOT NULL,
  `email` varchar(50) NOT NULL,
  `purpose_of_request` enum('TRANSFER','ENROLLMENT') NOT NULL,
  `remarks` text NOT NULL,
  `file_upload` text NOT NULL,
  `status` enum('PENDING','FOR PICK-UP','CLAIMED') NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmc_request`
--

CREATE TABLE `gmc_request` (
  `Request_ID` int(11) NOT NULL,
  `preferred_pickup_location` enum('MAIN','BATASAN','SAN FRANCISCO') NOT NULL,
  `enrollment_status` enum('BACHELOR','SHS','TECHVOC') NOT NULL,
  `data_privacy` enum('Yes','No') NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmc_request_status`
--

CREATE TABLE `gmc_request_status` (
  `Confirmation_ID` int(11) NOT NULL,
  `DegreeCourses_ID` int(11) NOT NULL,
  `SHS_ID` int(11) NOT NULL,
  `Techvoc_ID` int(11) NOT NULL,
  `confirmation_date` date NOT NULL,
  `status` enum('CONFIRMED','CANCELLED') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmc_shs`
--

CREATE TABLE `gmc_shs` (
  `SHS_ID` int(11) NOT NULL,
  `Request_ID` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` varchar(50) NOT NULL,
  `suffix` varchar(6) NOT NULL,
  `student_number` varchar(7) NOT NULL,
  `course` enum('BSIT') NOT NULL,
  `sy_started` enum('2024-2025') NOT NULL,
  `semester_started` enum('2024-2025') NOT NULL,
  `last_sy_attended` enum('2024-2025') NOT NULL,
  `last_semester_attended` enum('2024-2025') NOT NULL,
  `is_graduate` enum('YES','NO') NOT NULL,
  `email` varchar(50) NOT NULL,
  `purpose_of_request` enum('TRANSFER','ENROLLMENT') NOT NULL,
  `remarks` text NOT NULL,
  `file_upload` text NOT NULL,
  `status` enum('PENDING','FOR PICK-UP','CLAIMED') NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmc_techvoc`
--

CREATE TABLE `gmc_techvoc` (
  `Techvoc_ID` int(11) NOT NULL,
  `Request_ID` int(11) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `middle_initial` varchar(6) NOT NULL,
  `suffix` varchar(10) NOT NULL,
  `student_number` varchar(7) NOT NULL,
  `course` varchar(50) NOT NULL,
  `sy_started` enum('2024-2025') NOT NULL,
  `semester_started` enum('2024-2025') NOT NULL,
  `last_sy_attended` enum('2024-2025') NOT NULL,
  `last_semester_attended` enum('2024-2025') NOT NULL,
  `is_graduate` enum('YES','NO') NOT NULL,
  `email` varchar(50) NOT NULL,
  `purpose_of_request` enum('TRANSFER','ENROLLMENT') NOT NULL,
  `remarks` text NOT NULL,
  `file_upload` text NOT NULL,
  `status` enum('PENDING','FOR PICK-UP','CLAIMED') NOT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `ModuleID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `FilePath` text NOT NULL,
  `Status` enum('post','draft') NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`ModuleID`, `Title`, `Description`, `FilePath`, `Status`) VALUES
(5, 'What is Guidance and Counselor', 'About Guidance and Counselor', 'AboutGuidanceandCounselor.pdf', 'post'),
(6, 'THE ROLE OF TEACHERS IN THE GUIDANCE PROCESS', 'In this Module shows how teachers can be a guidance.', 'TheZRoleZofZTeachersZinZGuidance.pdf', 'post');

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `User_ID` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_details`
--

INSERT INTO `student_details` (`User_ID`, `first_name`, `last_name`, `year_level`, `course`) VALUES
(20, 'Ayra', 'Peta', '3rd', 'BSIT'),
(24, 'Camille', 'Amdeyao', '2nd year', 'BSAC'),
(25, 'Paul', 'Camacho', '2nd year', 'BSIT'),
(26, 'Allyana', 'Cabilling', '4th', 'BS ENG');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `student_number` int(10) NOT NULL,
  `User_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `User_ID` int(10) NOT NULL,
  `student_number` int(10) DEFAULT NULL,
  `employee_ID` int(10) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`User_ID`, `student_number`, `employee_ID`, `email`, `password`, `role`, `created_at`) VALUES
(20, 21, NULL, 'student@gmail.com', '$2y$10$/ZaG.jT6tovoJvpyQXUD5OrBrbpTRkcBSSHeUlwje2LeYAnmUafIu', 'student', '2024-12-02 20:56:42'),
(21, NULL, NULL, 'counselor@gmail.com', '$2y$10$ZXVqHDcFLm.sJDW7NQNKa.euGYk/C8rQtiE5SmavtbZlVmNgcai96', 'counselor', '2024-12-02 20:57:45'),
(22, NULL, NULL, 'staff@gmail.com', '$2y$10$ljuCCS63UtEadMdKXj0ef.AIjgSV65xMEI7/y9pKpNH7xyFqBbJUi', 'employee', '2024-12-02 21:09:04'),
(23, NULL, NULL, 'admin@gmail.com', '$2y$10$OxVKVpoYc2LNLcIs7n8Fb.HZX3Hi5KCtsY0E9YJDxUJzPgdCvFGeu', 'admin', '2024-12-02 21:09:40'),
(24, 21, NULL, 'camille@gmail.com', '$2y$10$91AkrB9L7Ambh7Suvp6ZlO6feBeLrhzQa2Imqg06NQz5o6DnbIdmy', 'student', '2024-12-04 08:37:36'),
(25, 23, NULL, 'camacho@gmail.com', '$2y$10$GXdgU6iepO//RW8WPTVQ7etyjXldP.j7eMIgyORhW4k7rsoevq9Om', 'student', '2024-12-04 08:39:23'),
(26, 22, NULL, 'cabilling@gmail.com', '$2y$10$5XGutF1590ny7Nu3NnCPgOzPq6UJwB1bDPHUQZ5l5DAYr7yVzviBO', 'student', '2024-12-04 08:40:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_info`
--
ALTER TABLE `admin_info`
  ADD PRIMARY KEY (`Employee_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`Announcement_ID`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`);

--
-- Indexes for table `appointment_history`
--
ALTER TABLE `appointment_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `assesment`
--
ALTER TABLE `assesment`
  ADD PRIMARY KEY (`Assesment_ID`);

--
-- Indexes for table `assesment_questions`
--
ALTER TABLE `assesment_questions`
  ADD PRIMARY KEY (`Question_ID`),
  ADD KEY `Assesment_ID` (`Assesment_ID`);

--
-- Indexes for table `assesment_response`
--
ALTER TABLE `assesment_response`
  ADD PRIMARY KEY (`Response_ID`),
  ADD KEY `Question_ID` (`Question_ID`);

--
-- Indexes for table `assesment_result`
--
ALTER TABLE `assesment_result`
  ADD PRIMARY KEY (`Result_ID`);

--
-- Indexes for table `counseling_appointment`
--
ALTER TABLE `counseling_appointment`
  ADD PRIMARY KEY (`Appointment_ID`);

--
-- Indexes for table `counseling_modules`
--
ALTER TABLE `counseling_modules`
  ADD PRIMARY KEY (`Module_ID`);

--
-- Indexes for table `counselor_info`
--
ALTER TABLE `counselor_info`
  ADD PRIMARY KEY (`Employee_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD PRIMARY KEY (`Employee_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `gmc_degree_course`
--
ALTER TABLE `gmc_degree_course`
  ADD PRIMARY KEY (`DegreeCourses_ID`);

--
-- Indexes for table `gmc_request`
--
ALTER TABLE `gmc_request`
  ADD PRIMARY KEY (`Request_ID`);

--
-- Indexes for table `gmc_request_status`
--
ALTER TABLE `gmc_request_status`
  ADD PRIMARY KEY (`Confirmation_ID`);

--
-- Indexes for table `gmc_shs`
--
ALTER TABLE `gmc_shs`
  ADD PRIMARY KEY (`SHS_ID`);

--
-- Indexes for table `gmc_techvoc`
--
ALTER TABLE `gmc_techvoc`
  ADD PRIMARY KEY (`Techvoc_ID`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`ModuleID`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`User_ID`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD KEY `student_number` (`student_number`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`User_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_info`
--
ALTER TABLE `admin_info`
  MODIFY `Employee_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `Announcement_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `appointment_history`
--
ALTER TABLE `appointment_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assesment`
--
ALTER TABLE `assesment`
  MODIFY `Assesment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assesment_questions`
--
ALTER TABLE `assesment_questions`
  MODIFY `Question_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `assesment_response`
--
ALTER TABLE `assesment_response`
  MODIFY `Response_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assesment_result`
--
ALTER TABLE `assesment_result`
  MODIFY `Result_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counseling_appointment`
--
ALTER TABLE `counseling_appointment`
  MODIFY `Appointment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counseling_modules`
--
ALTER TABLE `counseling_modules`
  MODIFY `Module_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `counselor_info`
--
ALTER TABLE `counselor_info`
  MODIFY `Employee_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `Employee_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gmc_degree_course`
--
ALTER TABLE `gmc_degree_course`
  MODIFY `DegreeCourses_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmc_request`
--
ALTER TABLE `gmc_request`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmc_request_status`
--
ALTER TABLE `gmc_request_status`
  MODIFY `Confirmation_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmc_shs`
--
ALTER TABLE `gmc_shs`
  MODIFY `SHS_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmc_techvoc`
--
ALTER TABLE `gmc_techvoc`
  MODIFY `Techvoc_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `ModuleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `User_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment_history`
--
ALTER TABLE `appointment_history`
  ADD CONSTRAINT `appointment_history_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD CONSTRAINT `employee_info_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `tbl_users` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_details`
--
ALTER TABLE `student_details`
  ADD CONSTRAINT `student_details_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `tbl_users` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `student_info`
--
ALTER TABLE `student_info`
  ADD CONSTRAINT `student_info_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `tbl_users` (`User_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
