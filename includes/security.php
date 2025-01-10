<?php
class Security {
    // CSRF Token Management
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = array();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$token] = time();
        return $token;
    }

    public static function validateCSRFToken($token) {
        if (isset($_SESSION['csrf_tokens'][$token])) {
            unset($_SESSION['csrf_tokens'][$token]);
            return true;
        }
        return false;
    }

    // XSS Prevention
    public static function sanitizeOutput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitizeOutput($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    // SQL Injection Prevention
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return trim(strip_tags($data));
    }

    // File Upload Validation
    public static function validateFileUpload($file, $allowedTypes = ['image/jpeg', 'image/png'], $maxSize = 5242880) {
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed with error code: ' . $file['error'];
            return $errors;
        }

        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes);
        }

        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds limit of ' . ($maxSize / 1024 / 1024) . 'MB';
        }

        // Check for PHP code in images
        $content = file_get_contents($file['tmp_name']);
        if (preg_match('/<\?php/i', $content)) {
            $errors[] = 'File contains PHP code';
        }

        return $errors;
    }

    // Password Strength Validation
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }
        
        return $errors;
    }

    // Session Security
    public static function secureSession() {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        session_regenerate_id(true);
    }

    // Log Security Events
    public static function logSecurityEvent($event, $details) {
        $logFile = __DIR__ . '/../logs/security.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
        
        $logEntry = sprintf(
            "[%s] IP: %s, User: %s, Event: %s, Details: %s\n",
            $timestamp,
            $ip,
            $user,
            $event,
            json_encode($details)
        );
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    // Rate Limiting
    public static function checkRateLimit($key, $limit = 10, $period = 60) {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        
        $current = $redis->incr($key);
        if ($current === 1) {
            $redis->expire($key, $period);
        }
        
        return $current <= $limit;
    }
} 