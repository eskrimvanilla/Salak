-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2025 at 06:48 AM
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
-- Database: `salakbogor`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(6) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `specialization`, `experience`, `image_url`, `reg_date`) VALUES
(3, 'Dr. Rizal Pratama', 'Spesialis Bedah Umum', 'Terampil dalam prosedur bedah rumit. Prioritas keselamatan pasien dan pemulihan cepat dengan teknologi terkini.', 'https://placehold.co/300x300', '2025-09-03 06:45:17'),
(4, 'Dr. Andi Susanto', 'Spesialis Kardiovaskular', 'Berpengalaman lebih dari 15 tahun dalam penanganan penyakit jantung. Komitmen tinggi pada pencegahan dan pengobatan inovatif.', 'https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5404996e-f6d3-4fd8-87e4-230da89c39d8.png', '2025-09-03 06:45:59'),
(5, 'Dr. Sari Wulandari', 'Spesialis Anak', 'Ahli dalam kesehatan anak-anak dan remaja. Fokus pada perkembangan holistik dan kesejahteraan keluarga.', 'https://placehold.co/300x300', '2025-09-03 06:45:59'),
(6, 'Dr. Rizal Pratama', 'Spesialis Bedah Umum', 'Terampil dalam prosedur bedah rumit. Prioritas keselamatan pasien dan pemulihan cepat dengan teknologi terkini.', 'https://placehold.co/300x300', '2025-09-03 06:45:59'),
(7, 'Dr. Andi Susanto', 'Spesialis Kardiovaskular', 'Berpengalaman lebih dari 15 tahun dalam penanganan penyakit jantung. Komitmen tinggi pada pencegahan dan pengobatan inovatif.', 'https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5404996e-f6d3-4fd8-87e4-230da89c39d8.png', '2025-09-03 06:46:31'),
(8, 'Dr. Sari Wulandari', 'Spesialis Anak', 'Ahli dalam kesehatan anak-anak dan remaja. Fokus pada perkembangan holistik dan kesejahteraan keluarga.', 'https://placehold.co/300x300', '2025-09-03 06:46:31'),
(9, 'Dr. Rizal Pratama', 'Spesialis Bedah Umum', 'Terampil dalam prosedur bedah rumit. Prioritas keselamatan pasien dan pemulihan cepat dengan teknologi terkini.', 'https://placehold.co/300x300', '2025-09-03 06:46:31'),
(10, 'Dr. Andi Susanto', 'Spesialis Kardiovaskular', 'Berpengalaman lebih dari 15 tahun dalam penanganan penyakit jantung. Komitmen tinggi pada pencegahan dan pengobatan inovatif.', 'https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5404996e-f6d3-4fd8-87e4-230da89c39d8.png', '2025-09-03 06:46:53'),
(11, 'Dr. Sari Wulandari', 'Spesialis Anak', 'Ahli dalam kesehatan anak-anak dan remaja. Fokus pada perkembangan holistik dan kesejahteraan keluarga.', 'https://placehold.co/300x300', '2025-09-03 06:46:53'),
(12, 'Dr. Rizal Pratama', 'Spesialis Bedah Umum', 'Terampil dalam prosedur bedah rumit. Prioritas keselamatan pasien dan pemulihan cepat dengan teknologi terkini.', 'https://placehold.co/300x300', '2025-09-03 06:46:53'),
(13, 'Dr. Nama Dokter Baru', 'Spesialisasi', 'Pengalaman singkat.', 'https://placehold.co/300x300', '2025-09-03 06:46:53'),
(14, 'Dr. Andi Susanto', 'Spesialis Kardiovaskular', 'Berpengalaman lebih dari 15 tahun dalam penanganan penyakit jantung. Komitmen tinggi pada pencegahan dan pengobatan inovatif.', 'https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5404996e-f6d3-4fd8-87e4-230da89c39d8.png', '2025-09-03 06:47:13'),
(15, 'Dr. Sari Wulandari', 'Spesialis Anak', 'Ahli dalam kesehatan anak-anak dan remaja. Fokus pada perkembangan holistik dan kesejahteraan keluarga.', 'https://placehold.co/300x300', '2025-09-03 06:47:13'),
(16, 'Dr. Rizal Pratama', 'Spesialis Bedah Umum', 'Terampil dalam prosedur bedah rumit. Prioritas keselamatan pasien dan pemulihan cepat dengan teknologi terkini.', 'https://placehold.co/300x300', '2025-09-03 06:47:13'),
(17, 'Dr. Nama Dokter Baru', 'Spesialisasi', 'Pengalaman singkat.', 'https://placehold.co/300x300', '2025-09-03 06:47:13'),
(18, 'Dr. Andi Susanto', 'Spesialis Kardiovaskular', 'Berpengalaman lebih dari 15 tahun dalam penanganan penyakit jantung. Komitmen tinggi pada pencegahan dan pengobatan inovatif.', 'https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/5404996e-f6d3-4fd8-87e4-230da89c39d8.png', '2025-09-03 06:47:25'),
(19, 'Dr. Sari Wulandari', 'Spesialis Anak', 'Ahli dalam kesehatan anak-anak dan remaja. Fokus pada perkembangan holistik dan kesejahteraan keluarga.', 'https://placehold.co/300x300', '2025-09-03 06:47:25'),
(20, 'Dr. Rizal Pratama', 'Spesialis Bedah Umum', 'Terampil dalam prosedur bedah rumit. Prioritas keselamatan pasien dan pemulihan cepat dengan teknologi terkini.', 'https://placehold.co/300x300', '2025-09-03 06:47:25');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `published_at` datetime DEFAULT current_timestamp(),
  `category` enum('Urgent','Info','Program') NOT NULL DEFAULT 'Info'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `author`, `published_at`, `category`) VALUES
