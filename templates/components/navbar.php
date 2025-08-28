<?php use App\Config\Config; ?>
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <a href="/" class="text-2xl font-bold text-blue-600">
                <?= htmlspecialchars($sitename) ?>
            </a>
            <div class="flex items-center space-x-4">
                <span class="hidden sm:inline text-gray-600">Need help?</span>
                <a href="tel:<?= Config::getInstance()->get('phones.standard') ?>" class="text-blue-600 font-semibold">
                    <?= Config::getInstance()->get('phones.standard') ?>
                </a>
            </div>
        </div>
    </div>
</nav>