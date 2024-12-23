import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, Spinner, SelectControl } from '@wordpress/components';

function Controls(props) {
	const isLoading = false;
	return (
		<InspectorControls>
			<PanelBody title="Options" initialOpen={true}>
				<RadioControl
					label="select a source"
					selected={props.attributes.source}
					options={{ category: 'Category', tag: 'Tag', postID: 'select post' }}
					onChange={null}
				/>
				{isLoading === true && (
					<p>
						<Spinner />
						<em>Loading selections...</em>
					</p>
				)}
				{props.attributes.source === 'category' && (
					<SelectControl
						label=<strong>Select a Category Override</strong>
						value={props.attributes.category_id}
						options={null}
						onChange={null}
					/>
				)}

				{props.attributes.source === 'post' && null}
			</PanelBody>
		</InspectorControls>
	);
}

export default Controls;
