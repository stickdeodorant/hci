<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config
{
    private static ?self $instance = null;
    private array $config = [];

    private function __construct()
    {
        $this->loadEnvironment();
        $this->loadConfiguration();
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
    }

    private function loadConfiguration(): void
    {
        $this->config = [
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'Healthcare Insurance',
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'url' => $_ENV['APP_URL'] ?? 'https://healthcare-insurance.com',
            ],
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
                'port' => $_ENV['DB_PORT'] ?? '3306',
                'database' => $_ENV['DB_DATABASE'] ?? 'healthca_leads',
                'username' => $_ENV['DB_USERNAME'] ?? 'root',
                'password' => $_ENV['DB_PASSWORD'] ?? 'mysql',
            ],
            'phones' => [
                'typ' => $_ENV['PHONE_TYP'] ?? '',
                'popup' => $_ENV['PHONE_POPUP'] ?? '',
                'standard' => $_ENV['PHONE_STANDARD'] ?? '',
                'premium' => $_ENV['PHONE_PREMIUM'] ?? '',
                'h2' => $_ENV['PHONE_H2'] ?? '',
                'medicare' => $_ENV['PHONE_MEDICARE'] ?? '',
            ],
            'tracking' => [
                'gtm_id' => $_ENV['GOOGLE_TAG_MANAGER_ID'] ?? '',
                'ga_id' => $_ENV['GOOGLE_ADS_ID'] ?? '',
                'fb_pixel' => $_ENV['FACEBOOK_PIXEL_ID'] ?? '',
                'bing_uet' => $_ENV['BING_UET_TAG'] ?? '',
            ],
            'states' => $this->getStates(),
        ];
    }

    public function get(string $key, $default = null)
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