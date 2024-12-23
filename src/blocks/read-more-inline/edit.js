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
			<div {...useBlockProps()}>
				<p>{props.attributes.source}</p>
				<ServerSideRender
					block="coco/read-more-inline"
					LoadingResponsePlaceholder={'Loading'}
					attributes={{
						source: ['category', 'post_tag', 'postID'].includes(props.attributes.source)
							? props.attributes.source
							: 'category',
						termID: props.attributes.termID,
						postID: props.attributes.postID,
					}}
				/>
			</div>
		</>
	);
};

export default Edit;
