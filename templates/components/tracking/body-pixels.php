<?php 
use App\Config\Config;
$config = Config::getInstance();
?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= $config->get('tracking.gtm_id') ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

<!-- Facebook Pixel (noscript) -->
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= $config->get('tracking.facebook_pixel_id') ?>&ev=PageView&noscript=1"/></noscript>

<?php if ($type !== 'medicare'): ?>
<!-- Conversion Pixel for Healthcare -->
<script>
gtag('event', 'conversion', {
    'send_to': 'AW-<?= $config->get('tracking.google_ads_id') ?>/<?= $config->get('tracking.healthcare_conversion_label') ?>',
    'value': 1.0,
    'currency': 'USD'
});

fbq('track', 'Lead', {
    value: 1.0,
    currency: 'USD',
});
</script>
<?php endif; ?>

<!-- CallRail -->
<?php if ($config->get('tracking.enable_callrail')): ?>
<script type="text/javascript" src="//cdn.callrail.com/companies/<?= $config->get('tracking.callrail_company_id') ?>/<?= $config->get('tracking.callrail_tracker_id') ?>/12/swap.js"></script>
<?php endif; ?>

<!-- Custom Conversion Tracking -->
<script>
// Fire conversion event
window.conversionData = {
    leadId: '<?= $_SESSION['lead_id'] ?? '' ?>',
    type: '<?= $type ?? '' ?>',
    state: '<?= $state ?? '' ?>',
    value: '<?= $did ?? '' ?>'
};

// Send to your analytics
if (typeof gtag !== 'undefined') {
    gtag('event', 'form_submit', {
        'event_category': 'Lead',
        'event_label': '<?= $type ?? 'healthcare' ?>',
        'value': 1
    });
}
</script>