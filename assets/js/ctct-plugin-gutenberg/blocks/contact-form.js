const { __ } = wp.i18n;
const {
	registerBlockType,
} = wp.blocks;

import SingleFormSelect from '../components/single-form-select';

export default registerBlockType( 'constant-contact/single-contact-form', {
	title: __( 'Constant Contact: Single Form', 'constant-contact' ),
	icon: 'index-card',
	category: 'layout',
	attributes: {
		selectedForm: {
			type: 'string',
		}
	},
	edit: SingleFormSelect,
	save: () => null
});
