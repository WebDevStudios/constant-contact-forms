import { SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const { Component } = wp.element;
const { __ } = wp.i18n;

class SingleFormSelect extends Component {
	/**
	 * Constructor
	 * @param props
	 */
	constructor( props ) {
		super( props );

		// Set the initial state of the component.
		this.state = {
			forms: [
				{ label: __( 'Select a form', 'constant-contact' ), value: 0 }
			]
		}
	}

	/**
	 * After the component mounts, retrieve the forms and add them to the local component state.
	 */
	async componentDidMount() {

		try {
			const results = await apiFetch( { path: '/?rest_route=/wp/v2/ctct_forms' } );
			const forms = results.map( result => ( { label: result.title.rendered, value: result.id } ) );
			this.setState( { forms: [...this.state.forms, ...forms ] } );
		} catch ( e ) {
			console.error('ERROR: ', e.message );
		}
	}

	/**
	 * Render the Gutenberg block in the admin area.
	 */
	render() {
		// Destructure the selectedFrom from props.
		let { selectedForm } = this.props.attributes;

		return (
			<div className="ctct-block-container">
				<h4 className="ctct-block-title">{ __( 'Constant Contact Forms', 'constant-contact' ) }</h4>
				<small>{ __( 'Choose the form to display with the dropdown below.', 'constant-contact' ) }</small>
				<SelectControl
					value={ selectedForm }
					options={ this.state.forms }
					onChange={ value => this.props.setAttributes( { selectedForm: value } ) } />
			</div>
		)
	}
}

export default SingleFormSelect;
