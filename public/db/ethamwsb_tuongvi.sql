-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 01, 2025 lúc 04:50 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ethamwsb_tuongvi`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbw_hrm_attendance`
--

CREATE TABLE `tbw_hrm_attendance` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `jobs` varchar(50) NOT NULL,
  `date_report` date NOT NULL,
  `time_create` datetime NOT NULL,
  `type_work` varchar(50) NOT NULL,
  `att_ver` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbw_hrm_attendance`
--

INSERT INTO `tbw_hrm_attendance` (`id`, `code`, `driver`, `jobs`, `date_report`, `time_create`, `type_work`, `att_ver`) VALUES
(1, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-01', '2025-04-01 04:48:14', '', 'v2'),
(2, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-02', '2025-04-01 04:48:14', '', 'v2'),
(3, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-03', '2025-04-01 04:48:14', '', 'v2'),
(4, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-04', '2025-04-01 04:48:14', '', 'v2'),
(5, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-05', '2025-04-01 04:48:14', '', 'v2'),
(6, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-06', '2025-04-01 04:48:14', '', 'v2'),
(7, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-08', '2025-04-01 04:48:14', '', 'v2'),
(8, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-09', '2025-04-01 04:48:14', '', 'v2'),
(9, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-10', '2025-04-01 04:48:14', '', 'v2'),
(10, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-11', '2025-04-01 04:48:14', '', 'v2'),
(11, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-12', '2025-04-01 04:48:14', '', 'v2'),
(12, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-13', '2025-04-01 04:48:14', '', 'v2'),
(13, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-14', '2025-04-01 04:48:14', '', 'v2'),
(14, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-17', '2025-04-01 04:48:14', '2', 'v2'),
(15, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-18', '2025-04-01 04:48:14', '', 'v2'),
(16, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-19', '2025-04-01 04:48:14', '', 'v2'),
(17, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-20', '2025-04-01 04:48:14', '', 'v2'),
(18, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-21', '2025-04-01 04:48:14', '', 'v2'),
(19, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-22', '2025-04-01 04:48:14', '', 'v2'),
(20, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', '0c2a8b77-6b10-4c36-be87-98db97e4766e', '2025-03-23', '2025-04-01 04:48:14', '', 'v2'),
(21, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-01', '2025-04-01 04:48:14', '', 'v2'),
(22, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-02', '2025-04-01 04:48:14', '', 'v2'),
(23, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-03', '2025-04-01 04:48:14', '', 'v2'),
(24, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-04', '2025-04-01 04:48:14', '', 'v2'),
(25, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-05', '2025-04-01 04:48:14', '', 'v2'),
(26, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-06', '2025-04-01 04:48:14', '', 'v2'),
(27, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-08', '2025-04-01 04:48:14', '', 'v2'),
(28, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-09', '2025-04-01 04:48:14', '', 'v2'),
(29, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-10', '2025-04-01 04:48:14', '', 'v2'),
(30, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-11', '2025-04-01 04:48:14', '', 'v2'),
(31, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-12', '2025-04-01 04:48:14', '', 'v2'),
(32, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-13', '2025-04-01 04:48:14', '', 'v2'),
(33, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-14', '2025-04-01 04:48:14', '', 'v2'),
(34, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-17', '2025-04-01 04:48:14', '2', 'v2'),
(35, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-18', '2025-04-01 04:48:14', '', 'v2'),
(36, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-19', '2025-04-01 04:48:14', '', 'v2'),
(37, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-20', '2025-04-01 04:48:14', '', 'v2'),
(38, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-21', '2025-04-01 04:48:14', '', 'v2'),
(39, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-22', '2025-04-01 04:48:14', '', 'v2'),
(40, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'ad175e58-76fc-4ed5-9b85-1a2d069e54b6', '2025-03-23', '2025-04-01 04:48:14', '', 'v2'),
(41, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-01', '2025-04-01 04:48:14', '', 'v2'),
(42, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-02', '2025-04-01 04:48:14', '', 'v2'),
(43, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-03', '2025-04-01 04:48:14', '', 'v2'),
(44, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-04', '2025-04-01 04:48:14', '', 'v2'),
(45, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-05', '2025-04-01 04:48:14', '', 'v2'),
(46, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-06', '2025-04-01 04:48:14', '', 'v2'),
(47, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-08', '2025-04-01 04:48:14', '', 'v2'),
(48, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-09', '2025-04-01 04:48:14', '', 'v2'),
(49, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-10', '2025-04-01 04:48:14', '', 'v2'),
(50, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-11', '2025-04-01 04:48:14', '', 'v2'),
(51, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-12', '2025-04-01 04:48:14', '', 'v2'),
(52, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-13', '2025-04-01 04:48:14', '', 'v2'),
(53, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-14', '2025-04-01 04:48:14', '', 'v2'),
(54, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-17', '2025-04-01 04:48:14', '2', 'v2'),
(55, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-18', '2025-04-01 04:48:14', '', 'v2'),
(56, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-19', '2025-04-01 04:48:14', '', 'v2'),
(57, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-20', '2025-04-01 04:48:14', '', 'v2'),
(58, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-21', '2025-04-01 04:48:14', '', 'v2'),
(59, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-22', '2025-04-01 04:48:14', '', 'v2'),
(60, '', 'da36a0b8-ac9a-4786-84d2-875c900dd077', 'a5c2e063-0b25-4d98-ae86-85c7b834a7c3', '2025-03-23', '2025-04-01 04:48:14', '', 'v2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbw_jobs`
--

