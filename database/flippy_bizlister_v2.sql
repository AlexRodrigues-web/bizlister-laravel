-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/10/2025 às 20:17
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `flippy_bizlister_v2`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administrator`
--

CREATE TABLE `administrator` (
  `id` int(11) NOT NULL,
  `username` varchar(500) NOT NULL,
  `password` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `administrator`
--

INSERT INTO `administrator` (`id`, `username`, `password`) VALUES
(1, 'alex.tecinf', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Estrutura para tabela `advertisements`
--

CREATE TABLE `advertisements` (
  `id` int(11) NOT NULL,
  `ad1` varchar(999) NOT NULL,
  `ad2` varchar(999) NOT NULL,
  `ad3` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `advertisements`
--

INSERT INTO `advertisements` (`id`, `ad1`, `ad2`, `ad3`) VALUES
(1, '<div id=\"ad1-marker\"><div style=\"height:120px;border:1px dashed #999;display:flex;align-items:center;justify-content:center\">AD-1 TEST</div></div>', '<div id=\"ad2-marker\"><div style=\"height:120px;border:1px dashed #999;display:flex;align-items:center;justify-content:center\">AD-2 TEST</div></div>', '<div id=\"ad3-marker\"><div style=\"height:90px;border:1px dashed #999;display:flex;align-items:center;justify-content:center\">AD-3 TEST</div></div>');

-- --------------------------------------------------------

--
-- Estrutura para tabela `app_users`
--

CREATE TABLE `app_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `app_users`
--

INSERT INTO `app_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@tecinfosp.local', '2025-09-29 19:56:56', '$2y$10$aG4p1kX.EUdronljHZq0n.ATG0d0PDitDrpxBa85X8NuNIQZlV.DC', 1, NULL, '2025-09-29 19:56:56', '2025-10-08 15:37:05'),
(2, 'Alex', 'contact@alexdevcode.com', '2025-09-30 06:32:34', '$2y$10$N.DFok.6rc.i8CiTngoDCOjFXpEXfzw1Mx2rXYQlwnqDi0Bi/701i', 1, 'FeGVbpD0GjUJPlDacUXGGFQ1f2d8n2ivZBR0jHa1VUIdAGtRft8pj2sg108H', '2025-09-30 06:32:34', '2025-09-30 06:32:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `bookmarks`
--

CREATE TABLE `bookmarks` (
  `bm_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bizid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `bookmarks`
--

INSERT INTO `bookmarks` (`bm_id`, `user_id`, `bizid`) VALUES
(1, 1, 11),
(2, 3, 14);

-- --------------------------------------------------------

--
-- Estrutura para tabela `business`
--

