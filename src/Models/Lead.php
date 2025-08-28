<?php

namespace App\Models;

use App\Core\Database;

class Lead
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function create(array $data): int
    {
        $sql = "INSERT INTO leads (
            first_name, last_name, email, phone, dob, 
            household_size, household_income, zip, city, state,
            ip_address, source, type, gclid, utm_source, 
            utm_medium, utm_campaign, created_at
        ) VALUES (
            :first_name, :last_name, :email, :phone, :dob,
            :household_size, :household_income, :zip, :city, :state,
            :ip_address, :source, :type, :gclid, :utm_source,
            :utm_medium, :utm_campaign, NOW()
        )";
        
        return $this->db->insert($sql, $data);
    }
    
    public function findByPhone(string $phone): ?array
    {
        $sql = "SELECT * FROM leads WHERE phone = :phone ORDER BY created_at DESC LIMIT 1";
        return $this->db->fetch($sql, ['phone' => $phone]);
    }
    
    public function updateStatus(int $id, string $status, ?string $response = null): bool
    {
        $sql = "UPDATE leads SET status = :status, api_response = :response WHERE id = :id";
        return $this->db->execute($sql, [
            'id' => $id,
            'status' => $status,
            'response' => $response
        ]);
    }
}