-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2023 at 02:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `MyProject`
--

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `task` varchar(255) NOT NULL,
  `Date` date DEFAULT NULL,
  `Responsible` varchar(255) DEFAULT NULL,
  `Completed` tinyint(1) DEFAULT NULL,
  `task_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`task`, `Date`, `Responsible`, `Completed`, `task_id`) VALUES
('samer', '2023-07-29', 'user1@test.test', 1, 'Task A'),
('zngolyo', '2023-08-01', 'user1@test.test', 0, 'Task A'),
('zongol', '2023-07-29', 'user1@test.test', 0, 'Task B'),
('zzngur', '2023-08-01', 'user1@test.test', 0, 'Task A');

-- --------------------------------------------------------

--
-- Table structure for table `taskaccess`
--

CREATE TABLE `taskaccess` (
  `Task` varchar(255) NOT NULL,
  `CreationDate` date DEFAULT NULL,
  `Users` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taskaccess`
--

INSERT INTO `taskaccess` (`Task`, `CreationDate`, `Users`) VALUES
('Task A', '2023-07-29', 'user1@test.test'),
('Task B', '2023-07-29', 'user1@test.test, user2@test.test');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `token`) VALUES
('user1', 'user1', 'user1@test.test', 'test123', '$2y$10$r6soH.7qyEcsmDlpYzWNB.W0h4Wa6beiNd7wUYkXqEdv.jyTKQsXO'),
('user2', 'user2', 'user2@test.test', 'test123', '$2y$10$Le.bRRZ/ro0KbZTGhrCGLOQZYzhvGYMDSQ9HgW4nc7Zz1Ht3x0iQ2'),
('user3', 'user3', 'user3@test.test', 'test123', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`task`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `taskaccess`
--
ALTER TABLE `taskaccess`
  ADD PRIMARY KEY (`Task`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `taskaccess` (`Task`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;