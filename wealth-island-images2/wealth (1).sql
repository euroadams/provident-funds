-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 29, 2017 at 10:11 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 7.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wealth`
--

-- --------------------------------------------------------

--
-- Table structure for table `declinations`
--

CREATE TABLE `declinations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `DECLINED_MEMBER1` varchar(100) NOT NULL,
  `DECLINE1_TIME` int(11) NOT NULL,
  `DECLINED_MEMBER2` varchar(100) NOT NULL,
  `DECLINE2_TIME` int(11) NOT NULL,
  `TOTAL` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_classic_donations`
--

CREATE TABLE `euro_classic_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_classic_matching`
--

CREATE TABLE `euro_classic_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_elite_donations`
--

CREATE TABLE `euro_elite_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_elite_matching`
--

CREATE TABLE `euro_elite_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_lord_donations`
--

CREATE TABLE `euro_lord_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_lord_matching`
--

CREATE TABLE `euro_lord_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_master_donations`
--

CREATE TABLE `euro_master_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_master_matching`
--

CREATE TABLE `euro_master_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_premium_donations`
--

CREATE TABLE `euro_premium_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_premium_matching`
--

CREATE TABLE `euro_premium_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_royal_donations`
--

CREATE TABLE `euro_royal_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_royal_matching`
--

CREATE TABLE `euro_royal_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_standard_donations`
--

CREATE TABLE `euro_standard_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_standard_matching`
--

CREATE TABLE `euro_standard_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_ultimate_donations`
--

CREATE TABLE `euro_ultimate_donations` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `AMOUNT_PLEDGED` int(11) NOT NULL,
  `RETURN_AMOUNT` int(11) NOT NULL,
  `TIME_OF_PLEDGE` int(11) NOT NULL,
  `MATCH_STATUS` varchar(50) NOT NULL,
  `AMOUNT_MATCHED` int(11) NOT NULL,
  `AMOUNT_REM` int(11) NOT NULL,
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRM_TIME` int(11) NOT NULL,
  `REC_COUNTER` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `euro_ultimate_matching`
--

