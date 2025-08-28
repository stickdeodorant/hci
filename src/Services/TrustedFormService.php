<?php

namespace App\Services;

use App\Config\Config;

class TrustedFormService
{
    private Config $config;
    private string $apiKey;
    
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->apiKey = $this->config->get('trusted_form.api_key', '');
    }
    
    public function claimCertificate(string $certUrl, array $leadData): array
    {
        if (empty($certUrl) || empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'TrustedForm not configured'
            ];
        }
        
        // Prepare claim data
        $claimData = [
            'reference' => $leadData['gclid'] ?? $leadData['phone'],
            'vendor' => $this->config->get('app.name'),
            'ip' => $leadData['ip_address'],
            'phone_1' => $leadData['phone'],
            'email' => $leadData['email']
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $certUrl . '/claim',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($claimData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($this->apiKey . ':'),
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 201) {
            return [
                'success' => false,
                'error' => 'Failed to claim certificate',
                'http_code' => $httpCode,
                'response' => $response
            ];
        }
        
        $data = json_decode($response, true);
        
        return [
            'success' => true,
            'cert_id' => $data['cert']['id'] ?? null,
            'claim_id' => $data['claim']['id'] ?? null,
            'expires_at' => $data['cert']['expires_at'] ?? null
        ];
    }
    
    public function verifyCertificate(string $certUrl): bool
    {
        if (empty($certUrl) || empty($this->apiKey)) {
            return false;
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $certUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($this->apiKey . ':'),
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 5
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
}