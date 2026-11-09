-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 06:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `etalase_stella`
--

-- --------------------------------------------------------

--
-- Table structure for table `projek`
--

CREATE TABLE `projek` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `link_pendukung` varchar(255) NOT NULL,
  `link_demo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projek`
--

INSERT INTO `projek` (`id`, `user_id`, `judul`, `deskripsi`, `gambar`, `link_pendukung`, `link_demo`, `created_at`, `updated_at`) VALUES
(1, 4, '11 RPL2', 'Ini ketika kami mengoding', 'e096ac2dfbb4c6a3.png', 'https://github.com', 'http://acaaannnn', '2026-10-19 03:31:53', '2026-10-19 03:31:53'),
(2, 4, 'Porto', 'Portofolio yang kami buat', 'fa8addf3a6a86b47.png', 'https://github.com/acaagfjeno/portofolio_2.git', 'https://portofolio-inky-pi.vercel.app', '2026-10-20 02:31:14', '2026-10-20 02:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','siswa') NOT NULL DEFAULT 'siswa',
  `kelas` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `kelas`, `created_at`, `foto_profil`) VALUES
(1, 'Super Admin', 'superadmin', '$2y$10$ag7jwLrUAMdIguF9Pd2SvemEza15Ixlkqor9IDm5Ye4XdNJUezeG.', 'super_admin', NULL, '2026-10-07 03:07:31', NULL),
(2, 'Admin Stella', 'admin', '$2y$10$sTpm1n/JzvAzqiXKzJ.ZO.V5dq7H2GkQoMORS8SUDWpWyFQZtWokK', 'admin', NULL, '2026-10-07 03:07:31', NULL),
(3, 'Siswa Contoh', 'siswa1', '$2y$10$/Qous4wq8Qjf1MRBbNkWze0t7ZSv7Z0R..ww/M5vzpaSWBX/Hn9zu', 'siswa', 'XI RPL 2', '2026-10-07 03:07:31', NULL),
(4, 'acantik', 'acantik23', '$2y$10$QcL5ogfiVpvrAglPavmj8.fHHtLOebG748Fe50MoegfAQVKLL8eAq', 'siswa', 'XI Farmasi 1', '2026-10-07 03:58:19', NULL),
(5, 'jonathan', 'jo123', '$2y$10$VGx60ar1Iu50VdwaSRd.HumwnStOcvjor2aEuyerH4WQB4wTqypWa', 'siswa', 'XI RPL2', '2026-10-16 07:20:31', NULL),
(6, 'asyaa', 'asyaa22', '$2y$10$9HaGtmSEfeipjxPvV7KuI.9AFeP6//zuHnQpsQa4GOgz3bKSP4MdO', 'admin', NULL, '2026-10-19 06:10:17', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `projek`
--
ALTER TABLE `projek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_projek_user` (`user_id`);

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
-- AUTO_INCREMENT for table `projek`
--
ALTER TABLE `projek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `projek`
--
ALTER TABLE `projek`
  ADD CONSTRAINT `fk_projek_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
