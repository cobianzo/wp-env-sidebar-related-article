import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';

registerBlockType('coco/read-more-inline', {
	edit: Edit,
	save: () => null,
});
