import { SelectControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

const { Component } = wp.element;

class SingleFormSelect extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			forms: []
		}
	}

	async componentDidMount() {

		try {
			const results = await apiFetch( { path: '/wp-json/wp/v2/ctct_forms' } );
			const forms = results.map( result => ( { label: result.title.rendered, value: result.id } ) );
			this.setState( { forms } );
		} catch ( e ) {
			console.error('ERROR: ', e.message );
		}
	}

	render() {
		const { selectedForm } = this.props.attributes;

		return <SelectControl
			value={ selectedForm }
			options={ this.state.forms }
			onChange={ value => this.props.setAttributes( { selectedForm: value })}
		/>
	}
}

export default SingleFormSelect;
