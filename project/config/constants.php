<?php
/**
 * config/constants.php
 * Application-wide constants
 */

// ── Paths ──────────────────────────────────────────────────────
define('BASE_PATH',    dirname(__DIR__));
define('PUBLIC_PATH',  BASE_PATH . '/public');
define('UPLOAD_PATH',  BASE_PATH . '/uploads');
define('OUTPUT_PATH',  BASE_PATH . '/outputs');

// ── URLs ───────────────────────────────────────────────────────
define('BASE_URL',     'https://yourdomain.com');   // ← change in production
define('UPLOAD_URL',   BASE_URL . '/uploads');
define('OUTPUT_URL',   BASE_URL . '/outputs');

// ── App ────────────────────────────────────────────────────────
define('APP_NAME',     'Online Urgent Service LTD');
define('APP_VERSION',  '1.0.0');

// ── Service Pricing ────────────────────────────────────────────
define('PRICE_SMART_CARD_PDF',  100);
define('PRICE_NID_PDF',          80);
define('PRICE_SERVER_COPY',      50);
define('PRICE_SIGN_COPY',        30);

// ── API Endpoints ──────────────────────────────────────────────
define('SMART_NID_API_URL', 'https://api.example.com/smart-nid');  // ← change

// ── Session / Security ─────────────────────────────────────────
define('SESSION_LIFETIME', 3600);   // 1 hour
define('CSRF_TOKEN_NAME',  'csrf_token');

// ── Firebase (push notifications) ─────────────────────────────
define('FIREBASE_API_KEY',           'AIzaSyBCC4-0Ok1gvUGldoweDTL2ippcayR7dRY');
define('FIREBASE_AUTH_DOMAIN',       'besytbuy-notification.firebaseapp.com');
define('FIREBASE_PROJECT_ID',        'besytbuy-notification');
define('FIREBASE_MESSAGING_SENDER',  '158336646594');
define('FIREBASE_APP_ID',            '1:158336646594:web:6470de363990c062152a5b');

// ── Allowed file types ─────────────────────────────────────────
define('ALLOWED_PDF_MIME',   'application/pdf');
define('ALLOWED_IMAGE_MIME', ['image/jpeg', 'image/png', 'image/webp']);
define('MAX_UPLOAD_BYTES',   10 * 1024 * 1024);   // 10 MB
