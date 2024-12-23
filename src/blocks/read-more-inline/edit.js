// Wordpress dependencies
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import { __ } from '@wordpress/i18n';

// Internal dependencies
import Controls from './controls';

// Styles
import './style.css';

const Edit = (props) => {
	const myHeader = 'MyHEADER in editor';
	const preTitle = '<span style="color: red;">➲➲</span>';
	const shortTitle = 'My Title';
	const longTitle = 'this is the title or the post ok';
	const imageSrc = 'https://via.placeholder.com/150';
	const catTitle = 'Politics';
	const catLink = '#';
	const isInEditor = true;
	const readMoreText = __('read more', 'coco');
	const isOpinion = false;
	const postLink = '#';
	console.log('Edit', props);
	return (
		<>
			<Controls {...props} />
			<div {...useBlockProps()}>
				<ServerSideRender block="coco/read-more-inline" attributes={null} />
				<hr />
				<div className="coco__readmoreinline__content">
					{myHeader && <h2>{myHeader}</h2>}

					<a className="coco__readmoreinline__media" href={postLink} title={longTitle}>
						<figure>
							<img src={imageSrc} alt={longTitle}></img>
						</figure>
					</a>
					<div className="coco__readmoreinline__content">
						<a
							className="coco__readmoreinline__category coco__readmoreinline__badge"
							href={catLink}
						>
							{catTitle}
						</a>
						<a
							href={postLink}
							title={longTitle}
							className="coco__readmoreinline__content__headline"
						>
							{preTitle && <span dangerouslySetInnerHTML={{ __html: preTitle }} />}
							<h2>{shortTitle}</h2>
						</a>
						<a
							href={postLink}
							className="coco__readmoreinline__content__readmore"
							title={longTitle}
						>
							{readMoreText}
						</a>
					</div>
				</div>
			</div>
		</>
	);
};

export default Edit;
