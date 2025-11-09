// js/global-tracker.js
document.addEventListener('DOMContentLoaded', function() {
    
    // Check if dataLayer exists and has the push method
    const canPushToDataLayer = window.dataLayer && typeof window.dataLayer.push === 'function';
    if (!canPushToDataLayer) {
        return; // No GTM, so do nothing.
    }

    // Get all our toggle settings
    const settings = window.seo44_tracking_settings || {};

    // 1. Jump Link Tracking (This can be moved from view.js to here)
    if (settings.trackJumpLinks) {
        // Find all jump link blocks
        document.querySelectorAll('.wp-block-seo44-jump-links a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                // (Your existing view.js logic for pushing to dataLayer)
                window.dataLayer.push({
                    'event': 'jump_link_click',
                    'click_text': e.target.textContent,
                    'click_anchor': e.target.getAttribute('href')
                });
            });
        });
    }

    // 2. Outbound/Affiliate Link Tracking
    if (settings.trackOutboundLinks) {
        document.body.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.hostname && link.hostname !== window.location.hostname) {
                let eventType = 'external_link_click';
                if (link.getAttribute('rel') === 'sponsored') {
                    eventType = 'affiliate_link_click';
                }
                window.dataLayer.push({
                    'event': eventType,
                    'outbound_url': link.href
                });
            }
        });
    }

    // 3. Scroll Depth Tracking
    if (settings.trackScrollDepth) {
        // (This logic is more complex, it involves checking scroll position
        // and using a 'Set' to only fire each event once)
        console.log('Scroll Depth Tracking Enabled');
        // ... logic to track 25%, 50%, 75%, 100% ...
    }
});