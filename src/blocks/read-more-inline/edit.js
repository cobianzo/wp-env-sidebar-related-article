// Wordpress dependencies
import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

import { __ } from '@wordpress/i18n';

// Internal dependencies
import Controls from './controls';

const Edit = (props) => {
	return (
		<>
			<Controls {...props} />
			<div data-iseditor="true" {...useBlockProps({ className: 'alignleft is-editor' })}>
				<ServerSideRender
					block="coco/read-more-inline"
					attributes={{
						source: props.attributes.source.toString(),
						termID: Number(props.attributes.termID).toFixed(0),
						postID: Number(props.attributes.postID).toFixed(0),
					}}
					className="idle-wrapper iddle-wrapper--editor"
				/>
			</div>
		</>
	);
};

export default Edit;
