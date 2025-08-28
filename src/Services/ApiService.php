<?php

namespace App\Services;

use App\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    private Client $client;
    private Config $config;
    
    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->config = Config::getInstance();
    }
    
    public function submitToPingPost(array $leadData): array
    {
        try {
            $payload = $this->formatForPingPost($leadData);
            
            $response = $this->client->post('https://api.pingpost.com/submit', [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['PINGPOST_API_KEY'],
                    'Content-Type' => 'application/json'
                ]
            ]);
            
            $result = json_decode($response->getBody()->getContents(), true);
            
            return [
                'success' => $result['success'] ?? false,
                'external_id' => $result['lead_id'] ?? null,
                'response' => $result
            ];
            
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function formatForPingPost(array $leadData): array
    {
        return [
            'first_name' => $leadData['first_name'],
            'last_name' => $leadData['last_name'],
            'email' => $leadData['email'],
            'phone' => preg_replace('/[^0-9]/', '', $leadData['phone']),
            'dob' => $leadData['dob'],
            'address' => $leadData['address'],
            'city' => $leadData['city'],
            'state' => $leadData['state'],
            'zip' => $leadData['zip'],
            'ip_address' => $leadData['ip_address'],
            'source' => $leadData['src'],
            'sub_id' => $_SESSION['sub_id'] ?? '',
            'trusted_form_token' => $_SESSION['trusted_form_token'] ?? '',
        ];
    }
    
    public function submitToFacebook(array $leadData): array
    {
        // Implement Facebook Conversions API
        // This is a placeholder - implement based on your needs
        return ['success' => true];
    }
    
    public function submitToDefault(array $leadData): array
    {
        // Default submission logic
        return ['success' => true];
    }
}