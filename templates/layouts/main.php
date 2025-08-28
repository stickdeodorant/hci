<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Healthcare Insurance') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <?php \App\Core\View::component('navbar', ['sitename' => $sitename ?? 'Healthcare Insurance']); ?>
    
    <!-- Main Content -->
    <?= $content ?>
    
    <!-- Footer -->
    <?php \App\Core\View::component('footer'); ?>
    
    <!-- Scripts -->
    <script src="/assets/js/main.js"></script>
</body>
</html>