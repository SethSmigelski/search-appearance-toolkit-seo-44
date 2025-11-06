// src/view.js (Final Version)

window.addEventListener('load', function () {
    const jumpLinksBlocks = document.querySelectorAll('.wp-block-seo44-jump-links');

    jumpLinksBlocks.forEach(block => {
        // --- SMOOTH SCROLLING LOGIC ---
        const links = block.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // --- COLLAPSIBLE LOGIC (SIMPLIFIED & ROBUST) ---
        if (block.classList.contains('is-collapsible')) {
            const button = block.querySelector('.seo-44-show-more');

            // If the button exists, make it work. No height check needed.
            if (button) {
                button.style.display = 'inline-block'; // Always show the button if the feature is on.

				// Get translated strings from PHP, with a fallback
                const l10n = window.seo44JumpLinksL10n || {};
                const showMoreText = l10n.showMore || 'Show More';
                const showLessText = l10n.showLess || 'Show Less';

                // Set initial aria-label just in case (though save.js should do it)
                button.setAttribute('aria-label', showMoreText);

                button.addEventListener('click', function () {
                    block.classList.toggle('is-expanded');
					
					// Check the new state
                    const isExpanded = block.classList.contains('is-expanded');
					// 1. UPDATE aria-expanded on the button
                    button.setAttribute('aria-expanded', isExpanded);

                    // 2. UPDATE the button's aria-label using our new strings
                    // This handles the icon change via CSS.
					if (isExpanded) {
						button.setAttribute('aria-label', showLessText);
					} else {
						button.setAttribute('aria-label', showMoreText);
					}
                });
            }
        }
    });
});
