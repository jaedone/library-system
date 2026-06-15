-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: library_system
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account_statuses`
--

DROP TABLE IF EXISTS `account_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_statuses`
--

LOCK TABLES `account_statuses` WRITE;
/*!40000 ALTER TABLE `account_statuses` DISABLE KEYS */;
INSERT INTO `account_statuses` VALUES (2,'Approved'),(5,'Expired'),(1,'Pending'),(3,'Rejected'),(4,'Suspended');
/*!40000 ALTER TABLE `account_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alumni_profiles`
--

DROP TABLE IF EXISTS `alumni_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumni_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `alumni_id_number` varchar(50) NOT NULL,
  `graduated_program` varchar(150) NOT NULL,
  `graduation_year` year(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `alumni_id_number` (`alumni_id_number`),
  CONSTRAINT `alumni_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumni_profiles`
--

LOCK TABLES `alumni_profiles` WRITE;
/*!40000 ALTER TABLE `alumni_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `alumni_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` bigint(20) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `author_name` (`author_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authors`
--

LOCK TABLES `authors` WRITE;
/*!40000 ALTER TABLE `authors` DISABLE KEYS */;
/*!40000 ALTER TABLE `authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_reservations`
--

DROP TABLE IF EXISTS `book_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `resource_id` bigint(20) unsigned NOT NULL,
  `copy_id` bigint(20) unsigned DEFAULT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `requested_at` timestamp NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `claim_deadline` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `resource_id` (`resource_id`),
  KEY `copy_id` (`copy_id`),
  KEY `status_id` (`status_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `book_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `book_reservations_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`),
  CONSTRAINT `book_reservations_ibfk_3` FOREIGN KEY (`copy_id`) REFERENCES `resource_copies` (`id`),
  CONSTRAINT `book_reservations_ibfk_4` FOREIGN KEY (`status_id`) REFERENCES `request_statuses` (`id`),
  CONSTRAINT `book_reservations_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_reservations`
--

LOCK TABLES `book_reservations` WRITE;
/*!40000 ALTER TABLE `book_reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrow_statuses`
--

DROP TABLE IF EXISTS `borrow_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `borrow_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_statuses`
--

LOCK TABLES `borrow_statuses` WRITE;
/*!40000 ALTER TABLE `borrow_statuses` DISABLE KEYS */;
INSERT INTO `borrow_statuses` VALUES (1,'Borrowed'),(5,'Damaged'),(4,'Lost'),(3,'Overdue'),(2,'Returned');
/*!40000 ALTER TABLE `borrow_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrow_transactions`
--

DROP TABLE IF EXISTS `borrow_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `borrow_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `copy_id` bigint(20) unsigned NOT NULL,
  `reservation_id` bigint(20) unsigned DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `borrowed_at` datetime NOT NULL,
  `due_at` datetime NOT NULL,
  `returned_at` datetime DEFAULT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `copy_id` (`copy_id`),
  KEY `reservation_id` (`reservation_id`),
  KEY `processed_by` (`processed_by`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `borrow_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `borrow_transactions_ibfk_2` FOREIGN KEY (`copy_id`) REFERENCES `resource_copies` (`id`),
  CONSTRAINT `borrow_transactions_ibfk_3` FOREIGN KEY (`reservation_id`) REFERENCES `book_reservations` (`id`),
  CONSTRAINT `borrow_transactions_ibfk_4` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `borrow_transactions_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `borrow_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_transactions`
--

LOCK TABLES `borrow_transactions` WRITE;
/*!40000 ALTER TABLE `borrow_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `borrow_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (4,'Business'),(5,'Communication'),(1,'Computer Science'),(3,'Education'),(2,'Engineering'),(10,'General References'),(6,'Law'),(8,'Literature'),(9,'Science and Technology'),(7,'Tourism and Hospitality');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colleges`
--

DROP TABLE IF EXISTS `colleges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colleges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `college_name` (`college_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colleges`
--

LOCK TABLES `colleges` WRITE;
/*!40000 ALTER TABLE `colleges` DISABLE KEYS */;
/*!40000 ALTER TABLE `colleges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `copy_statuses`
--

DROP TABLE IF EXISTS `copy_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `copy_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `copy_statuses`
--

LOCK TABLES `copy_statuses` WRITE;
/*!40000 ALTER TABLE `copy_statuses` DISABLE KEYS */;
INSERT INTO `copy_statuses` VALUES (1,'Available'),(2,'Borrowed'),(5,'Damaged'),(7,'Digital Access Only'),(4,'Lost'),(3,'Reserved'),(6,'Room Use Only');
/*!40000 ALTER TABLE `copy_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_score_logs`
--

DROP TABLE IF EXISTS `credit_score_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_score_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `points_change` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `credit_score_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_score_logs`
--

LOCK TABLES `credit_score_logs` WRITE;
/*!40000 ALTER TABLE `credit_score_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_score_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_scores`
--

DROP TABLE IF EXISTS `credit_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `current_score` int(11) NOT NULL DEFAULT 100,
  `status_level` varchar(50) DEFAULT 'Good',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `credit_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_scores`
--

LOCK TABLES `credit_scores` WRITE;
/*!40000 ALTER TABLE `credit_scores` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `department_name` (`department_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_types`
--

DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_type_name` (`document_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_types`
--

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
INSERT INTO `document_types` VALUES (2,'2x2 Photo'),(5,'Alumni ID'),(1,'Certificate of Registration'),(4,'Employee ID'),(3,'PUP ID'),(6,'Referral Letter'),(7,'Valid Government ID');
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `facility_name` varchar(150) NOT NULL,
  `facility_type` varchar(100) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facility_name` (`facility_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

LOCK TABLES `facilities` WRITE;
/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
INSERT INTO `facilities` VALUES (1,'Discussion Room 1','Discussion Room',8,'Main Library',1),(2,'Discussion Room 2','Discussion Room',8,'Main Library',1),(3,'Reading Table Area','Table/Desk Reservation',50,'Reading Area',1);
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facility_reservations`
--

DROP TABLE IF EXISTS `facility_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facility_reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `facility_id` bigint(20) unsigned NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` text NOT NULL,
  `number_of_participants` int(11) DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `facility_id` (`facility_id`),
  KEY `status_id` (`status_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `facility_reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `facility_reservations_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`),
  CONSTRAINT `facility_reservations_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `request_statuses` (`id`),
  CONSTRAINT `facility_reservations_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facility_reservations`
--

LOCK TABLES `facility_reservations` WRITE;
/*!40000 ALTER TABLE `facility_reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `facility_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faculty_employee_profiles`
--

DROP TABLE IF EXISTS `faculty_employee_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faculty_employee_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `employee_number` varchar(50) NOT NULL,
  `college_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `personnel_type` enum('Faculty','Employee') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `employee_number` (`employee_number`),
  KEY `college_id` (`college_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `faculty_employee_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `faculty_employee_profiles_ibfk_2` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
  CONSTRAINT `faculty_employee_profiles_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faculty_employee_profiles`
--

LOCK TABLES `faculty_employee_profiles` WRITE;
/*!40000 ALTER TABLE `faculty_employee_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `faculty_employee_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `library_branches`
--

DROP TABLE IF EXISTS `library_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `library_branches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(150) NOT NULL,
  `location` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_name` (`branch_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `library_branches`
--

LOCK TABLES `library_branches` WRITE;
/*!40000 ALTER TABLE `library_branches` DISABLE KEYS */;
INSERT INTO `library_branches` VALUES (1,'Main Library','PUP Main Campus');
/*!40000 ALTER TABLE `library_branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_types`
--

DROP TABLE IF EXISTS `material_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_type_name` (`material_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_types`
--

LOCK TABLES `material_types` WRITE;
/*!40000 ALTER TABLE `material_types` DISABLE KEYS */;
INSERT INTO `material_types` VALUES (1,'Book'),(3,'Dissertation'),(5,'E-Book'),(7,'Fiction Book'),(4,'Journal'),(8,'Non-print Material'),(6,'Reference Material'),(2,'Thesis');
/*!40000 ALTER TABLE `material_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `notification_type` varchar(100) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_resource_roles`
--

DROP TABLE IF EXISTS `online_resource_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_resource_roles` (
  `online_resource_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`online_resource_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `online_resource_roles_ibfk_1` FOREIGN KEY (`online_resource_id`) REFERENCES `online_resources` (`id`),
  CONSTRAINT `online_resource_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_resource_roles`
--

LOCK TABLES `online_resource_roles` WRITE;
/*!40000 ALTER TABLE `online_resource_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_resource_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_resources`
--

DROP TABLE IF EXISTS `online_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `online_resources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_resources`
--

LOCK TABLES `online_resources` WRITE;
/*!40000 ALTER TABLE `online_resources` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalties`
--

DROP TABLE IF EXISTS `penalties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `borrow_transaction_id` bigint(20) unsigned DEFAULT NULL,
  `penalty_type_id` bigint(20) unsigned NOT NULL,
  `penalty_status_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `days_overdue` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `issued_at` timestamp NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `borrow_transaction_id` (`borrow_transaction_id`),
  KEY `penalty_type_id` (`penalty_type_id`),
  KEY `penalty_status_id` (`penalty_status_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `penalties_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `penalties_ibfk_2` FOREIGN KEY (`borrow_transaction_id`) REFERENCES `borrow_transactions` (`id`),
  CONSTRAINT `penalties_ibfk_3` FOREIGN KEY (`penalty_type_id`) REFERENCES `penalty_types` (`id`),
  CONSTRAINT `penalties_ibfk_4` FOREIGN KEY (`penalty_status_id`) REFERENCES `penalty_statuses` (`id`),
  CONSTRAINT `penalties_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalties`
--

LOCK TABLES `penalties` WRITE;
/*!40000 ALTER TABLE `penalties` DISABLE KEYS */;
/*!40000 ALTER TABLE `penalties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalty_statuses`
--

DROP TABLE IF EXISTS `penalty_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalty_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalty_statuses`
--

LOCK TABLES `penalty_statuses` WRITE;
/*!40000 ALTER TABLE `penalty_statuses` DISABLE KEYS */;
INSERT INTO `penalty_statuses` VALUES (2,'Paid'),(1,'Unpaid'),(3,'Waived');
/*!40000 ALTER TABLE `penalty_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penalty_types`
--

DROP TABLE IF EXISTS `penalty_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penalty_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `penalty_type_name` varchar(100) NOT NULL,
  `default_amount` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penalty_type_name` (`penalty_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penalty_types`
--

LOCK TABLES `penalty_types` WRITE;
/*!40000 ALTER TABLE `penalty_types` DISABLE KEYS */;
INSERT INTO `penalty_types` VALUES (1,'Overdue Book',0.00),(2,'Lost Book',0.00),(3,'Damaged Book',0.00),(4,'Reservation No-show',0.00);
/*!40000 ALTER TABLE `penalty_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_name` (`permission_name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'search_catalog','Search books and library materials'),(2,'view_availability','View book availability'),(3,'reserve_books','Reserve books'),(4,'borrow_materials','Borrow allowed materials'),(5,'renew_books','Renew borrowed books'),(6,'view_own_history','View own borrowing history'),(7,'view_due_dates_penalties','View due dates and penalties'),(8,'request_referral_letters','Request referral letters'),(9,'reserve_facilities','Reserve library facilities'),(10,'access_online_resources','Access online resources'),(11,'view_own_profile','View own account details'),(12,'request_library_visits','Request library visits'),(13,'view_visit_status','View visit request status'),(14,'view_research_materials','View approved research materials'),(15,'manage_borrowing','Manage borrowing transactions'),(16,'manage_returns','Manage return transactions'),(17,'approve_reservations','Approve or reject reservations'),(18,'verify_accounts','Verify library account registrations'),(19,'manage_penalties','Manage penalties and fines'),(20,'update_book_availability','Update book availability'),(21,'generate_operational_reports','Generate operational reports'),(22,'manage_referral_requests','Manage referral letter requests'),(23,'manage_facility_reservations','Manage facility reservations'),(24,'manage_users_roles','Manage users and roles'),(25,'manage_staff_accounts','Manage library staff accounts'),(26,'manage_books_categories','Manage books and categories'),(27,'configure_system_settings','Configure system settings'),(28,'view_analytics_reports','View analytics and reports'),(29,'view_activity_logs','View activity logs'),(30,'backup_restore','Backup and restore data'),(31,'create_admin_accounts','Create administrator accounts');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `college_id` bigint(20) unsigned NOT NULL,
  `program_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `college_id` (`college_id`),
  CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programs`
--

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publishers`
--

DROP TABLE IF EXISTS `publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publishers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `publisher_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `publisher_name` (`publisher_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publishers`
--

LOCK TABLES `publishers` WRITE;
/*!40000 ALTER TABLE `publishers` DISABLE KEYS */;
/*!40000 ALTER TABLE `publishers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral_requests`
--

DROP TABLE IF EXISTS `referral_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referral_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `destination_library` varchar(150) NOT NULL,
  `purpose` text NOT NULL,
  `material_needed` text NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `request_date` timestamp NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `letter_file_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status_id` (`status_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `referral_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `referral_requests_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `request_statuses` (`id`),
  CONSTRAINT `referral_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referral_requests`
--

LOCK TABLES `referral_requests` WRITE;
/*!40000 ALTER TABLE `referral_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `referral_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `renewal_requests`
--

DROP TABLE IF EXISTS `renewal_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `renewal_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `borrow_transaction_id` bigint(20) unsigned NOT NULL,
  `requested_by` bigint(20) unsigned NOT NULL,
  `status_id` bigint(20) unsigned NOT NULL,
  `old_due_at` datetime NOT NULL,
  `new_due_at` datetime DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrow_transaction_id` (`borrow_transaction_id`),
  KEY `requested_by` (`requested_by`),
  KEY `status_id` (`status_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `renewal_requests_ibfk_1` FOREIGN KEY (`borrow_transaction_id`) REFERENCES `borrow_transactions` (`id`),
  CONSTRAINT `renewal_requests_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  CONSTRAINT `renewal_requests_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `request_statuses` (`id`),
  CONSTRAINT `renewal_requests_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `renewal_requests`
--

LOCK TABLES `renewal_requests` WRITE;
/*!40000 ALTER TABLE `renewal_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `renewal_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_statuses`
--

DROP TABLE IF EXISTS `request_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `request_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_statuses`
--

LOCK TABLES `request_statuses` WRITE;
/*!40000 ALTER TABLE `request_statuses` DISABLE KEYS */;
INSERT INTO `request_statuses` VALUES (2,'Approved'),(6,'Cancelled'),(4,'Claimed'),(7,'Completed'),(5,'Expired'),(1,'Pending'),(3,'Rejected');
/*!40000 ALTER TABLE `request_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resource_authors`
--

DROP TABLE IF EXISTS `resource_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_authors` (
  `resource_id` bigint(20) unsigned NOT NULL,
  `author_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`resource_id`,`author_id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `resource_authors_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`),
  CONSTRAINT `resource_authors_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource_authors`
--

LOCK TABLES `resource_authors` WRITE;
/*!40000 ALTER TABLE `resource_authors` DISABLE KEYS */;
/*!40000 ALTER TABLE `resource_authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resource_copies`
--

DROP TABLE IF EXISTS `resource_copies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resource_copies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` bigint(20) unsigned NOT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `accession_number` varchar(100) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `shelf_location` varchar(100) DEFAULT NULL,
  `copy_status_id` bigint(20) unsigned NOT NULL,
  `is_borrowable` tinyint(1) DEFAULT 1,
  `copy_condition` varchar(100) DEFAULT 'Good',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `accession_number` (`accession_number`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `resource_id` (`resource_id`),
  KEY `branch_id` (`branch_id`),
  KEY `copy_status_id` (`copy_status_id`),
  CONSTRAINT `resource_copies_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`),
  CONSTRAINT `resource_copies_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `library_branches` (`id`),
  CONSTRAINT `resource_copies_ibfk_3` FOREIGN KEY (`copy_status_id`) REFERENCES `copy_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource_copies`
--

LOCK TABLES `resource_copies` WRITE;
/*!40000 ALTER TABLE `resource_copies` DISABLE KEYS */;
/*!40000 ALTER TABLE `resource_copies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_type_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `publisher_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `publication_year` year(4) DEFAULT NULL,
  `edition` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `is_reference_only` tinyint(1) DEFAULT 0,
  `is_digital` tinyint(1) DEFAULT 0,
  `digital_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `material_type_id` (`material_type_id`),
  KEY `category_id` (`category_id`),
  KEY `publisher_id` (`publisher_id`),
  CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`material_type_id`) REFERENCES `material_types` (`id`),
  CONSTRAINT `resources_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `resources_ibfk_3` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resources`
--

LOCK TABLES `resources` WRITE;
/*!40000 ALTER TABLE `resources` DISABLE KEYS */;
/*!40000 ALTER TABLE `resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),(1,11),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(3,1),(3,2),(3,3),(3,4),(3,5),(3,6),(3,7),(3,8),(3,9),(3,11),(4,1),(4,2),(4,11),(5,1),(5,11),(5,12),(5,13),(5,14),(6,1),(6,15),(6,16),(6,17),(6,18),(6,19),(6,20),(6,21),(6,22),(6,23),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),(7,7),(7,8),(7,9),(7,10),(7,11),(7,12),(7,13),(7,14),(7,15),(7,16),(7,17),(7,18),(7,19),(7,20),(7,21),(7,22),(7,23),(7,24),(7,25),(7,26),(7,27),(7,28),(7,29),(7,30),(7,31);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Student','Currently studying in PUP','2026-06-15 19:56:54','2026-06-15 19:56:54'),(2,'Faculty','Teaching personnel in PUP','2026-06-15 19:56:54','2026-06-15 19:56:54'),(3,'Employee','Non-teaching or administrative personnel in PUP','2026-06-15 19:56:54','2026-06-15 19:56:54'),(4,'Alumni','Former PUP student','2026-06-15 19:56:54','2026-06-15 19:56:54'),(5,'External Researcher','Visitor or researcher from another institution','2026-06-15 19:56:54','2026-06-15 19:56:54'),(6,'Library Staff','Library personnel in PUP','2026-06-15 19:56:54','2026-06-15 19:56:54'),(7,'Administrator','System manager with full access','2026-06-15 19:56:54','2026-06-15 19:56:54');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff_profiles`
--

DROP TABLE IF EXISTS `staff_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `employee_number` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `assigned_branch_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `employee_number` (`employee_number`),
  KEY `assigned_branch_id` (`assigned_branch_id`),
  CONSTRAINT `staff_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `staff_profiles_ibfk_2` FOREIGN KEY (`assigned_branch_id`) REFERENCES `library_branches` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff_profiles`
--

LOCK TABLES `staff_profiles` WRITE;
/*!40000 ALTER TABLE `staff_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_profiles`
--

DROP TABLE IF EXISTS `student_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `student_number` varchar(50) NOT NULL,
  `college_id` bigint(20) unsigned NOT NULL,
  `program_id` bigint(20) unsigned NOT NULL,
  `year_level` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `student_number` (`student_number`),
  KEY `college_id` (`college_id`),
  KEY `program_id` (`program_id`),
  CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `student_profiles_ibfk_2` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`),
  CONSTRAINT `student_profiles_ibfk_3` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_profiles`
--

LOCK TABLES `student_profiles` WRITE;
/*!40000 ALTER TABLE `student_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `updated_by` (`updated_by`),
  CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_documents`
--

DROP TABLE IF EXISTS `user_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `document_type_id` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `document_type_id` (`document_type_id`),
  KEY `verified_by` (`verified_by`),
  CONSTRAINT `user_documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_documents_ibfk_2` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`),
  CONSTRAINT `user_documents_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_documents`
--

LOCK TABLES `user_documents` WRITE;
/*!40000 ALTER TABLE `user_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `library_account_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `library_account_number` (`library_account_number`),
  CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_profiles`
--

LOCK TABLES `user_profiles` WRITE;
/*!40000 ALTER TABLE `user_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `account_status_id` bigint(20) unsigned NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `account_status_id` (`account_status_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`account_status_id`) REFERENCES `account_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor_profiles`
--

DROP TABLE IF EXISTS `visitor_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitor_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `institution` varchar(150) NOT NULL,
  `research_topic` varchar(255) NOT NULL,
  `intended_visit_date` date DEFAULT NULL,
  `purpose_of_visit` text DEFAULT NULL,
  `approved_until` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `visitor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor_profiles`
--

LOCK TABLES `visitor_profiles` WRITE;
/*!40000 ALTER TABLE `visitor_profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitor_profiles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-16  4:19:26
