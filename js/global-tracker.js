// js/global-tracker.js
document.addEventListener('DOMContentLoaded', function() {
    
    // Check if dataLayer exists and has the push method
    const canPushToDataLayer = window.dataLayer && typeof window.dataLayer.push === 'function';
    if (!canPushToDataLayer) {
        return; // No GTM, so do nothing.
    }

    // Get all our toggle settings
    const settings = window.seo44_tracking_settings || {};

    // 1. Jump Link Tracking
    if (settings.trackJumpLinks) {
        // Use event delegation for jump links, which is more efficient
        document.body.addEventListener('click', function(e) {
            // Check if the clicked element is a jump link inside our block
            const jumpLink = e.target.closest('.wp-block-seo44-jump-links a[href^="#"]');
            if (jumpLink) {
                window.dataLayer.push({
                    'event': 'jump_link_click',
                    'click_text': jumpLink.textContent,
                    'click_anchor': jumpLink.getAttribute('href')
                });
            }
        });
    }

    // 2. Outbound/Affiliate Link Tracking
    if (settings.trackOutboundLinks) {
        document.body.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            // Check if it's a link, has a hostname, and is not a jump link
            if (link && link.hostname && link.hostname !== window.location.hostname && !link.getAttribute('href').startsWith('#')) {
                let eventType = 'external_link_click';
                if (link.getAttribute('rel') && link.getAttribute('rel').includes('sponsored')) {
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
        
        // --- NEW SCROLL LOGIC ---

        // Helper function to throttle scroll events for performance
        let throttleTimer;
        const throttle = (callback, time) => {
            if (throttleTimer) return;
            throttleTimer = true;
            setTimeout(() => {
                callback();
                throttleTimer = false;
            }, time);
        };

        // A Set to store percentages we've already fired
        const firedPercentages = new Set();
        const percentagesToTrack = [25, 50, 75, 100];

        const handleScroll = () => {
            // Get scroll depth
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = (window.scrollY / docHeight) * 100;

            for (const percent of percentagesToTrack) {
                // If user passed the percentage AND we haven't fired it yet
                if (scrollPercent >= percent && !firedPercentages.has(percent)) {
                    
                    // 1. Fire the event
                    window.dataLayer.push({
                        'event': 'scroll_depth',
                        'scroll_percentage': percent
                    });

                    // 2. Add it to the Set so we don't fire it again
                    firedPercentages.add(percent);
                }
            }
        };

        // Attach the throttled event listener
        window.addEventListener('scroll', () => throttle(handleScroll, 200));
        // --- END NEW SCROLL LOGIC ---
    }
});
