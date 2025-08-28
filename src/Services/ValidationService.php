<?php

namespace App\Services;

class ValidationService
{
    private array $tollFreeAreaCodes = ['800', '822', '833', '844', '855', '866', '877', '880', '887', '888', '889'];
    
    public function validatePhone(string $phone): array
    {
        $errors = [];
        
        // Remove non-numeric characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($cleanPhone) !== 10) {
            $errors[] = 'Phone number must be 10 digits';
        }
        
        return $errors;
    }
    
    public function isValidPhone(string $phone): bool
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if toll-free
        $areaCode = substr($cleanPhone, 0, 3);
        if (in_array($areaCode, $this->tollFreeAreaCodes)) {
            return false;
        }
        
        // Check for repeated digits (e.g., 1111111111)
        if (preg_match('/(\d)\1{9}/', $cleanPhone)) {
            return false;
        }
        
        // Check for repeated prefix (e.g., xxx-111-xxxx)
        $prefix = substr($cleanPhone, 3, 3);
        if (preg_match('/(\d)\1{2}/', $prefix)) {
            return false;
        }
        
        return true;
    }
    
    public function validateLead(array $data): array
    {
        $errors = [];
        
        // Required fields
        $required = ['first_name', 'last_name', 'email', 'phone', 'dob', 'zip'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }
        
        // Check for banned emails
        if ($this->isBannedEmail($data['email'] ?? '')) {
            $errors['email'] = 'This email domain is not allowed';
        }
        
        // Phone validation
        if (!empty($data['phone'])) {
            $phoneErrors = $this->validatePhone($data['phone']);
            if (!empty($phoneErrors)) {
                $errors['phone'] = $phoneErrors[0];
            } elseif (!$this->isValidPhone($data['phone'])) {
                $errors['phone'] = 'Invalid phone number';
            }
        }
        
        // Age validation
        if (!empty($data['age']) && ($data['age'] < 18 || $data['age'] > 100)) {
            $errors['age'] = 'Invalid age';
        }
        
        // State validation
        if (!empty($data['state']) && in_array($data['state'], ['NY', 'MA'])) {
            $errors['state'] = 'Service not available in your state';
        }
        
        return $errors;
    }
    
    private function isBannedEmail(string $email): bool
    {
        $bannedDomains = ['mailinator.com', 'guerrillamail.com', '10minutemail.com'];
        $domain = substr(strrchr($email, '@'), 1);
        
        return in_array($domain, $bannedDomains);
    }
}