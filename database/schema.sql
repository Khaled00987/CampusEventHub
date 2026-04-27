-- ============================================================================
-- Campus EventHub — Database Schema
-- Engine: InnoDB | Charset: utf8mb4
-- Import via phpMyAdmin or: mysql -u root campus_eventhub < schema.sql
-- ============================================================================

CREATE DATABASE IF NOT EXISTS campus_eventhub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE campus_eventhub;

-- ----------------------------------------------------------------------------
-- Table: users
-- Stores admin and standard user accounts with role-based access control.
-- ----------------------------------------------------------------------------
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password VARCHAR(255) NOT NULL COMMENT 'bcrypt hash via password_hash()',
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status ENUM('active','blocked') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email),
  INDEX idx_users_role (role),
  INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Application users (admin and standard)';

-- ----------------------------------------------------------------------------
-- Table: events
-- Campus events managed by admins; public users browse published events.
-- ----------------------------------------------------------------------------
CREATE TABLE events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(80) NOT NULL,
  location VARCHAR(200) NOT NULL,
  event_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  capacity INT UNSIGNED NOT NULL DEFAULT 100,
  image VARCHAR(120) NULL COMMENT 'Filename only, stored under public/assets/images/events/',
  status ENUM('draft','published','cancelled') NOT NULL DEFAULT 'draft',
  created_by INT UNSIGNED NULL,
  updated_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_events_slug (slug),
  INDEX idx_events_title (title),
  INDEX idx_events_category (category),
  INDEX idx_events_status (status),
  INDEX idx_events_event_date (event_date),
  CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_events_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='University club and local events';

-- ----------------------------------------------------------------------------
-- Table: ticket_requests
-- Standard users request tickets; admins approve/reject with capacity checks.
-- ----------------------------------------------------------------------------
CREATE TABLE ticket_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  quantity TINYINT UNSIGNED NOT NULL DEFAULT 1,
  attendee_name VARCHAR(100) NOT NULL,
  attendee_email VARCHAR(150) NOT NULL,
  note VARCHAR(500) NULL,
  status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  admin_note TEXT NULL,
  created_by INT UNSIGNED NULL,
  updated_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_tickets_status (status),
  INDEX idx_tickets_attendee_email (attendee_email),
  CONSTRAINT fk_tickets_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_tickets_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_tickets_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Event ticket requests from standard users';

-- ----------------------------------------------------------------------------
-- Table: announcements
-- Admin-authored news; only published rows appear on the public site.
-- ----------------------------------------------------------------------------
CREATE TABLE announcements (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  body TEXT NOT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_by INT UNSIGNED NULL,
  updated_by INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_announcements_title (title),
  INDEX idx_announcements_status (status),
  CONSTRAINT fk_announcements_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_announcements_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Campus announcements and notices';

-- ----------------------------------------------------------------------------
-- Table: activity_logs
-- Audit trail for security and accountability (who did what, when, from where).
-- ----------------------------------------------------------------------------
CREATE TABLE activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity_type VARCHAR(50) NULL,
  entity_id INT UNSIGNED NULL,
  details TEXT NULL,
  ip_address VARCHAR(45) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_activity_action (action),
  INDEX idx_activity_created (created_at),
  CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='System activity and audit log';

-- ----------------------------------------------------------------------------
-- Table: ai_logs
-- Records AI suggestions, human edits, and acceptance for responsible AI use.
-- ----------------------------------------------------------------------------
CREATE TABLE ai_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  feature VARCHAR(50) NOT NULL,
  input_text TEXT NOT NULL,
  ai_suggestion TEXT NOT NULL,
  final_text TEXT NULL COMMENT 'Human-edited text after review',
  accepted TINYINT(1) NOT NULL DEFAULT 0,
  disclaimer_shown TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ai_feature (feature),
  INDEX idx_ai_accepted (accepted),
  CONSTRAINT fk_ai_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='AI feature usage and human review outcomes';

-- ----------------------------------------------------------------------------
-- Table: faq_items
-- Knowledge base for help assistant keyword matching and fallback answers.
-- ----------------------------------------------------------------------------
CREATE TABLE faq_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question VARCHAR(255) NOT NULL,
  answer TEXT NOT NULL,
  keywords VARCHAR(255) NOT NULL COMMENT 'Comma-separated terms for search',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_faq_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='FAQ entries for help assistant';
