// WordPress dependencies
import { registerBlockType } from '@wordpress/blocks';

// Styles
import './style.css';
import Edit from './Edit';

registerBlockType('coco/aside-related-article', {
	/* eslint-disable react-hooks/rules-of-hooks */
	edit: Edit,
	save: () => null,
});

/* eslint-enable react-hooks/rules-of-hooks */