(5, 'vaks', 'vaksin', '1757898062_Gz_ok2fbkAANfa3.jpeg', 'vaksin', '2025-09-15 08:01:02', 'Info'),
(6, 'vaks', 'vaksin', '1757898097_Gz_ok2fbkAANfa3.jpeg', 'vaksin', '2025-09-15 08:01:37', 'Info'),
(7, 'asfasdas', 'afasdasd', '1757898377_Screenshot (1).png', 'asfafwdasd', '2025-09-15 08:06:17', 'Info'),
(8, 'asdasdwasd', 'sasdwasdwafs', '1757898388_Screenshot (6).png', 'sadwfsfawdsd', '2025-09-15 08:06:28', 'Info'),
(9, 'sdawsd', 'sadwafs', '1757898425_Screenshot (15).png', 'safwaxcv', '2025-09-15 08:07:05', 'Info'),
(10, 'asffffff', 'fewwwwwwwwwwwwwwwwwwww', '1757898439_Screenshot (8).png', 'asddddddddddddddddd', '2025-09-15 08:07:19', 'Info'),
(11, 'vaksin', 'vaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksin', '1757899527_Gz_ok2fbkAANfa3.jpeg', 'dokter', '2025-09-15 08:25:27', 'Info'),
(12, 'vaksin?', 'vaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksinvaksin', '1757900220_Screenshot 2025-04-20 231149.png', 'haqi', '2025-09-15 08:37:00', 'Info'),
(13, 'va', 'a', '1757901898_Screenshot (4).png', 'a', '2025-09-15 09:04:58', 'Urgent'),
(14, 'afasfasfass', 'fas fa-newspaper mr-3', '1757903550_Screenshot (2).png', 'asd', '2025-09-15 09:32:30', 'Program'),
(15, 'aaaaaaaaaaa', 'aaaaaaaaaa', '1757904065_Screenshot (5).png', 'aaaaaaaaaaaa', '2025-09-15 09:41:05', 'Info'),
(16, 'Program Vaksinasi', 'Program VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram VaksinasiProgram Vaksinasi', '1757904196_Screenshot (119).png', 'dokter', '2025-09-15 09:43:16', 'Urgent'),
(17, 'Senam Sehat Lansia', 'Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia Senam Sehat Lansia ', '1757904242_Screenshot 2023-11-25 214025.png', 'Senam Bugar Jasmani', '2025-09-15 09:44:02', 'Program');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `reg_date`) VALUES
(1, 'admin', '$2y$10$8DVhqVtO3dSqFxrUS0XdM.4SR0xWXeCbMJTltEl4XPvQIhpo7Hrie', '2025-09-03 06:54:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
