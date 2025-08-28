<?php

namespace App\Services;

class LogService
{
    private static string $logPath = __DIR__ . '/../../logs/';
    
    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }
    
    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }
    
    public static function api(string $service, array $request, array $response): void
    {
        self::log('api', "API call to {$service}", [
            'service' => $service,
            'request' => $request,
            'response' => $response,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    private static function log(string $level, string $message, array $context = []): void
    {
        $logFile = self::$logPath . $level . '_' . date('Y-m-d') . '.log';
        
        // Create logs directory if it doesn't exist
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0777, true);
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
            'context' => $context
        ];
        
        file_put_contents($logFile, json_encode($logEntry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}