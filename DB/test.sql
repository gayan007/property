-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2020 at 04:24 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

DROP TABLE IF EXISTS `property`;
CREATE TABLE `property` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `property_type_id` int(11) NOT NULL,
  `county` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(255) NOT NULL,
  `image_full` varchar(255) NOT NULL,
  `image_thumbnail` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `num_bedrooms` int(11) NOT NULL,
  `num_bathrooms` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted` tinyint(1) DEFAULT NULL,
  `updated` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `property_type`
--

DROP TABLE IF EXISTS `property_type`;
CREATE TABLE `property_type` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `property_type`
--

INSERT INTO `property_type` VALUES(1, 'Flat', 'rom studio flats, to maisonettes and 2-storey flats, a flat is a living area that is self-contained and in one part of a building. A building is usually split into individual flats and the communal areas are those that are shared e.g. lifts, stairwells, receptions etc.', '2020-01-08 12:55:35', NULL);
INSERT INTO `property_type` VALUES(2, 'Detatched', 'Detached houses are more likely to be the property types we all dream of owning. They tend to be more private as they are single standing properties, and do not share walls with other houses. Due to its privacy, detached houses are a lot more expensive and high in demand.', '2020-01-08 12:55:35', NULL);
INSERT INTO `property_type` VALUES(3, 'Semi-detached', 'Semi-detached properties are a lot more common for homeowners to purchase/rent. There are a lot more semi-detached properties in the UK as they save a lot of space as they are houses paired together by a common wall. Semi-detached properties are fantastic options for homeowners to extend at the back and side and have an element of privacy too.', '2020-01-08 12:55:35', NULL);
INSERT INTO `property_type` VALUES(4, 'Terraced', 'Terraced houses are common in old industrial towns and cities such as Manchester, Bath and areas of central London. Terraced houses became extremely popular to provide high-density accommodation for the working class in the 19th century. Terraced houses are structurally built the same and both sides of each house shares walls with neighbours.', '2020-01-08 12:55:35', NULL);
INSERT INTO `property_type` VALUES(5, 'End of Terrace', 'It says it in the name. It is the end of a terraced house and only one side shares a common wall, while the other is detached.', '2020-01-08 12:55:35', NULL);
INSERT INTO `property_type` VALUES(6, 'Cottage', 'It says it in the name. It is the end of a terraced house and only one side shares a common wall, while the other is detached.', '2020-01-08 12:55:35', NULL);
INSERT INTO `property_type` VALUES(7, 'Bungalow', 'The word ‘bungalow’, originates from the Indian word ‘Bangla’, which in the 19th century referred to houses that were built in a Bengali style. Houses that were made in Bengali style were traditionally very small and only one storey high and detached.', '2020-01-08 12:55:35', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`id`),
  ADD KEY `propertytype` (`property_type_id`);

--
-- Indexes for table `property_type`
--
ALTER TABLE `property_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1182;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `property`
--
ALTER TABLE `property`
  ADD CONSTRAINT `property_ibfk_1` FOREIGN KEY (`property_type_id`) REFERENCES `property_type` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
