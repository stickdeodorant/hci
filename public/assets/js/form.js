class MultiStepForm {
    constructor() {
        this.currentStep = parseInt(localStorage.getItem('currentStep') || '1');
        this.formData = JSON.parse(localStorage.getItem('formData') || '{}');
        this.totalSteps = 6;
        
        this.init();
    }
    
    init() {
        this.form = document.getElementById('multi-step-form');
        this.progressBar = document.getElementById('progress-bar');
        
        // Show current step
        this.showStep(this.currentStep);
        
        // Bind events
        this.bindEvents();
        
        // Restore form data
        this.restoreFormData();
    }
    
    bindEvents() {
        // Household selection (Step 1)
        document.querySelectorAll('.household-option').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = e.target.dataset.value;
                document.getElementById('household').value = value;
                this.saveAndNext();
            });
        });
        
        // Next buttons
        document.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', () => this.nextStep());
        });
        
        // Previous buttons
        document.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', () => this.previousStep());
        });
        
        // Income selection (Step 3)
        document.querySelectorAll('.income-option').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = e.target.dataset.value;
                document.getElementById('household-income').value = value;
                this.saveAndNext();
            });
        });
        
        // Form submission
        document.getElementById('submit-lead')?.addEventListener('click', (e) => {
            e.preventDefault();
            this.submitForm();
        });
        
        // Auto-save form data
        this.form.addEventListener('input', (e) => {
            this.saveFormData();
        });
        
        // Phone formatting
        const phoneInput = document.querySelector('input[name="Primary_Phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 6) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
                } else if (value.length >= 3) {
                    value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                }
                e.target.value = value;
            });
        }
    }
    
    showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(s => {
            s.classList.remove('active');
        });
        
        // Show current step
        const currentStepElement = document.querySelector(`[data-step="${step}"]`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }
        
        // Update progress bar
        const progress = (step / this.totalSteps) * 100;
        this.progressBar.style.width = `${progress}%`;
        
        // Update localStorage
        localStorage.setItem('currentStep', step);
        
        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('step', step);
        window.history.pushState({step}, '', url);
    }
    
    nextStep() {
        if (this.validateCurrentStep()) {
            this.saveFormData();
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.showStep(this.currentStep);
            }
        }
    }
    
    previousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
        }
    }
    
    saveAndNext() {
        this.saveFormData();
        this.nextStep();
    }
    
    validateCurrentStep() {
        const currentStepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        const inputs = currentStepElement.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        // Special validation for Step 2 (DOB)
        if (this.currentStep === 2) {
            const month = document.querySelector('[name="birthmonth"]').value;
            const day = document.querySelector('[name="birthday"]').value;
            const year = document.querySelector('[name="birthyear"]').value;
            
            if (month && day && year) {
                const dob = `${month}/${day}/${year}`;
                document.getElementById('dob').value = dob;
                
                // Calculate age
                const today = new Date();
                const birthDate = new Date(dob);
                const age = today.getFullYear() - birthDate.getFullYear();
                
                if (age < 18 || age > 100) {
                    alert('Age must be between 18 and 100');
                    return false;
                }
            }
        }
        
        // Phone validation for last step
        if (this.currentStep === 6) {
            const phone = document.querySelector('[name="Primary_Phone"]').value;
            const cleanPhone = phone.replace(/\D/g, '');
            
            if (cleanPhone.length !== 10) {
                alert('Please enter a valid 10-digit phone number');
                return false;
            }
            
            // Check for invalid patterns
            const invalidPatterns = [
                /^800|^822|^833|^844|^855|^866|^877|^880|^887|^888|^889/, // Toll-free
                /(\d)\1{9}/, // All same digit
                /^(...)(111)/ // 111 prefix
            ];
            
            for (let pattern of invalidPatterns) {
                if (pattern.test(cleanPhone)) {
                    alert('Please enter a valid phone number');
                    return false;
                }
            }
        }
        
        return isValid;
    }
    
    saveFormData() {
        const currentStepElement = document.querySelector(`[data-step="${this.currentStep}"]`);
        const inputs = currentStepElement.querySelectorAll('input, select');
        
        inputs.forEach(input => {
            if (input.name && input.value) {
                this.formData[input.name] = input.value;
            }
        });
        
        localStorage.setItem('formData', JSON.stringify(this.formData));
    }
    
    restoreFormData() {
        Object.keys(this.formData).forEach(key => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = this.formData[key];
            }
        });
    }
    
    async submitForm() {
        if (!this.validateCurrentStep()) {
            return;
        }
        
        this.saveFormData();
        
        // Show loading state
        const submitBtn = document.getElementById('submit-lead');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="animate-spin">⏳</span> Processing...';
        
        try {
            // First check phone
            const phoneCheck = await fetch('/api/check-phone', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    phone: this.formData.Primary_Phone
                })
            });
            
            const phoneResult = await phoneCheck.json();
            
            if (!phoneResult.success) {
                alert('Please enter a valid phone number');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                return;
            }
            
            // Submit lead
            const response = await fetch('/api/submit-lead', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(this.formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Clear local storage
                localStorage.removeItem('formData');
                localStorage.removeItem('currentStep');
                
                // Redirect to thank you page
                window.location.href = result.redirect;
            } else {
                alert(result.message || 'An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new MultiStepForm();
});

// Handle browser back button
window.addEventListener('popstate', (event) => {
    if (event.state && event.state.step) {
        const form = new MultiStepForm();
        form.currentStep = event.state.step;
        form.showStep(form.currentStep);
    }
});