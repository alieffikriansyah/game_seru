-- ============================================================
-- GAME SERU - PRODUCTION DATABASE SCHEMA (STRUCTURE ONLY)
-- Generated for Production Deployment
-- Character Set: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- Total Tables: 17 (Auth, Lobby, Skenalitik, UNO, Ular Tangga, Werewolf)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ------------------------------------------------------------
-- 1. USERS & AUTENTIKASI
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `uno_wins` int(11) DEFAULT '0',
  `ww_wins` int(11) DEFAULT '0',
  `sk_wins` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. GAME ROOMS & ROOM PLAYERS (LOBBY SYSTEM)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `game_rooms`;
CREATE TABLE `game_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `game_type` enum('uno','werewolf','ular_tangga','skenalitik','ludo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_user_id` int(11) NOT NULL,
  `status` enum('waiting','playing','finished') COLLATE utf8mb4_unicode_ci DEFAULT 'waiting',
  `max_players` tinyint(4) DEFAULT '10',
  `state_version` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_code` (`room_code`),
  KEY `host_user_id` (`host_user_id`),
  KEY `idx_game_rooms_status` (`status`),
  CONSTRAINT `game_rooms_ibfk_1` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `room_players`;
CREATE TABLE `room_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seat_order` tinyint(4) NOT NULL,
  `is_ready` tinyint(1) DEFAULT '0',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_player_in_room` (`room_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `idx_room_players_room` (`room_id`),
  CONSTRAINT `room_players_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `game_rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `room_players_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. GAME SKENALITIK (MONOPOLI SKENA POLITIK)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `sk_games`;
CREATE TABLE `sk_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `current_turn_user_id` int(11) NOT NULL,
  `winner_user_id` int(11) DEFAULT NULL,
  `dice_1` tinyint(4) DEFAULT NULL,
  `dice_2` tinyint(4) DEFAULT NULL,
  `consecutive_doubles` tinyint(4) DEFAULT '0',
  `pot_tax` int(11) DEFAULT '0',
  `game_phase` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'roll',
  `pending_action` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pending_tile_id` tinyint(4) DEFAULT NULL,
  `last_action` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_action_desc` text COLLATE utf8mb4_unicode_ci,
  `state_version` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sk_players`;
CREATE TABLE `sk_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sk_game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_character` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Kamera Analog',
  `token_icon` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '📷',
  `position` tinyint(4) NOT NULL DEFAULT '0',
  `reputation_points` int(11) NOT NULL DEFAULT '1500',
  `is_in_jail` tinyint(1) DEFAULT '0',
  `jail_turns` tinyint(4) DEFAULT '0',
  `is_eliminated` tinyint(1) DEFAULT '0',
  `eliminated_reason` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `turn_order` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_sk_game` (`sk_game_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sk_properties`;
CREATE TABLE `sk_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sk_game_id` int(11) NOT NULL,
  `tile_id` tinyint(4) NOT NULL,
  `owner_user_id` int(11) DEFAULT NULL,
  `building_level` tinyint(4) DEFAULT '0',
  `rent_blocked_turns` tinyint(4) DEFAULT '0',
  `rent_multiplier_buff` decimal(3,1) DEFAULT '1.0',
  PRIMARY KEY (`id`),
  KEY `idx_sk_game_prop` (`sk_game_id`),
  KEY `idx_tile_prop` (`sk_game_id`,`tile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sk_cards`;
CREATE TABLE `sk_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sk_game_id` int(11) NOT NULL,
  `deck_type` enum('politik','skena') COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_index` tinyint(4) NOT NULL,
  `status` enum('deck','hand','discard') COLLATE utf8mb4_unicode_ci DEFAULT 'deck',
  `holder_user_id` int(11) DEFAULT NULL,
  `card_order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_sk_cards` (`sk_game_id`,`deck_type`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sk_logs`;
CREATE TABLE `sk_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sk_game_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sk_logs` (`sk_game_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. GAME UNO
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `uno_games`;
CREATE TABLE `uno_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `current_turn_user_id` int(11) NOT NULL,
  `direction` tinyint(4) DEFAULT '1',
  `active_color` enum('merah','kuning','hijau','biru') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_value` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `draw_stack` tinyint(4) DEFAULT '0',
  `winner_user_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_id` (`room_id`),
  CONSTRAINT `uno_games_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `game_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `uno_cards`;
CREATE TABLE `uno_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uno_game_id` int(11) NOT NULL,
  `card_color` enum('merah','kuning','hijau','biru','wild') COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_value` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` enum('deck','hand','discard') COLLATE utf8mb4_unicode_ci DEFAULT 'deck',
  `owner_user_id` int(11) DEFAULT NULL,
  `discard_order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uno_cards_game_loc` (`uno_game_id`,`location`),
  KEY `idx_uno_cards_owner` (`owner_user_id`),
  CONSTRAINT `uno_cards_ibfk_1` FOREIGN KEY (`uno_game_id`) REFERENCES `uno_games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `uno_cards_ibfk_2` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. GAME ULAR TANGGA
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `ut_games`;
CREATE TABLE `ut_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `current_turn_user_id` int(11) NOT NULL,
  `winner_user_id` int(11) DEFAULT NULL,
  `last_dice` tinyint(4) DEFAULT NULL,
  `last_from_pos` int(11) DEFAULT NULL,
  `last_to_pos` int(11) DEFAULT NULL,
  `last_action` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consecutive_six` tinyint(4) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ut_players`;
CREATE TABLE `ut_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ut_game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '1',
  `pawn_color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'merah',
  `pawn_icon` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '🚀',
  `rank` int(11) DEFAULT NULL,
  `total_rolls` int(11) DEFAULT '0',
  `snakes_hit` int(11) DEFAULT '0',
  `ladders_hit` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ut_game` (`ut_game_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ut_logs`;
CREATE TABLE `ut_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ut_game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dice` tinyint(4) NOT NULL,
  `from_pos` int(11) NOT NULL,
  `to_pos` int(11) NOT NULL,
  `action_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_game_logs` (`ut_game_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. GAME WEREWOLF
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `ww_games`;
CREATE TABLE `ww_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `day_count` tinyint(4) DEFAULT '1',
  `current_phase` enum('malam','pagi_pengumuman','siang_diskusi','siang_voting','selesai') COLLATE utf8mb4_unicode_ci DEFAULT 'malam',
  `phase_end_time` datetime NOT NULL,
  `winner_team` enum('warga','werewolf') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_id` (`room_id`),
  CONSTRAINT `ww_games_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `game_rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ww_players`;
CREATE TABLE `ww_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ww_game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('warga','werewolf','penerawang','pelindung') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_alive` tinyint(1) DEFAULT '1',
  `eliminated_reason` enum('dimangsa','divoting') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_ww_players_game` (`ww_game_id`),
  CONSTRAINT `ww_players_ibfk_1` FOREIGN KEY (`ww_game_id`) REFERENCES `ww_games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ww_players_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ww_actions`;
CREATE TABLE `ww_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ww_game_id` int(11) NOT NULL,
  `day_count` tinyint(4) NOT NULL,
  `phase` enum('malam','siang_voting') COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_user_id` int(11) NOT NULL,
  `target_user_id` int(11) NOT NULL,
  `action_type` enum('mangsa','terawang','lindungi','vote_gantung') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_action_per_phase` (`ww_game_id`,`day_count`,`phase`,`actor_user_id`),
  CONSTRAINT `ww_actions_ibfk_1` FOREIGN KEY (`ww_game_id`) REFERENCES `ww_games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ww_chats`;
CREATE TABLE `ww_chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ww_game_id` int(11) NOT NULL,
  `sender_user_id` int(11) DEFAULT NULL,
  `channel` enum('publik','werewolf','arwah') COLLATE utf8mb4_unicode_ci DEFAULT 'publik',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ww_chats_game` (`ww_game_id`,`created_at`),
  CONSTRAINT `ww_chats_ibfk_1` FOREIGN KEY (`ww_game_id`) REFERENCES `ww_games` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. GAME LUDO (MULTIPLAYER CLASSIC)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `ludo_tokens`;
DROP TABLE IF EXISTS `ludo_players`;
DROP TABLE IF EXISTS `ludo_logs`;
DROP TABLE IF EXISTS `ludo_games`;

CREATE TABLE `ludo_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `current_turn_user_id` int(11) NOT NULL,
  `winner_user_id` int(11) DEFAULT NULL,
  `dice_value` tinyint(4) DEFAULT NULL,
  `consecutive_sixes` tinyint(4) NOT NULL DEFAULT '0',
  `game_phase` enum('roll','select_token','ended') NOT NULL DEFAULT 'roll',
  `last_action` varchar(50) DEFAULT NULL,
  `last_action_desc` text DEFAULT NULL,
  `state_version` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ludo_room` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ludo_players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ludo_game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `color` enum('red','green','yellow','blue') NOT NULL,
  `turn_order` tinyint(4) NOT NULL DEFAULT '1',
  `finished_rank` tinyint(4) NOT NULL DEFAULT '0',
  `tokens_finished` tinyint(4) NOT NULL DEFAULT '0',
  `is_resigned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_lp_game` (`ludo_game_id`),
  KEY `idx_lp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ludo_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ludo_game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `color` enum('red','green','yellow','blue') NOT NULL,
  `token_index` tinyint(4) NOT NULL,
  `state` enum('yard','track','home_run','finished') NOT NULL DEFAULT 'yard',
  `position` int(11) NOT NULL DEFAULT '-1',
  `steps_moved` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_lt_game_user` (`ludo_game_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ludo_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ludo_game_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ludo_logs` (`ludo_game_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
