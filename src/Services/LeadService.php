<?php

namespace App\Services;

use App\Config\Config;
use App\Models\Lead;

class LeadService
{
    private Config $config;
    private Lead $leadModel;
    
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->leadModel = new Lead();
    }
    
    public function determineSRC(array $data): string
    {
        // Determine SRC based on campaign, search partners, etc.
        if (isset($_SESSION['fb']) && $_SESSION['fb'] === 'true') {
            return base64_encode("Infinix-KFB");
        }
        
        if (isset($_SESSION['campaign'])) {
            return base64_encode($_SESSION['campaign']);
        }
        
        if (isset($_SESSION['utm_medium']) && $_SESSION['utm_medium'] === 'Search_partners') {
            return base64_encode('InfinixMedia-Ksp');
        }
        
        return base64_encode("Infinix-K-Ping");
    }
    
    public function determineType(array $data): int
    {
        if (isset($_SESSION['fb']) && $_SESSION['fb'] === 'true') {
            return 19;
        }
        
        if (isset($_SESSION['campaign'])) {
            return 19;
        }
        
        if (isset($_SESSION['utm_medium']) && $_SESSION['utm_medium'] === 'Search_partners') {
            return 29;
        }
        
        return 24;
    }
    
    public function routeLead(array $leadData): string
    {
        $age = $this->calculateAge($leadData['dob']);
        $income = intval($leadData['household_income']);
        $state = $leadData['state'];
        
        // Medicare routing for 65+
        if ($age >= 65) {
            return 'medicare';
        }
        
        // State-based routing
        $healthTwoStates = ['AL', 'FL', 'GA', 'KS', 'MS', 'MO', 'NC', 'OH', 'OK', 'SC', 'TN', 'TX'];
        
        if (in_array($state, $healthTwoStates)) {
            return $income > 39999 ? 'premium' : 'h2';
        } else {
            return $income > 54999 ? 'premium' : 'standard';
        }
    }
    
    private function calculateAge(string $dob): int
    {
        $birthDate = new \DateTime($dob);
        $now = new \DateTime();
        return $now->diff($birthDate)->y;
    }
    
    public function saveLead(array $data): int
    {
        // Prepare lead data
        $leadData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => preg_replace('/[^0-9]/', '', $data['phone']),
            'dob' => $data['dob'],
            'household_size' => $data['household'],
            'household_income' => $data['household_income'],
            'zip' => $data['zip'],
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'ip_address' => $data['ip_address'],
            'source' => $data['src'],
            'type' => $data['type'],
            'gclid' => $_SESSION['gclid'] ?? null,
            'utm_source' => $_SESSION['utm_source'] ?? null,
            'utm_medium' => $_SESSION['utm_medium'] ?? null,
            'utm_campaign' => $_SESSION['utm_campaign'] ?? null,
        ];
        
        return $this->leadModel->create($leadData);
    }

    public function updateStatus(int $leadId, string $status, ?string $response = null): bool
    {
        return $this->leadModel->updateStatus($leadId, $status, $response);
    }
}