CREATE TABLE `euro_ultimate_matching` (
  `ID` int(11) NOT NULL,
  `PAYER_DID` int(11) NOT NULL,
  `PAYER_USERNAME` varchar(100) NOT NULL,
  `AMOUNT_TO_PAY` int(11) NOT NULL,
  `PAYER_DEADLINE` int(11) NOT NULL,
  `METHOD_OF_PAY` varchar(50) NOT NULL,
  `PAYMENT_SLIP_NAME` varchar(255) NOT NULL,
  `UPLOADED_PROOF` varchar(255) NOT NULL,
  `PAID_OR_DECLINED` varchar(50) NOT NULL,
  `TIME_OF_PAY` int(11) NOT NULL,
  `REC_DID` int(11) NOT NULL,
  `REC_USERNAME` varchar(100) NOT NULL,
  `COMMENTS` text NOT NULL,
  `CONFIRMED` varchar(50) NOT NULL,
  `CONFIRM_TIME` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `help`
--

CREATE TABLE `help` (
  `ID` int(11) NOT NULL,
  `TICKET_NO` varchar(100) NOT NULL,
  `SENDER` varchar(100) NOT NULL,
  `SUBJECT` varchar(100) NOT NULL,
  `CONTENT` text NOT NULL,
  `TIME` int(11) NOT NULL,
  `TOTAL_REPLIES` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `help`
--

INSERT INTO `help` (`ID`, `TICKET_NO`, `SENDER`, `SUBJECT`, `CONTENT`, `TIME`, `TOTAL_REPLIES`) VALUES
(1, '248532446110050', 'skyz', 'i need to unlock my account', 'please how do i go about it', 1488741414, 3),
(2, '534487692492696', 'trump', 'ACCOUNT SUSPENSION', 'please my account was suspended and I wish to unlock it', 1489130316, 0);

-- --------------------------------------------------------

--
-- Table structure for table `letters_of_happiness`
--

CREATE TABLE `letters_of_happiness` (
  `ID` int(11) NOT NULL,
  `SENDER` varchar(100) NOT NULL,
  `FULL_NAME` varchar(255) NOT NULL,
  `CONTENT` text NOT NULL,
  `LOCATION` varchar(100) NOT NULL,
  `TIME` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `letters_of_happiness`
--

INSERT INTO `letters_of_happiness` (`ID`, `SENDER`, `FULL_NAME`, `CONTENT`, `LOCATION`, `TIME`) VALUES
(1, 'wealth', '', 'My name is Emeka \r\nGuys it''s real o', '', 1487425473),
(2, 'wealth', 'wealth Stanz', 'Its hot and paying like water\r\njoin now', '', 1488829396),
(3, 'wealth', 'wealth Stanz', 'kk', '', 1488829670),
(4, 'wealth', 'wealth Stanz', 'Hi guys \r\nits paying o', 'Lagos, Nigeria', 1488832662);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `ID` int(11) NOT NULL,
  `SESSION_ID` varchar(255) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `EMAIL_CONFIRMATION_CODE` varchar(255) NOT NULL,
  `FIRST_NAME` varchar(255) NOT NULL,
  `LAST_NAME` varchar(255) NOT NULL,
  `FULL_NAME` varchar(255) NOT NULL,
  `TIME` int(11) NOT NULL,
  `AVN` varchar(255) NOT NULL,
  `OT_AVN` varchar(50) NOT NULL,
  `SUSPENSION_STATUS` varchar(50) NOT NULL DEFAULT 'NO',
  `ACCOUNT_STATUS` varchar(50) NOT NULL,
  `ACC_CONFIRMATION_CODE` varchar(255) NOT NULL,
  `AVATAR` varchar(255) NOT NULL,
  `TIME_UPLOADED` int(11) NOT NULL,
  `GENDER` varchar(50) NOT NULL,
  `COUNTRY` varchar(100) NOT NULL,
  `STATE` varchar(50) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL,
  `DOB` varchar(30) NOT NULL,
  `MARITAL_STATUS` varchar(30) NOT NULL,
  `USER_PRIVILEGE` varchar(50) NOT NULL DEFAULT 'MEMBER',
  `MOBILE_PHONE` varchar(30) NOT NULL,
  `ALT_MOBILE_PHONE` varchar(30) NOT NULL,
  `BANK_NAME` varchar(100) NOT NULL,
  `ACCOUNT_NUMBER` varchar(50) NOT NULL,
  `ACCOUNT_NAME` varchar(255) NOT NULL,
  `BVN` varchar(255) NOT NULL,
  `CURRENT_PACKAGE` varchar(50) NOT NULL DEFAULT 'NONE',
  `FLOW_DIRECTION` varchar(50) NOT NULL DEFAULT 'NONE',
  `LOOP_STATUS` varchar(50) NOT NULL DEFAULT 'COMPLETE',
  `COMMENT1` text NOT NULL,
  `COMMENT2` text NOT NULL,
  `TOTAL_DECL` int(11) NOT NULL,
  `TOTAL_PURGE` int(11) NOT NULL,
  `RECYCLING_DEADLINE` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`ID`, `SESSION_ID`, `USERNAME`, `PASSWORD`, `EMAIL`, `EMAIL_CONFIRMATION_CODE`, `FIRST_NAME`, `LAST_NAME`, `FULL_NAME`, `TIME`, `AVN`, `OT_AVN`, `SUSPENSION_STATUS`, `ACCOUNT_STATUS`, `ACC_CONFIRMATION_CODE`, `AVATAR`, `TIME_UPLOADED`, `GENDER`, `COUNTRY`, `STATE`, `ADDRESS`, `DOB`, `MARITAL_STATUS`, `USER_PRIVILEGE`, `MOBILE_PHONE`, `ALT_MOBILE_PHONE`, `BANK_NAME`, `ACCOUNT_NUMBER`, `ACCOUNT_NAME`, `BVN`, `CURRENT_PACKAGE`, `FLOW_DIRECTION`, `LOOP_STATUS`, `COMMENT1`, `COMMENT2`, `TOTAL_DECL`, `TOTAL_PURGE`, `RECYCLING_DEADLINE`) VALUES
(1, '', 'wealth', 'ae809921305294715d1952c8baba77796ec35d3d', 'sentinel@gmail.com', '0', 'wealth', 'Stanz', 'wealth Stanz', 1487282118, 'fd64bfd46af4c6fee8fb8dca28c6d4ebc13eaca8', '', 'NO', 'ACTIVATED', '0', '', 1489083145, 'Male', 'Nigeria', 'Lagos', '', '04-May-1989', 'Single', 'ADMIN', '07033029793', '', 'ACCESS BANK NG PLC', '0029558618', 'ADIAGWAI GODSWILL', '', 'NONE', 'NONE', 'COMPLETE', 'YOU HAVE BEEN CONFIRMED AND AWAITING MATCH TO RECEIVE', '', 0, 0, 1490741596),
(2, '', 'tressy', '40b8c2bd5bdde2184f39ece5a885bcfe9ddb149d', 'gregerry@gmail.com', '0', 'Tressy', 'Morgan', 'Tressy Morgan', 1487358294, '72fad23df48f54abdf674cd2c3b08b936ae42c35', '', 'NO', 'ACTIVATED', '0', '', 1487359607, 'Female', '', '', '', '01-January-2017', 'Single', 'MEMBER', '07081126264', '', 'UBA', '00341278934', 'Tressy Morgans', '', 'NONE', 'NONE', 'COMPLETE', 'YOU HAVE BEEN CONFIRMED AND AWAITING MATCH TO RECEIVE', '', 0, 0, 1490741596),
(3, '', 'seer', '17e22adc5f2438de4eed62b846bf65ace7bb42dc', 'deux@gmail.com', '0', 'stanz', 'Mathewson', 'stanz Mathewson', 1487440828, '7593', '', 'NO', 'ACTIVATED', '0', '', 0, 'Male', '', '', '', '01-January-2017', 'Single', 'MEMBER', '090346123987', '', 'ECOBANK', '00341278934', 'Henry Starks', '', 'NONE', 'NONE', 'COMPLETE', '', '', 0, 0, 1490741596),
(5, '', 'skyz', 'a65a1b6782a94b510f2fbbed886ae08a905c6c0a', 'frews@live.com', '0', 'Rita', 'Drakes', 'Rita Drakes', 1487860790, '2575', '', 'YES', 'ACTIVATED', '0', '', 0, 'Female', '', '', '', '', '', 'MEMBER', '09034128723', '', '', '', '', '', 'NONE', 'NONE', 'COMPLETE', 'YOUR ACCOUNT WAS SUSPENDED FOR DECLINING TO MAKE PAYMENT TWICE', '', 0, 0, 1490741596),
(6, '', 'trump', '88e03102ce2d19a4c3b3473882d04cc276d3ca45', 'trump@gmail.com', '0', 'Donald', 'Trump', 'Donald Trump', 1488355173, 'baa1ffd967d907f3476d4d924663f11f9d902061', '125152', 'YES', 'ACTIVATED', '0', '', 0, 'Male', '', '', '', '', '', 'MEMBER', '08034526754', '', '', '', '', '', 'NONE', 'NONE', 'COMPLETE', 'YOUR ACCOUNT WAS SUSPENDED FOR FAILING TO MEET UP WITH YOUR RECYCLING DEADLINE', '', 0, 0, 0),
(7, '', 'freddy', '5c8a7a129de8b649e9a0cbfbb7e9cec37a6efcb6', 'freddy@hotmail.com', '0', 'Fred', 'James', 'Fred James', 1488361818, '7773', '', 'YES', 'ACTIVATED', '0', '', 0, 'Male', '', '', '', '', '', 'MEMBER', '09054321899', '', '', '', '', '', 'NONE', 'NONE', 'COMPLETE', 'YOUR ACCOUNT WAS SUSPENDED FOR FAILING TO MEET UP WITH YOUR RECYCLING DEADLINE', '', 0, 0, 1490741596),
(8, '', '', '', 'seawealths@gmail.com', '20441913tE6543iuhcGHhObCVKL713viNNkggwTCcfYWrrVSs67731732343573075', '', '', '', 1490696596, '', '', 'YES', '', '', '', 0, '', '', '', '', '', '', 'MEMBER', '', '', '', '', '', '', 'NONE', 'NONE', 'COMPLETE', 'YOUR ACCOUNT WAS SUSPENDED FOR FAILING TO MEET UP WITH YOUR RECYCLING DEADLINE', '', 0, 0, 0),
(9, '', 'scorez', '00d8238525af88174819451b8535a8fef8c191b0', 'freddyz@hotmail.com', '0', 'Demi', 'Starks', 'Demi Starks', 1490708140, 'f25b706132a83d6133e8cbc90f7022fda9b01b1f', '158276', 'NO', 'ACTIVATED', '0', '', 0, 'Male', 'USA', 'California', '', '', '', 'MEMBER', '07035281528', '', '', '', '', '', 'NONE', 'NONE', 'COMPLETE', '', '', 0, 0, 1490794540);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `ID` int(11) NOT NULL,
  `AUTHOR` varchar(100) NOT NULL,
  `HEADER` varchar(100) NOT NULL,
  `CONTENT` text NOT NULL,
  `FOOTER` varchar(100) NOT NULL,
  `TIME` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`ID`, `AUTHOR`, `HEADER`, `CONTENT`, `FOOTER`, `TIME`) VALUES
(1, '', 'UPDATE', 'We are fully launched and active', 'Together we cross the bridge', 1488404225),
(2, 'wealth', 'We Are launching Soon', 'Let be on the look out\r\nWe will be rolling out soon', 'Together we cross the bridge', 1488579433);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `ID` int(11) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `DONATION` int(11) NOT NULL,
  `RETURNS` int(11) NOT NULL,
  `FOLLOWER_COUNTS` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`ID`, `PACKAGE`, `DONATION`, `RETURNS`, `FOLLOWER_COUNTS`) VALUES
