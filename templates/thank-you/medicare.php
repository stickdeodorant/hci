<div class="min-h-screen flex flex-col bg-blue-50">
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Medicare Hero Section -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                    <div class="bg-red-600 text-white p-4">
                        <h1 class="text-2xl font-bold">Medicare Coverage Information Submitted</h1>
                    </div>
                    <div class="p-8">
                        <p class="text-lg text-gray-700 mb-4">
                            Thank you, <?= htmlspecialchars($first_name) ?>. Your Medicare information has been received 
                            and forwarded to licensed Medicare advisors in <?= htmlspecialchars($state_name) ?>.
                        </p>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                            <p class="text-blue-700">
                                <strong>Important:</strong> Medicare plans have specific enrollment periods. 
                                An advisor will contact you to discuss your eligibility and enrollment options.
                            </p>
                        </div>
                        
                        <?php if ($call_active): ?>
                        <div class="text-center">
                            <h2 class="text-xl font-bold mb-4">Speak with a Medicare Expert Now</h2>
                            <a href="tel:<?= $phone_clean ?>" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-8 rounded-full text-xl transition-colors">
                                <svg class="inline-block w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                                <?= htmlspecialchars($phone) ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Medicare Benefits -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Medicare Plans May Include:
                    </h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>Original Medicare (Parts A & B)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>Medicare Advantage Plans (Part C)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>Prescription Drug Coverage (Part D)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span>Medicare Supplement Plans</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
</div>