CREATE TABLE `tbw_jobs` (
  `id` varchar(50) NOT NULL,
  `job_name` varchar(50) NOT NULL,
  `type_works` varchar(10) NOT NULL,
  `job_ver` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbw_jobs`
--

INSERT INTO `tbw_jobs` (`id`, `job_name`, `type_works`, `job_ver`) VALUES
('0c2a8b77-6b10-4c36-be87-98db97e4766e', 'BHX - BTRE', '', 'v2'),
('ad175e58-76fc-4ed5-9b85-1a2d069e54b6', 'Trứng', '', 'v2'),
('a5c2e063-0b25-4d98-ae86-85c7b834a7c3', 'Gà NM', '', 'v2'),
('7becf28b-2110-4e9d-8790-e04cf1b69a05', 'Heo', '', 'v2'),
('49a3e302-6324-4fd9-8f79-6644c8ac97d1', 'BHX Vị Thanh', '', 'v2'),
('164933ff-d8b7-49c7-8b7e-eb4283f7202c', 'BHX HCM', '', 'v2'),
('d408b0a8-7970-410e-8778-d4786b16ee39', 'Gà hàng chợ', '', 'v2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbw_users`
--

CREATE TABLE `tbw_users` (
  `id` varchar(50) NOT NULL,
  `fullname` varchar(80) NOT NULL,
  `position` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbw_users`
--

INSERT INTO `tbw_users` (`id`, `fullname`, `position`, `status`) VALUES
('0c343fa8-1fd5-4abc-a5b7-bd44cc7fef7d', 'Cao Trọng Anh', 'Tài xế', 'active'),
('dc858d45-740e-4845-980d-5d22d6b3b468', 'Danh Cường', 'Tài xế', 'active'),
('da36a0b8-ac9a-4786-84d2-875c900dd077', 'Đào Minh Quân', 'Tài xế', 'active'),
('acebe4cd-fdd2-4ffd-acb0-6664c47b7cb2', 'Đoàn Huỳnh Công Thanh', 'Tài xế', 'active'),
('72f979f4-5ed7-44fa-a6df-ff343f19356a', 'Lê Phước Thiện', 'Tài xế', 'active'),
('98925aaa-4c61-4fb6-b5b4-422af9b33ebe', 'Lưu Nhựt Trường', 'Tài xế', 'active'),
('1f55d595-d2bc-4dac-9abf-352f2c1a4cfd', 'Nguyễn Chánh Quyền', 'Tài xế', 'active'),
('fffbdeba-6dcc-438c-bd0b-3eca4d16b82b', 'Nguyễn Đình Tuấn', 'Tài xế', 'active'),
('8da0f777-5663-49d6-8889-56bdfb13e5e1', 'Nguyễn Lê Vi', 'Tài xế', 'active'),
('ce7a3bfd-14d7-4f15-830e-1d6cc9c0a588', 'Nguyễn Mạnh Cường', 'Tài xế', 'active'),
('8172a3d1-1f3a-4ceb-b4fd-bd834143289b', 'Nguyễn Ngọc Quang', 'Tài xế', 'active'),
('8045ef72-f5bf-4f12-9a5a-ad9155338d3a', 'Nguyễn Quốc Thái', 'Tài xế', 'active'),
('80fdc9fb-3536-483b-a019-c2c5fe6486cf', 'Nguyễn Tấn Huệ', 'Tài xế', 'active'),
('6309d2a0-acb6-47da-b8c9-d54e7c0cc9ca', 'Nguyễn Thành Tuẩn', 'Tài xế', 'active'),
('b3407258-072d-4755-b0ed-d7acd0c9c230', 'Nguyễn Văn Sơn', 'Tài xế', 'active'),
('0dda06da-247b-498c-b865-6d9e4164bdb5', 'Nguyễn Văn Thái', 'Tài xế', 'active'),
('ac3c293e-6ab6-4fa7-bdad-ab65afb70322', 'Nguyễn Văn Thiện Thật', 'Tài xế', 'active'),
('fca9d35e-4528-4f74-a926-d094d857c678', 'Nguyễn Văn Tú', 'Tài xế', 'active'),
('1d7d3953-4363-4ee7-8148-b614a20db00c', 'Phạm Quốc Anh', 'Tài xế', 'active'),
('fd32e073-5a2c-4439-a1c0-58218e73ee4b', 'Phạm Quốc Vương', 'Tài xế', 'active'),
('5c3fc8e0-f347-4e63-9e6c-aa91edc9874e', 'Phạm Văn Vĩnh', 'Tài xế', 'active'),
('a67f0251-895a-416f-8905-8d86c01ba1e7', 'Trần Đình Dương', 'Tài xế', 'active'),
('0b782746-ea0c-412d-9727-3229105358fa', 'Trần Gia Lộc', 'Tài xế', 'active'),
('8ecba552-14dd-434c-9b95-fa8734ee1b62', 'Trần Thanh Phong', 'Tài xế', 'active'),
('2301e288-ac64-4fb9-99fa-f5f191a03945', 'Trần Văn Mạnh', 'Tài xế', 'active'),
('ae6e8a59-3a9b-423f-b5d7-aec888de2131', 'Trần Văn Sáu', 'Tài xế', 'active'),
('17d961d5-c842-4e53-bbab-1b75839f2549', 'Võ Ngọc Hầu', 'Tài xế', 'active'),
('98149151-adf6-4570-aff6-b65fa6e8a311', 'Võ Văn Toán', 'Tài xế', 'active'),
('c7dd6e81-6ce7-40e8-88ab-9b8b7ecda52d', 'Vũ Văn Phiên', 'Tài xế', 'active'),
('fc2c2ca4-4610-4486-8976-c4e183812c66', 'Võ Văn Trình', 'Tài xế', 'active'),
('cd70b321-fb3f-4824-9fc4-08896586d7db', 'Hồ Văn Tài', 'Tài xế', 'active'),
('0500c232-3b9b-4ea7-b063-b71dcb0f1a4e', 'Văn Trung Toàn', 'Tài xế', 'active'),
('7821023c-ecd5-4ca4-827c-a94038e41a3f', 'Dương Văn An', 'Tài xế', 'active'),
('acf124c5-5a08-467d-bce6-036fc45c8652', 'Hà Minh Quân', 'Tài xế', 'active'),
('eaaada70-54c0-49eb-b7e7-7666754bfc25', 'Nguyễn Trọng Tài Đức', 'Tài xế', 'active'),
('ede923ba-4c9d-4724-b34a-4e6a2826fa98', 'Nguyễn Tuấn Vũ', 'Tài xế', 'active'),
('bf88bbae-b17e-4974-ac4b-7b388aa4d1d9', 'Châu Quốc Triệu', 'Tài xế', 'active'),
('72d49959-d434-403a-b66e-9224de029e19', 'Trương Vũ Trường', 'Tài xế', 'active'),
('cc92ce9b-d69c-4570-829e-c81a25bd780b', 'Phạm Ngọc Quốc', 'Tài xế', 'active');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbw_hrm_attendance`
--
ALTER TABLE `tbw_hrm_attendance`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tbw_jobs`
--
ALTER TABLE `tbw_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tbw_users`
--
ALTER TABLE `tbw_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `tbw_hrm_attendance`
--
ALTER TABLE `tbw_hrm_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
