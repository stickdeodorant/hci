<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Config\Config;

class HomeController
{
    public function index(Request $request, Response $response): void
    {
        $config = Config::getInstance();
        
        $data = [
            'title' => 'Affordable Health Insurance Plans',
            'sitename' => $config->get('app.name'),
            'state' => $request->get('st') ? $config->get('states.' . strtoupper($request->get('st'))) : null,
            'state_abbr' => $request->get('st') ? strtoupper($request->get('st')) : null,
        ];
        
        echo View::render('home', $data);
    }
}