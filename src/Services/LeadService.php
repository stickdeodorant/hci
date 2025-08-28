<?php

namespace App\Services;

use App\Models\Lead;
use App\Services\ApiService;

class LeadService
{
    private Lead $leadModel;
    private ApiService $apiService;
    
    public function __construct()
    {
        $this->leadModel = new Lead();
        $this->apiService = new ApiService();
    }
    
    public function process(array $leadData): array
    {
        try {
            // Save lead to database
            $leadId = $this->leadModel->create($leadData);
            
            // Update lead with ID
            $leadData['lead_id'] = $leadId;
            
            // Submit to external APIs based on configuration
            $apiResponse = $this->submitToApis($leadData);
            
            // Update lead status
            $this->leadModel->update($leadId, [
                'status' => $apiResponse['success'] ? 'accepted' : 'rejected',
                'lead_id' => $apiResponse['external_id'] ?? null,
                'response_data' => json_encode($apiResponse)
            ]);
            
            return [
                'success' => true,
                'lead_id' => $leadId,
                'external_id' => $apiResponse['external_id'] ?? null,
                'message' => 'Lead processed successfully'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function submitToApis(array $leadData): array
    {
        // Determine which API to use based on SRC
        $src = $leadData['src'];
        
        // Map SRC to API endpoint
        $apiMap = [
            'Infinix-K-Ping' => 'pingpost',
            'InfinixMedia-Ksp' => 'pingpost',
            'Infinix-KFB' => 'facebook',
            // Add more mappings as needed
        ];
        
        $apiType = $apiMap[$src] ?? 'default';
        
        // Submit to appropriate API
        switch ($apiType) {
            case 'pingpost':
                return $this->apiService->submitToPingPost($leadData);
            case 'facebook':
                return $this->apiService->submitToFacebook($leadData);
            default:
                return $this->apiService->submitToDefault($leadData);
        }
    }
}