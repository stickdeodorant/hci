<?php use App\Core\View; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Thank You') ?> | <?= htmlspecialchars($sitename) ?></title>
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/thank-you.css">
    
    <!-- Tracking Pixels -->
    <?php View::component('tracking/head-pixels', $data ?? []); ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-blue-900 text-white py-4">
        <div class="container mx-auto px-4">
            <img src="/assets/images/logo-white.svg" alt="<?= htmlspecialchars($sitename) ?>" class="h-10">
        </div>
    </nav>
    
    <!-- Main Content -->
    <?= $content ?>
    
    <!-- Footer -->
    <footer class="bg-gray-100 py-8 mt-auto">
        <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($sitename) ?>. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Tracking Pixels -->
    <?php View::component('tracking/body-pixels', $data ?? []); ?>
    
    <!-- Scripts -->
    <script src="/assets/js/thank-you.js"></script>
</body>
</html>