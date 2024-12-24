// WordPress dependencies
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, Spinner, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

// External dependencies
import PostLookup from '@cobianzo/gutenberg-post-lookup-component';

// Internal dependencies
import usePostTermsAsOptions from '../../lib/usePostTermsAsOptions';

function Controls(props) {
	const [termOptions, setTermOptions] = useState([]);
	const [isLoading, setIsLoading] = useState(false);
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
				console.log('>>>> 	Options set to', newOptions);
				setTermOptions(newOptions);
				setIsLoading(false);
			});
		},
		[props.attributes.source]
	);

	console.log('>>>>>>Controls', props);
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

				<p>The VALue of termID: {props.attributes.termID} </p>
				<p>The VALue of postID: {props.attributes.postId} </p>
				<p>All props attr: {JSON.stringify(props.attributes)} </p>
				<p>Options: {JSON.stringify(termOptions)}</p>
				<p>Sent to serverside render</p>
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
