-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 04, 2016 at 09:47 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `live_search`
--
CREATE DATABASE IF NOT EXISTS `live_search` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `live_search`;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_image` text NOT NULL,
  `name` varchar(200) NOT NULL,
  `price` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `pro_image`, `name`, `price`) VALUES
(1, '1.png', 'Mi 4i (Grey, 32 GB)', '20000'),
(2, '4.png', 'Samsung Galaxy J7 (Gold, 16 GB)', '12657'),
(3, '2.png', 'Samsung Tab', '26779'),
(4, '7.png', 'Nokia 5233', '6500'),
(5, '5.png', 'Microsoft Lumia 950', '12000'),
(6, '3.png', 'Mi Tab', '15000'),
(7, '8.png', 'Sandisk Memory Card', '345'),
(8, '6.png', 'Philips HL1631 500 W Juicer Mixer Grinder', '4500');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
