<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Config\Config;
use App\Services\LeadService;
use App\Services\ValidationService;

class FormController
{
    private Config $config;
    private LeadService $leadService;
    private ValidationService $validator;
    
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->leadService = new LeadService();
        $this->validator = new ValidationService();
    }
    
    public function index(Request $request, Response $response): void
    {
        // Store campaign data in session
        $this->storeCampaignData($request);
        
        $data = [
            'title' => 'Get Free Health Insurance Quotes',
            'sitename' => $this->config->get('app.name'),
            'step' => $request->get('step', 1),
            'zip' => $request->get('zip', ''),
            'states' => $this->config->get('states'),
        ];
        
        echo View::render('multi-step-form', $data);
    }
    
    private function storeCampaignData(Request $request): void
    {
        $campaignFields = ['gclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content'];
        
        foreach ($campaignFields as $field) {
            if ($value = $request->get($field)) {
                $_SESSION[$field] = $value;
            }
        }
        
        // Store IP address
        $_SESSION['ip_address'] = $request->ip();
    }
    
    public function checkPhone(Request $request, Response $response): void
    {
        $phone = $request->post('phone');
        
        // Validate phone
        $errors = $this->validator->validatePhone($phone);
        
        if (!empty($errors)) {
            $response->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Check if phone is valid (not toll-free, not repeated digits)
        $isValid = $this->validator->isValidPhone($phone);
        
        $response->json(['success' => $isValid]);
    }
    
    public function submitLead(Request $request, Response $response): void
    {
        // Collect all form data
        $leadData = $this->collectLeadData($request);
        
        // Validate all data
        $errors = $this->validator->validateLead($leadData);
        
        if (!empty($errors)) {
            $response->json(['success' => false, 'errors' => $errors], 400);
            return;
        }
        
        // Process lead
        $result = $this->leadService->process($leadData);
        
        if ($result['success']) {
            // Store data for thank you page
            $_SESSION['lead_data'] = $leadData;
            $_SESSION['lead_result'] = $result;
            
            $response->json([
                'success' => true,
                'redirect' => $this->getRedirectUrl($leadData)
            ]);
        } else {
            $response->json([
                'success' => false,
                'message' => $result['message'] ?? 'An error occurred'
            ], 500);
        }
    }
    
    private function collectLeadData(Request $request): array
    {
        return [
            // Personal Info
            'first_name' => $request->post('First_Name'),
            'last_name' => $request->post('Last_Name'),
            'email' => $request->post('Email'),
            'phone' => $request->post('Primary_Phone'),
            'dob' => $request->post('DOB'),
            'age' => $this->calculateAge($request->post('DOB')),
            
            // Address
            'address' => $request->post('Address', '-'),
            'city' => $request->post('City'),
            'state' => $request->post('State'),
            'zip' => $request->post('Zip'),
            
            // Insurance Details
            'household_size' => $request->post('Household'),
            'household_income' => $request->post('Household_Income'),
            
            // Tracking
            'ip_address' => $_SESSION['ip_address'] ?? $request->ip(),
            'gclid' => $_SESSION['gclid'] ?? '',
            'landing_page' => $_SESSION['landing_page'] ?? $_SERVER['HTTP_REFERER'] ?? '',
            'src' => $this->determineSRC($request),
            'type' => $this->determineType($request),
        ];
    }
    
    private function calculateAge(string $dob): int
    {
        $birthDate = new \DateTime($dob);
        $today = new \DateTime();
        return $today->diff($birthDate)->y;
    }
    
    private function determineSRC(Request $request): string
    {
        // Implement your SRC logic based on campaign, state, income etc.
        if ($_SESSION['utm_medium'] ?? '' == 'Search_partners') {
            return 'InfinixMedia-Ksp';
        }
        
        return $_SESSION['campaign'] ?? 'Infinix-K-Ping';
    }
    
    private function determineType(Request $request): string
    {
        $age = $this->calculateAge($request->post('DOB'));
        
        if ($age >= 65) {
            return '23'; // Medicare
        }
        
        return '24'; // Regular health insurance
    }
    
    private function getRedirectUrl(array $leadData): string
    {
        $age = $leadData['age'];
        $state = $leadData['state'];
        $income = $leadData['household_income'];
        
        // Determine redirect based on age and income
        if ($age >= 65) {
            $did = 'medicare';
            $page = '/thank-you/65typ.php';
        } else {
            $healthTwoStates = ['AL', 'FL', 'GA', 'KS', 'MS', 'MO', 'NC', 'OH', 'OK', 'SC', 'TN', 'TX'];
            
            if (in_array($state, $healthTwoStates)) {
                if ($income > 39999) {
                    $did = 'premium';
                    $page = '/thank-you/thank-you-h1-b.php';
                } else {
                    $did = 'h2';
                    $page = '/thank-you/thank-you-h2-b.php';
                }
            } else {
                if ($income > 54999) {
                    $did = 'premium';
                    $page = '/thank-you/thank-you-h1-b.php';
                } else {
                    $did = 'standard';
                    $page = '/thank-you/thank-you-h1-b.php';
                }
            }
        }
        
        $params = http_build_query([
            'type' => $age >= 65 ? 'medicare' : 'healthcare',
            'city' => $leadData['city'],
            'state' => $leadData['state'],
            'did' => $did,
            'first_name' => $leadData['first_name'],
            'age' => $leadData['age']
        ]);
        
        return $page . '?' . $params;
    }
}