<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config
{
    private static ?Config $instance = null;
    private array $config = [];
    
    private function __construct()
    {
        $this->loadEnvironment();
        $this->loadDefaults();
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function loadEnvironment(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();
        
        // Load environment variables into config
        $this->config = array_merge($this->config, $_ENV);
    }
    
    private function loadDefaults(): void
    {
        // Default configuration values
        $defaults = [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'Healthcare Insurance',
                'url' => $_ENV['APP_URL'] ?? 'http://localhost',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/New_York',
            ],
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'name' => $_ENV['DB_NAME'] ?? '',
                'username' => $_ENV['DB_USER'] ?? 'root',
                'password' => $_ENV['DB_PASS'] ?? '',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
            ],
            'api' => [
                'endpoint' => $_ENV['API_ENDPOINT'] ?? '',
                'timeout' => (int)($_ENV['API_TIMEOUT'] ?? 30),
            ],
            'trusted_form' => [
                'api_key' => $_ENV['TRUSTED_FORM_API_KEY'] ?? '',
            ],
            'convoso' => [
                'auth_token' => $_ENV['CONVOSO_AUTH_TOKEN'] ?? '',
                'list_id' => $_ENV['CONVOSO_LIST_ID'] ?? '',
            ],
            'tracking' => [
                'gtm_id' => $_ENV['GTM_ID'] ?? '',
                'facebook_pixel_id' => $_ENV['FACEBOOK_PIXEL_ID'] ?? '',
                'google_ads_id' => $_ENV['GOOGLE_ADS_ID'] ?? '',
                'healthcare_conversion_label' => $_ENV['HEALTHCARE_CONVERSION_LABEL'] ?? '',
                'bing_uet_id' => $_ENV['BING_UET_ID'] ?? '',
                'enable_callrail' => filter_var($_ENV['ENABLE_CALLRAIL'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'callrail_company_id' => $_ENV['CALLRAIL_COMPANY_ID'] ?? '',
                'callrail_tracker_id' => $_ENV['CALLRAIL_TRACKER_ID'] ?? '',
            ],
            'phone_numbers' => [
                'standard' => $_ENV['PHONE_STANDARD'] ?? '(866) 307-0165',
                'premium' => $_ENV['PHONE_PREMIUM'] ?? '(866) 303-4071',
                'h2' => $_ENV['PHONE_H2'] ?? '(866) 231-7963',
                'medicare' => $_ENV['PHONE_MEDICARE'] ?? '(888) 670-1899',
            ],
            'states' => $this->getStates(),
        ];
        
        $this->config = array_merge($this->config, $defaults);
    }
    
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
    
    public function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;
        
        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $config[$k] = $value;
            } else {
                if (!isset($config[$k]) || !is_array($config[$k])) {
                    $config[$k] = [];
                }
                $config = &$config[$k];
            }
        }
    }
    
    private function getStates(): array
    {
        return [
            'AK' => 'Alaska',
            'AL' => 'Alabama',
            'AR' => 'Arkansas',
            'AZ' => 'Arizona',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DC' => 'District of Columbia',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'IA' => 'Iowa',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'MA' => 'Massachusetts',
            'MD' => 'Maryland',
            'ME' => 'Maine',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MO' => 'Missouri',
            'MS' => 'Mississippi',
            'MT' => 'Montana',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'NE' => 'Nebraska',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NV' => 'Nevada',
            'NY' => 'New York',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VA' => 'Virginia',
            'VT' => 'Vermont',
            'WA' => 'Washington',
            'WI' => 'Wisconsin',
            'WV' => 'West Virginia',
            'WY' => 'Wyoming',
        ];
    }
}