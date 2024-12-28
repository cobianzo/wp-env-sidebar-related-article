// WordPress dependencies
import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

// Internal dependencies
import Controls from './controls';

// Styles
import './style.css';

registerBlockType('coco/aside-related-article', {
	/* eslint-disable react-hooks/rules-of-hooks */
	edit: (props) => (
		<>
			<Controls {...props} />
			<div {...useBlockProps({ className: 'is-editor' })}>
				<ServerSideRender
					block="coco/aside-related-article"
					attributes={{
						source: props.attributes.source.toString(),
						termID: Number(props.attributes.termID).toFixed(0),
						postID: Number(props.attributes.postID).toFixed(0),
					}}
					className="idle-wrapper iddle-wrapper--editor"
				/>
			</div>
		</>
	),
	save: () => null,
});

/* eslint-enable react-hooks/rules-of-hooks */
