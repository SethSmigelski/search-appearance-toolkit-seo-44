import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
// import { useSelect } from '@wordpress/data';

export default function save({ attributes }) {
	// Read directly from attributes, no hooks!
const { 
	blockInstanceId, layout, 
	isCollapsible,  isSmartIndentation,
	headings, showHeading, headingText, headingTag, 
	listStyle, fontSize, textColor, linkColor,  blockBackgroundColor,
	linkBackgroundColor, linkBackgroundColorHover, linkBorderColor, linkBorderRadius, 
	isSticky, stickyOffset, jumpOffset, stickyStrategy,
} = attributes;
	
	// Pass the font size as a CSS Custom Property for dynamic height calculations
	// Consolidate all dynamic styles onto the parent wrapper
	const style = {
		// Text & Font
		color: textColor,
		fontSize: fontSize,
		'--jump-link-font-size': fontSize || '18px',
		'--seo44-block-bg': blockBackgroundColor,
		
		// Link Variables (Applied to wrapper, consumed by children)
		'--seo44-link-color': linkColor,
		'--seo44-link-bg': layout === 'horizontal' ? linkBackgroundColor : undefined,
		'--seo44-link-hover-bg': layout === 'horizontal' ? linkBackgroundColorHover : undefined,
		'--seo44-link-border-color': layout === 'horizontal' ? linkBorderColor : undefined,
		'--seo44-link-radius': layout === 'horizontal' && linkBorderRadius ? `${linkBorderRadius}px` : undefined,

		// sticky positioning
    	'--seo44-sticky-offset': isSticky ? `${stickyOffset}px` : undefined
	};
	
	const ListTag = listStyle === 'ol' ? 'ol' : 'ul';
	
	// 2. Calculate the ID here (OUTSIDE the return statement)
	const listId = `seo44-jump-links-list-${blockInstanceId}`;
	

	const blockProps = useBlockProps.save({
   		className: `${layout === 'horizontal' ? 'is-layout-horizontal' : ''} ${isCollapsible ? 'is-collapsible' : ''} ${listStyle === 'none' ? 'list-style-none' : ''} ${isSticky ? 'is-sticky' : ''} ${stickyStrategy === 'desktop-only' ? 'sticky-desktop-only' : ''}`.trim(),
		style,
		// --- NEW: Pass the offset (User setting OR default 30px) ---
        'data-seo44-jump-offset': isSticky ? jumpOffset : 30
	});
	
	// Show More Expand and Contract Arrows
	const arrowDownIcon = (
		<svg className="arrow-down" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
			<path d="M12 16l-6-6 1.41-1.41L12 13.17l4.59-4.58L18 10l-6 6z"></path>
		</svg>
	);
	const arrowUpIcon = (
		<svg className="arrow-up" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
			<path d="M12 8l-6 6 1.41 1.41L12 10.83l4.59 4.58L18 14l-6-6z"></path>
		</svg>
	);

	return (
		<div {...blockProps}>
			<div className="seo44-sticky-sentinel" aria-hidden="true"></div>
			{showHeading && <RichText.Content 
				tagName={headingTag || 'h2'} // Fallback to h2 if undefined
				className="wp-block-seo44-jump-links-heading" 
				value={headingText} 
			/>}
			{headings && headings.length > 0 && (
                <nav aria-label={__('Table of contents', 'search-appearance-toolkit-seo-44')}>
                    <ListTag id={listId}>
                        {headings.filter(h => h.isVisible !== false).map(h => (
                            <li 
								key={h.anchor}
								className={isSmartIndentation ? `seo44-jump-link-level-${h.level}` : ''}
							>
								<a href={`#${h.anchor}`}>{h.linkText}</a>
				
                            </li>
                        ))}
                    </ListTag>
                    {isCollapsible && (
						<button type="button" className="seo-44-show-more" aria-label={__('Show More', 'search-appearance-toolkit-seo-44')}
							aria-expanded="false"
                            aria-controls={listId}
						>
							{arrowDownIcon}
							{arrowUpIcon}
						</button>
					)}
                </nav>
			)}
		</div>
	);
}
