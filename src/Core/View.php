<?php

namespace App\Core;

class View
{
    private static string $layoutsPath = __DIR__ . '/../../templates/layouts/';
    private static string $viewsPath = __DIR__ . '/../../templates/pages/';
    private static string $componentsPath = __DIR__ . '/../../templates/components/';
    
    public static function render(string $view, array $data = [], string $layout = 'main'): string
    {
        // Extract data to make it available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = self::$viewsPath . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View file not found: $viewFile");
        }
        
        $content = ob_get_clean();
        
        // If layout is specified, wrap content in layout
        if ($layout) {
            ob_start();
            $layoutFile = self::$layoutsPath . $layout . '.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                throw new \Exception("Layout file not found: $layoutFile");
            }
            return ob_get_clean();
        }
        
        return $content;
    }
    
    public static function component(string $component, array $data = []): void
    {
        extract($data);
        $componentFile = self::$componentsPath . $component . '.php';
        if (file_exists($componentFile)) {
            include $componentFile;
        }
    }
}