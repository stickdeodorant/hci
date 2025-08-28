<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Config\Config;

class ThankYouController
{
    private Config $config;
    private array $phoneNumbers;
    
    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->phoneNumbers = $this->config->get('phone_numbers');
    }
    
    public function index(Request $request, Response $response): void
    {
        // Get parameters
        $type = $request->get('type', 'healthcare');
        $did = $request->get('did', 'standard');
        
        // Get lead data from session or URL params
        $leadData = $_SESSION['lead_data'] ?? [
            'first_name' => $request->get('first_name', ''),
            'age' => $request->get('age', ''),
            'city' => $request->get('city', ''),
            'state' => $request->get('state', ''),
        ];
        
        // Determine which thank you page variant to show
        $variant = $this->determineVariant($type, $did, $leadData['state'] ?? '');
        
        // Get appropriate phone number
        $phoneNumber = $this->getPhoneNumber($did, $type);
        
        // Check if call hours are active
        $callActive = $this->isCallCenterOpen();
        
        $data = [
            'title' => 'Your Application Has Been Received',
            'sitename' => $this->config->get('app.name'),
            'first_name' => $leadData['first_name'],
            'age' => $leadData['age'],
            'city' => $leadData['city'],
            'state' => $leadData['state'],
            'state_name' => $this->config->get('states.' . $leadData['state'], $leadData['state']),
            'type' => $type,
            'did' => $did,
            'phone' => $phoneNumber['formatted'],
            'phone_clean' => $phoneNumber['clean'],
            'variant' => $variant,
            'call_active' => $callActive,
            'show_animation' => !isset($_SESSION['thank_you_shown']),
        ];
        
        // Mark thank you page as shown
        $_SESSION['thank_you_shown'] = true;
        
        // Render appropriate template
        echo View::render("thank-you/{$variant}", $data, 'thank-you');
    }
    
    private function determineVariant(string $type, string $did, string $state): string
    {
        // Medicare always gets specific variant
        if ($type === 'medicare') {
            return 'medicare';
        }
        
        // State-specific variants
        $stateVariants = [
            'FL' => 'florida-special',
            'TX' => 'texas-special',
        ];
        
        if (isset($stateVariants[$state])) {
            return $stateVariants[$state];
        }
        
        // DID-based variants
        $didVariants = [
            'premium' => 'premium',
            'h2' => 'budget',
            'standard' => 'standard',
        ];
        
        return $didVariants[$did] ?? 'standard';
    }
    
    private function getPhoneNumber(string $did, string $type): array
    {
        $key = $type === 'medicare' ? 'medicare' : $did;
        $phone = $this->phoneNumbers[$key] ?? $this->phoneNumbers['standard'];
        
        return [
            'formatted' => $phone,
            'clean' => preg_replace('/[^0-9]/', '', $phone)
        ];
    }
    
    private function isCallCenterOpen(): bool
    {
        $currentTime = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $hour = (int)$currentTime->format('G');
        $dayOfWeek = (int)$currentTime->format('N');
        $date = $currentTime->format('m/d');
        
        // Check holidays
        $holidays = [
            '01/01', // New Year's Day
            '07/04', // Independence Day
            '11/24', // Thanksgiving (update yearly)
            '12/24', // Christmas Eve
            '12/25', // Christmas Day
        ];
        
        if (in_array($date, $holidays)) {
            return false;
        }
        
        // Check business hours (Mon-Fri 9 AM - 6 PM EST)
        return $dayOfWeek >= 1 && $dayOfWeek <= 5 && $hour >= 9 && $hour < 18;
    }
}