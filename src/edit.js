import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { Tooltip, PanelBody, Button, ButtonGroup, CheckboxControl, FontSizePicker, SelectControl, TextControl, ToggleControl, RangeControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data'; 
import { useEffect, Fragment } from '@wordpress/element';

// Helper function to strip HTML from heading content.
function stripHtml(html) {
	const doc = new DOMParser().parseFromString(html, 'text/html');
	return doc.body.textContent || '';
}

// set svg chevrons
const arrowUpIcon = (
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <path d="M3 21v-2h18v2zm8-4v-6.175L9.4 12.4L8 11l4-4l4 4l-1.4 1.4l-1.6-1.575V17zM3 5V3h18v2z"></path>
    </svg>
);
const arrowDownIcon = (
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <path d="M3 5V3h18v2zm9 12l-4-4l1.4-1.4l1.6 1.575V7h2v6.175l1.6-1.575L16 13zm-9 4v-2h18v2z"></path>
    </svg>
);
const expandDownIcon = (
    <svg className="arrow-down" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <path d="M12 16l-6-6 1.41-1.41L12 13.17l4.59-4.58L18 10l-6 6z"></path>
    </svg>
);

export default function Edit({ attributes, setAttributes }) {
    // Removed 'blockInstanceId' and 'style' from destructuring to avoid conflicts
	const { 
		headingLevels, headings: savedHeadings, showHeading, headingText, headingTag, 
		layout, listStyle, 
		isEditing, isCollapsible, isSmartIndentation,
		fontSize, textColor, linkColor, blockBackgroundColor,
		linkBackgroundColor, linkBackgroundColorHover, linkBorderColor, linkBorderRadius, linkStyle, separatorType,
		isSticky, stickyOffset, jumpOffset, stickyStrategy, stickyBehavior,
	} = attributes;

	// Consolidate all dynamic styles onto the parent wrapper
	const style = {
		// Text & Font
		color: textColor,
		fontSize: fontSize,
		'--jump-link-font-size': fontSize || '18px',
		
		// Link Variables
		'--seo44-link-color': linkColor,
		'--seo44-link-bg': layout === 'horizontal' ? linkBackgroundColor : undefined,
		'--seo44-link-hover-bg': layout === 'horizontal' ? linkBackgroundColorHover : undefined,
		'--seo44-link-border-color': layout === 'horizontal' ? linkBorderColor : undefined,
		'--seo44-link-radius': layout === 'horizontal' && linkBorderRadius ? `${linkBorderRadius}px` : undefined,
		'--seo44-block-bg': blockBackgroundColor,
		
		'--seo44-sticky-offset': isSticky ? `${stickyOffset}px` : undefined
	};

	const ListTag = listStyle === 'ol' ? 'ol' : 'ul';
	const { createInfoNotice } = useDispatch( 'core/notices' );
	
    // Generate the Dynamic ID (matching save.js logic)
    // We use attributes.blockInstanceId directly since it wasn't destructured
    const listId = attributes.blockInstanceId ? `seo44-jump-links-list-${attributes.blockInstanceId}` : 'seo44-jump-links-list';
	const separatorClass = linkStyle === 'text' && separatorType !== 'none' ? `has-separator-${separatorType}` : '';
	const linkStyleClass = linkStyle === 'text' ? 'is-style-text-links' : '';
	
	const blockProps = useBlockProps({ style });
    blockProps.className = `${blockProps.className} ${layout === 'horizontal' ? 'is-layout-horizontal' : ''} ${isCollapsible && !isEditing ? 'is-collapsible' : ''} ${listStyle === 'none' ? 'list-style-none' : ''} ${separatorClass} ${linkStyleClass}`.trim();

	const blocks = useSelect((select) => select('core/block-editor').getBlocks(), []);
	const { updateBlockAttributes } = useDispatch('core/block-editor');
	
	// --- EFFECT: Initialization & Scanning ---
	useEffect(() => {
        const newAttributes = {};

		// A. Generate a unique ID if missing
		if (!attributes.blockInstanceId) {
	        newAttributes.blockInstanceId = Math.random().toString(36).substr(2, 9);
	    }

        // Apply initialization changes if needed
        if (Object.keys(newAttributes).length > 0) {
            setAttributes(newAttributes);
        }
	
        // 1. Get all current heading blocks
        const currentBlocks = blocks
            .filter(block => block.name === 'core/heading' && headingLevels.includes(`h${block.attributes.level}`));

        // --- Tools for de-duping and warning ---
        const seenAnchors = new Set();
        let wasDuplicateFound = false;

        // 2. Create Map of SAVED headings
        const savedHeadingsMap = new Map(savedHeadings.map(h => [h.anchor, h]));
        
        const newHeadings = [];

        // 3. Process all blocks
        for (const block of currentBlocks) {
            const originalText = stripHtml(block.attributes.content);
            
            // Generate a base anchor
            let baseAnchor = block.attributes.anchor || originalText.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
            
            // De-duping logic
            let uniqueAnchor = baseAnchor;
            let counter = 2;

            while (seenAnchors.has(uniqueAnchor)) {
                uniqueAnchor = `${baseAnchor}-${counter}`;
                counter++;
                wasDuplicateFound = true; 
            }
            seenAnchors.add(uniqueAnchor);
            
            if (block.attributes.anchor !== uniqueAnchor) {
                updateBlockAttributes(block.clientId, { anchor: uniqueAnchor });
            }

            const oldState = savedHeadingsMap.get(block.attributes.anchor) || savedHeadingsMap.get(uniqueAnchor);
            const wasLinkTextManuallyEdited = oldState && oldState.linkText !== oldState.text;
            const linkText = wasLinkTextManuallyEdited ? oldState.linkText : originalText;
            const isVisible = oldState ? oldState.isVisible : true;

            newHeadings.push({
                anchor: uniqueAnchor,
                text: originalText,
                linkText: linkText,
                isVisible: isVisible,
                level: block.attributes.level,
            });
        }

        // 4. Update attributes
        if (JSON.stringify(newHeadings) !== JSON.stringify(savedHeadings)) {
            setAttributes({ headings: newHeadings });
        }

        // 5. Snackbar Warning
        if (wasDuplicateFound) {
            createInfoNotice(
            	__('Jump Links Block: Duplicate headings were found. Unique IDs have been auto-generated, but this may be a sign of redundancy. Please review your headings for clarity.', 'search-appearance-toolkit-seo-44'),
            	{ type: 'snackbar' }
            );
        }
    }, [blocks, headingLevels, savedHeadings, attributes.blockInstanceId, setAttributes, updateBlockAttributes, createInfoNotice]);
	
	// Handle Layout/List Style conflict
	useEffect(() => {
		if (layout === 'horizontal' && listStyle !== 'none') {
			setAttributes({ listStyle: 'none' });
		}
	}, [layout, listStyle, setAttributes]);

	// --- Event Handlers ---
	const updateLinkText = (index, newLinkText) => {
		const newHeadings = [...savedHeadings];
		newHeadings[index].linkText = newLinkText;
		setAttributes({ headings: newHeadings });
	};
	
	const toggleVisibility = (index) => {
		const newHeadings = [...savedHeadings];
		newHeadings[index].isVisible = !newHeadings[index].isVisible;
		setAttributes({ headings: newHeadings });
	};
	
	const moveItem = (index, direction) => {
		const newHeadings = [...savedHeadings];
		const item = newHeadings.splice(index, 1)[0];
		if (direction === 'up') {
			newHeadings.splice(index - 1, 0, item);
		} else {
			newHeadings.splice(index + 1, 0, item);
		}
		setAttributes({ headings: newHeadings });
	};

	const toggleHeadingLevel = (level) => {
		const newLevels = headingLevels.includes(level)
			? headingLevels.filter(item => item !== level)
			: [...headingLevels, level];
		setAttributes({ headingLevels: newLevels.sort() });
	};

	// --- Render ---
	return (
		<>
			<InspectorControls>

				{/* Panel 1: Presentation */}
				<PanelBody title={__('Presentation', 'search-appearance-toolkit-seo-44')}>
					<ButtonGroup>
						<Button
							isPrimary={!isEditing}
							isPressed={!isEditing}
							onClick={() => setAttributes({ isEditing: false })}
						>
							{__('Viewing Mode', 'search-appearance-toolkit-seo-44')}
						</Button>
						<Button
							isPrimary={isEditing}
							isPressed={isEditing}
							onClick={() => setAttributes({ isEditing: true })}
						>
							{__('Editing Mode', 'search-appearance-toolkit-seo-44')}
						</Button>
					</ButtonGroup>
					<p className="description">{__('Switch to Editing Mode to customize link text, visibility, and order.', 'search-appearance-toolkit-seo-44')}</p>
				</PanelBody>

				{/* Panel 2: Appearance */}
				<PanelBody title={__('Appearance', 'search-appearance-toolkit-seo-44')}>
				
					<p><strong>{__('Layout', 'search-appearance-toolkit-seo-44')}</strong></p>
					<ButtonGroup>
						<Button
							isPrimary={layout === 'vertical'}
							isPressed={layout === 'vertical'}
							onClick={() => setAttributes({ layout: 'vertical' })}
						>
							{__('Vertical', 'search-appearance-toolkit-seo-44')}
						</Button>
						<Button
							isPrimary={layout === 'horizontal'}
							isPressed={layout === 'horizontal'}
							onClick={() => setAttributes({ layout: 'horizontal' })}
						>
							{__('Horizontal', 'search-appearance-toolkit-seo-44')}
						</Button>
					</ButtonGroup>
                    
                    {/* Updated ToggleControl with __nextHasNoMarginBottom */}
					<ToggleControl
						label={__('Make Jump Links Area Expandable', 'search-appearance-toolkit-seo-44')}
    					help={__('Conserve screen space by collapsing a long list of jump links, providing users with an elegant "show more" button to see the entire list.', 'search-appearance-toolkit-seo-44')}
						checked={isCollapsible}
						onChange={() => setAttributes({ isCollapsible: !isCollapsible })}
                        __nextHasNoMarginBottom={true}
					/>
                    
                    {/* Updated SelectControl with opt-in props */}
					<SelectControl
						label={__('List Style', 'search-appearance-toolkit-seo-44')}
						value={listStyle}
						options={[
							{ label: __('Bulleted', 'search-appearance-toolkit-seo-44'), value: 'ul' },
            				{ label: __('Numbered', 'search-appearance-toolkit-seo-44'), value: 'ol' },
            				{ label: __('None', 'search-appearance-toolkit-seo-44'), value: 'none' },
						]}
						onChange={(newListStyle) => setAttributes({ listStyle: newListStyle })}
        				disabled={layout === 'horizontal'} 
                        __nextHasNoMarginBottom={true}
                        __next40pxDefaultSize={true}
					/>
                    
                    {/* Updated FontSizePicker */}
					<FontSizePicker
						fontSizes={[
							{ name: __('S', 'search-appearance-toolkit-seo-44'), slug: 'small', size: '14px' },
							{ name: __('M', 'search-appearance-toolkit-seo-44'), slug: 'normal', size: '17px' },
							{ name: __('L', 'search-appearance-toolkit-seo-44'), slug: 'large', size: '20px' },
							{ name: __('XL', 'search-appearance-toolkit-seo-44'), slug: 'extra-large', size: '23px' },
						]}
						value={fontSize}
						onChange={(newSize) => setAttributes({ fontSize: newSize })}
						withReset
                        __next40pxDefaultSize={true}
					/>
					<PanelColorSettings
						title={__('Colors', 'search-appearance-toolkit-seo-44')}
						colorSettings={[
							{ 
                                value: blockBackgroundColor, 
                                onChange: (newColor) => setAttributes({ blockBackgroundColor: newColor }), 
                                label: __('Block Background', 'search-appearance-toolkit-seo-44') 
                            },
							{ value: linkColor, onChange: (newColor) => setAttributes({ linkColor: newColor }), label: __('Link Color', 'search-appearance-toolkit-seo-44') },
							{ value: textColor, onChange: (newColor) => setAttributes({ textColor: newColor }), label: __('Other Text Color', 'search-appearance-toolkit-seo-44') },
        				]}
					/>
					{layout === 'horizontal' && (
                        <>
                            <hr />
                            <p><strong>{__('Horizontal Link Styles', 'search-appearance-toolkit-seo-44')}</strong></p>
							{/* NEW: Link Style Selection */}
							        <SelectControl
							            label={__('Link Style', 'search-appearance-toolkit-seo-44')}
							            value={linkStyle}
							            options={[
							                { label: __('Button (Default)', 'search-appearance-toolkit-seo-44'), value: 'button' },
							                { label: __('Plain Text', 'search-appearance-toolkit-seo-44'), value: 'text' },
							            ]}
							            onChange={(val) => setAttributes({ linkStyle: val })}
							            __nextHasNoMarginBottom={true}
							        />
							
							        {/* SHOW BUTTON SETTINGS (Existing) ONLY IF BUTTON STYLE */}
							        {linkStyle === 'button' && (
							            <>
							                <PanelColorSettings
				                                title={__('Link Colors', 'search-appearance-toolkit-seo-44')}
				                                colorSettings={[
				                                    { value: linkBackgroundColor, onChange: (newColor) => setAttributes({ linkBackgroundColor: newColor }), label: __('Background', 'search-appearance-toolkit-seo-44') },
				                                	{ value: linkBackgroundColorHover, onChange: (newColor) => setAttributes({ linkBackgroundColorHover: newColor }), label: __('Background Hover', 'search-appearance-toolkit-seo-44') },    
													{ value: linkBorderColor, onChange: (newColor) => setAttributes({ linkBorderColor: newColor }), label: __('Border', 'search-appearance-toolkit-seo-44') },
				                                ]}
                            				/>
							                <RangeControl
				                                label={__('Link Border Radius', 'search-appearance-toolkit-seo-44')}
				                                value={linkBorderRadius}
				                                onChange={(newValue) => setAttributes({ linkBorderRadius: newValue })}
				                                min={0}
				                                max={50}
				                                __nextHasNoMarginBottom={true}
				                                __next40pxDefaultSize={true}
				                            />
							            </>
							        )}
									{/* NEW: SEPARATOR SETTINGS (Only if Text Style) */}
							        {linkStyle === 'text' && (
							            <SelectControl
							                label={__('Separator', 'search-appearance-toolkit-seo-44')}
							                value={separatorType}
							                options={[
                                                // REPLACED 'None' with 'Space'
							                    { label: __('Space ( )', 'search-appearance-toolkit-seo-44'), value: 'space' },
							                    { label: __('Middle Dot (·)', 'search-appearance-toolkit-seo-44'), value: 'dot' },
							                    { label: __('Pipe (|)', 'search-appearance-toolkit-seo-44'), value: 'pipe' },
							                    { label: __('Slash (/)', 'search-appearance-toolkit-seo-44'), value: 'slash' },
							                ]}
							                onChange={(val) => setAttributes({ separatorType: val })}
							                __nextHasNoMarginBottom={true}
							            />
							        )}
							    </>
					)}
				</PanelBody>				  

				{/* Panel 3: Content Settings */}
				<PanelBody title={__('Content Settings', 'search-appearance-toolkit-seo-44')}>
					<ToggleControl
						label={__('Display Block Title', 'search-appearance-toolkit-seo-44')}
						checked={showHeading}
						onChange={() => setAttributes({ showHeading: !showHeading })}
                        __nextHasNoMarginBottom={true}
					/>
					{showHeading && (
						<>
							<TextControl
								label={__('Title Text', 'search-appearance-toolkit-seo-44')}
								value={headingText}
								onChange={(newText) => setAttributes({ headingText: newText })}
								help={__('The text that appears above your list of links.', 'search-appearance-toolkit-seo-44')}
							/>
							<SelectControl
                                label={__('Title Tag', 'search-appearance-toolkit-seo-44')}
                                value={headingTag}
                                options={[
                                    { label: 'H2', value: 'h2' },
                                    { label: 'H3', value: 'h3' },
                                    { label: 'H4', value: 'h4' },
                                    { label: 'H5', value: 'h5' },
                                    { label: 'Paragraph (Bold)', value: 'p' },
                                    { label: 'Div (No Semantic Value)', value: 'div' },
                                ]}
                                onChange={(newTag) => setAttributes({ headingTag: newTag })}
								help={__('Choose a level that fits your page\'s structure.', 'search-appearance-toolkit-seo-44')}
                                __nextHasNoMarginBottom={true}
                                __next40pxDefaultSize={true}
                            />
						</>
					)}
					<hr />
					{/* SECTION 2: SCANNED CONTENT */}
					<p><strong>{__('Included Headings', 'search-appearance-toolkit-seo-44')}</strong></p>
                    <p className="description" style={{ marginBottom: '10px' }}>
                        {__('Select which heading levels from your post content should appear in the jump links list.', 'search-appearance-toolkit-seo-44')}
                    </p>

					<CheckboxControl label="H2" checked={headingLevels.includes('h2')} onChange={() => toggleHeadingLevel('h2')} />
					<CheckboxControl label="H3" checked={headingLevels.includes('h3')} onChange={() => toggleHeadingLevel('h3')} />
					<CheckboxControl label="H4" checked={headingLevels.includes('h4')} onChange={() => toggleHeadingLevel('h4')} />
					
                    <ToggleControl
                        label={__('Create Visual Hierarchy', 'search-appearance-toolkit-seo-44')}
                        help={__('Indents sub-headings (H3, H4) to create a nested outline structure.', 'search-appearance-toolkit-seo-44')}
                        checked={isSmartIndentation}
                        onChange={() => setAttributes({ isSmartIndentation: !isSmartIndentation })}
                        __nextHasNoMarginBottom={true}
                    />
				</PanelBody>

				{/* Panel 4: Position Settings */}																
				<PanelBody title={__('Position Settings', 'search-appearance-toolkit-seo-44')}>
				    <ToggleControl
				        label={__('Sticky Position', 'search-appearance-toolkit-seo-44')}
				        help={__('Keep the table of contents visible while scrolling.', 'search-appearance-toolkit-seo-44')}
				        checked={isSticky}
				        onChange={() => setAttributes({ isSticky: !isSticky })}
                        __nextHasNoMarginBottom={true}
				    />
				    {isSticky && (
						<>
                            <p className="description" style={{ marginBottom: '15px' }}>
                                {__('Customize how the block behaves when it sticks to the top of the screen.', 'search-appearance-toolkit-seo-44')}
                            </p>
				            <RangeControl
				                label={__('Top Offset (px)', 'search-appearance-toolkit-seo-44')}
                                help={__('The distance between the top of the screen and the block when stuck (useful for clearing sticky headers).', 'search-appearance-toolkit-seo-44')}
				                value={stickyOffset}
				                onChange={(value) => setAttributes({ stickyOffset: value })}
				                min={0}
				                max={200}
                                __nextHasNoMarginBottom={true}
                                __next40pxDefaultSize={true}
				            />
                            <RangeControl
				                label={__('Jump Offset (px)', 'search-appearance-toolkit-seo-44')}
                                help={__('The buffer distance to stop *before* the heading. Increase this if your sticky header covers the text.', 'search-appearance-toolkit-seo-44')}
				                value={jumpOffset}
				                onChange={(value) => setAttributes({ jumpOffset: value })}
				                min={0}
				                max={200}
                                __nextHasNoMarginBottom={true}
                                __next40pxDefaultSize={true}
				            />
							{/* NEW: Smart Sticky Control */}
					        <SelectControl
					            label={__('Scroll Behavior', 'search-appearance-toolkit-seo-44')}
					            help={__('Choose "Scroll-Up-To-Reveal" to hide the nav when scrolling down (saves screen space).', 'search-appearance-toolkit-seo-44')}
					            value={stickyBehavior}
					            options={[
					                { label: __('Always Visible', 'search-appearance-toolkit-seo-44'), value: 'always' },
					                { label: __('Scroll-Up-To-Reveal', 'search-appearance-toolkit-seo-44'), value: 'smart' },
					            ]}
					            onChange={(val) => setAttributes({ stickyBehavior: val })}
					            __nextHasNoMarginBottom={true}
					        />		
				            <ToggleControl
				                label={__('Disable on Mobile', 'search-appearance-toolkit-seo-44')}
				                help={__('Prevents the block from sticking on small screens to save reading space.', 'search-appearance-toolkit-seo-44')}
				                checked={stickyStrategy === 'desktop-only'}
				                onChange={(isChecked) => setAttributes({ 
				                    stickyStrategy: isChecked ? 'desktop-only' : 'always' 
				                })}
                                __nextHasNoMarginBottom={true}
				            />
				        </>
				    )}
				</PanelBody>															
			</InspectorControls>

			<div {...blockProps}>
		        {showHeading && (
		            <RichText
		                tagName={headingTag}
		                className="wp-block-seo44-jump-links-heading"
		                value={headingText}
		                onChange={(newText) => setAttributes({ headingText: newText })}
		                placeholder={__('On This Page', 'search-appearance-toolkit-seo-44')}
		            />
		        )}

				{savedHeadings.length > 0 ? ( // We now use savedHeadings for a stable display
					<nav aria-label={__('Table of contents', 'search-appearance-toolkit-seo-44')}>
                    	<ListTag id={listId}>
							{savedHeadings.map((heading, index) => 
								isEditing ? (
									<li key={heading.anchor}>
										<TextControl
											value={heading.linkText}
											onChange={(newText) => updateLinkText(index, newText)}
										/>
										<div className="edit-controls-wrapper">
											<div className="reorder-buttons">
												<Button
													icon={arrowUpIcon}
													label={__('Move Up', 'search-appearance-toolkit-seo-44')}
													onClick={() => moveItem(index, 'up')}
													disabled={index === 0}
												/>
												<Button
													icon={arrowDownIcon}
													label={__('Move Down', 'search-appearance-toolkit-seo-44')}
													onClick={() => moveItem(index, 'down')}
													disabled={index === savedHeadings.length - 1}
												/>
											</div>
											<ToggleControl
												label={
													heading.isVisible !== false 
													? __('Included', 'search-appearance-toolkit-seo-44') 
													: __('This Jump Link will not be shown', 'search-appearance-toolkit-seo-44')
												}
												checked={heading.isVisible !== false}
												onChange={() => toggleVisibility(index)}
                                                __nextHasNoMarginBottom={true}
											/>
										</div>
									</li>
								) : (
									heading.isVisible !== false && (
										<li 
											key={heading.anchor}
											className={isSmartIndentation ? `seo44-jump-link-level-${heading.level}` : ''}
										>
                                            {/* REMOVED 'style={linkStyle}' FROM THIS LINE */}
											<a href={`#${heading.anchor}`} onClick={(e) => e.preventDefault()}>
												{heading.linkText}
											</a>
										</li>
									)
								)
							)}
						</ListTag>

						{/* ADD THIS SIMULATED BUTTON */}
						{!isEditing && isCollapsible && savedHeadings.length > 0 && (
		                    <Tooltip text={__('This button is functional on the front-end to expand the list.', 'search-appearance-toolkit-seo-44')}>
		                        <button
		                            type="button"
		                            className="seo-44-show-more"
		                            aria-label={__('Show More', 'search-appearance-toolkit-seo-44')}
		                            aria-expanded="false"
                            		aria-controls={listId}
		                            onClick={() => {
		                                createInfoNotice(
		                                    __('The "Show More" button is interactive on the published page.', 'search-appearance-toolkit-seo-44'),
		                                    { type: 'snackbar' }
		                                );
		                            }}
		                        >
		                            {expandDownIcon}
		                        </button>
		                    </Tooltip>
		                )}	
					</nav>
				) : (
					<p>{__('No headings found. Select a heading level in the block settings to generate links.', 'search-appearance-toolkit-seo-44')}</p>
				)}																			
		    </div>
		</>
	);
}
