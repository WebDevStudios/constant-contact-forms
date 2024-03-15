import {__} from '@wordpress/i18n';
import {useSelect} from '@wordpress/data';
import {useBlockProps} from '@wordpress/block-editor';
import { SelectControl, Spinner } from '@wordpress/components';

export default function Edit( props ) {

	const {
		attributes: {
			selectedForm,
			displayTitle
		},
	} = props;

	const theforms = useSelect((select) => {
		const posts = select('core').getEntityRecords('postType', 'ctct_forms', {
			per_page: -1,
		});
		return {
			posts,
		};
	}, ['ctct_forms']);

	let formEntryObjs;
	if (theforms) {
		formEntryObjs = theforms.posts?.map((form) => {
			return {label: form.title.rendered, value: form.id};
		});
	}
	return (
		<div { ...useBlockProps() }>
			{!formEntryObjs && <Spinner />}
			{formEntryObjs &&
				<div>
					<div className="ctct-block-container--header">
						<img
							alt="Constant Contact Forms"
							src="https://images.ctfassets.net/t21gix3kzulv/78gf1S3CjPrnl9rURf6Q8w/3c20fb510dd4d4653feddf86ece35e1a/ctct_ripple_logo_horizontal_white_orange.svg"
						/>
					</div>
					<div className="ctct-block-container--selection">
						<small>{__("Display Form Title", "constant-contact")}</small>
						<div className="ctct-block-container--component">

						</div>
					</div>
					<div className="ctct-block-container--selection">
						<small>{__("Choose the form to display with the dropdown below.", "constant-contact")}</small>
						<div className="ctct-block-container--component">

						</div>
					</div>
				</div>
			}
		</div>
	)
}
/*
class SingleFormSelect extends Component {
	constructor(props) {
		super(props);

		// Set the initial state of the component.
		this.state = {
			forms       : [{label: __("Select a form", "constant-contact"), value: 0}],
			displayTitle: [
				{label: __("Display Title", "constant-contact"), value: true},
				{label: __("Hide Title", "constant-contact"), value: false},
			],
		};
	}


	async componentDidMount() {
		try {
			const results = await apiFetch({path: "/?rest_route=/wp/v2/ctct_forms"});
			const forms = results.map((result) => ({label: result.title.rendered, value: result.id}));
			this.setState({forms: [...this.state.forms, ...forms]});
		} catch (e) {
			console.error("ERROR: ", e.message);
		}
	}

	render() {
		// Destructure the selectedFrom from props.
		let { selectedForm, displayTitle } = this.props.attributes;

  }
}
*/
