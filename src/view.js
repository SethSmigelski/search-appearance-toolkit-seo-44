// src/view.js

window.addEventListener('load', function () {
    const jumpLinksBlocks = document.querySelectorAll('.wp-block-seo44-jump-links');

	jumpLinksBlocks.forEach(block => {
        
        // --- SMOOTH SCROLLING LOGIC (With Offset) ---
        block.addEventListener('click', function(e) {
            const link = e.target.closest('a[href^="#"]');
            if (link) {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
					
					// BRANCH A: IS IT STICKY?
                    // If yes, use manual calculation to handle the Offset + Sticky Bar height
                    if (block.classList.contains('is-sticky')) {
	                    // 1. Get the offset from our data attribute
	                    // (It will be 30 by default, or the user's custom value if sticky)
	                    let offset = 30; 
	                    const dataOffset = block.getAttribute('data-seo44-jump-offset');
	                    if (dataOffset) {
	                        offset = parseInt(dataOffset, 10);
	                    }
	
	                    // 2. Calculate the position
	                    // elementRect.top is relative to viewport.
	                    // window.scrollY is current scroll amount.
	                    // We subtract the offset to stop *before* the element.
	                    const elementPosition = targetElement.getBoundingClientRect().top;
	                    const offsetPosition = elementPosition + window.scrollY - offset;
	
	                    // 3. Scroll there
	                    window.scrollTo({
	                        top: offsetPosition,
	                        behavior: "smooth"
	                    });
					} 
						
                    // BRANCH B: STANDARD BEHAVIOR
                    // If not sticky, use the native browser method. 
                    // This respects WP Admin Bar and Theme defaults better.
                    else {
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }
        });

		// --- SMART STICKY LOGIC (Scroll-Up-To-Reveal) ---
        // Filter for blocks that actually have the 'is-smart-sticky' class
        const smartBlocks = Array.from(jumpLinksBlocks).filter(b => b.classList.contains('is-smart-sticky'));
    
        if (smartBlocks.length > 0) {
            let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let ticking = false; // For throttling
            const delta = 10; // Minimum scroll amount to trigger change
    
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        
                        smartBlocks.forEach(block => {
                            // 1. SAFETY CHECK: Is the block actually stuck?
                            // If the block is NOT stuck (it's sitting normally in the content), 
                            // we must ensure it is visible and then stop.
                            if (!block.classList.contains('is-stuck')) {
                                block.classList.remove('is-scroll-hidden');
                                return; 
                            }
    
                            // 2. Determine Direction
                            if (Math.abs(lastScrollTop - scrollTop) <= delta) {
                                return; // Ignore tiny movements
                            }
    
                            if (scrollTop > lastScrollTop) {
                                // SCROLLING DOWN -> HIDE
                                block.classList.add('is-scroll-hidden');
                            } else {
                                // SCROLLING UP -> SHOW
                                block.classList.remove('is-scroll-hidden');
                            }
                        });
    
                        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }
		
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
// --- STICKY STATE DETECTION ---
// Only run this if the block is actually set to be sticky
	    if (block.classList.contains('is-sticky') && 'IntersectionObserver' in window) {
	        const stickyObserver = new IntersectionObserver((entries) => {
	            entries.forEach(entry => {
	                // We already know which block this is because we are inside the forEach(block) loop!
	                
	                // Logic: 
                    // 1. !isIntersecting: The sentinel has gone "out of bounds" (scrolled up).
                    // 2. top < 0: It went out the TOP, not the bottom.
	                if (!entry.isIntersecting && entry.boundingClientRect.top < 0) {
	                    block.classList.add('is-stuck');
	                } else {
	                    block.classList.remove('is-stuck');
	                }
	            });
	        }, {
	            threshold: 0,
	            rootMargin: "0px" // Trigger exactly at the viewport edge
	        });
	
	        // Find and observe all sentinels
            const sentinels = block.querySelectorAll('.seo44-sticky-sentinel');

	        sentinels.forEach(sentinel => {
	            stickyObserver.observe(sentinel);
	        });
	    }
	
		// --- SCROLLSPY LOGIC ---
	    // Only run if the browser supports IntersectionObserver
	    if ('IntersectionObserver' in window) {
	        const observerOptions = {
	            root: null,
	            rootMargin: '-100px 0px -60% 0px', // Adjusts the "active zone" of the screen
	            threshold: 0
	        };
	
	        const observer = new IntersectionObserver((entries) => {
	            entries.forEach(entry => {
	                if (entry.isIntersecting) {
	                    // 1. Find the link that points to this heading
	                    const activeId = entry.target.getAttribute('id');
	                    const activeLink = block.querySelector(`a[href="#${activeId}"]`);
	
	                    if (activeLink) {
	                        // 2. Remove active class from all links in this block
	                        block.querySelectorAll('a').forEach(link => link.classList.remove('is-active'));
	                        
	                        // 3. Add active class to the current link
	                        activeLink.classList.add('is-active');
	                    }
	                }
	            });
	        }, observerOptions);
	
	        // Start watching all headings that are linked in our TOC
	        const links = block.querySelectorAll('a[href^="#"]');
	        links.forEach(link => {
	            const id = link.getAttribute('href').substring(1);
	            const heading = document.getElementById(id);
	            if (heading) {
	                observer.observe(heading);
	            }
	        });
	    }
	});
});
