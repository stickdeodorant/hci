<?php

namespace App\Services;

use App\Config\Config;

class ApiService
{
    private Config $config;
    private string $apiUrl;
    private int $timeout = 30;
    
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->apiUrl = $this->config->get('api.endpoint', 'https://healthcare-insurance.com/get-quotes/form-processing.php');
    }
    
    public function submitLead(array $leadData): array
    {
        // Prepare the data in the format your API expects
        $apiData = $this->prepareApiData($leadData);
        
        // Submit to API
        $response = $this->makeRequest($apiData);
        
        // Parse and return response
        return $this->parseResponse($response);
    }
    
    private function prepareApiData(array $leadData): array
    {
        // Format data according to your API requirements
        $apiData = [
            'TYPE' => $leadData['type'],
            'SRC' => base64_decode($leadData['src']), // Decode SRC for API
            'IP_Address' => $leadData['ip_address'],
            'First_Name' => $leadData['first_name'],
            'Last_Name' => $leadData['last_name'],
            'Email' => $leadData['email'],
            'Primary_Phone' => $leadData['phone'],
            'DOB' => $leadData['dob'],
            'Household' => $leadData['household_size'],
            'Household_Income' => $leadData['household_income'],
            'Address' => '-', // Default if not provided
            'City' => $leadData['city'] ?? '',
            'State' => $leadData['state'] ?? '',
            'Zip' => $leadData['zip'],
            'Landing_Page' => $_SESSION['landing_page'] ?? $_SERVER['REQUEST_URI'],
            'LeadiD_URL' => $_SESSION['trusted_form_cert'] ?? '',
            'Age' => $this->calculateAge($leadData['dob']),
        ];
        
        // Add tracking parameters
        if (isset($_SESSION['gclid'])) {
            $apiData['gclid'] = $_SESSION['gclid'];
        }
        
        if (isset($_SESSION['utm_source'])) {
            $apiData['utm_source'] = $_SESSION['utm_source'];
        }
        
        if (isset($_SESSION['utm_medium'])) {
            $apiData['utm_medium'] = $_SESSION['utm_medium'];
        }
        
        if (isset($_SESSION['utm_campaign'])) {
            $apiData['adset_id'] = $_SESSION['utm_campaign'];
        }
        
        if (isset($_SESSION['Sub_ID'])) {
            $apiData['Sub_ID'] = $_SESSION['Sub_ID'];
        } else {
            $apiData['Sub_ID'] = $this->getPeakStatus();
        }
        
        return $apiData;
    }
    
    private function makeRequest(array $data): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'User-Agent: HealthInsuranceLeads/2.0'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200 && !$error,
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error
        ];
    }
    
    private function parseResponse(array $response): array
    {
        if (!$response['success']) {
            return [
                'success' => false,
                'error' => $response['error'] ?: 'API request failed',
                'http_code' => $response['http_code']
            ];
        }
        
        // Parse XML response (based on your current API)
        if (strpos($response['response'], '<?xml') !== false) {
            $xml = simplexml_load_string($response['response']);
            
            if ($xml !== false) {
                $status = (string)$xml->status;
                $leadId = (string)$xml->lead_id;
                $error = (string)$xml->error;
                
                return [
                    'success' => strtolower($status) === 'success',
                    'lead_id' => $leadId,
                    'error' => $error,
                    'raw_response' => $response['response']
                ];
            }
        }
        
        // Try JSON response
        $json = json_decode($response['response'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'success' => $json['success'] ?? false,
                'lead_id' => $json['lead_id'] ?? null,
                'error' => $json['error'] ?? null,
                'raw_response' => $response['response']
            ];
        }
        
        // Default response
        return [
            'success' => true,
            'raw_response' => $response['response']
        ];
    }
    
    private function calculateAge(string $dob): int
    {
        $birthDate = new \DateTime($dob);
        $now = new \DateTime();
        return $now->diff($birthDate)->y;
    }
    
    private function getPeakStatus(): string
    {
        $hour = (int)date('G');
        $dayOfWeek = (int)date('N');
        
        // Peak hours: Monday-Friday 9 AM - 7 PM
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5 && $hour >= 9 && $hour < 19) {
            return 'peak';
        }
        
        return 'off-peak';
    }
}