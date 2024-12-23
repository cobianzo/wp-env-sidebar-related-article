import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';

import './style.css';

registerBlockType('coco/read-more-inline', {
	edit: Edit,
	save: () => null,
});
