-- phpMyAdmin SQL Dump
-- version 3.4.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 06 Sty 2013, 00:37
-- Wersja serwera: 5.0.92
-- Wersja PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `weather_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `soap_source` varchar(255) NOT NULL,
  `timeout` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `config`
--

INSERT INTO `config` (`soap_source`, `timeout`) VALUES
('http://www.webservicex.net/globalweather.asmx?WSDL', 1),
('http://www.webservicex.net/globalweather.asmx?WSDL', 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(45) NOT NULL default '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `sessions`
--

INSERT INTO `sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('5ca3ab7be48411bc1894df5c44d06ca8', '66.249.76.1', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1357351868, ''),
('3eb9f0eef78fc2a9414c054e0e279e77', '66.249.76.1', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1356431157, ''),
('565bc16d284e9dde920310f074781ad7', '85.198.198.136', 'Mozilla/5.0 (X11; Linux i686; rv:10.0.11) Gecko/20100101 Firefox/10.0.11 Iceweasel/10.0.11', 1356198216, ''),
('8771a76799a9c085cb260a317429a922', '66.249.76.1', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 1355472758, ''),
('50fd256160ac03dfe397f8acb10ba7fe', '85.198.198.136', 'Mozilla/5.0 (X11; Linux i686; rv:10.0.11) Gecko/20100101 Firefox/10.0.11 Iceweasel/10.0.11', 1355089821, '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `users_id` int(11) NOT NULL auto_increment,
  `login` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY  (`users_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`users_id`, `login`, `password`) VALUES
(1, 'admin', 'b190b0ecf310b7bda7a2b8708021d3e9');

-- --------------------------------------------------------

--
-- Struktura tabeli dla  `weather`
--

CREATE TABLE IF NOT EXISTS `weather` (
  `weather_id` int(11) NOT NULL auto_increment,
  `city` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `wind` varchar(255) NOT NULL,
  `visibility` varchar(255) NOT NULL,
  `temperature` varchar(255) NOT NULL,
  `dewpoint` varchar(255) NOT NULL,
  `relativehumidity` varchar(255) NOT NULL,
  `pressure` varchar(255) NOT NULL,
  PRIMARY KEY  (`weather_id`),
  UNIQUE KEY `city_idx` (`city`),
  UNIQUE KEY `location_idx` (`location`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Zrzut danych tabeli `weather`
--

INSERT INTO `weather` (`weather_id`, `city`, `location`, `time`, `wind`, `visibility`, `temperature`, `dewpoint`, `relativehumidity`, `pressure`) VALUES
(10, 'Warszawa', 'Warszawa-Okecie, Poland (EPWA) 52-10N 020-58E 107M', 'Dec 22, 2012 - 12:30 PM EST / 2012.12.22 1730 UTC', ' from the E (100 degrees) at 6 MPH (5 KT):0', ' greater than 7 mile(s):0', ' 14 F (-10 C)', ' 10 F (-12 C)', ' 85%', ' 30.24 in. Hg (1024 hPa)'),
(12, 'Reszów', 'Ibiza / Es Codola, Spain (LEIB) 38-52N 001-23E 12M', 'Dec 22, 2012 - 11:30 AM EST / 2012.12.22 1630 UTC', ' from the S (170 degrees) at 6 MPH (5 KT):0', ' greater than 7 mile(s):0', ' 60 F (16 C)', ' 55 F (13 C)', ' 82%', ' 30.24 in. Hg (1024 hPa)'),
(13, 'Fuerteventura', 'Fuerteventura / Aeropuerto, Spain (GCFV) 28-27N 013-52W 30M', 'Dec 22, 2012 - 11:30 AM EST / 2012.12.22 1630 UTC', ' Variable at 2 MPH (2 KT):0', ' greater than 7 mile(s):0', ' 68 F (20 C)', ' 62 F (17 C)', ' 82%', ' 30.12 in. Hg (1020 hPa)'),
(14, 'Rzym / Ciampino', 'Roma / Ciampino, Italy (LIRA) 41-47N 012-35E 105M', 'Dec 22, 2012 - 11:15 AM EST / 2012.12.22 1615 UTC', ' from the ENE (060 degrees) at 3 MPH (3 KT):0', ' greater than 7 mile(s):0', ' 48 F (9 C)', ' 37 F (3 C)', ' 66%', ' 30.03 in. Hg (1017 hPa)'),
(16, 'Wrocław', 'Wroclaw Ii, Poland (EPWR) 51-06N 016-53E 121M', 'Dec 22, 2012 - 12:00 PM EST / 2012.12.22 1700 UTC', ' from the SE (130 degrees) at 15 MPH (13 KT):0', ' 3 mile(s):0', ' 24 F (-4 C)', ' 19 F (-7 C)', ' 79%', ' 30.12 in. Hg (1020 hPa)'),
(18, 'Katowice', 'Katowice, Poland (EPKT) 50-14N 019-02E 284M', 'Dec 22, 2012 - 12:00 PM EST / 2012.12.22 1700 UTC', ' from the ESE (110 degrees) at 6 MPH (5 KT):0', ' 2 mile(s):0', ' 21 F (-6 C)', ' 21 F (-6 C)', ' 100%', ' 30.12 in. Hg (1020 hPa)'),
(19, 'Lanzarote', 'Lanzarote / Aeropuerto, Spain (GCRR) 28-57N 013-36W 21M', 'Dec 22, 2012 - 11:30 AM EST / 2012.12.22 1630 UTC', ' from the E (080 degrees) at 3 MPH (3 KT):0', ' greater than 7 mile(s):0', ' 69 F (21 C)', ' 64 F (18 C)', ' 82%', ' 30.12 in. Hg (1020 hPa)'),
(20, 'Malaga', 'Malaga / Aeropuerto, Spain (LEMG) 36-40N 004-29W 7M', 'Dec 22, 2012 - 12:30 PM EST / 2012.12.22 1730 UTC', ' from the SSE (160 degrees) at 3 MPH (3 KT):0', ' greater than 7 mile(s):0', ' 60 F (16 C)', ' 53 F (12 C)', ' 77%', ' 30.21 in. Hg (1023 hPa)'),
(21, 'Dortmund', 'Dortmund / Wickede, Germany (EDLW) 51-31N 007-37E', 'Dec 22, 2012 - 12:20 PM EST / 2012.12.22 1720 UTC', ' from the SSE (150 degrees) at 12 MPH (10 KT):0', ' greater than 7 mile(s):0', ' 42 F (6 C)', ' 39 F (4 C)', ' 86%', ' 29.80 in. Hg (1009 hPa)'),
(22, 'Poznan', 'Poznan, Poland (EPPO) 52-25N 016-50E 92M', 'Dec 22, 2012 - 12:00 PM EST / 2012.12.22 1700 UTC', ' from the E (100 degrees) at 12 MPH (10 KT):0', ' 3 mile(s):0', ' 21 F (-6 C)', ' 17 F (-8 C)', ' 85%', ' 30.15 in. Hg (1021 hPa)'),
(23, 'Velikie Luki', 'Velikie Luki, Russia (ULOL) 56-21N 030-37E 106M', 'Aug 05, 2009 - 04:30 AM EDT / 2009.08.05 0830 UTC', ' from the NW (310 degrees) at 9 MPH (8 KT):0', ' greater than 7 mile(s):0', ' 62 F (17 C)', ' 60 F (16 C)', ' 93%', ' 30.09 in. Hg (1019 hPa)'),
(24, 'Zielona Gora', 'Zielona Gora, Poland (EPZG) 51-56N 015-32E 192M', 'Oct 25, 2009 - 07:00 AM EDT / 2009.10.25 1100 UTC', ' from the S (180 degrees) at 9 MPH (8 KT):0', ' 4 mile(s):0', ' 51 F (11 C)', ' 46 F (8 C)', ' 81%', ' 29.97 in. Hg (1015 hPa)');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
