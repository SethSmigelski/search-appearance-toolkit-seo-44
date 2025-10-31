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

                button.addEventListener('click', function () {
                    block.classList.toggle('is-expanded');

                    // This handles the icon change via CSS.
					if (block.classList.contains('is-expanded')) {
						button.setAttribute('aria-label', 'Show Less');
					} else {
						button.setAttribute('aria-label', 'Show More');
					}
                });
            }
        }
    });
});