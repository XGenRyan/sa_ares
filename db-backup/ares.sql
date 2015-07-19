-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 19, 2015 at 06:42 PM
-- Server version: 5.6.24-0ubuntu2
-- PHP Version: 5.6.4-4ubuntu6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ares`
--

-- --------------------------------------------------------

--
-- Table structure for table `clanmembers`
--

CREATE TABLE IF NOT EXISTS `clanmembers` (
`id` int(99) NOT NULL,
  `username` varchar(32) NOT NULL,
  `clan` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clanrequests`
--

CREATE TABLE IF NOT EXISTS `clanrequests` (
`id` int(99) NOT NULL,
  `name` text NOT NULL,
  `leader` text NOT NULL,
  `website` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clans`
--

CREATE TABLE IF NOT EXISTS `clans` (
`id` int(99) NOT NULL,
  `points` decimal(20,10) NOT NULL DEFAULT '1500.0000000000',
  `name` varchar(32) NOT NULL,
  `leader` varchar(32) NOT NULL,
  `members` int(99) NOT NULL DEFAULT '1',
  `wins` int(99) NOT NULL DEFAULT '0',
  `losses` int(99) NOT NULL DEFAULT '0',
  `logo` varchar(255) NOT NULL DEFAULT 'http://microvolts.com/Content/images/CLUB_HOUSE/clan/crew_default.jpg',
  `website` text NOT NULL,
  `motto` varchar(99) NOT NULL,
  `creation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE IF NOT EXISTS `contact` (
`id` int(11) NOT NULL,
  `sender` varchar(32) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `category` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `sender_ip` varchar(39) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cookies`
--

CREATE TABLE IF NOT EXISTS `cookies` (
`id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `cookie` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `joinrequests`
--

CREATE TABLE IF NOT EXISTS `joinrequests` (
`id` int(99) NOT NULL,
  `username` varchar(32) NOT NULL,
  `clan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE IF NOT EXISTS `queue` (
`id` int(99) NOT NULL,
  `sender` varchar(32) NOT NULL,
  `recipient` varchar(32) NOT NULL,
  `action` varchar(32) NOT NULL,
  `sender_read` datetime DEFAULT NULL,
  `recipient_read` datetime DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `resetquestion`
--

CREATE TABLE IF NOT EXISTS `resetquestion` (
`id` int(99) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `skey` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
`id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `perms` tinyint(4) NOT NULL DEFAULT '0',
  `cookie` text,
  `current_ip` varchar(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `statmonitor`
--

CREATE TABLE IF NOT EXISTS `statmonitor` (
`id` int(99) NOT NULL,
  `attacker` varchar(32) NOT NULL,
  `defender` varchar(32) NOT NULL,
  `a_p1` varchar(32) DEFAULT NULL,
  `a_p2` varchar(32) DEFAULT NULL,
  `a_wins` tinyint(4) NOT NULL DEFAULT '0',
  `d_p1` varchar(32) DEFAULT NULL,
  `d_p2` varchar(32) DEFAULT NULL,
  `d_wins` tinyint(4) NOT NULL DEFAULT '0',
  `tac` tinyint(1) NOT NULL,
  `a_ready` tinyint(1) NOT NULL DEFAULT '0',
  `d_ready` tinyint(1) NOT NULL DEFAULT '0',
  `started` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tempids`
--

CREATE TABLE IF NOT EXISTS `tempids` (
`id` int(11) NOT NULL,
  `user_id` int(255) NOT NULL,
  `username` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(99) NOT NULL,
  `uid` int(99) NOT NULL,
  `username` varchar(32) NOT NULL,
  `clan` varchar(255) NOT NULL DEFAULT 'no',
  `email` text NOT NULL,
  `sec_question` text NOT NULL,
  `sec_answer` text NOT NULL,
  `reg_ip` text NOT NULL,
  `reg_date` text NOT NULL,
  `current_ip` text NOT NULL,
  `last_login` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wardeclares`
--

CREATE TABLE IF NOT EXISTS `wardeclares` (
`id` int(99) NOT NULL,
  `attacker` varchar(32) NOT NULL,
  `defender` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `rounds` varchar(32) NOT NULL,
  `map` varchar(32) NOT NULL,
  `tac` tinyint(1) NOT NULL DEFAULT '0',
  `fks` tinyint(1) NOT NULL DEFAULT '1',
  `guns` tinyint(1) NOT NULL DEFAULT '1',
  `melee` tinyint(1) NOT NULL DEFAULT '1',
  `hammercamps` tinyint(1) NOT NULL DEFAULT '1',
  `running` tinyint(1) NOT NULL DEFAULT '1',
  `stalling` tinyint(1) NOT NULL DEFAULT '1',
  `podcamps` tinyint(1) NOT NULL DEFAULT '1',
  `taor` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `warlogs`
--

CREATE TABLE IF NOT EXISTS `warlogs` (
`id` int(11) NOT NULL,
  `attacker` varchar(32) NOT NULL,
  `a_starting_points` decimal(20,10) NOT NULL,
  `a_point_diff` decimal(20,10) NOT NULL,
  `a_p1` varchar(32) DEFAULT NULL,
  `a_p2` varchar(32) DEFAULT NULL,
  `a_deaths` int(99) NOT NULL,
  `defender` varchar(32) NOT NULL,
  `d_starting_points` decimal(20,10) NOT NULL,
  `d_point_diff` decimal(20,10) NOT NULL,
  `d_p1` varchar(32) DEFAULT NULL,
  `d_p2` varchar(32) DEFAULT NULL,
  `d_deaths` int(99) NOT NULL,
  `a_wins` tinyint(4) NOT NULL,
  `d_wins` int(11) NOT NULL,
  `completed` tinyint(1) NOT NULL,
  `type` varchar(32) DEFAULT NULL,
  `rounds` varchar(32) DEFAULT NULL,
  `map` varchar(32) DEFAULT NULL,
  `tac` tinyint(1) NOT NULL DEFAULT '0',
  `fks` tinyint(1) NOT NULL DEFAULT '1',
  `guns` tinyint(1) NOT NULL DEFAULT '1',
  `melee` tinyint(1) NOT NULL DEFAULT '1',
  `hammercamps` tinyint(1) NOT NULL DEFAULT '1',
  `running` tinyint(1) NOT NULL DEFAULT '1',
  `stalling` tinyint(1) NOT NULL DEFAULT '1',
  `podcamps` tinyint(1) NOT NULL DEFAULT '1',
  `taor` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clanmembers`
--
ALTER TABLE `clanmembers`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clanrequests`
--
ALTER TABLE `clanrequests`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clans`
--
ALTER TABLE `clans`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cookies`
--
ALTER TABLE `cookies`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `joinrequests`
--
ALTER TABLE `joinrequests`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resetquestion`
--
ALTER TABLE `resetquestion`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statmonitor`
--
ALTER TABLE `statmonitor`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tempids`
--
ALTER TABLE `tempids`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wardeclares`
--
ALTER TABLE `wardeclares`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `warlogs`
--
ALTER TABLE `warlogs`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clanmembers`
--
ALTER TABLE `clanmembers`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `clanrequests`
--
ALTER TABLE `clanrequests`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `clans`
--
ALTER TABLE `clans`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cookies`
--
ALTER TABLE `cookies`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `joinrequests`
--
ALTER TABLE `joinrequests`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `resetquestion`
--
ALTER TABLE `resetquestion`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `statmonitor`
--
ALTER TABLE `statmonitor`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tempids`
--
ALTER TABLE `tempids`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wardeclares`
--
ALTER TABLE `wardeclares`
MODIFY `id` int(99) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `warlogs`
--
ALTER TABLE `warlogs`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
