<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | <?= htmlspecialchars($sitename) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-step { display: none; }
        .form-step.active { display: block; }
        .option-button:hover { transform: translateY(-2px); transition: all 0.2s; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-blue-600"><?= htmlspecialchars($sitename) ?></h1>
                <div class="text-sm text-gray-600">
                    <span>Need help?</span>
                    <a href="tel:<?= htmlspecialchars($config->get('phones.standard')) ?>" class="ml-2 text-blue-600 font-semibold">
                        <?= htmlspecialchars($config->get('phones.standard')) ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Form Container -->
    <div class="max-w-2xl mx-auto mt-8 px-4 sm:px-6 lg:px-8 pb-12">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <!-- Progress Bar -->
            <div class="bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-600">Step <span class="step-number">1</span> of 6</span>
                    <span class="text-xs text-gray-600"><span class="step-percentage">16</span>% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress-bar" class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: 16.66%"></div>
                </div>
            </div>

            <!-- Form -->
            <form id="multi-step-form" class="p-6 sm:p-8">
                <!-- Hidden fields -->
                <input type="hidden" name="ip_address" value="<?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '') ?>">
                <input type="hidden" name="landing_page" value="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '') ?>">
                
                <!-- Step 1: Household Size -->
                <div class="form-step active" data-step="1">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">How many people are you insuring?</h2>
                    <div class="space-y-4">
                        <button type="button" class="household-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="1">
                            <span class="text-lg font-medium">Just myself</span>
                            <span class="block text-sm text-gray-600 mt-1">Individual coverage</span>
                        </button>
                        <button type="button" class="household-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="2">
                            <span class="text-lg font-medium">Me and my spouse</span>
                            <span class="block text-sm text-gray-600 mt-1">Coverage for 2 people</span>
                        </button>
                        <button type="button" class="household-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="3">
                            <span class="text-lg font-medium">My family</span>
                            <span class="block text-sm text-gray-600 mt-1">Coverage for 3 or more</span>
                        </button>
                    </div>
                    <input type="hidden" name="Household" id="household">
                </div>

                <!-- Step 2: Date of Birth -->
                <div class="form-step" data-step="2">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">What is your date of birth?</h2>
                    <p class="text-gray-600 mb-6">This helps us find age-appropriate plans for you.</p>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                            <select name="birthmonth" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Month</option>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day</label>
                            <select name="birthday" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Day</option>
                                <?php for($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                            <select name="birthyear" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Year</option>
                                <?php 
                                $currentYear = date('Y');
                                for($i = $currentYear - 18; $i >= $currentYear - 100; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="DOB" id="dob">
                    <div class="flex justify-between">
                        <button type="button" class="prev-step text-gray-600 hover:text-gray-800">&larr; Back</button>
                        <button type="button" class="next-step bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Continue</button>
                    </div>
                </div>

                <!-- Step 3: Income -->
                <div class="form-step" data-step="3">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">What is your household income?</h2>
                    <p class="text-gray-600 mb-6">This helps determine available subsidies and savings.</p>
                    <div class="space-y-3">
                        <button type="button" class="income-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="24999">
                            Under $25,000
                        </button>
                        <button type="button" class="income-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="39999">
                            $25,000 - $39,999
                        </button>
                        <button type="button" class="income-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="54999">
                            $40,000 - $54,999
                        </button>
                        <button type="button" class="income-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="69999">
                            $55,000 - $69,999
                        </button>
                        <button type="button" class="income-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="99999">
                            $70,000 - $99,999
                        </button>
                        <button type="button" class="income-option option-button w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:shadow-md transition-all" data-value="100000">
                            $100,000+
                        </button>
                    </div>
                    <input type="hidden" name="Household_Income" id="household-income">
                </div>

                <!-- Step 4: Name -->
                <div class="form-step" data-step="4">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">What is your name?</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" name="First_Name" required placeholder="John" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="Last_Name" required placeholder="Doe" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" class="prev-step text-gray-600 hover:text-gray-800">&larr; Back</button>
                        <button type="button" class="next-step bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Continue</button>
                    </div>
                </div>

                <!-- Step 5: Contact Info -->
                <div class="form-step" data-step="5">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">How can we reach you?</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="Email" required placeholder="john@example.com" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ZIP Code</label>
                            <input type="text" name="Zip" required placeholder="12345" maxlength="5" pattern="[0-9]{5}"
                                   value="<?= htmlspecialchars($zip ?? '') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" class="prev-step text-gray-600 hover:text-gray-800">&larr; Back</button>
                        <button type="button" class="next-step bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Continue</button>
                    </div>
                </div>

                <!-- Step 6: Phone -->
                <div class="form-step" data-step="6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Last step! What's your phone number?</h2>
                    <p class="text-gray-600 mb-6">An agent will call to help you enroll and answer any questions.</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="Primary_Phone" required placeholder="(555) 555-5555" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg">
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" class="prev-step text-gray-600 hover:text-gray-800">&larr; Back</button>
                        <button type="button" id="submit-lead" class="bg-green-600 text-white px-8 py-3 rounded-md hover:bg-green-700 font-semibold">
                            Get My Quotes
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-4">
                        By clicking "Get My Quotes", I agree to the <a href="#" class="text-blue-600">Terms of Use</a> and <a href="#" class="text-blue-600">Privacy Policy</a>, 
                        and consent to be contacted by phone, email, and SMS.
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/multi-step-form.js"></script>
</body>
</html>