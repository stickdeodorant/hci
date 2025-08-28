class MultiStepForm {
    constructor() {
        this.form = document.getElementById('multi-step-form');
        this.currentStep = 1;
        this.totalSteps = 6;
        this.formData = {};
        
        this.init();
    }
    
    init() {
        // Restore form data from localStorage
        this.restoreFormData();
        
        // Bind events
        this.bindEvents();
        
        // Show current step
        this.showStep(this.currentStep);
        
        // Update progress
        this.updateProgress();
    }
    
    bindEvents() {
        // Household selection
        document.querySelectorAll('.household-option').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = e.target.closest('.household-option').dataset.value;
                document.getElementById('household').value = value;
                this.saveField('household', value);
                this.nextStep();
            });
        });
        
        // Income selection
        document.querySelectorAll('.income-option').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = e.target.closest('.income-option').dataset.value;
                document.getElementById('household-income').value = value;
                this.saveField('household_income', value);
                this.nextStep();
            });
        });
        
        // Next buttons
        document.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', () => this.validateAndNext());
        });
        
        // Previous buttons
        document.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', () => this.previousStep());
        });
        
        // Form inputs
        this.form.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('change', () => {
                this.saveField(input.name, input.value);
            });
        });
        
        // Phone input mask
        const phoneInput = document.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 6) {
                    value = `(${value.slice(0,3)}) ${value.slice(3,6)}-${value.slice(6,10)}`;
                } else if (value.length >= 3) {
                    value = `(${value.slice(0,3)}) ${value.slice(3)}`;
                }
                e.target.value = value;
            });
        }
        
        // Submit button
        const submitBtn = document.querySelector('.submit-form');
        if (submitBtn) {
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.submitForm();
            });
        }
        
        // Browser back button
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.step) {
                this.currentStep = e.state.step;
                this.showStep(this.currentStep);
                this.updateProgress();
            }
        });
    }
    
    showStep(step) {
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.remove('active');
        });
        
        const stepEl = document.querySelector(`[data-step="${step}"]`);
        if (stepEl) {
            stepEl.classList.add('active');
        }
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('step', step);
        history.pushState({step: step}, '', url);
        
        // Update progress
        this.updateProgress();
        
        // Focus first input
        setTimeout(() => {
            const firstInput = stepEl.querySelector('input:not([type="hidden"]), select');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    }
    
    nextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.showStep(this.currentStep);
            
            // Save to localStorage
            localStorage.setItem('currentStep', this.currentStep);
        }
    }
    
    previousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.showStep(this.currentStep);
            
            // Save to localStorage
            localStorage.setItem('currentStep', this.currentStep);
        }
    }
    
    validateAndNext() {
        const currentStepEl = document.querySelector(`[data-step="${this.currentStep}"]`);
        const inputs = currentStepEl.querySelectorAll('input:not([type="hidden"]), select');
        let isValid = true;
        
        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value.trim()) {
                isValid = false;
                input.classList.add('error');
                
                // Show error message
                let errorMsg = input.parentElement.querySelector('.error-message');
                if (!errorMsg) {
                    errorMsg = document.createElement('span');
                    errorMsg.className = 'error-message text-red-500 text-sm';
                    errorMsg.textContent = 'This field is required';
                    input.parentElement.appendChild(errorMsg);
                }
            } else {
                input.classList.remove('error');
                const errorMsg = input.parentElement.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
        
        if (isValid) {
            // Special validation for DOB
            if (this.currentStep === 2) {
                const month = document.getElementById('birthmonth').value;
                const day = document.getElementById('birthday').value;
                const year = document.getElementById('birthyear').value;
                
                if (month && day && year) {
                    const dob = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                    this.saveField('dob', dob);
                }
            }
            
            this.nextStep();
        }
    }
    
    updateProgress() {
        const progress = (this.currentStep / this.totalSteps) * 100;
        const progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = `${progress}%`;
        }
        
        // Update step indicators
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            if (index < this.currentStep) {
                indicator.classList.add('completed');
            } else {
                indicator.classList.remove('completed');
            }
            
            if (index + 1 === this.currentStep) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });
    }
    
    saveField(name, value) {
        this.formData[name] = value;
        localStorage.setItem('formData', JSON.stringify(this.formData));
    }
    
    restoreFormData() {
        // Restore step
        const savedStep = localStorage.getItem('currentStep');
        if (savedStep) {
            this.currentStep = parseInt(savedStep);
        }
        
        // Restore form data
        const savedData = localStorage.getItem('formData');
        if (savedData) {
            this.formData = JSON.parse(savedData);
            
            // Populate form fields
            Object.keys(this.formData).forEach(key => {
                const input = this.form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = this.formData[key];
                }
            });
        }
    }
    
    async submitForm() {
        // Show loading
        this.showLoading();
        
        // Prepare form data
        const formData = new FormData(this.form);
        for (const [key, value] of Object.entries(this.formData)) {
            formData.append(key, value);
        }
        
        try {
            const response = await fetch('/api/submit-lead', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Clear localStorage
                localStorage.removeItem('formData');
                localStorage.removeItem('currentStep');
                
                // Redirect to thank you page
                window.location.href = result.redirect;
            } else {
                this.hideLoading();
                this.showErrors(result.errors);
            }
        } catch (error) {
            this.hideLoading();
            alert('An error occurred. Please try again.');
        }
    }
    
    showLoading() {
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) {
            loadingEl.classList.remove('hidden');
        }
    }
    
    hideLoading() {
        const loadingEl = document.getElementById('loading-modal');
        if (loadingEl) {
            loadingEl.classList.add('hidden');
        }
    }
    
    showErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = this.form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('error');
                
                const errorMsg = document.createElement('div');
                errorMsg.className = 'error-message text-red-500 text-sm mt-1';
                errorMsg.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                
                // Remove existing error message
                const existing = input.parentElement.querySelector('.error-message');
                if (existing) {
                    existing.remove();
                }
                
                input.parentElement.appendChild(errorMsg);
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new MultiStepForm();
});