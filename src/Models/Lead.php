<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Lead
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    
    public function create(array $data): int
    {
        $sql = "INSERT INTO leads (
            first_name, last_name, email, phone, dob, age,
            address, city, state, zip,
            household_size, household_income,
            ip_address, landing_page, src, type, gclid,
            utm_source, utm_medium, utm_campaign
        ) VALUES (
            :first_name, :last_name, :email, :phone, :dob, :age,
            :address, :city, :state, :zip,
            :household_size, :household_income,
            :ip_address, :landing_page, :src, :type, :gclid,
            :utm_source, :utm_medium, :utm_campaign
        )";
        
        $stmt = $this->db->prepare($sql);
        
        $params = [
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':dob' => $data['dob'],
            ':age' => $data['age'],
            ':address' => $data['address'] ?? '-',
            ':city' => $data['city'] ?? '',
            ':state' => $data['state'] ?? '',
            ':zip' => $data['zip'] ?? '',
            ':household_size' => $data['household_size'] ?? 1,
            ':household_income' => $data['household_income'] ?? 0,
            ':ip_address' => $data['ip_address'] ?? '',
            ':landing_page' => $data['landing_page'] ?? '',
            ':src' => $data['src'] ?? '',
            ':type' => $data['type'] ?? '',
            ':gclid' => $data['gclid'] ?? '',
            ':utm_source' => $_SESSION['utm_source'] ?? '',
            ':utm_medium' => $_SESSION['utm_medium'] ?? '',
            ':utm_campaign' => $_SESSION['utm_campaign'] ?? '',
        ];
        
        $stmt->execute($params);
        
        return (int) $this->db->lastInsertId();
    }
    
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }
        
        $sql = "UPDATE leads SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM leads WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $result = $stmt->fetch();
        
        return $result ?: null;
    }
    
    public function saveFormSession(string $sessionId, array $formData, int $step): void
    {
        $sql = "INSERT INTO form_sessions (session_id, form_data, current_step, ip_address, user_agent)
                VALUES (:session_id, :form_data, :current_step, :ip_address, :user_agent)
                ON DUPLICATE KEY UPDATE
                form_data = :form_data, current_step = :current_step, updated_at = NOW()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':session_id' => $sessionId,
            ':form_data' => json_encode($formData),
            ':current_step' => $step,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    public function logApiCall(int $leadId, string $apiName, array $request, array $response, int $statusCode): void
    {
        $sql = "INSERT INTO api_logs (lead_id, api_name, request_data, response_data, status_code)
                VALUES (:lead_id, :api_name, :request_data, :response_data, :status_code)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':lead_id' => $leadId,
            ':api_name' => $apiName,
            ':request_data' => json_encode($request),
            ':response_data' => json_encode($response),
            ':status_code' => $statusCode
        ]);
    }
}