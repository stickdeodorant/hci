// Success Animation
document.addEventListener('DOMContentLoaded', function() {
    const animationEl = document.querySelector('.success-animation');
    
    if (animationEl) {
        // Show fade items
        const fadeItems = document.querySelectorAll('.fade-item');
        fadeItems.forEach((item, index) => {
            const delay = parseInt(item.dataset.delay) || (index * 500);
            setTimeout(() => {
                item.classList.add('visible');
            }, delay);
        });
        
        // Hide animation after all items shown
        setTimeout(() => {
            animationEl.classList.add('fade-out');
            setTimeout(() => {
                animationEl.remove();
            }, 500);
        }, 4000);
    }
    
    // Track call button clicks
    const callButtons = document.querySelectorAll('.call-button, a[href^="tel:"]');
    callButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Fire conversion events
            if (typeof gtag !== 'undefined') {
                gtag('event', 'conversion', {
                    'send_to': 'AW-XXXXXXXXX/XXXXXXXXX',
                    'value': 1.0,
                    'currency': 'USD'
                });
            }
            
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Contact');
            }
        });
    });
});