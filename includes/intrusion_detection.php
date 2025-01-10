<?php
class IntrusionDetection {
    private static $suspiciousPatterns = [
        'sql_injection' => [
            '/UNION\s+SELECT/i',
            '/OR\s+1\s*=\s*1/i',
            '/DROP\s+TABLE/i',
            '/SLEEP\s*\(/i',
            '/BENCHMARK\s*\(/i'
        ],
        'xss' => [
            '/<script/i',
            '/javascript:/i',
            '/onerror=/i',
            '/onload=/i',
            '/eval\s*\(/i'
        ],
        'path_traversal' => [
            '/\.\.\//i',
            '/\.\.\\\/i'
        ],
        'command_injection' => [
            '/;\s*\w+/i',
            '/\|\s*\w+/i',
            '/`.*`/i'
        ]
    ];

    public static function detectAttack($input, $type = null) {
        $patterns = $type ? [self::$suspiciousPatterns[$type]] : self::$suspiciousPatterns;
        
        foreach ($patterns as $attackType => $patternList) {
            foreach ($patternList as $pattern) {
                if (preg_match($pattern, $input)) {
                    self::logAttack($attackType, $input);
                    return true;
                }
            }
        }
        return false;
    }

    private static function logAttack($type, $input) {
        $logFile = __DIR__ . '/../logs/attacks.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'guest';
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $logEntry = sprintf(
            "[%s] Attack Type: %s\nIP: %s\nUser: %s\nUser Agent: %s\nInput: %s\n\n",
            $timestamp,
            $type,
            $ip,
            $user,
            $userAgent,
            $input
        );
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    public static function blockSuspiciousIP() {
        $logFile = __DIR__ . '/../logs/attacks.log';
        $ip = $_SERVER['REMOTE_ADDR'];
        
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $matches = [];
            preg_match_all("/IP: $ip/", $logs, $matches);
            
            if (count($matches[0]) > 5) { // If IP appears more than 5 times
                header('HTTP/1.0 403 Forbidden');
                die('Access Denied');
            }
        }
    }
} 