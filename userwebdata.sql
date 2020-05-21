-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 21, 2020 at 06:32 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 7.0.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `userwebdata`
--

-- --------------------------------------------------------

--
-- Table structure for table `confirmationdata`
--

CREATE TABLE `confirmationdata` (
  `user_id` varchar(255) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `day` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `reg_date` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `string` varchar(25) NOT NULL,
  `otp` int(4) NOT NULL,
  `attempts` int(4) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userprofiledata`
--

CREATE TABLE `userprofiledata` (
  `user_id` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `country` char(100) DEFAULT NULL,
  `mobile` bigint(15) DEFAULT NULL,
  `gender` char(10) DEFAULT NULL,
  `about` varchar(2000) DEFAULT NULL,
  `quotes` char(255) DEFAULT NULL,
  `profile_pic` char(255) DEFAULT NULL,
  `wall_pic` char(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userrootdata`
--

CREATE TABLE `userrootdata` (
  `user_id` varchar(255) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `day` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `reg_date` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `string` varchar(25) NOT NULL DEFAULT '0',
  `otp` int(4) NOT NULL DEFAULT '0',
  `blockpriority` int(4) NOT NULL DEFAULT '0',
  `useragent` varchar(255) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `usersessiondata`
--

CREATE TABLE `usersessiondata` (
  `user_id` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `expire` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` varchar(255) NOT NULL DEFAULT '0',
  `cookie` varchar(255) NOT NULL DEFAULT '0',
  `user` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `confirmationdata`
--
ALTER TABLE `confirmationdata`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userprofiledata`
--
ALTER TABLE `userprofiledata`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userrootdata`
--
ALTER TABLE `userrootdata`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `usersessiondata`
--
ALTER TABLE `usersessiondata`
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
