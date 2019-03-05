import { SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const { Component } = wp.element;
const { __ } = wp.i18n;

class SingleFormSelect extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			forms: [
				{ label: __( 'Select a form' ), value: 0 }
			]
		}
	}

	async componentDidMount() {

		try {
			const results = await apiFetch( { path: '/wp-json/wp/v2/ctct_forms' } );
			const forms = results.map( result => ( { label: result.title.rendered, value: result.id } ) );
			this.setState( { forms: [...this.state.forms, ...forms ] } );
		} catch ( e ) {
			console.error('ERROR: ', e.message );
		}
	}

	render() {
		let { selectedForm } = this.props.attributes;

		return (
			<div>
			<h4 className="ctct-block-title">{ __( 'Constant Contact Forms' ) }</h4>
			<SelectControl
				value={ selectedForm }
				options={ this.state.forms }
				onChange={ value => this.props.setAttributes( { selectedForm: value } ) } /></div>
		)
	}
}

export default SingleFormSelect;
