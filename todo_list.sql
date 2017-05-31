-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Oct 28, 2014 at 04:39 PM
-- Server version: 5.5.34
-- PHP Version: 5.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `simple_task_list_tutorial`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `completed`  TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `todo` (`id`, `title`, `date`, `time`, `completed`) VALUES
(2, 'Build to-do list app', '2014-10-23', '04:02:31', 0),
(3, 'Add to-do items', '2014-10-28', '16:21:12', 0);
