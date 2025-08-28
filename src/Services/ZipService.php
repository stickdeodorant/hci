<?php

namespace App\Services;

class ZipService
{
    public function lookup(string $zip): ?array
    {
        // Using Ziptastic API
        $url = "https://zip.getziptastic.com/v2/US/{$zip}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            return null;
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            return null;
        }
        
        return [
            'city' => $this->formatCity($data['city'] ?? ''),
            'state' => strtoupper($data['state_short'] ?? ''),
            'state_name' => $data['state'] ?? '',
            'country' => $data['country'] ?? 'US'
        ];
    }
    
    private function formatCity(string $city): string
    {
        return ucwords(strtolower($city));
    }
}