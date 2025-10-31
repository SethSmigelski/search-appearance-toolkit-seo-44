import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
// This is the correct import for PanelColorSettings
import { Tooltip, PanelBody, Button, ButtonGroup, CheckboxControl, FontSizePicker, SelectControl, TextControl, ToggleControl, RangeControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data'; 
import { useEffect } from '@wordpress/element';

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
	const { headingLevels, headings: savedHeadings, layout, showHeading, headingText, isEditing, isCollapsible, listStyle, fontSize, textColor, linkColor, linkBackgroundColor, linkBackgroundColorHover, linkBorderColor, linkBorderRadius } = attributes;
	const style = {
		color: textColor,
		fontSize: fontSize,
		'--jump-link-font-size': fontSize || '18px', // Use font size or a default
	};
	const linkStyle = {
		'--link-bg-color': layout === 'horizontal' ? linkBackgroundColor : undefined,
		borderColor: layout === 'horizontal' ? linkBorderColor : undefined,
		borderRadius: layout === 'horizontal' && linkBorderRadius ? `${linkBorderRadius}px` : undefined,
		color: linkColor, // Always apply the custom link color
		'--link-bg-hover-color': layout === 'horizontal' ? linkBackgroundColorHover : undefined,
    };
	// Determine which HTML tag to use for the list
	const ListTag = listStyle === 'ol' ? 'ol' : 'ul';
	const { createInfoNotice } = useDispatch( 'core/notices' );
	
	// Add a check for listStyle === 'none'
	const blockProps = useBlockProps({ style });
		blockProps.className = `${blockProps.className} ${layout === 'horizontal' ? 'is-layout-horizontal' : ''} ${isCollapsible && !isEditing ? 'is-collapsible' : ''} ${listStyle === 'none' ? 'list-style-none' : ''}`.trim();

	// Use useSelect to get all the editor blocks.
	// This gives us a `blocks` variable that we can use in our effects.
	const blocks = useSelect((select) => select('core/block-editor').getBlocks(), []);
	const { updateBlockAttributes } = useDispatch('core/block-editor');
	
	// Use useEffect to process the blocks.
	// This hook runs whenever the blocks on the page change or the user toggles a heading level
	// NEW: Reconciling logic to avoid re-ordering conflict

	useEffect(() => {
    // 1. Get all current heading blocks and ensure they have an anchor.
    const currentBlocks = blocks
        .filter(block => block.name === 'core/heading' && headingLevels.includes(`h${block.attributes.level}`));


	currentBlocks.forEach(block => {
        if (!block.attributes.anchor) {
            const text = stripHtml(block.attributes.content);
            if (text) {
                const anchor = text.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
                updateBlockAttributes(block.clientId, { anchor });
            }
        }
    });

    // Create a map of the current headings on the page for easy lookup.
    const currentHeadingsMap = new Map(
        currentBlocks.map(block => {
            const anchor = block.attributes.anchor || stripHtml(block.attributes.content).toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
            return [anchor, { text: stripHtml(block.attributes.content) }];
        })
    );
	
    // 2. UPDATE & FILTER: Start with your saved list. Remove any headings that no longer exist
    // and UPDATE the text of any that have changed.
    let reconciledHeadings = savedHeadings
        .filter(savedHeading => currentHeadingsMap.has(savedHeading.anchor))
        .map(savedHeading => {
            const currentHeading = currentHeadingsMap.get(savedHeading.anchor);
            const newText = currentHeading.text;
            const wasLinkTextManuallyEdited = savedHeading.text !== savedHeading.linkText;

            return {
                ...savedHeading, // Keeps isVisible, custom order, etc.
                text: newText, // Update the base text to the new version from the page.
                linkText: wasLinkTextManuallyEdited ? savedHeading.linkText : newText, // Update linkText only if it wasn't already customized.
            };
        });
	
    // 3. ADD: Find any brand new headings and add them to the end of the list.
    currentBlocks.forEach(block => {
        const { anchor, content } = block.attributes;
        if (anchor && !reconciledHeadings.some(h => h.anchor === anchor)) {
            const text = stripHtml(content);
            reconciledHeadings.push({
                anchor: anchor,
                text: text,
                linkText: text,
                isVisible: true,
            });
        }
    });

    // 4. Only update attributes if the final list is different.
    if (JSON.stringify(reconciledHeadings) !== JSON.stringify(savedHeadings)) {
        setAttributes({ headings: reconciledHeadings });
    }
}, [blocks, headingLevels, savedHeadings, setAttributes, updateBlockAttributes]);
	
	// useEffect to handle conditional logic to force list style for the horizontal layout
	useEffect(() => {
		if (layout === 'horizontal' && listStyle !== 'none') {
			setAttributes({ listStyle: 'none' });
		}
	}, [layout, listStyle, setAttributes]);

	// Handle LinkText
	const updateLinkText = (index, newLinkText) => {
		const newHeadings = [...savedHeadings]; // Create a copy
		newHeadings[index].linkText = newLinkText;
		setAttributes({ headings: newHeadings });
	};
	
	// Handle User option to hide specific jump links 
	const toggleVisibility = (index) => {
		const newHeadings = [...savedHeadings];
		// Flip the boolean value
		newHeadings[index].isVisible = !newHeadings[index].isVisible;
		setAttributes({ headings: newHeadings });
	};
	
	// Allow users to organize jump links
	const moveItem = (index, direction) => {
		const newHeadings = [...savedHeadings];
		const item = newHeadings.splice(index, 1)[0]; // Cut the item out of the array
		
		if (direction === 'up') {
			newHeadings.splice(index - 1, 0, item); // Insert it one position up
		} else {
			newHeadings.splice(index + 1, 0, item); // Insert it one position down
		}
	
		setAttributes({ headings: newHeadings });
	};

	// Toggle heading leveles shown in jump links block
	const toggleHeadingLevel = (level) => {
		const newLevels = headingLevels.includes(level)
			? headingLevels.filter(item => item !== level)
			: [...headingLevels, level];
		setAttributes({ headingLevels: newLevels.sort() });
	};

	// Troubleshooting Area
	return (
		<>
			<InspectorControls>

				{/* Panel 1: For the mode switcher */}
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

				{/*Panel 2: For styling settings */}
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
					<ToggleControl
						label={__('Make Jump Links Area Expandable', 'search-appearance-toolkit-seo-44')}
    					help={__('Conserve screen space by collapsing a long list of jump links, providing users with an elegant "show more" button to see the entire list.', 'search-appearance-toolkit-seo-44')}
						checked={isCollapsible}
						onChange={() => setAttributes({ isCollapsible: !isCollapsible })}
					/>
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
					/>
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
					/>
					<PanelColorSettings
						title={__('Colors', 'search-appearance-toolkit-seo-44')}
						colorSettings={[
							{ value: linkColor, onChange: (newColor) => setAttributes({ linkColor: newColor }), label: __('Link Color', 'search-appearance-toolkit-seo-44') },
							{ value: textColor, onChange: (newColor) => setAttributes({ textColor: newColor }), label: __('Other Text Color', 'search-appearance-toolkit-seo-44') },
        				]}
					/>
					{layout === 'horizontal' && (
                        <>
                            <hr />
                            <p><strong>{__('Horizontal Link Styles', 'search-appearance-toolkit-seo-44')}</strong></p>
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
                            />
                        </>
                    )}
				</PanelBody>
										  

				{/* Panel 3: For all other settings */}
				<PanelBody title={__('Heading Settings', 'search-appearance-toolkit-seo-44')}>
					<ToggleControl
						label={__('Show Heading for Jump Links Block (off by default)', 'search-appearance-toolkit-seo-44')}
						checked={showHeading}
						onChange={() => setAttributes({ showHeading: !showHeading })}
					/>
					{showHeading && (
						<TextControl
							label={__('Heading Text', 'search-appearance-toolkit-seo-44')}
							value={headingText}
							onChange={(newText) => setAttributes({ headingText: newText })}
						/>
					)}
					<p>{__('Select heading levels to include:', 'search-appearance-toolkit-seo-44')}</p>
					<CheckboxControl label="H2" checked={headingLevels.includes('h2')} onChange={() => toggleHeadingLevel('h2')} />
					<CheckboxControl label="H3" checked={headingLevels.includes('h3')} onChange={() => toggleHeadingLevel('h3')} />
					<CheckboxControl label="H4" checked={headingLevels.includes('h4')} onChange={() => toggleHeadingLevel('h4')} />	
				</PanelBody>				
			</InspectorControls>

			<div {...blockProps}>
		        {showHeading && (
		            <RichText
		                tagName="div"
		                className="wp-block-seo44-jump-links-heading"
		                value={headingText}
		                onChange={(newText) => setAttributes({ headingText: newText })}
		                placeholder={__('On This Page', 'search-appearance-toolkit-seo-44')}
		            />
		        )}

				{savedHeadings.length > 0 ? ( // We now use savedHeadings for a stable display
					<ListTag>
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
										/>
									</div>
								</li>
							) : (
								heading.isVisible !== false && (
									<li key={heading.anchor}>
										<a href={`#${heading.anchor}`} style={linkStyle} onClick={(e) => e.preventDefault()}>
											{heading.linkText}
										</a>
									</li>
								)
							)
						)}
					</ListTag>
				) : (
					<p>{__('No headings found. Select a heading level in the block settings to generate links.', 'search-appearance-toolkit-seo-44')}</p>
				)}
				
				{/* ADD THIS SIMULATED BUTTON */}
				{!isEditing && isCollapsible && savedHeadings.length > 0 && (
                    <Tooltip text={__('This button is functional on the front-end to expand the list.', 'search-appearance-toolkit-seo-44')}>
                        <button
                            type="button"
                            className="seo-44-show-more"
                            aria-label={__('Show More', 'search-appearance-toolkit-seo-44')}
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
		    </div>
		</>
	);
}