(1, 'STANDARD', 5000, 10000, 50),
(2, 'CLASSIC', 10000, 20000, 36),
(3, 'PREMIUM', 20000, 40000, 12),
(4, 'ELITE', 50000, 100000, 33),
(5, 'LORD', 100000, 200000, 2),
(6, 'MASTER', 200000, 400000, 4),
(7, 'ROYAL', 500000, 1000000, 6),
(8, 'ULTIMATE', 1000000, 2000000, 93);

-- --------------------------------------------------------

--
-- Table structure for table `package_followers`
--

CREATE TABLE `package_followers` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `TIME_OF_FOLLOW` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `package_followers`
--

INSERT INTO `package_followers` (`ID`, `USERNAME`, `PACKAGE`, `TIME_OF_FOLLOW`) VALUES
(1, 'trump', 'STANDARD', 1489076796),
(2, 'wealth', 'STANDARD', 1490123812),
(3, 'seer', 'STANDARD', 1490123910),
(4, 'wealth', 'CLASSIC', 1490648536),
(5, 'tressy', 'CLASSIC', 1490651115),
(6, 'trump', 'CLASSIC', 1490652896);

-- --------------------------------------------------------

--
-- Table structure for table `privatemessage`
--

CREATE TABLE `privatemessage` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(255) NOT NULL,
  `MESSAGE_SUBJECT` varchar(255) NOT NULL,
  `INBOX` mediumtext NOT NULL,
  `OUTBOX` mediumtext NOT NULL,
  `TIME` int(11) NOT NULL,
  `DATE` datetime NOT NULL,
  `SENDER` varchar(255) NOT NULL,
  `OLD_INBOX` mediumtext NOT NULL,
  `COPY_OF_INBOX` mediumtext NOT NULL,
  `INBOX_STATUS` varchar(255) NOT NULL,
  `SELECTION_STATUS` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `privatemessage`
