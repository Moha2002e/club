-- Script de création de la base de données HEPL Tech Lab - Version en ligne
-- Exécutez ce script dans phpMyAdmin de votre hébergement AlwaysData

-- Utiliser la base de données
USE club_database;

-- Désactiver temporairement les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS=0;

-- Supprimer d'abord les tables dépendantes puis les tables parentes
DROP TABLE IF EXISTS task_assignments;
DROP TABLE IF EXISTS event_participants;
DROP TABLE IF EXISTS dashboard_messages;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS project_members;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS announcements;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;

-- Réactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS=1;

-- Créer la table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
    theme_preference ENUM('light', 'dark', 'blue', 'green') DEFAULT 'light',
    activation_token VARCHAR(255) NULL,
    otp_expires_at TIMESTAMP NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_activation_token (activation_token)
);

-- Table des paramètres généraux du dashboard (clé/valeur)
CREATE TABLE settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT NULL,
    description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des projets
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('planning', 'active', 'completed', 'cancelled') DEFAULT 'planning',
    visibility ENUM('public', 'private') DEFAULT 'public',
    start_date DATE,
    due_date DATE,
    owner_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_owner (owner_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
);

-- Table pour les participations aux projets
CREATE TABLE project_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('member', 'moderator', 'owner') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_user (project_id, user_id)
);

-- Table pour les tâches
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    created_by INT NOT NULL,
    project_id INT NOT NULL,
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_by (created_by),
    INDEX idx_due_date (due_date),
    INDEX idx_project_id (project_id)
);

-- Table pour les assignations multiples des tâches
CREATE TABLE task_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_task_user (task_id, user_id),
    INDEX idx_task_id (task_id),
    INDEX idx_user_id (user_id)
);

-- Table des événements
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    location VARCHAR(200),
    start_date DATETIME NOT NULL,
    end_date DATETIME NULL,
    event_type ENUM('meeting', 'deadline', 'milestone', 'presentation', 'training', 'social', 'other') DEFAULT 'meeting',
    project_id INT NULL,
    target_type ENUM('all', 'specific') DEFAULT 'all',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    INDEX idx_start (start_date),
    INDEX idx_target_type (target_type),
    INDEX idx_event_type (event_type),
    INDEX idx_project (project_id)
);

-- Table pour les participants/destinataires des événements
CREATE TABLE event_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_user (event_id, user_id),
    INDEX idx_event_id (event_id),
    INDEX idx_user_id (user_id)
);

-- Table des annonces
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    visibility ENUM('public','private') DEFAULT 'public',
    pinned BOOLEAN DEFAULT FALSE,
    publish_date DATE NOT NULL,
    expire_date DATE NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_publish_date (publish_date),
    INDEX idx_expire_date (expire_date),
    INDEX idx_pinned (pinned)
);

-- Table des messages du dashboard
CREATE TABLE dashboard_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_is_active (is_active)
);

-- Insérer un utilisateur admin par défaut
INSERT INTO users (username, email, password, first_name, last_name, role, is_verified, is_active) 
VALUES ('admin', 'admin@hepltechlab.be', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'HEPL Tech Lab', 'admin', TRUE, TRUE);

-- Insérer des paramètres par défaut
INSERT INTO settings (`key`, `value`, description) VALUES
('site_name', 'HEPL Tech Lab', 'Nom du site'),
('welcome_message', 'Bienvenue sur la plateforme HEPL Tech Lab !', 'Message de bienvenue'),
('max_projects_per_user', '10', 'Nombre maximum de projets par utilisateur'),
('max_tasks_per_project', '50', 'Nombre maximum de tâches par projet');

-- Message de succès
SELECT 'Base de données créée avec succès !' as message;

