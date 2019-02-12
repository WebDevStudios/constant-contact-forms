const { __ } = wp.i18n;
const {
	registerBlockType,
} = wp.blocks;

export default registerBlockType( 'constant-contact/single-contact-form', {
	title: __( 'Constant Contact: Single Form', 'constant-contact' ),
	icon: 'index-card',
	category: 'layout',
	edit: () => {
		return (
			<div>
				<h1>{ __( 'Hello, from EDIT', 'constant-contact' ) }</h1>
			</div>
		)
	},
	save: () => {
		return (
			<div>
				<h1>{ __( 'Hello, from SAVE', 'constant-contact' ) }</h1>
			</div>
		)
	}
});
