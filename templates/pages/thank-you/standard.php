<div class="min-h-screen flex flex-col">
    <!-- Success Animation -->
    <?php if ($show_animation): ?>
    <div class="success-animation fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <svg class="checkmark w-24 h-24 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
            <h2 class="text-3xl font-bold text-green-600 mt-4">Information Received!</h2>
            <div class="mt-4 text-left inline-block">
                <p class="fade-item" data-delay="500">Name: <strong><?= htmlspecialchars($first_name) ?></strong> ✓</p>
                <p class="fade-item" data-delay="1000">Age: <strong><?= htmlspecialchars($age) ?></strong> ✓</p>
                <p class="fade-item" data-delay="1500">City: <strong><?= htmlspecialchars($city) ?></strong> ✓</p>
                <p class="fade-item" data-delay="2000">State: <strong><?= htmlspecialchars($state) ?></strong> ✓</p>
                <p class="fade-item" data-delay="2500">Application: <strong class="text-green-600">Submitted</strong> ✓</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Hero Section -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold text-blue-900 mb-4">
                        <?= htmlspecialchars($first_name) ?>, Your Information Has Been Received!
                    </h1>
                    
                    <p class="text-lg text-gray-700 mb-6">
                        Your information has been matched with several licensed insurance agents who specialize in 
                        <?= htmlspecialchars($state_name) ?> health insurance plans. They will be reaching out shortly 
                        to discuss your coverage options.
                    </p>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <p class="text-blue-700">
                            <strong>What happens next?</strong><br>
                            A licensed agent will call you within the next 24-48 hours to review your options 
                            and help you find the most affordable coverage for your needs.
                        </p>
                    </div>
                </div>
                
                <!-- Call Now Section -->
                <?php if ($call_active): ?>
                <div class="bg-green-50 rounded-lg shadow-lg p-8 mb-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        Ready to Enroll Now?
                    </h2>
                    <p class="text-lg text-gray-700 mb-6">
                        Speak with a licensed agent immediately to review your options and enroll today!
                    </p>
                    <a href="tel:<?= $phone_clean ?>" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-8 rounded-full text-xl transition-colors call-button">
                        <svg class="inline-block w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                        <?= htmlspecialchars($phone) ?>
                    </a>
                    <p class="text-sm text-gray-600 mt-2">Mon-Fri 9:00 AM - 6:00 PM EST</p>
                </div>
                <?php else: ?>
                <div class="bg-yellow-50 rounded-lg shadow-lg p-8 mb-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        Our Call Center is Currently Closed
                    </h2>
                    <p class="text-lg text-gray-700 mb-4">
                        Please call during business hours or an agent will contact you during the next business day.
                    </p>
                    <p class="text-xl font-bold text-blue-900 mb-2"><?= htmlspecialchars($phone) ?></p>
                    <p class="text-sm text-gray-600">Mon-Fri 9:00 AM - 6:00 PM EST</p>
                </div>
                <?php endif; ?>
                
                <!-- Benefits Section -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        Your Potential Benefits May Include:
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>$0 Monthly Premiums</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Low or No Deductibles</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Doctor Visit Copays as Low as $0</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Prescription Coverage Included</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>