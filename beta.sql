-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 07, 2023 lúc 06:51 PM
-- Phiên bản máy phục vụ: 10.4.28-MariaDB
-- Phiên bản PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `beta`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baiviet_hoangvietdung`
--

CREATE TABLE `baiviet_hoangvietdung` (
  `id` int(11) NOT NULL,
  `account_id` text NOT NULL,
  `top_baiviet` int(11) NOT NULL,
  `new` text NOT NULL,
  `tieude` text NOT NULL,
  `noidung` text NOT NULL,
  `time` varchar(99) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cmt_hoangvietdung`
--

CREATE TABLE `cmt_hoangvietdung` (
  `id` int(11) NOT NULL,
  `baiviet_id` text NOT NULL,
  `khach_id` text NOT NULL,
  `noidung` text NOT NULL,
  `time` varchar(99) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `baiviet_hoangvietdung`
--
ALTER TABLE `baiviet_hoangvietdung`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `cmt_hoangvietdung`
--
ALTER TABLE `cmt_hoangvietdung`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--
--
-- AUTO_INCREMENT cho bảng `baiviet_hoangvietdung`
--
ALTER TABLE `baiviet_hoangvietdung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cmt_hoangvietdung`
--
ALTER TABLE `cmt_hoangvietdung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
