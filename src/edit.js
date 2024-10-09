import {__} from '@wordpress/i18n';
import {useSelect} from '@wordpress/data';
import {useBlockProps} from '@wordpress/block-editor';
import {SelectControl, Spinner} from '@wordpress/components';

import './editor.scss';

export default function Edit(props) {
	const {
		attributes: {
			selectedForm,
			displayTitle,
		},
		setAttributes
	} = props;

	const blockProps = useBlockProps(
		{
			className: 'ctct-block-container'
		}
	)

	const theforms = useSelect((select) => {
		return select('core').getEntityRecords('postType', 'ctct_forms', {per_page: -1});
	}, []);
	let formEntryObjs;
	if (theforms) {
		formEntryObjs = theforms.map((form) => {
			return {label: form.title.rendered, value: form.id};
		});
		const isDisabled = (formEntryObjs && formEntryObjs.length === 0);
		formEntryObjs.unshift(
			{
				label: __('Select Form', 'constant-contact-forms'),
				value: 0,
				disabled: isDisabled,
			}
		)
	}
	let smMsg = (formEntryObjs && formEntryObjs.length > 0 ) ? __('Choose the form to display with the dropdown below.', 'constant-contact-forms' ) : __('Please create a Constant Contact Form.', 'constant-contact-forms');

	return (
		<div {...blockProps}>
			{!formEntryObjs && <Spinner />}
			{formEntryObjs &&
				<div>
					<div className="ctct-block-container--header">
						<img
							alt={__('Constant Contact Forms', 'constant-contact-forms')}
							src="https://images.ctfassets.net/t21gix3kzulv/78gf1S3CjPrnl9rURf6Q8w/3c20fb510dd4d4653feddf86ece35e1a/ctct_ripple_logo_horizontal_white_orange.svg"
						/>
					</div>
					<div className="ctct-block-container--selection">
						<small>{__('Display Form Title', 'constant-contact-forms')}</small>
						<div className="ctct-block-container--component">
							<SelectControl value={displayTitle} options={[
								{label: __('Display Title', 'constant-contact-forms'), value: 'true'},
								{label: __('Hide Title', 'constant-contact-forms'), value: 'false'},
							]} onChange={(displayTitle) => setAttributes({displayTitle})} />
						</div>
					</div>
					<div className="ctct-block-container--selection">
						<small>{smMsg}</small>
						<div className="ctct-block-container--component">
							<SelectControl
								label={__('Chosen form', 'constant-contact-forms')}
								value={selectedForm ?? ''}
								options={formEntryObjs}
								onChange={(selectedForm) => setAttributes({selectedForm})}
							/>
						</div>
					</div>
				</div>
			}
		</div>
	)
}