--

INSERT INTO `privatemessage` (`ID`, `USERNAME`, `MESSAGE_SUBJECT`, `INBOX`, `OUTBOX`, `TIME`, `DATE`, `SENDER`, `OLD_INBOX`, `COPY_OF_INBOX`, `INBOX_STATUS`, `SELECTION_STATUS`) VALUES
(1, 'tressy', 'PLEASE WHEN ARE YOU PAYING', '', '', 1487716029, '2017-02-21 11:27:09', 'wealth', 'This the guy u are matched to pay', 'This the guy u are matched to pay', 'read', ''),
(2, 'wealth', 'RE: PLEASE WHEN ARE YOU PAYING', '', '', 1488014117, '2017-02-25 10:15:17', 'tressy', '', 'soon', 'read', ''),
(3, 'tressy', 'Please when are u disbursing', '', '', 1488024013, '2017-02-25 01:00:13', 'seer', 'Goodafter noon, pls this ur match mate. when r u disbursing the payment', 'Goodafter noon, pls this ur match mate. when r u disbursing the payment', 'read', ''),
(5, 'seer', 'RE: hey', 'kk', '', 1490135984, '2017-03-21 11:39:44', 'wealth', '', 'kk', 'read', ''),
(4, 'wealth', 'hey', '', '', 1490124082, '2017-03-21 08:21:22', 'seer', '', 'paying now', 'read', '');

