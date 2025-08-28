<?php

namespace App\Services;

use App\Config\Config;

class ConvosoService
{
    private Config $config;
    private string $authToken;
    private string $listId;
    private string $apiUrl = 'https://api.convoso.com/v1/leads/insert';
    
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->authToken = $this->config->get('convoso.auth_token', '');
        $this->listId = $this->config->get('convoso.list_id', '11817');
    }
    
    public function submitLead(array $leadData): array
    {
        if (empty($this->authToken)) {
            return [
                'success' => false,
                'error' => 'Convoso not configured'
            ];
        }
        
        // Prepare Convoso data
        $convosoData = [
            'first_name' => $leadData['first_name'],
            'last_name' => $leadData['last_name'],
            'phone_number' => $leadData['phone'],
            'email' => $leadData['email'],
            'address1' => $leadData['address'] ?? '',
            'city' => $leadData['city'] ?? '',
            'state' => $leadData['state'] ?? '',
            'postal_code' => $leadData['zip'],
            'date_of_birth' => $leadData['dob'],
            'comments' => $this->buildComments($leadData),
            'source_id' => $leadData['src'] ?? '',
            'vendor_lead_code' => $leadData['lead_id'] ?? uniqid('lead_')
        ];
        
        $url = $this->apiUrl . '?' . http_build_query([
            'auth_token' => $this->authToken,
            'list_id' => $this->listId
        ]);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $convosoData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        return [
            'success' => $httpCode === 200 && ($result['success'] ?? false),
            'lead_id' => $result['lead_id'] ?? null,
            'error' => $result['error'] ?? null,
            'http_code' => $httpCode,
            'raw_response' => $response
        ];
    }
    
    private function buildComments(array $leadData): string
    {
        $comments = [];
        
        if (isset($leadData['household_income'])) {
            $comments[] = "Income: $" . number_format($leadData['household_income']);
        }
        
        if (isset($leadData['household_size'])) {
            $comments[] = "Household Size: " . $leadData['household_size'];
        }
        
        if (isset($leadData['age'])) {
            $comments[] = "Age: " . $leadData['age'];
        }
        
        if (isset($leadData['gclid'])) {
            $comments[] = "GCLID: " . $leadData['gclid'];
        }
        
        return implode(' | ', $comments);
    }
}