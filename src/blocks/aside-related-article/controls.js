// WordPress dependencies
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, Spinner, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

// External dependencies
import PostLookup from '@cobianzo/gutenberg-post-lookup-component';

function Controls(props) {
	const [termOptions, setTermOptions] = useState([]);
	const [isLoading, setIsLoading] = useState(false);

	// We could convert this into a custom hook
	useSelect(
		(select) => {
			setIsLoading(true);
			const mappingPostTax = { category: 'categories', post_tag: 'tags' };
			const tax = mappingPostTax[props.attributes.source];
			const terms = select('core/editor').getEditedPostAttribute(tax);

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
							return termObject
								? { label: termObject.name, value: termObject.id }
								: null;
						})
						.filter((term) => term !== null);

			setTermOptions((prevOptions) => {
				const newOptions = [{ label: '--select term---', value: 0 }, ...options];
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
					label="Select a source"
					selected={props.attributes.source}
					options={[
						{ label: 'Category', value: 'category' },
						{ label: 'Tag', value: 'post_tag' },
						{ label: 'Select post', value: 'postID' },
					]}
					onChange={(newValue) => props.setAttributes({ source: newValue, termID: 0 })}
				/>

				{isLoading && <Spinner />}
				{(props.attributes.source === 'category' ||
					props.attributes.source === 'post_tag') && (
					<>
						<p>Select terms:</p>
						<SelectControl
							label={<strong>Select a Category or Tag Override</strong>}
							value={props.attributes.termID || 0}
							options={termOptions}
							onChange={(newValue) => {
								console.log(
									'%c>>>>> newValue',
									'color: orange; font-size:2rem;',
									newValue
								);
								props.setAttributes({ termID: parseInt(newValue) });
							}}
						/>
					</>
				)}

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

				<h3>
					{['category', 'post_tag', 'postID'].includes(props.attributes.source.toString())
						? props.attributes.source
						: 'category'}
					<br />
					termID: {props.attributes.termID}, postID: {props.attributes.postID},
				</h3>
			</PanelBody>
		</InspectorControls>
	);
}

export default Controls;