-- --------------------------------------------------------

--
-- Table structure for table `purges`
--

CREATE TABLE `purges` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `PURGER1` varchar(100) NOT NULL,
  `PURGE1_TIME` int(11) NOT NULL,
  `PURGER2` varchar(100) NOT NULL,
  `PURGE2_TIME` int(11) NOT NULL,
  `TOTAL` int(11) NOT NULL,
  `DEFAULTER_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `ID` int(11) NOT NULL,
  `REFERRAL` varchar(100) NOT NULL,
  `REFERRED` varchar(100) NOT NULL,
  `TIME` int(11) NOT NULL,
  `INCENTIVE` int(11) NOT NULL,
  `REMIT_STATUS` varchar(50) NOT NULL DEFAULT 'PENDING',
  `CONFIRMATION` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`ID`, `REFERRAL`, `REFERRED`, `TIME`, `INCENTIVE`, `REMIT_STATUS`, `CONFIRMATION`) VALUES
(1, 'wealth', 'trump', 1488354620, 500, 'CASHED', 'CONFIRMED'),
(2, 'wealth', 'trumpz', 1488355026, 500, 'CASHED', 'confirmed'),
(3, 'wealth', 'trump', 1488355173, 500, 'PENDING', 'CONFIRMED'),
(4, 'wealth', 'freddy', 1488361818, 500, 'PENDING', 'PENDING');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `ID` int(11) NOT NULL,
  `REPORTING_USERNAME` varchar(100) NOT NULL,
  `REPORTED_USERNAME` varchar(100) NOT NULL,
  `CONTENT` text NOT NULL,
  `TIME` int(11) NOT NULL,
  `STATUS` varchar(50) NOT NULL DEFAULT 'PENDING',
  `LAST_TREATED_BY` varchar(100) NOT NULL,
  `LT_TIME` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`ID`, `REPORTING_USERNAME`, `REPORTED_USERNAME`, `CONTENT`, `TIME`, `STATUS`, `LAST_TREATED_BY`, `LT_TIME`) VALUES
(1, 'WEST', 'TRESSY', 'She has refused to confirm me even after several calls\r\nI have paid and sent pop yet he has refused to confirm me for two days now', 1488897285, 'TREATED', 'wealth', 1489049499),
(2, 'wealth', 'trump', 'FAILED TO DISBURSE', 1489070042, 'PENDING', 'wealth', 1490044013);

-- --------------------------------------------------------

--
-- Table structure for table `support_replies`
--

CREATE TABLE `support_replies` (
  `ID` int(11) NOT NULL,
  `HID` int(11) NOT NULL,
  `TICKET_NO` varchar(100) NOT NULL,
  `SENDER` varchar(100) NOT NULL,
  `SUBJECT` varchar(100) NOT NULL,
  `REPLY_CONTENT` text NOT NULL,
  `TIME` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `support_replies`
--

INSERT INTO `support_replies` (`ID`, `HID`, `TICKET_NO`, `SENDER`, `SUBJECT`, `REPLY_CONTENT`, `TIME`) VALUES
(1, 1, '248532446110050', 'skyz', 'RE: i need to unlock my account', 'Once your account has been suspended due to inability to meet up with the recycling deadline, then it cannot be unblocked because chances are that you will continue with the act in the future which is a highly undesirable feature in this community.\r\nWe are regrettably sorry for any inconviniences caused.\r\nThank you.', 1488745304),
(2, 1, '248532446110050', 'skyz', 'RE: i need to unlock my account', 'JUST FORGET IT\r\nYOUR ACCOUNT CANNOT BE UNBLOCKED\r\nJUST LEARN FROM YOUR MISTAKES AND HOPE FOR BETTER DAYS\r\nOK', 1488746077),
(3, 0, '248532446110050', 'skyz', 'RE: i need to unlock my account', 'JUST FORGET IT\r\nYOUR ACCOUNT CANNOT BE UNBLOCKED\r\nJUST LEARN FROM YOUR MISTAKES AND HOPE FOR BETTER DAYS\r\nOK', 1488751000),
(4, 1, '248532446110050', 'wealth', 'RE: i need to unlock my account', 'oga you will wait long o\r\nYou hear', 1488752061);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `ID` int(11) NOT NULL,
  `TRANS_NUMBER` varchar(100) NOT NULL,
  `USERNAME` varchar(100) NOT NULL,
  `DESCRIPTION` varchar(100) NOT NULL,
  `AMOUNT` int(11) NOT NULL,
  `TRANS_TIME` int(11) NOT NULL,
  `PACKAGE` varchar(50) NOT NULL,
  `DONATION_ID` int(11) NOT NULL,
  `STATUS` varchar(50) NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `declinations`
--
ALTER TABLE `declinations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_classic_donations`
--
ALTER TABLE `euro_classic_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_classic_matching`
--
ALTER TABLE `euro_classic_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_elite_donations`
--
ALTER TABLE `euro_elite_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_elite_matching`
--
ALTER TABLE `euro_elite_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_lord_donations`
--
ALTER TABLE `euro_lord_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_lord_matching`
--
ALTER TABLE `euro_lord_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_master_donations`
--
ALTER TABLE `euro_master_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_master_matching`
--
ALTER TABLE `euro_master_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_premium_donations`
--
ALTER TABLE `euro_premium_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_premium_matching`
--
ALTER TABLE `euro_premium_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_royal_donations`
--
ALTER TABLE `euro_royal_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_royal_matching`
--
ALTER TABLE `euro_royal_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_standard_donations`
--
ALTER TABLE `euro_standard_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_standard_matching`
--
ALTER TABLE `euro_standard_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_ultimate_donations`
--
ALTER TABLE `euro_ultimate_donations`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `euro_ultimate_matching`
--
ALTER TABLE `euro_ultimate_matching`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `help`
--
ALTER TABLE `help`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `letters_of_happiness`
--
ALTER TABLE `letters_of_happiness`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `package_followers`
--
ALTER TABLE `package_followers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `privatemessage`
--
ALTER TABLE `privatemessage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `purges`
--
ALTER TABLE `purges`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `support_replies`
--
ALTER TABLE `support_replies`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `declinations`
--
ALTER TABLE `declinations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_classic_donations`
--
ALTER TABLE `euro_classic_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_classic_matching`
--
ALTER TABLE `euro_classic_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_elite_donations`
--
ALTER TABLE `euro_elite_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_elite_matching`
--
ALTER TABLE `euro_elite_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_lord_donations`
--
ALTER TABLE `euro_lord_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_lord_matching`
--
ALTER TABLE `euro_lord_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_master_donations`
--
ALTER TABLE `euro_master_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_master_matching`
--
ALTER TABLE `euro_master_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_premium_donations`
--
ALTER TABLE `euro_premium_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_premium_matching`
--
ALTER TABLE `euro_premium_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_royal_donations`
--
ALTER TABLE `euro_royal_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_royal_matching`
--
ALTER TABLE `euro_royal_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_standard_donations`
--
ALTER TABLE `euro_standard_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_standard_matching`
--
ALTER TABLE `euro_standard_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_ultimate_donations`
--
ALTER TABLE `euro_ultimate_donations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `euro_ultimate_matching`
--
ALTER TABLE `euro_ultimate_matching`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `help`
--
ALTER TABLE `help`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `letters_of_happiness`
--
ALTER TABLE `letters_of_happiness`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `package_followers`
--
ALTER TABLE `package_followers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `privatemessage`
--
ALTER TABLE `privatemessage`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `purges`
--
ALTER TABLE `purges`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `support_replies`
--
ALTER TABLE `support_replies`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
