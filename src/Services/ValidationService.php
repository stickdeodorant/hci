<?php

namespace App\Services;

class ValidationService
{
    private array $errors = [];
    
    public function validatePhone(string $phone): array
    {
        $this->errors = [];
        
        // Remove all non-numeric characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check length
        if (strlen($cleanPhone) !== 10) {
            $this->errors[] = 'Phone number must be 10 digits';
        }
        
        // Check for toll-free numbers
        $tollFreePrefix = ['800', '822', '833', '844', '855', '866', '877', '880', '887', '888', '889'];
        if (in_array(substr($cleanPhone, 0, 3), $tollFreePrefix)) {
            $this->errors[] = 'Toll-free numbers are not accepted';
        }
        
        // Check for repeated digits
        if (preg_match('/(\d)\1{9}/', $cleanPhone)) {
            $this->errors[] = 'Invalid phone number format';
        }
        
        // Check for repeated prefix
        if (preg_match('/(\d)\1{2}/', substr($cleanPhone, 3, 3))) {
            $this->errors[] = 'Invalid phone number format';
        }
        
        return $this->errors;
    }
    
    public function isValidPhone(string $phone): bool
    {
        return empty($this->validatePhone($phone));
    }
    
    public function validateEmail(string $email): array
    {
        $this->errors = [];
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Invalid email format';
        }
        
        // Check for banned domains
        $bannedDomains = ['mailinator.com', 'guerrillamail.com', 'temp-mail.org'];
        $domain = substr(strrchr($email, "@"), 1);
        
        if (in_array($domain, $bannedDomains)) {
            $this->errors[] = 'Please use a valid email address';
        }
        
        return $this->errors;
    }
    
    public function validateDOB(int $month, int $day, int $year): array
    {
        $this->errors = [];
        
        if (!checkdate($month, $day, $year)) {
            $this->errors[] = 'Invalid date of birth';
        }
        
        // Calculate age
        $dob = new \DateTime("$year-$month-$day");
        $now = new \DateTime();
        $age = $now->diff($dob)->y;
        
        if ($age < 18) {
            $this->errors[] = 'You must be at least 18 years old';
        }
        
        if ($age > 120) {
            $this->errors[] = 'Invalid date of birth';
        }
        
        return $this->errors;
    }
}