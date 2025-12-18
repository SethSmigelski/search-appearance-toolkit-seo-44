jQuery(document).ready(function($) {

    // --- Add custom class to metabox heading ---
    // Find the metabox by its ID and then find the title element inside it.
    var metabox = $('#seo44_meta_box');
    if (metabox.length) {
        metabox.find('h2.hndle').addClass('seo44-metabox-heading');
    }

    // --- "Use Example" Title Button ---
    $('#seo44-use-example-title').on('click', function() {
        var exampleText = $('#seo44-title-example').text();
        var titleInput = $('#seo44_title');
        titleInput.val(exampleText);
        
        // Trigger the keyup event to update the snippet preview and character counter
        titleInput.trigger('keyup'); 
    });

    // --- Character Counter Functionality (from v1.4) ---
    function createCounter(inputId, displayId, maxLength) {
        const inputField = document.getElementById(inputId);
        const displayElement = document.getElementById(displayId);

        if (!inputField || !displayElement) {
            return;
        }

        function updateCount() {
            const currentLength = inputField.value.length;
            displayElement.textContent = currentLength + ' / ' + maxLength;
            
            if (currentLength > maxLength) {
                displayElement.classList.add('over-limit');
            } else {
                displayElement.classList.remove('over-limit');
            }
        }
        inputField.addEventListener('keyup', updateCount);
        inputField.addEventListener('paste', updateCount);
        inputField.addEventListener('change', updateCount);
        updateCount();
    }

    createCounter('seo44_title', 'seo44_title_char_count', 60);
    createCounter('seo44_description', 'seo44_description_char_count', 160);


    // --- HowTo Schema Trigger Logic ---
    const howToWrapper = $('#seo44-howto-trigger-wrapper');
    const howToCheckbox = $('#seo44_enable_howto');
    const descriptionField = $('#seo44_description');
    
    // List of triggers (lowercase)
    const triggers = ['step guide', 'step-by-step', 'how to', 'how-to', 'guide', 'walkthrough', 'directions', 'instructions', 'tutorial'];

    // Flag to track if user has manually overridden the automation
    let userHasInteracted = false;

    // Listen for manual clicks
    howToCheckbox.on('change', function() {
        userHasInteracted = true;
    });

    function checkHowToTriggers() {
        // 1. If user manually touched the box, do nothing.
        if (userHasInteracted) return;

        // 2. Get description text
        const text = descriptionField.val().toLowerCase();
        
        // 3. Check for triggers
        const hasTrigger = triggers.some(trigger => text.includes(trigger));

        if (hasTrigger) {
            // Found a trigger!
            howToWrapper.show(); // Make visible
            
            // Only check it if it wasn't already checked (to avoid redundant events)
            if (!howToCheckbox.is(':checked')) {
                howToCheckbox.prop('checked', true);
            }
        } else {
            // No trigger found.
            // Behavior: If we are purely automated (user hasn't clicked), 
            // we hide it and uncheck it.
            howToWrapper.hide();
            howToCheckbox.prop('checked', false);
        }
    }

    // Run on keyup in description
    descriptionField.on('keyup change paste', checkHowToTriggers);

    // Run once on load to handle pre-filled content (if user hasn't saved yet)
    // We delay slightly to ensure values are populated
    setTimeout(function() {
        // Only run auto-check if the box is currently hidden/unchecked. 
        // If it's already visible/checked from DB, we respect that state.
        if (!howToCheckbox.is(':checked')) {
            checkHowToTriggers();
        } else {
            // If it IS checked from DB, mark as "interacted" so we don't auto-uncheck it 
            // just because the user edits the description and removes a keyword.
            userHasInteracted = true;
        }
    }, 500);


    // --- Snippet Preview Functionality ---
    const titleInput = $('#seo44_title');
    const descriptionInput = $('#seo44_description');
    
    const previewHeaderUrl = $('.seo44-preview-breadcrumb-url');
    const previewTitle = $('.seo44-preview-title');
    const previewDescription = $('.seo44-preview-description');

    // Get placeholder values from localized data
    const defaultTitle = seo44_data.post_title;
    const siteName = seo44_data.site_name;
    const permalink = seo44_data.permalink; 
    
    function updatePreview() {
        // Update Title
        let titleVal = titleInput.val();
        previewTitle.text(titleVal ? titleVal : defaultTitle);

        // Update Description
        let descVal = descriptionInput.val();
        if (descVal) {
            previewDescription.text(descVal);
        } else {
            // Show a generic placeholder if the description is empty
            previewDescription.text('Enter a meta description to see how it will appear in search results...');
        }
        
        // Update Breadcrumb URL
        let breadcrumb = permalink.replace(/^https?:\/\//, '').replace(/\/$/, '');
        breadcrumb = breadcrumb.replace(/\//g, ' &rsaquo; '); // &rsaquo; is the > symbol
        previewHeaderUrl.html(breadcrumb);
    }

    // Initial update on page load
    updatePreview();

    // Update on keyup in the input fields
    titleInput.on('keyup', updatePreview);
    descriptionInput.on('keyup', updatePreview);

});
