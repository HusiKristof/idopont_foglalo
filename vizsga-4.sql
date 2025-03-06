-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: localhost:8889
-- Létrehozás ideje: 2025. Már 02. 11:08
-- Kiszolgáló verziója: 8.0.35
-- PHP verzió: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `vizsga`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `provider_id` int NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','canceled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `provider_id`, `appointment_date`, `status`) VALUES
(114, 21, 13, '2025-03-13 12:30:00', 'canceled'),
(115, 21, 14, '2025-02-26 11:00:00', 'confirmed'),
(120, 21, 15, '2025-03-13 15:00:00', 'pending');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `appointment_id` int DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `providers`
--

CREATE TABLE `providers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` text,
  `name` varchar(255) DEFAULT NULL,
  `working_hours` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `price` int DEFAULT NULL,
  `duration` int DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `providers`
--

INSERT INTO `providers` (`id`, `user_id`, `type`, `description`, `name`, `working_hours`, `address`, `phone_number`, `price`, `duration`, `image_path`) VALUES
(13, 14, 'Egészségügy', 'A VitalMed Egészségközpont modern felszereltséggel és szakértő orvosi csapattal várja ügyfeleit. Széles körű egészségügyi szolgáltatásokat kínálunk, beleértve általános orvosi vizsgálatokat, laboratóriumi teszteket és szakorvosi konzultációkat. Célunk, hogy minden páciens magabiztosan és kényelmesen érezze magát nálunk.', 'VitalMed Egészségközpont', 'Hétfő-Péntek 08:00-18:00', '1061 Budapest, Andrássy út 45.', '+36 12-312-3132', 15000, 60, '/uploads/67c1c0121148b.jpg'),
(14, 14, 'Szépségipar', 'A Glamour Szépségszalonban minden szépségápolási szolgáltatás megtalálható egy helyen. Hajvágás, smink, körmös és kozmetikai kezelések mellett kínálunk luxus spa-programokat is. Célunk, hogy ügyfeleink kifogástalanul érezzék magukat.', 'Glamour Szépségszalon', 'Hétfő-Szombat 09:00-19:00', '1052 Budapest, Deák Ferenc utca 12.', '+36 12-312-3132', 10000, 90, '/uploads/67c1c0ae493ad.jpg'),
(15, 14, 'Oktatás', 'Az OkosFej Tanácsadó Központban szakértő tanárok segítenek diákoknak és felnőtteknek a tanulmányi és szakmai céljaik elérésében. Kínálunk egyéni és csoportos órákat, valamint vizsgafelkészítést.', 'OkosFej Tanácsadó Központ', 'Hétfő-Péntek 10:00-20:00', '1073 Budapest, Rákóczi út 34.', '+36 12-312-3132', 8000, 60, '/uploads/67c1c17b7d090.jpg'),
(16, 14, 'Adminisztratív', 'A ProfiÜgy Kft. gyors és megbízható ügyintézést kínál vállalatoknak és magánszemélyeknek. Adóügyek, jogi kérdések és egyéb hivatalos ügyek intézésében nyújtunk segítséget.', 'ProfiÜgy Kft.', 'Hétfő-Péntek 09:00-17:00', '1132 Budapest, Váci út 67.', '+36 12-312-3132', 20000, 60, '/uploads/67c1c2530fd82.jpg'),
(17, 18, 'Egészségügy', 'A MediCare Diagnosztikai Központban modern technológiával végzünk részletes egészségügyi kivizsgálásokat. Szív- és érrendszeri vizsgálatok, képalkotó diagnosztika és laboratóriumi tesztek segítségével pontos diagnózist állítunk fel.', 'MediCare Diagnosztikai Központ', 'Hétfő-Péntek 07:00-20:00', '1024 Budapest, Margit körút 35.', '+36 30-790-0489', 25000, 120, '/uploads/67c1c34181d41.jpg'),
(18, 18, 'Szépségipar', 'A Crystal Nails Körömstúdióban kreatív és precíz körömdesignok készítésével foglalkozunk. Géllakk, műköröm és manikűr szolgáltatásaink segítségével tökéletes megjelenést garantálunk.', 'Crystal Nails Körömstúdió', 'Hétfő-Péntek 10:00-19:00', '1136 Budapest, Pozsonyi út 20.', '+36 30-790-0489', 8000, 60, '/uploads/67c1c411bcadf.jpg'),
(19, 18, 'Oktatás', 'A NyelvMester Nyelviskolában hatékányan tanulhatsz idegen nyelveket. Angol, német, spanyol és más nyelvek oktatását kínáljuk, akár kezdőknek, akár haladóknak.', 'NyelvMester Nyelviskola', 'Hétfő-Szombat 09:00-21:00', '1056 Budapest, Váci utca 30.', '+36 30-790-0489', 12000, 45, '/uploads/67c1c4cef041e.jpg'),
(20, 18, 'Adminisztratív', 'A GyorsÜgy Ügyintéző Iroda segít ügyfeleinek a hivatalos ügyek gyors és stresszmentes intézésében. Különböző adminisztratív feladatokra specializálódtunk, hogy ügyfeleink időt és energiát spórolhassanak.', 'GyorsÜgy Ügyintéző Iroda', 'Hétfő-Péntek 08:00-16:00', '1085 Budapest, József körút 40.', '+36 30-790-0489', 15000, 30, '/uploads/67c1c63d7fc27.jpg'),
(21, 25, 'Egészségügy', 'A Harmony Gyógymasszázs Stúdióban professzionális masszázsokkal segítjük ügyfeleink egészségének és relaxációjának helyreállítását. Kínálunk sportmasszázst, relaxációs masszázst és gyógymasszázst, mindegyik testre szabottan.', 'Harmony Gyógymasszázs Stúdió', 'Hétfő-Szombat 10:00-20:00', '1075 Budapest, Madách Imre út 8.', '+36 23-643-1242', 12000, 50, '/uploads/67c1c76c8ce07.jpg'),
(22, 25, 'Szépségipar', 'A PureSkin Kozmetikai Stúdióban a bőr egészségére és szépségére összpontosítunk. Arctisztítás, mikrodermabrázió és egyéb bőrkezelések segítségével friss és ragyogó bőrt érhet el.', 'PureSkin Kozmetikai Stúdió', 'Hétfő-Péntek 09:00-18:00', '1117 Budapest, Budafoki út 56.', '+36 23-643-1242', 18000, 75, '/uploads/67c1c802ae63f.jpg'),
(23, 25, 'Oktatás', 'A CodeMaster Programozó Iskolában programozási és webfejlesztési készségeket sajátíthatsz el. Python, JavaScript és más nyelvek oktatását kínáljuk, valamint projektalapú tanulási lehetőségeket.', 'CodeMaster Programozó Iskola', 'Hétfő-Péntek 12:00-20:00', '1092 Budapest, Ráday utca 15.', '+36 23-643-1242', 15000, 90, '/uploads/67c1c8b322023.jpg'),
(24, 25, 'Adminisztratív', 'A CityHelp Ügyfélszolgálat célja, hogy ügyfeleink számára egyszerűvé és kényelmessé tegye a hivatalos ügyek intézését. Telefonos és személyes ügyintézést is kínálunk, hogy mindenki megtalálja a számára legmegfelelőbb megoldást.', 'CityHelp Ügyfélszolgálat', 'Hétfő-Péntek 10:00-18:00', '1011 Budapest, Fő utca 10.', '+36 23-643-1242', 18000, 45, '/uploads/67c1c925b9ed3.jpg');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ratings`
--

CREATE TABLE `ratings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `appointment_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `rating` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `appointment_id`, `provider_id`, `rating`) VALUES
(101, 21, 108, 13, 4),
(102, 21, 109, 13, 5),
(103, 21, 110, 13, 2),
(104, 21, 109, 13, 5),
(105, 21, 109, 13, 5),
(106, 21, 109, 13, 5),
(107, 21, 109, 13, 5),
(108, 21, 109, 13, 5),
(109, 21, 109, 13, 5),
(110, 21, 111, 14, 5),
(111, 21, 112, 14, 2),
(112, 21, 112, 14, 4),
(113, 21, 112, 14, 1),
(114, 21, 112, 14, 5),
(115, 21, 112, 14, 5),
(116, 21, 113, 15, 5),
(117, 21, 113, 15, 3),
(118, 21, 113, 15, 4),
(119, 21, 113, 15, 3);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `register_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `register_date`) VALUES
(13, 'Menrót Tiborka', 'menrottibi@gmail.com', '$2y$10$nNAAneJdze.mwnTLxigbRuvhmwKHHZykJrEc46NvzOyu40uKX0C4y', '+36 69-420-1234', 'admin', '2025-01-25 17:38:42'),
(14, 'Kicsi Rics', 'kicsilicsi@gmail.com', '$2y$10$kW2BZE1DTZ2JyxPLW.2sX.778gMPwE.yKSAHtdKwfgoH6boKSLafG', '+36 12-312-3132', 'admin', '2025-01-26 16:25:30'),
(18, 'Husi Kristóf', 'husikristof@gmail.com', '$2y$10$e3jMgPpfINC6u0eyVTNUT.lvy9i6tf0cOJpIOEu489S8ZsLq4tqLe', '+36 30-790-0489', 'admin', '2025-02-06 19:34:54'),
(19, 'teszt tesztt', 'nemtudom@vnauk.com', '$2y$10$s0lK8hXEJuAmqvZGKNAxJ.0Lffi5MifmMCdnKRKdWORv9phaCnc1i', '+36 34-545-7685', 'admin', '2025-02-14 19:17:36'),
(20, 'teszt teszt', 'teszt@gmail.com', '$2y$10$HaSW5VXYz4ju4WwlNg2t0ucJ.QkU4knQRKijM5WmELC2aYMtYZ4fa', '+36 12-345-6788', 'admin', '2025-02-14 19:20:40'),
(21, 'customer', 'customer@gmail.com', '$2y$10$Zf0Z0y3bo7dmgTGYEUFVOOpVvOHAuOeMGaeOAFzU0tJCe9mGNsrve', '+36 12-345-6789', 'customer', '2025-02-14 19:46:05'),
(22, 'dasdsa213', 'ljkhsadf@gmail.com', '$2y$10$QbO5UJao0Pb8xIdzSX9LR.b5AFdz2aa.yhuFa7r7cQt2ycoZNy8LG', '+36 65-465-6456', 'customer', '2025-02-15 15:29:42'),
(23, 'teszt teszt2', 'teszt2@gmail.com', '$2y$10$MuCGaWQZA0LK734j/qsPzejsWLh5aCNC/piLJforKZdXPRonVYsv2', '+36 12-345-6789', 'admin', '2025-02-20 15:56:36'),
(25, 'Erdei Tamás', 'erdositomika@gmail.com', '$2y$10$jwr7t37g2FkoB94RAScOQO7qXsF5heCndbHMtX3e4xjckmPogy9FK', '+36 12-235-3213', 'customer', '2025-03-01 20:31:54');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`user_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- A tábla indexei `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- A tábla indexei `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT a táblához `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT a táblához `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Megkötések a táblához `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

--
-- Megkötések a táblához `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `providers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Megkötések a táblához `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_4` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
