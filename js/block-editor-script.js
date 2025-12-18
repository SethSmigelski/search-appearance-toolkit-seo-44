(function() {
    // Safety check (shouldn't be needed if dependencies are correct, but good practice)
    if ( typeof wp === 'undefined' || typeof wp.data === 'undefined' ) {
        return;
    }

    const { select, dispatch, subscribe } = wp.data;
    const BLOCK_NAMESPACE = 'seo44/jump-links'; 
    const ATTRIBUTE_NAME  = 'headings'; 
    
    let wasSaving = false;

    subscribe(() => {
        const isSaving = select('core/editor').isSavingPost();
        const isAutosaving = select('core/editor').isAutosavingPost();

        if (isSaving && !isAutosaving && !wasSaving) {
            wasSaving = true;
            syncJumpLinksToMeta();
        }

        if (!isSaving) {
            wasSaving = false;
        }
    });

    function syncJumpLinksToMeta() {
        const blocks = select('core/block-editor').getBlocks();
        
        // Find FIRST matching block
        const findFirstBlock = (innerBlocks) => {
            for (let block of innerBlocks) {
                if (block.name === BLOCK_NAMESPACE) {
                    return block; 
                }
                if (block.innerBlocks.length > 0) {
                    const found = findFirstBlock(block.innerBlocks);
                    if (found) return found;
                }
            }
            return null;
        };

        const jumpLinksBlock = findFirstBlock(blocks);
        let activeIDs = [];

        if (jumpLinksBlock && jumpLinksBlock.attributes[ATTRIBUTE_NAME]) {
            const headings = jumpLinksBlock.attributes[ATTRIBUTE_NAME];
            
            // Filter: Only visible items are "Steps"
            activeIDs = headings
                .filter(item => {
                    // Exclude if explicitly set to false
                    if (item.isVisible === false) return false;
                    return true;
                })
                .map(item => String(item.anchor || item.id || ''));
        }

        const currentMeta = select('core/editor').getEditedPostAttribute('meta')['_seo44_howto_step_ids'];
        
        // Avoid unnecessary updates if array is identical
        const isSame = Array.isArray(currentMeta) && 
            currentMeta.length === activeIDs.length && 
            currentMeta.every((val, index) => val === activeIDs[index]);

        if (!isSame) {
            dispatch('core/editor').editPost({
                meta: { '_seo44_howto_step_ids': activeIDs }
            });
        }
    }
})();
