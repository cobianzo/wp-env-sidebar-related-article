// WordPress dependencies
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, Spinner, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

// External dependencies
import PostLookup from '@cobianzo/gutenberg-post-lookup-component';

// The exported function will be used in the Edit component
function Controls(props) {
	const [termOptions, setTermOptions] = useState([]);
	const [isLoading, setIsLoading] = useState(false);

	// We could convert this into a custom hook (@TODO)
	// This hook will be triggered when the source changes
	// It will retrieve the terms from the selected taxonomy
	// and populate the SelectControl
	useSelect(
		(select) => {
			setIsLoading(true);
			const mappingPostTax = { category: 'categories', post_tag: 'tags' };
			const tax = mappingPostTax[props.attributes.source];
			const terms = select('core/editor').getEditedPostAttribute(tax);

			// retrieve the terms from the selected taxonomy
			const options = !terms
				? []
				: terms
						.map((termId) => {
							const termObject = select('core').getEntityRecord(
								'taxonomy',
								props.attributes.source,
								termId,
								{ context: 'view' }
							);
							return termObject ? { label: termObject.name, value: termObject.id } : null;
						})
						.filter((term) => term !== null);

			// These terms will be populated in the SelectControl
			setTermOptions(() => {
				const newOptions = [
					{
						label: __('--select term---', 'aside-related-article-block'),
						value: 0,
					},
					...options,
				];
				setTermOptions(newOptions);
				setIsLoading(false);
			});
		},
		[props.attributes.source]
	);

	return (
		<InspectorControls>
			<PanelBody title="Options" initialOpen={true}>
				<RadioControl
					label={__('Select a source', 'aside-related-article-block')}
					selected={props.attributes.source}
					options={[
						{ label: 'Category', value: 'category' },
						{ label: 'Tag', value: 'post_tag' },
						{ label: __('Select post', 'aside-related-article-block'), value: 'postID' },
					]}
					onChange={(newValue) => props.setAttributes({ source: newValue, termID: 0 })}
				/>

				{isLoading && <Spinner />}
				{(props.attributes.source === 'category' || props.attributes.source === 'post_tag') && (
					<>
						<p>{__('Select terms', 'aside-related-article-block')}:</p>
						<SelectControl
							label={<strong>{__('Select a Category or Tag', 'aside-related-article-block')}</strong>}
							value={props.attributes.termID || 0}
							options={termOptions}
							onChange={(newValue) => props.setAttributes({ termID: parseInt(newValue) })}
						/>
					</>
				)}

				{/* The lookup which allows the user to select a post in case the source is postId */}
				{props.attributes.source === 'postID' && (
					<PostLookup
						selectedPostId={props.attributes.postID}
						onChange={(newPostId) =>
							props.setAttributes({
								postID: newPostId || 0,
							})
						}
					/>
				)}
			</PanelBody>
		</InspectorControls>
	);
}

export default Controls;