CREATE TABLE `business` (
  `biz_id` int(11) NOT NULL,
  `business_name` varchar(999) NOT NULL DEFAULT '',
  `description` varchar(999) NOT NULL DEFAULT '',
  `image` varchar(255) DEFAULT NULL,
  `image_path_lg` varchar(255) DEFAULT NULL,
  `image_path_sm` varchar(255) DEFAULT NULL,
  `image_lg` varchar(255) DEFAULT NULL,
  `image_sm` varchar(255) DEFAULT NULL,
  `address_1` varchar(255) DEFAULT NULL,
  `address_2` varchar(999) DEFAULT NULL,
  `city` varchar(500) NOT NULL DEFAULT '',
  `phone` varchar(500) DEFAULT NULL,
  `website` varchar(999) DEFAULT NULL,
  `email` varchar(500) DEFAULT NULL,
  `menu` varchar(999) NOT NULL DEFAULT '',
  `featured_image` varchar(999) NOT NULL DEFAULT '',
  `facebook` varchar(999) DEFAULT NULL,
  `twitter` varchar(999) DEFAULT NULL,
  `pinterest` varchar(999) NOT NULL DEFAULT '',
  `tags` varchar(999) NOT NULL DEFAULT '',
  `cid` int(11) NOT NULL DEFAULT 0,
  `subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sid` int(11) NOT NULL DEFAULT 0,
  `date` varchar(255) NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT 1,
  `star1` int(11) NOT NULL DEFAULT 0,
  `star2` int(11) NOT NULL DEFAULT 0,
  `star3` int(11) NOT NULL DEFAULT 0,
  `star4` int(11) NOT NULL DEFAULT 0,
  `star5` int(11) NOT NULL DEFAULT 0,
  `tot` int(11) NOT NULL DEFAULT 0,
  `avg` varchar(255) NOT NULL DEFAULT '0',
  `reviews` int(11) NOT NULL DEFAULT 0,
  `feat` int(11) NOT NULL DEFAULT 0,
  `biz_user` int(11) NOT NULL DEFAULT 0,
  `latitude` varchar(255) NOT NULL DEFAULT '',
  `longitude` varchar(255) NOT NULL DEFAULT '',
  `unique_biz` varchar(999) NOT NULL DEFAULT '',
  `hits` int(11) NOT NULL DEFAULT 0,
  `bookmarks` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `business`
--

INSERT INTO `business` (`biz_id`, `business_name`, `description`, `image`, `image_path_lg`, `image_path_sm`, `image_lg`, `image_sm`, `address_1`, `address_2`, `city`, `phone`, `website`, `email`, `menu`, `featured_image`, `facebook`, `twitter`, `pinterest`, `tags`, `cid`, `subcategory_id`, `sid`, `date`, `active`, `star1`, `star2`, `star3`, `star4`, `star5`, `tot`, `avg`, `reviews`, `feat`, `biz_user`, `latitude`, `longitude`, `unique_biz`, `hits`, `bookmarks`) VALUES
(1, 'Pizzaria Central', 'Pizzaria artesanal com forno a lenha e delivery rápido no Centro.', 'businesses/1/orig.webp', NULL, NULL, 'businesses/1/lg.webp', 'businesses/1/sm.webp', 'Rua Barão de Jaguara, 123', '', 'Sumarú', '(19) 99999-9999', '', '', '', '', '', '', '', 'pizza;delivery', 1, NULL, 5, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 1, 0),
(2, 'Hamburgueria Max', 'Burgers artesanais e smash com ingredientes locais.', 'businesses/2/orig.webp', NULL, NULL, 'businesses/2/lg.webp', 'businesses/2/sm.webp', 'Av. Moraes Salles, 450', '', 'Sumarú', '(19) 98888-7777', '', '', '', '', '', '', '', 'burger;artesanal', 1, NULL, 5, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(3, 'Burger da Praça', 'Hambúrguer artesanal e smash.', 'businesses/3/orig.webp', NULL, NULL, 'businesses/3/lg.webp', 'businesses/3/sm.webp', 'Centro', '', 'Hortolôndia', '(19) 91111-1111', '', '', '', '', '', '', '', 'hamburguer;burger;artesanal', 1, NULL, 4, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 9, 0),
(4, 'Pizzaria do Lago', 'Pizzas no forno a lenha com delivery.', 'businesses/4/orig.webp', NULL, NULL, 'businesses/4/lg.webp', 'businesses/4/sm.webp', 'Bairro Lagoa', '', 'Hortolôndia', '(19) 92222-2222', '', '', '', '', '', '', '', 'pizza;delivery;forno', 1, NULL, 4, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 12, 0),
(5, 'Auto Center Campinas', 'Mecânica e serviços automotivos.', 'businesses/5/orig.webp', NULL, NULL, 'businesses/5/lg.webp', 'businesses/5/sm.webp', 'Av. Brasil, 100', '', 'Campinas', '(19) 93333-3333', '', '', '', '', '', '', '', 'carro;mecânica;oficina', 6, NULL, 1, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 1, 0),
(6, 'Pousada das Flores', 'Pousada familiar com café da manhã.', 'businesses/6/orig.webp', NULL, NULL, 'businesses/6/lg.webp', 'businesses/6/sm.webp', 'Estrada Velha, s/n', '', 'Valinhos', '(19) 97777-7777', '', '', '', '', '', '', '', 'pousada;hospedagem;familiar', 9, NULL, 3, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(7, 'Curso Alfa', 'Cursos livres e profissionalizantes.', 'businesses/7/orig.webp', NULL, NULL, 'businesses/7/lg.webp', 'businesses/7/sm.webp', 'Rua das Flores, 50', '', 'Valinhos', '(19) 94444-4444', '', '', '', '', '', '', '', 'curso;educacao;profissional', 5, NULL, 3, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 1, 0),
(8, 'Eletricista 24h', 'Instalações e manutenções residenciais.', 'businesses/8/orig.webp', NULL, NULL, 'businesses/8/lg.webp', 'businesses/8/sm.webp', 'Rua Azul, 200', '', 'Valinhos', '(19) 95555-5555', '', '', '', '', '', '', '', 'eletrica;residencial;manutencao', 10, NULL, 3, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 1, 0),
(9, 'Hotel Centro', 'Hotel economico no centro.', 'businesses/9/orig.webp', NULL, NULL, 'businesses/9/lg.webp', 'businesses/9/sm.webp', 'Rua A, 10', '', 'São Paulo', '(19) 96666-6666', '', '', '', '', '', '', '', 'hotel;hospedagem;centro', 2, NULL, 2, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 2, 0),
(10, 'Clinica Bem-Estar', 'Consultas e exames.', 'businesses/10/orig.webp', NULL, NULL, 'businesses/10/lg.webp', 'businesses/10/sm.webp', 'Av. Saúde, 300', '', 'São Paulo', '(19) 98888-8888', '', '', '', '', '', '', '', 'clinica;saude;exames', 4, NULL, 2, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 2, 0),
(11, 'Multi Serviços', 'Pequenos reparos e manutenção.', 'businesses/11/orig.webp', NULL, NULL, 'businesses/11/lg.webp', 'businesses/11/sm.webp', 'Rua das Palmeiras, 12', '', 'São Paulo', '(19) 99999-0000', '', '', '', '', '', '', '', 'serviços;manutenção;reparos', 3, NULL, 2, '2025-09-27', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 10, 1),
(13, 'Mercearia', 'Mercearia online', 'businesses/13/orig.webp', NULL, NULL, 'businesses/13/lg.webp', 'businesses/13/sm.webp', NULL, NULL, 'Campinas', '', '', NULL, '', 'businesses/large/h7JDs6ZQde3sBASISaVtf1HG2LMqkLKML1rK2bDH.webp', NULL, NULL, '', '', 3, NULL, 1, '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '', '', '', 0, 0),
(14, 'MERCEARIA ONLINE PS', 'A Mercearia online é a forma mais simples de encher a despensa sem sair de casa — a qualquer hora do dia ou da noite. Entregamos fruta e legumes frescos, padaria, laticínios, mercearia seca, bebidas, produtos de limpeza e higiene com rapidez, preços justos e atendimento próximo. Seja para a compra do mês ou para aquela urgência às 2 da manhã, estamos sempre abertos 24/7.', 'businesses/14/orig.webp', NULL, NULL, 'businesses/14/lg.webp', 'businesses/14/sm.webp', NULL, NULL, 'Ribeirão Preto', '', '', '', '0', 'businesses/c2sHXQAmt1aEN6vfKwIZbty5OCilFihgzDfhbTuX.webp', '', '', '', '', 8, NULL, 8, '2025-10-11 13:18:58', 0, 0, 0, 0, 0, 0, 0, '0', 0, 0, 1, '', '', '2dfb5bdf-541f-4d7f-a36b-4d96be41525f', 0, 1),
(20, 'MERCEARIA ONLINE', 'Desc PS', 'businesses/20/orig.webp', NULL, NULL, 'businesses/20/lg.webp', 'businesses/20/sm.webp', NULL, NULL, 'Rio de Janeiro', NULL, NULL, NULL, '0', '', NULL, NULL, '', '', 3, NULL, 7, '2025-10-12 12:14:00', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(28, 'Teste PowerShell 223745', 'Criado via fluxo automatizado.', 'businesses/28/orig.webp', NULL, NULL, 'businesses/28/lg.webp', 'businesses/28/sm.webp', NULL, NULL, 'Campinas', NULL, NULL, NULL, '1', '', NULL, NULL, '', '', 1, NULL, 4, '', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(29, 'PS Upload 230544', 'Criado via PS', 'businesses/WqV3gK2eDS2EhxZpOMPH02ywnwPMDPhkDQI0t8Em.png', NULL, NULL, 'businesses/29/lg.webp', 'businesses/29/sm.webp', NULL, NULL, '8', '0838571999', NULL, NULL, '0', '', NULL, NULL, '', '', 1, NULL, 8, '', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(38, 'TEST100', 'tESTE100', 'businesses/22bVxzpuvmEfuIWqXWhEqaDgTB1S4b187uABCwFJ.png', NULL, NULL, NULL, NULL, NULL, NULL, 'Valinhos', '', NULL, NULL, '', '', NULL, NULL, '', '', 5, NULL, 3, '', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(40, 'Pastelaria Sabor da Casa', 'A Pastelaria Sabor da Casa é o seu ponto de paragem para um café bem tirado e doçaria artesanal feita diariamente. Dos clássicos portugueses aos bolos de assinatura, tudo é preparado com ingredientes frescos e um toque caseiro. Servimos pequenos-almoços completos, lanches, opções salgadas e fazemos encomendas para festas e eventos. Entre, relaxe e sinta-se em casa.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '3', '', NULL, NULL, '1', '', NULL, NULL, '', '', 3, NULL, 3, '', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(41, 'Pastelaria Sabor da Casa', 'A Pastelaria Sabor da Casa é o seu ponto de paragem para um café bem tirado e doçaria artesanal feita diariamente. Dos clássicos portugueses aos bolos de assinatura, tudo é preparado com ingredientes frescos e um toque caseiro. Servimos pequenos-almoços completos, lanches, opções salgadas e fazemos encomendas para festas e eventos. Entre, relaxe e sinta-se em casa', 'businesses/Y2F66js287jhryY02fRSJlRFxqRWszB2ckfGze84.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '8', '', NULL, NULL, '1', '', NULL, NULL, '', '', 3, NULL, 8, '', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0),
(42, 'Pizzaria Italian Republic', 'Pizzaria Italian Republic é o seu destino para sabores autênticos da Itália, preparados com massa de fermentação lenta, molho de tomate artesanal e ingredientes frescos selecionados. Do clássico Margherita às criações da casa com burrata, pesto e prosciutto, cada pizza é assada em forno de alta temperatura para uma base leve, crocante e cheia de aroma. O cardápio traz ainda focaccias, saladas, pastas e sobremesas como tiramisù, além de opções vegetarianas e sem glúten. A carta de vinhos italianos e as cervejas artesanais acompanham na medida certa, num ambiente acolhedor, com atendimento atento e descontraído. Perfeito para jantares em família, encontros entre amigos ou take-away rápido, a Italian Republic celebra a verdadeira experiência italiana — simples, saborosa e inesquecível.', 'businesses/bBNHxG0bsyEP7sYoJ6HtZKhoDCgLyx3eqTtkcnRT.jpg', NULL, NULL, NULL, NULL, NULL, NULL, '7', '', NULL, NULL, '1', '', NULL, NULL, '', '', 7, NULL, 7, '', 1, 0, 0, 0, 0, 0, 0, '0', 0, 0, 0, '', '', '', 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `business_images`
--

CREATE TABLE `business_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(1024) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `business_images`
--

INSERT INTO `business_images` (`id`, `business_id`, `path`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 41, 'businesses/41/gallery/W4bV35AC28uu67K8EZm68L2T21bbbQRKRVWhwCxy.png', 1, 0, '2025-10-26 11:54:40', '2025-10-26 11:54:40'),
(2, 41, 'businesses/41/gallery/ceE5Husj92YAxUZ0BuScpJgkkCMbPDOievCs0DOt.png', 0, 0, '2025-10-26 11:55:07', '2025-10-26 11:55:07'),
(3, 42, 'businesses/42/gallery/MmUyAyYUjNZFCaDTXabHuWlo3hfmmrcIRz8rd9LQ.jpg', 1, 0, '2025-10-26 13:22:46', '2025-10-26 13:22:46'),
(4, 42, 'businesses/42/gallery/y7hlwxcJbYq7WZdUbGspBDpOXQsUFMjjKlfuFsWH.jpg', 0, 0, '2025-10-26 13:22:46', '2025-10-26 13:22:46'),
(5, 42, 'businesses/42/gallery/N1cP8SXOuwkiYv61Jsj86k5NjoBNUuRLFRDHBhGv.png', 0, 0, '2025-10-26 13:22:46', '2025-10-26 13:22:46'),
(6, 42, 'businesses/42/gallery/DNZNMvh8hFwfw9pHVsSmGSY40uO57kgKVj0g8vxb.jpg', 0, 0, '2025-10-26 13:24:00', '2025-10-26 13:24:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `category` varchar(500) NOT NULL,
  `cat_description` varchar(999) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`cat_id`, `category`, `cat_description`, `parent_id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Restaurantes', '', 0, '', '-1', NULL, 1, NULL, NULL),
(2, 'Hotéis', 'Hospedagem', 0, '', '-2', NULL, 1, NULL, NULL),
(3, 'Serviços', 'Serviços gerais', 0, '', '-3', NULL, 1, NULL, NULL),
(4, 'Saúde', 'Clínicas e saúde', 0, '', '-4', NULL, 1, NULL, NULL),
(5, 'Educação', 'Cursos e escolas', 0, '', '-5', NULL, 1, NULL, NULL),
(6, 'Automotivo', 'Carros e motos', 0, '', '-6', NULL, 1, NULL, NULL),
(7, 'Pizzarias', 'Pizzarias e fornos a lenha', 1, '', '-7', NULL, 1, NULL, NULL),
(8, 'Hamburguerias', 'Burgers e smash', 1, '', '-8', NULL, 1, NULL, NULL),
(9, 'Pousadas', 'Pousadas e B&B', 0, '', '-9', NULL, 1, NULL, NULL),
(10, 'Elétrica Residencial', 'Serviços elétricos', 0, '', '-10', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `category`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `category` (
`cat_id` int(11)
,`category` varchar(500)
,`cat_description` varchar(999)
,`parent_id` int(11)
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `category_legacy_backup`
--

CREATE TABLE `category_legacy_backup` (
  `cat_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `category_legacy_backup`
--

INSERT INTO `category_legacy_backup` (`cat_id`, `category`) VALUES
(6, 'Automotivo'),
(5, 'Educação'),
(10, 'Elétrica Residencial'),
(7, 'Hamburguerias'),
(2, 'Hotéis'),
(1, 'Pizzarias'),
(9, 'Pousadas'),
(8, 'Restaurantes'),
(4, 'Saúde'),
(3, 'Serviços');

-- --------------------------------------------------------

--
-- Estrutura para tabela `category_table_old`
--

CREATE TABLE `category_table_old` (
  `cat_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `category_table_old`
--

INSERT INTO `category_table_old` (`cat_id`, `category`) VALUES
(6, 'Automotivo'),
(5, 'Educação'),
(10, 'Elétrica Residencial'),
(7, 'Hamburguerias'),
(2, 'Hotéis'),
(1, 'Pizzarias'),
(9, 'Pousadas'),
(8, 'Restaurantes'),
(4, 'Saúde'),
(3, 'Serviços');

-- --------------------------------------------------------

--
-- Estrutura para tabela `city`
--

CREATE TABLE `city` (
  `city_id` int(11) NOT NULL,
  `city` varchar(999) NOT NULL,
  `name` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `state` varchar(10) DEFAULT NULL,
  `is_active` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `city`
--

INSERT INTO `city` (`city_id`, `city`, `name`, `slug`, `state`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'São Paulo', 'São Paulo', '-2', NULL, 1, NULL, NULL),
(3, 'Valinhos', 'Valinhos', '-3', NULL, 1, NULL, NULL),
(4, 'Hortolândia', 'Hortolândia', '-4', NULL, 1, NULL, NULL),
(5, 'Sumaré', 'Sumaré', '-5', NULL, 1, NULL, NULL),
(7, 'Rio de Janeiro', 'Rio de Janeiro', '-7', NULL, 1, NULL, NULL),
(8, 'Ribeirão Preto', 'Ribeirão Preto', '-8', NULL, 1, NULL, NULL),
(13, 'Serrana', 'Serrana', '-13', NULL, 1, NULL, NULL),
(15, 'Araraquara', 'Araraquara', '-15', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `city_backup`
--

CREATE TABLE `city_backup` (
  `city_id` int(11) NOT NULL DEFAULT 0,
  `city` varchar(999) NOT NULL,
  `name` varchar(190) NOT NULL,
  `slug` varchar(190) NOT NULL,
  `state` varchar(10) DEFAULT NULL,
  `is_active` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `city_backup`
--

INSERT INTO `city_backup` (`city_id`, `city`, `name`, `slug`, `state`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'São Paulo', '', '-2', NULL, 1, NULL, NULL),
(3, 'Valinhos', '', '-3', NULL, 1, NULL, NULL),
(4, 'Hortolândia', '', '-4', NULL, 1, NULL, NULL),
(5, 'Sumaré', '', '-5', NULL, 1, NULL, NULL),
(7, 'Rio de Janeiro', '', '-7', NULL, 1, NULL, NULL),
(8, 'Ribeirão Preto', '', '-8', NULL, 1, NULL, NULL),
(13, 'Serrana', '', '-13', NULL, 1, NULL, NULL),
(15, 'Araraquara', '', '-15', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `galleries`
--

CREATE TABLE `galleries` (
  `img_id` int(11) NOT NULL,
  `image` varchar(999) NOT NULL,
  `uid` int(11) NOT NULL,
  `uniq` varchar(999) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `hours`
--

CREATE TABLE `hours` (
  `hour_id` int(11) NOT NULL,
  `day` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `open_from` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `open_till` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `unique_hours` varchar(999) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `laravel_users`
--

CREATE TABLE `laravel_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `laravel_users`
--

INSERT INTO `laravel_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@tecinfosp.local', '2025-10-03 18:11:54', '$2y$10$N0msTifC1sgzJF9CnHpYtOkTek9FCp4ZkangoDj0m5IebeyfDe9ou', 1, 'cthqzzhnnWkF9K7qyR7KY8iHcfnKHbCPT4WgzboDXoFE0lIlqlLLbvE5EmCF', '2025-10-03 21:33:59', '2025-10-12 18:39:22'),
(2, 'Teste', 'teste@teste.com', NULL, '$2y$10$Tdvxgu.NbxnkXqbSYe3yD.Am2tX9HVxO7hnKF7osxTYWoD/Yy48si', 1, NULL, '2025-10-08 19:02:24', '2025-10-12 18:39:22');

-- --------------------------------------------------------

--
-- Estrutura para tabela `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2025_09_29_203723_create_app_users_table', 2),
(5, '2025_10_02_220330_add_image_to_business_table', 3),
(6, '2025_10_02_220607_make_address1_nullable_in_business', 4),
(7, '2025_10_02_220800_make_optional_fields_nullable_in_business', 5),
(8, '2025_10_13_233031_add_image_sizes_to_business_table', 6),
(9, '2025_10_13_233157_add_image_sizes_to_business_table', 7),
(10, '2025_10_17_200343_add_is_admin_to_users_table', 8),
(11, '2025_10_18_152300_create_pages_table', 9),
(12, '2025_10_18_172010_add_static_fields_to_pages_table', 10),
(13, '2025_10_18_172500_fix_pages_table_structure', 10),
(14, '2025_10_18_172847_fix_pages_table_structure', 10),
(15, '2025_10_18_173041_fix_pages_id_autoincrement', 11),
(16, '2025_10_18_173500_fix_pages_id_autoincrement', 11),
(17, '2025_10_18_173256_fix_pages_page_column_nullable', 12),
(18, '2025_10_18_173900_fix_pages_page_column_nullable', 12),
(19, '2025_10_18_213528_add_image_paths_to_businesses_table', 13),
(20, '2025_09_30_000000_create_business_table', 14),
(21, '2025_10_19_124101_create_reviews_table', 14),
(22, '2025_10_19_130527_ensure_review_columns', 15),
(23, '2025_10_19_131040_ensure_reviews_min_columns', 16),
(24, '2025_10_22_191506_create_categories_table', 17),
(25, '2025_10_22_191851_create_categories_table', 18),
(26, '2025_10_22_191859_create_city_table', 19),
(28, '2025_10_25_212144_add_provider_columns_to_users_table', 20),
(29, '2025_10_26_092804_create_subcategories_table', 21),
(30, '2025_10_26_100000_add_subcategory_id_to_business_table', 21),
(31, '2025_10_26_120000_create_business_images_table', 22);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `is_active`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'page-1', '', NULL, 1, NULL, NULL, NULL),
(2, 'page-2', '', NULL, 1, NULL, NULL, NULL),
(3, 'page-3', '', NULL, 1, NULL, NULL, NULL),
(4, 'page-4', '', NULL, 1, NULL, NULL, NULL),
(5, 'sobre', 'Sobre Nós', '<h1>About Us</h1>\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor lobortis tortor sit amet auctor. Praesent pretium, leo eget luctus tempor, lectus erat vulputate libero, in viverra dolor velit quis ante. Vivamus viverra pulvinar sollicitudin. Vivamus dictum orci sed lorem venenatis quis rutrum risus dictum. Ut non enim elit. In consectetur, nibh sed iaculis pellentesque, nisi ligula egestas enim, sagittis fermentum nulla nisl sit amet velit. Aliquam erat volutpat. Suspendisse ac mi tortor, at vestibulum augue. Suspendisse ut nisl quam, euismod scelerisque urna. Donec non leo et nisi tempus fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec gravida sodales est vitae tincidunt.</p>\n<p>Curabitur neque dui, adipiscing a dignissim sed, congue ut sem. Vivamus eget tellus lectus. Nullam ut tempus purus. Vestibulum pellentesque lorem nec velit hendrerit porta. Cras sit amet mauris odio. Sed sit amet libero nec ipsum venenatis interdum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed at ligula eu enim congue molestie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tincidunt diam at metus facilisis aliquam. Vivamus a orci nunc, molestie consectetur purus. Vivamus aliquam, diam eu rhoncus mollis, est lorem aliquam neque, elementum molestie sapien velit id sapien. Maecenas quis dolor nisl.</p>\n<p>Morbi nisi quam, suscipit eu accumsan a, porttitor sed risus. Donec laoreet, dolor semper eleifend sodales, erat lacus pretium velit, vitae dignissim felis risus non magna. Suspendisse potenti. Proin at nulla massa, et dictum magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pharetra, purus vel egestas egestas, orci lectus vehicula quam, non placerat massa lacus id justo. Integer posuere laoreet porttitor.</p>\n<p>Nam nulla metus, rutrum in suscipit ac, hendrerit eget nunc. In hac habitasse platea dictumst. Mauris at justo magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis malesuada ultricies hendrerit. Vivamus ullamcorper consectetur dignissim. Praesent nunc lorem, elementum vitae scelerisque vitae, pellentesque quis ipsum. Cras volutpat, erat eu mollis pulvinar, justo libero dictum leo, ac accumsan tellus ipsum eget magna. Suspendisse tristique mauris nec odio fringilla sagittis. Donec rhoncus euismod tortor, nec commodo magna cursus at. Nunc ut euismod dui. Suspendisse sollicitudin, magna eget auctor euismod, dolor mi aliquet tellus, et suscipit dui est nec odio. Aliquam rutrum tellus in nisi ultricies sed elementum nisi molestie. Praesent sit amet ligula id lorem ultrices tristique.</p>\n<p>Suspendisse potenti. Sed urna est, fringilla a condimentum eu, dapibus et erat. Cras ac aliquet erat. Integer eget risus magna, non egestas nulla. Maecenas ut ante tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi vel eros nec quam cursus porttitor et nec orci. Nulla vel odio sit amet nisi feugiat dignissim. Mauris tincidunt enim quam, non pharetra risus. Sed sed mi nulla, sit amet aliquam ipsum. Ut faucibus vestibulum feugiat. Nulla ut dui eget massa pellentesque scelerisque. Aenean ut ipsum at orci lacinia mollis id nec urna. Proin eros dui, feugiat vitae adipiscing ut, rutrum vitae quam. Nam et convallis augue. Quisque ut odio eu ante consequat elementum.</p>', 1, '2025-10-26 18:46:49', '2025-10-26 15:19:46', '2025-10-26 15:19:46'),
(6, 'termos', 'Termos de Uso', '<h1>Terms of Use</h1>\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor lobortis tortor sit amet auctor. Praesent pretium, leo eget luctus tempor, lectus erat vulputate libero, in viverra dolor velit quis ante. Vivamus viverra pulvinar sollicitudin. Vivamus dictum orci sed lorem venenatis quis rutrum risus dictum. Ut non enim elit. In consectetur, nibh sed iaculis pellentesque, nisi ligula egestas enim, sagittis fermentum nulla nisl sit amet velit. Aliquam erat volutpat. Suspendisse ac mi tortor, at vestibulum augue. Suspendisse ut nisl quam, euismod scelerisque urna. Donec non leo et nisi tempus fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec gravida sodales est vitae tincidunt.</p>\n<p>Curabitur neque dui, adipiscing a dignissim sed, congue ut sem. Vivamus eget tellus lectus. Nullam ut tempus purus. Vestibulum pellentesque lorem nec velit hendrerit porta. Cras sit amet mauris odio. Sed sit amet libero nec ipsum venenatis interdum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed at ligula eu enim congue molestie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tincidunt diam at metus facilisis aliquam. Vivamus a orci nunc, molestie consectetur purus. Vivamus aliquam, diam eu rhoncus mollis, est lorem aliquam neque, elementum molestie sapien velit id sapien. Maecenas quis dolor nisl.</p>\n<p>Morbi nisi quam, suscipit eu accumsan a, porttitor sed risus. Donec laoreet, dolor semper eleifend sodales, erat lacus pretium velit, vitae dignissim felis risus non magna. Suspendisse potenti. Proin at nulla massa, et dictum magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pharetra, purus vel egestas egestas, orci lectus vehicula quam, non placerat massa lacus id justo. Integer posuere laoreet porttitor.</p>\n<p>Nam nulla metus, rutrum in suscipit ac, hendrerit eget nunc. In hac habitasse platea dictumst. Mauris at justo magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis malesuada ultricies hendrerit. Vivamus ullamcorper consectetur dignissim. Praesent nunc lorem, elementum vitae scelerisque vitae, pellentesque quis ipsum. Cras volutpat, erat eu mollis pulvinar, justo libero dictum leo, ac accumsan tellus ipsum eget magna. Suspendisse tristique mauris nec odio fringilla sagittis. Donec rhoncus euismod tortor, nec commodo magna cursus at. Nunc ut euismod dui. Suspendisse sollicitudin, magna eget auctor euismod, dolor mi aliquet tellus, et suscipit dui est nec odio. Aliquam rutrum tellus in nisi ultricies sed elementum nisi molestie. Praesent sit amet ligula id lorem ultrices tristique.</p>\n<p>Suspendisse potenti. Sed urna est, fringilla a condimentum eu, dapibus et erat. Cras ac aliquet erat. Integer eget risus magna, non egestas nulla. Maecenas ut ante tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi vel eros nec quam cursus porttitor et nec orci. Nulla vel odio sit amet nisi feugiat dignissim. Mauris tincidunt enim quam, non pharetra risus. Sed sed mi nulla, sit amet aliquam ipsum. Ut faucibus vestibulum feugiat. Nulla ut dui eget massa pellentesque scelerisque. Aenean ut ipsum at orci lacinia mollis id nec urna. Proin eros dui, feugiat vitae adipiscing ut, rutrum vitae quam. Nam et convallis augue. Quisque ut odio eu ante consequat elementum.</p>\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor lobortis tortor sit amet auctor. Praesent pretium, leo eget luctus tempor, lectus erat vulputate libero, in viverra dolor velit quis ante. Vivamus viverra pulvinar sollicitudin. Vivamus dictum orci sed lorem venenatis quis rutrum risus dictum. Ut non enim elit. In consectetur, nibh sed iaculis pellentesque, nisi ligula egestas enim, sagittis fermentum nulla nisl sit amet velit. Aliquam erat volutpat. Suspendisse ac mi tortor, at vestibulum augue. Suspendisse ut nisl quam, euismod scelerisque urna. Donec non leo et nisi tempus fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec gravida sodales est vitae tincidunt.</p>\n<p>Curabitur neque dui, adipiscing a dignissim sed, congue ut sem. Vivamus eget tellus lectus. Nullam ut tempus purus. Vestibulum pellentesque lorem nec velit hendrerit porta. Cras sit amet mauris odio. Sed sit amet libero nec ipsum venenatis interdum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed at ligula eu enim congue molestie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tincidunt diam at metus facilisis aliquam. Vivamus a orci nunc, molestie consectetur purus. Vivamus aliquam, diam eu rhoncus mollis, est lorem aliquam neque, elementum molestie sapien velit id sapien. Maecenas quis dolor nisl.</p>\n<p>Morbi nisi quam, suscipit eu accumsan a, porttitor sed risus. Donec laoreet, dolor semper eleifend sodales, erat lacus pretium velit, vitae dignissim felis risus non magna. Suspendisse potenti. Proin at nulla massa, et dictum magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pharetra, purus vel egestas egestas, orci lectus vehicula quam, non placerat massa lacus id justo. Integer posuere laoreet porttitor.</p>\n<p>Nam nulla metus, rutrum in suscipit ac, hendrerit eget nunc. In hac habitasse platea dictumst. Mauris at justo magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis malesuada ultricies hendrerit. Vivamus ullamcorper consectetur dignissim. Praesent nunc lorem, elementum vitae scelerisque vitae, pellentesque quis ipsum. Cras volutpat, erat eu mollis pulvinar, justo libero dictum leo, ac accumsan tellus ipsum eget magna. Suspendisse tristique mauris nec odio fringilla sagittis. Donec rhoncus euismod tortor, nec commodo magna cursus at. Nunc ut euismod dui. Suspendisse sollicitudin, magna eget auctor euismod, dolor mi aliquet tellus, et suscipit dui est nec odio. Aliquam rutrum tellus in nisi ultricies sed elementum nisi molestie. Praesent sit amet ligula id lorem ultrices tristique.</p>\n<p>Suspendisse potenti. Sed urna est, fringilla a condimentum eu, dapibus et erat. Cras ac aliquet erat. Integer eget risus magna, non egestas nulla. Maecenas ut ante tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi vel eros nec quam cursus porttitor et nec orci. Nulla vel odio sit amet nisi feugiat dignissim. Mauris tincidunt enim quam, non pharetra risus. Sed sed mi nulla, sit amet aliquam ipsum. Ut faucibus vestibulum feugiat. Nulla ut dui eget massa pellentesque scelerisque. Aenean ut ipsum at orci lacinia mollis id nec urna. Proin eros dui, feugiat vitae adipiscing ut, rutrum vitae quam. Nam et convallis augue. Quisque ut odio eu ante consequat elementum.</p>', 1, '2025-10-26 18:46:49', '2025-10-26 15:19:46', '2025-10-26 15:19:46'),
(7, 'politica-de-privacidade', 'Política de Privacidade', '<h1>Privacy Policy</h1>\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor lobortis tortor sit amet auctor. Praesent pretium, leo eget luctus tempor, lectus erat vulputate libero, in viverra dolor velit quis ante. Vivamus viverra pulvinar sollicitudin. Vivamus dictum orci sed lorem venenatis quis rutrum risus dictum. Ut non enim elit. In consectetur, nibh sed iaculis pellentesque, nisi ligula egestas enim, sagittis fermentum nulla nisl sit amet velit. Aliquam erat volutpat. Suspendisse ac mi tortor, at vestibulum augue. Suspendisse ut nisl quam, euismod scelerisque urna. Donec non leo et nisi tempus fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec gravida sodales est vitae tincidunt.</p>\n<p>Curabitur neque dui, adipiscing a dignissim sed, congue ut sem. Vivamus eget tellus lectus. Nullam ut tempus purus. Vestibulum pellentesque lorem nec velit hendrerit porta. Cras sit amet mauris odio. Sed sit amet libero nec ipsum venenatis interdum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed at ligula eu enim congue molestie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tincidunt diam at metus facilisis aliquam. Vivamus a orci nunc, molestie consectetur purus. Vivamus aliquam, diam eu rhoncus mollis, est lorem aliquam neque, elementum molestie sapien velit id sapien. Maecenas quis dolor nisl.</p>\n<p>Morbi nisi quam, suscipit eu accumsan a, porttitor sed risus. Donec laoreet, dolor semper eleifend sodales, erat lacus pretium velit, vitae dignissim felis risus non magna. Suspendisse potenti. Proin at nulla massa, et dictum magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pharetra, purus vel egestas egestas, orci lectus vehicula quam, non placerat massa lacus id justo. Integer posuere laoreet porttitor.</p>\n<p>Nam nulla metus, rutrum in suscipit ac, hendrerit eget nunc. In hac habitasse platea dictumst. Mauris at justo magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis malesuada ultricies hendrerit. Vivamus ullamcorper consectetur dignissim. Praesent nunc lorem, elementum vitae scelerisque vitae, pellentesque quis ipsum. Cras volutpat, erat eu mollis pulvinar, justo libero dictum leo, ac accumsan tellus ipsum eget magna. Suspendisse tristique mauris nec odio fringilla sagittis. Donec rhoncus euismod tortor, nec commodo magna cursus at. Nunc ut euismod dui. Suspendisse sollicitudin, magna eget auctor euismod, dolor mi aliquet tellus, et suscipit dui est nec odio. Aliquam rutrum tellus in nisi ultricies sed elementum nisi molestie. Praesent sit amet ligula id lorem ultrices tristique.</p>\n<p>Suspendisse potenti. Sed urna est, fringilla a condimentum eu, dapibus et erat. Cras ac aliquet erat. Integer eget risus magna, non egestas nulla. Maecenas ut ante tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi vel eros nec quam cursus porttitor et nec orci. Nulla vel odio sit amet nisi feugiat dignissim. Mauris tincidunt enim quam, non pharetra risus. Sed sed mi nulla, sit amet aliquam ipsum. Ut faucibus vestibulum feugiat. Nulla ut dui eget massa pellentesque scelerisque. Aenean ut ipsum at orci lacinia mollis id nec urna. Proin eros dui, feugiat vitae adipiscing ut, rutrum vitae quam. Nam et convallis augue. Quisque ut odio eu ante consequat elementum.</p>\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce porttitor lobortis tortor sit amet auctor. Praesent pretium, leo eget luctus tempor, lectus erat vulputate libero, in viverra dolor velit quis ante. Vivamus viverra pulvinar sollicitudin. Vivamus dictum orci sed lorem venenatis quis rutrum risus dictum. Ut non enim elit. In consectetur, nibh sed iaculis pellentesque, nisi ligula egestas enim, sagittis fermentum nulla nisl sit amet velit. Aliquam erat volutpat. Suspendisse ac mi tortor, at vestibulum augue. Suspendisse ut nisl quam, euismod scelerisque urna. Donec non leo et nisi tempus fermentum. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec gravida sodales est vitae tincidunt.</p>\n<p>Curabitur neque dui, adipiscing a dignissim sed, congue ut sem. Vivamus eget tellus lectus. Nullam ut tempus purus. Vestibulum pellentesque lorem nec velit hendrerit porta. Cras sit amet mauris odio. Sed sit amet libero nec ipsum venenatis interdum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed at ligula eu enim congue molestie. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent tincidunt diam at metus facilisis aliquam. Vivamus a orci nunc, molestie consectetur purus. Vivamus aliquam, diam eu rhoncus mollis, est lorem aliquam neque, elementum molestie sapien velit id sapien. Maecenas quis dolor nisl.</p>\n<p>Morbi nisi quam, suscipit eu accumsan a, porttitor sed risus. Donec laoreet, dolor semper eleifend sodales, erat lacus pretium velit, vitae dignissim felis risus non magna. Suspendisse potenti. Proin at nulla massa, et dictum magna. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer pharetra, purus vel egestas egestas, orci lectus vehicula quam, non placerat massa lacus id justo. Integer posuere laoreet porttitor.</p>\n<p>Nam nulla metus, rutrum in suscipit ac, hendrerit eget nunc. In hac habitasse platea dictumst. Mauris at justo magna. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis malesuada ultricies hendrerit. Vivamus ullamcorper consectetur dignissim. Praesent nunc lorem, elementum vitae scelerisque vitae, pellentesque quis ipsum. Cras volutpat, erat eu mollis pulvinar, justo libero dictum leo, ac accumsan tellus ipsum eget magna. Suspendisse tristique mauris nec odio fringilla sagittis. Donec rhoncus euismod tortor, nec commodo magna cursus at. Nunc ut euismod dui. Suspendisse sollicitudin, magna eget auctor euismod, dolor mi aliquet tellus, et suscipit dui est nec odio. Aliquam rutrum tellus in nisi ultricies sed elementum nisi molestie. Praesent sit amet ligula id lorem ultrices tristique.</p>\n<p>Suspendisse potenti. Sed urna est, fringilla a condimentum eu, dapibus et erat. Cras ac aliquet erat. Integer eget risus magna, non egestas nulla. Maecenas ut ante tortor. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi vel eros nec quam cursus porttitor et nec orci. Nulla vel odio sit amet nisi feugiat dignissim. Mauris tincidunt enim quam, non pharetra risus. Sed sed mi nulla, sit amet aliquam ipsum. Ut faucibus vestibulum feugiat. Nulla ut dui eget massa pellentesque scelerisque. Aenean ut ipsum at orci lacinia mollis id nec urna. Proin eros dui, feugiat vitae adipiscing ut, rutrum vitae quam. Nam et convallis augue. Quisque ut odio eu ante consequat elementum.</p>', 1, '2025-10-26 18:46:49', '2025-10-26 15:19:46', '2025-10-26 15:19:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reviews`
--

CREATE TABLE `reviews` (
  `rev_id` int(11) NOT NULL,
  `review` longtext NOT NULL,
  `u_id` int(11) NOT NULL,
  `rew_date` varchar(255) NOT NULL,
  `avg` varchar(255) NOT NULL,
  `uniq` varchar(999) NOT NULL,
  `b_id` int(11) NOT NULL,
  `rev_active` int(11) NOT NULL,
  `biz_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `reviews`
--

INSERT INTO `reviews` (`rev_id`, `review`, `u_id`, `rew_date`, `avg`, `uniq`, `b_id`, `rev_active`, `biz_id`, `user_id`, `rating`, `title`, `body`, `is_approved`, `created_at`, `updated_at`) VALUES
(5, 'BOM ATENDIMENTO, PRODUTOS FRESCOS', 3, '2025-10-22', '5', '40bf601de6495649-3-14-20251022201537', 14, 1, 14, 3, 5, 'BOM', 'BOM ATENDIMENTO, PRODUTOS FRESCOS', 1, '2025-10-22 19:15:37', '2025-10-22 19:15:37'),
(6, 'Ambiente super agradável e cheiro a pão quente logo à entrada. Os croissants folhados e os pastéis de nata estavam incríveis — massa leve, creme no ponto. Café bem tirado e preços justos. Equipa atenciosa e rápida. Recomendo sem hesitar!', 3, '2025-10-25', '5', 'b08d3f6032a30012-3-41-20251025145416', 41, 1, 41, 3, 5, 'Qualidade, Simpatia e Pão Quentinho', 'Ambiente super agradável e cheiro a pão quente logo à entrada. Os croissants folhados e os pastéis de nata estavam incríveis — massa leve, creme no ponto. Café bem tirado e preços justos. Equipa atenciosa e rápida. Recomendo sem hesitar!', 1, '2025-10-25 13:54:16', '2025-10-25 13:54:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_title` varchar(500) NOT NULL,
  `site_link` varchar(999) NOT NULL,
  `meta_keywords` varchar(999) NOT NULL,
  `meta_description` varchar(999) NOT NULL,
  `home_text` varchar(999) NOT NULL,
  `site_email` varchar(500) NOT NULL,
  `county` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `fb_app_id` varchar(999) NOT NULL,
  `fb_secret_key` varchar(500) NOT NULL,
  `fb_page` varchar(999) NOT NULL,
  `twitter_link` varchar(999) NOT NULL,
  `pinterest_link` varchar(999) NOT NULL,
  `google_pluse_link` varchar(999) NOT NULL,
  `active` int(11) NOT NULL,
  `rev_active` int(11) NOT NULL,
  `template` varchar(256) NOT NULL,
  `site_views` int(11) NOT NULL,
  `vertion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `settings`
--

INSERT INTO `settings` (`id`, `site_title`, `site_link`, `meta_keywords`, `meta_description`, `home_text`, `site_email`, `county`, `zip`, `fb_app_id`, `fb_secret_key`, `fb_page`, `twitter_link`, `pinterest_link`, `google_pluse_link`, `active`, `rev_active`, `template`, `site_views`, `vertion`) VALUES
(1, 'BizLister (dev)', 'localhost/TECINFOSP/Flippy BizLister/bizlister_legacy', '', '', 'Search Local Businesses', 'contact@alexdevcode.com', 'Portugal', '4400-048', '', '', '', '', '', '', 1, 1, 'default', 273, '1.0.0');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Exemplo', 'exemplo-slug', NULL, '2025-10-26 09:58:04', '2025-10-26 09:58:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(500) NOT NULL,
  `email` varchar(999) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `country` varchar(500) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `birthday` varchar(255) NOT NULL,
  `about` varchar(999) NOT NULL,
  `avatar` varchar(500) NOT NULL,
  `profile_photo_url` varchar(512) DEFAULT NULL,
  `password` varchar(500) NOT NULL,
  `provider` varchar(32) DEFAULT NULL,
  `provider_id` varchar(191) DEFAULT NULL,
  `provider_token` text DEFAULT NULL,
  `provider_refresh_token` text DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `registered_date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `email_verified_at`, `country`, `gender`, `birthday`, `about`, `avatar`, `profile_photo_url`, `password`, `provider`, `provider_id`, `provider_token`, `provider_refresh_token`, `is_admin`, `remember_token`, `registered_date`) VALUES
(1, 'alex.tecinf', 'contact@alexdevcode.com', NULL, '', '', '', '', '', NULL, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 0, NULL, 'September 28, 2025'),
(2, 'tester', 'tester@example.com', NULL, '', '', '', '', '', NULL, '$2y$10$wj8xWVvCLPyhPxOPG2BuauXHqs7/WL8Nx/rGhH0qfkfzoX0xToXv.', NULL, NULL, NULL, NULL, 0, '3SALcp3EnmFkxxXjCHFlJxmv7lbsD2AbtH5RSeKy0lAnQD6Lar5Zjl49thpN', 'October 14, 2025'),
(3, 'admin', 'admin@tecinfosp.local', '2025-10-15 22:38:42', '', '', '', '', '', NULL, '$2y$10$NCKxvNvfowU8lHFSBmpHR.8Jehuglr5XrxvhfkR7avZHF5v.a.Xxm', NULL, NULL, NULL, NULL, 0, 'LIz43XkDXnXCmtaoWteo65Q3WNeFPVHfkLJMLu1kbD6P3CJiVzZQG1AlZ1Xz', 'October 14, 2025'),
(4, 'QA Strong', 'qa.user+202510160019322@example.com', NULL, '', '', '', '', '', NULL, '$2y$10$Bgq5h8kvzEG87t3geitE1uC35F0gOuqkSveuVnzlsZ4Zvl0nY5L9S', NULL, NULL, NULL, NULL, 0, NULL, '2025-10-15 20:19:33'),
(5, 'Alex Rodolfo Rodrigues Oliveira', 'alexrroliver200@gmail.com', NULL, '', '', '', '', '', NULL, '$2y$10$z85Z01YYhdqiBAKleEqSpOCLej08DsP14Sw3klID28my1SVfBCNTm', NULL, NULL, NULL, NULL, 0, 'AWQjFwXcpkhjmyMJ4l5c1fIPd1qrSqgm5b7RRZbLcFKHCkrPhIeCCQQMX9BD', '2025-10-15 20:23:57'),
(6, 'dev_Zvc5pM', 'dev@example.test', NULL, 'BR', '', '2000-01-01', '', '', NULL, '$2y$10$pRzOXkK4Jd5J1lFiMOR0lu34Xghu8A8zmV/shW9V2XwKX8KLiHvcm', NULL, NULL, NULL, NULL, 0, 'JjBYmg60Cw6qmjc1gFwlmfD05nBAXIHOBUwasod4zkRkhV8RMe92mYTmrpxQ', '2025-10-18 14:21:07');

-- --------------------------------------------------------

--
-- Estrutura para view `category`
--
DROP TABLE IF EXISTS `category`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `category`  AS SELECT `categories`.`cat_id` AS `cat_id`, `categories`.`category` AS `category`, `categories`.`cat_description` AS `cat_description`, `categories`.`parent_id` AS `parent_id` FROM `categories` ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_users_email_unique` (`email`);

--
-- Índices de tabela `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`bm_id`);

--
-- Índices de tabela `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`biz_id`),
  ADD KEY `idx_business_sid` (`sid`),
  ADD KEY `idx_business_city` (`sid`),
  ADD KEY `idx_business_cat` (`cid`),
  ADD KEY `idx_business_name` (`business_name`(768)),
  ADD KEY `business_subcategory_id_index` (`subcategory_id`);

--
-- Índices de tabela `business_images`
--
ALTER TABLE `business_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_images_business_id_is_primary_sort_order_index` (`business_id`,`is_primary`,`sort_order`);

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Índices de tabela `category_legacy_backup`
--
ALTER TABLE `category_legacy_backup`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `uq_category_name` (`category`);

--
-- Índices de tabela `category_table_old`
--
ALTER TABLE `category_table_old`
  ADD PRIMARY KEY (`cat_id`),
  ADD UNIQUE KEY `uq_category_name` (`category`);

--
-- Índices de tabela `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `city_slug_unique` (`slug`);

--
-- Índices de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Índices de tabela `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`img_id`);

--
-- Índices de tabela `hours`
--
ALTER TABLE `hours`
  ADD PRIMARY KEY (`hour_id`);

--
-- Índices de tabela `laravel_users`
--
ALTER TABLE `laravel_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `laravel_users_email_unique` (`email`);

--
-- Índices de tabela `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`);

--
-- Índices de tabela `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Índices de tabela `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`rev_id`);

--
-- Índices de tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subcategories_category_id_name_unique` (`category_id`,`name`),
  ADD UNIQUE KEY `subcategories_slug_unique` (`slug`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `users_provider_provider_id_index` (`provider`,`provider_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `bm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `business`
--
ALTER TABLE `business`
  MODIFY `biz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `business_images`
--
ALTER TABLE `business_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=999904;

--
-- AUTO_INCREMENT de tabela `category_legacy_backup`
--
ALTER TABLE `category_legacy_backup`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `category_table_old`
--
ALTER TABLE `category_table_old`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=999904;

--
-- AUTO_INCREMENT de tabela `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `galleries`
--
ALTER TABLE `galleries`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `hours`
--
ALTER TABLE `hours`
  MODIFY `hour_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `laravel_users`
--
ALTER TABLE `laravel_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `reviews`
--
ALTER TABLE `reviews`
  MODIFY `rev_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `business`
--
ALTER TABLE `business`
  ADD CONSTRAINT `business_subcategory_id_fk` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
