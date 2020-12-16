const {
	components: {
		SelectControl,
	},
	apiFetch,
	element: {
		Component,
	},
	i18n: {
		__,
	},
} = wp;

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
			],
			displayTitle: [
				{ label: __( 'Display Title', 'constant-contact' ), value: true },
				{ label: __( 'Hide Title', 'constant-contact' ), value: false }
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
		let { selectedForm, displayTitle } = this.props.attributes;

		return (
			<div className="ctct-block-container">
				<img className="ctct-block-logo" src="https://images.ctfassets.net/t21gix3kzulv/78gf1S3CjPrnl9rURf6Q8w/3c20fb510dd4d4653feddf86ece35e1a/ctct_ripple_logo_horizontal_white_orange.svg"/>
				<small>{ __( 'Display Form Title', 'constant-contact' ) }</small>

				<SelectControl
					value={ displayTitle }
					options={ this.state.displayTitle }
					onChange={ value => this.props.setAttributes( { displayTitle: value } ) }
				/>

				<small>{ __( 'Choose the form to display with the dropdown below.', 'constant-contact' ) }</small>
				<SelectControl
					value={ selectedForm }
					options={ this.state.forms }
					onChange={ value => this.props.setAttributes( { selectedForm: value } ) }
				/>
			</div>
		)
	}
}

export default SingleFormSelect;
