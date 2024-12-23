import { useSelect } from '@wordpress/data';

const usePostTermsAsOptions = (taxonomy = 'category') => {
	return useSelect(
		(select) => {
			const postRecord = select('core').getEntityRecord('postType', 'post', 1);
			const mappingPostTerms = { category: 'categories', post_tag: 'tags' };
			const termsIds = postRecord[mappingPostTerms[taxonomy]];

			const terms = select('core').getEntityRecords('taxonomy', taxonomy, {
				include: termsIds,
			});

			return terms || [];
		},
		[taxonomy]
	);
};

export default usePostTermsAsOptions;
