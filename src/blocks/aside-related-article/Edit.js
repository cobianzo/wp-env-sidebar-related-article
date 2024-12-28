// WordPress dependencies
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

// Internal dependencies
import Controls from './controls';

// style only for editor
import './editor.css';

const Edit = function (props) {
	// set the warning message if the attributes have not been set.
	let message = null;
	const { source, termID, postID } = props.attributes;

	if (!source) {
		message = __('Please select a source', 'aside-related-article-block');
	} else if (['category', 'post_tag'].includes(source) && !parseInt(termID)) {
		message = __('Select a term in the dropdown. <br/>');
		message +=
			source === 'post_tag'
				? __('Only tags included in the current post will be shown', 'aside-related-article-block')
				: __('Only categories included in the current post will be shown', 'aside-related-article-block');
	} else if (source === 'postID' && !postID) {
		message = __('Select a post in the dropdown.', 'aside-related-article-block');
	}

	return (
		<>
			<Controls {...props} />
			<div {...useBlockProps({ className: 'is-editor' })}>
				{message ? (
					<div className="notice" dangerouslySetInnerHTML={{ __html: message }}></div>
				) : (
					<ServerSideRender
						block="coco/aside-related-article"
						attributes={{ ...props.attributes }}
						className="idle-wrapper iddle-wrapper--editor"
					/>
				)}
			</div>
		</>
	);
};

export default Edit;
