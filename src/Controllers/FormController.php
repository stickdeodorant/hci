<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Config\Config;
use App\Services\LeadService;
use App\Services\ValidationService;
use App\Services\ZipService;
use App\Services\ApiService;
use App\Services\TrustedFormService;
use App\Services\ConvosoService; 

class FormController
{
    private Config $config;
    private LeadService $leadService;
    private ValidationService $validator;
    private ZipService $zipService;
    private ApiService $apiService;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->leadService = new LeadService();
        $this->validator = new ValidationService();
        $this->zipService = new ZipService();
        $this->apiService = new ApiService();
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
        $campaignFields = ['gclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'fb', 'campaign'];

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

        $response->json(['success' => true, 'message' => 'Phone is valid']);
    }

    public function lookupZip(Request $request, Response $response): void
    {
        $zip = $request->post('zip');

        if (!preg_match('/^\d{5}$/', $zip)) {
            $response->json(['success' => false, 'error' => 'Invalid zip code'], 400);
            return;
        }

        $location = $this->zipService->lookup($zip);

        if (!$location) {
            $response->json(['success' => false, 'error' => 'Zip code not found'], 404);
            return;
        }

        // Check for restricted states
        if (in_array($location['state'], ['MA', 'NY'])) {
            $response->json([
                'success' => false,
                'restricted' => true,
                'message' => "We're sorry, but we don't currently offer coverage in {$location['state_name']}."
            ], 200);
            return;
        }

        $_SESSION['city'] = $location['city'];
        $_SESSION['state'] = $location['state'];

        $response->json(['success' => true, 'location' => $location]);
    }

    public function submitLead(Request $request, Response $response): void
    {
        // Get all form data
        $data = $request->all();

        // Validate all fields
        $errors = [];

        // Validate DOB
        $dobErrors = $this->validator->validateDOB(
            intval($data['birthmonth']),
            intval($data['birthday']),
            intval($data['birthyear'])
        );
        if (!empty($dobErrors)) {
            $errors['dob'] = $dobErrors;
        }

        // Validate email
        $emailErrors = $this->validator->validateEmail($data['email']);
        if (!empty($emailErrors)) {
            $errors['email'] = $emailErrors;
        }

        // Validate phone
        $phoneErrors = $this->validator->validatePhone($data['phone']);
        if (!empty($phoneErrors)) {
            $errors['phone'] = $phoneErrors;
        }

        if (!empty($errors)) {
            $response->json(['success' => false, 'errors' => $errors], 400);
            return;
        }

        // Handle TrustedForm certificate
        $trustedFormService = new TrustedFormService();
        if (!empty($data['xxTrustedFormToken'])) {
            $_SESSION['trusted_form_cert'] = $data['xxTrustedFormToken'];
            $certResult = $trustedFormService->claimCertificate(
                $data['xxTrustedFormToken'],
                $data
            );

            if ($certResult['success']) {
                $data['trusted_form_cert_id'] = $certResult['cert_id'];
            }
        }

        // Format DOB
        $data['dob'] = sprintf(
            '%s-%s-%s',
            $data['birthyear'],
            str_pad($data['birthmonth'], 2, '0', STR_PAD_LEFT),
            str_pad($data['birthday'], 2, '0', STR_PAD_LEFT)
        );

        // Add additional data
        $data['src'] = $this->leadService->determineSRC($data);
        $data['type'] = $this->leadService->determineType($data);
        $data['ip_address'] = $_SESSION['ip_address'] ?? $request->ip();
        $data['age'] = $this->calculateAge($data['dob']);

        // Save lead to database
        $leadId = $this->leadService->saveLead($data);
        $data['lead_id'] = $leadId;

        // Submit to main API
        $apiResponse = $this->apiService->submitLead($data);

        // Submit to Convoso for phone distribution
        $convosoService = new ConvosoService();
        $convosoResponse = $convosoService->submitLead($data);

        // Update lead status
        $status = 'failed';
        $responses = [
            'api' => $apiResponse,
            'convoso' => $convosoResponse
        ];

        if ($apiResponse['success'] || $convosoResponse['success']) {
            $status = 'sent';
        }

        $this->leadService->updateStatus($leadId, $status, json_encode($responses));

        // Store data in session for thank you page
        $_SESSION['lead_data'] = [
            'first_name' => $data['first_name'],
            'age' => $data['age'],
            'city' => $data['city'] ?? '',
            'state' => $data['state'] ?? '',
            'phone' => $data['phone']
        ];

        // Determine routing
        $route = $this->leadService->routeLead($data);

        // Generate thank you page URL
        $thankYouUrl = sprintf(
            '/thank-you?type=%s&city=%s&state=%s&did=%s&first_name=%s&age=%d',
            $route === 'medicare' ? 'medicare' : 'healthcare',
            urlencode($data['city'] ?? ''),
            $data['state'] ?? '',
            $route,
            urlencode($data['first_name']),
            $data['age']
        );

        $response->json([
            'success' => true,
            'redirect' => $thankYouUrl,
            'lead_id' => $leadId,
            'route' => $route
        ]);
    }

    private function calculateAge(string $dob): int
    {
        $birthDate = new \DateTime($dob);
        $now = new \DateTime();
        return $now->diff($birthDate)->y;
    }
}
