-- ============================================================================
-- Campus EventHub — Seed Data
-- Default passwords: Admin@123 (admin), User@123 (standard user)
-- Hashes generated with PHP password_hash(..., PASSWORD_BCRYPT)
-- ============================================================================

USE campus_eventhub;

-- Clear existing data (order respects foreign keys)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE ai_logs;
TRUNCATE TABLE activity_logs;
TRUNCATE TABLE ticket_requests;
TRUNCATE TABLE announcements;
TRUNCATE TABLE events;
TRUNCATE TABLE faq_items;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users: admin + standard
INSERT INTO users (name, email, password, role, status, created_at, updated_at) VALUES
('System Admin', 'admin@eventhub.test', '$2y$10$C1Ei6QgCqQOeXeP0ygXpGeZM.Saz67jjZsSO9EkXLYzQFJmazLAUq', 'admin', 'active', NOW(), NOW()),
('Jamie Student', 'user@eventhub.test', '$2y$10$sU54Dhj3XkptVaRajYnHTeEcVxd8U7upBlE.rSukgb9eXfDwTjKY2', 'user', 'active', NOW(), NOW());

-- Events (8+ published samples)
INSERT INTO events (title, slug, description, category, location, event_date, start_time, end_time, capacity, image, status, created_by, updated_by, created_at, updated_at) VALUES
('Welcome Week Fair', 'welcome-week-fair', 'Meet student clubs, societies, and local organisers at the main campus quad. Free entry for all new and returning students.', 'Social', 'Main Quad', DATE_ADD(CURDATE(), INTERVAL 14 DAY), '10:00:00', '16:00:00', 500, NULL, 'published', 1, 1, NOW(), NOW()),
('Coding Club Hack Night', 'coding-club-hack-night', 'Bring your laptop and collaborate on mini projects. Mentors available for Git, PHP, and JavaScript questions.', 'Technology', 'Library Lab 2', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '18:00:00', '21:00:00', 40, NULL, 'published', 1, 1, NOW(), NOW()),
('Sustainability Workshop', 'sustainability-workshop', 'Learn practical tips for reducing waste on campus. Includes short talks and a Q&A with the green society.', 'Workshop', 'Room B204', DATE_ADD(CURDATE(), INTERVAL 21 DAY), '13:00:00', '15:30:00', 60, NULL, 'published', 1, 1, NOW(), NOW()),
('International Food Festival', 'international-food-festival', 'Taste dishes from around the world prepared by student cultural groups. Tickets required for tasting plates.', 'Culture', 'Student Union Hall', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '12:00:00', '20:00:00', 300, NULL, 'published', 1, 1, NOW(), NOW()),
('Careers in Tech Panel', 'careers-in-tech-panel', 'Alumni share advice on internships and graduate roles. Networking session follows the panel discussion.', 'Careers', 'Auditorium A', DATE_ADD(CURDATE(), INTERVAL 10 DAY), '14:00:00', '16:00:00', 120, NULL, 'published', 1, 1, NOW(), NOW()),
('Charity Fun Run', 'charity-fun-run', '5K campus run raising funds for local charities. All fitness levels welcome; register for your race bib.', 'Sports', 'Sports Field', DATE_ADD(CURDATE(), INTERVAL 45 DAY), '09:00:00', '12:00:00', 200, NULL, 'published', 1, 1, NOW(), NOW()),
('Film Society Premiere', 'film-society-premiere', 'Exclusive screening of an award-winning indie film followed by director Q&A. Limited seating available.', 'Arts', 'Cinema Room', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '19:00:00', '22:00:00', 80, NULL, 'published', 1, 1, NOW(), NOW()),
('Mindfulness Morning', 'mindfulness-morning', 'Guided meditation and wellbeing resources for exam season. Quiet space with mats provided.', 'Wellbeing', 'Wellness Centre', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:30:00', '09:30:00', 35, NULL, 'published', 1, 1, NOW(), NOW()),
('Draft: Robotics Demo', 'draft-robotics-demo', 'Internal draft event for robotics society equipment testing. Not visible on public listings until published.', 'Technology', 'Engineering Lab', DATE_ADD(CURDATE(), INTERVAL 60 DAY), '15:00:00', '17:00:00', 25, NULL, 'draft', 1, 1, NOW(), NOW());

-- Announcements (4+)
INSERT INTO announcements (title, body, status, created_by, updated_by, created_at, updated_at) VALUES
('Ticket requests now open', 'You can request tickets for published events from your dashboard. Approvals may take 1–2 working days during busy periods.', 'published', 1, 1, NOW(), NOW()),
('Campus Wi-Fi maintenance', 'Wireless maintenance is scheduled Sunday 02:00–04:00. Event kiosks will use offline mode if needed.', 'published', 1, 1, NOW(), NOW()),
('AI draft assistant available', 'Admins can generate event draft text using the AI assistant. All drafts require human review before publishing.', 'published', 1, 1, NOW(), NOW()),
('Draft: Alumni reunion planning', 'Internal note: alumni reunion planning meeting minutes attached in admin drive.', 'draft', 1, 1, NOW(), NOW());

-- FAQ items (8+)
INSERT INTO faq_items (question, answer, keywords, status, created_at) VALUES
('How do I request tickets?', 'Log in, open a published event, and submit the ticket request form. Track status under My Tickets.', 'ticket,request,login', 'active', NOW()),
('How long does approval take?', 'Most requests are reviewed within two working days. You will see status updates on My Tickets.', 'approval,pending,time', 'active', NOW()),
('Can I cancel a ticket request?', 'Contact an admin or wait for your request to be rejected/cancelled. Duplicate active requests are blocked.', 'cancel,ticket', 'active', NOW()),
('What events are shown publicly?', 'Only events marked as published appear on the Events page. Draft and cancelled events are hidden.', 'published,events,public', 'active', NOW()),
('How do I register an account?', 'Use the Register page. New accounts are standard users only. Admins are created by the system.', 'register,account,signup', 'active', NOW()),
('What is the AI event draft?', 'Admins can generate draft text from a prompt. You must edit and manually create the event — nothing auto-publishes.', 'ai,draft,admin', 'active', NOW()),
('Is my password secure?', 'Passwords are hashed with bcrypt. Use at least 8 characters with upper, lower, and a number.', 'password,security', 'active', NOW()),
('Who can access the admin area?', 'Only users with the admin role can manage events, tickets, announcements, and view audit logs.', 'admin,role,access', 'active', NOW());

-- Sample ticket requests
INSERT INTO ticket_requests (event_id, user_id, quantity, attendee_name, attendee_email, note, status, admin_note, created_by, updated_by, created_at, updated_at) VALUES
(1, 2, 2, 'Jamie Student', 'user@eventhub.test', 'Bringing a friend from halls', 'pending', NULL, 2, 2, NOW(), NOW()),
(2, 2, 1, 'Jamie Student', 'user@eventhub.test', '', 'approved', 'Approved — enjoy hack night!', 2, 1, NOW(), NOW()),
(7, 2, 1, 'Jamie Student', 'user@eventhub.test', 'Seat near front if possible', 'rejected', 'Event at capacity', 2, 1, NOW(), NOW());

-- Sample activity logs
INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip_address, created_at) VALUES
(1, 'user_logged_in', 'user', 1, 'Seed: admin login sample', '127.0.0.1', NOW()),
(2, 'user_registered', 'user', 2, 'Seed: standard user registered', '127.0.0.1', NOW()),
(1, 'event_created', 'event', 1, 'Seed: welcome week fair created', '127.0.0.1', NOW());
