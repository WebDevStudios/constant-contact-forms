import {__} from '@wordpress/i18n';
import {useSelect} from '@wordpress/data';
import {useBlockProps, InspectorControls} from '@wordpress/block-editor';
import {SelectControl, Spinner, PanelBody, ExternalLink} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import {addQueryArgs} from '@wordpress/url';
import logo from './logo/ctct-logo-horizontal-color-reversed.svg';

import './editor.scss';

export default function Edit(props) {
	const {
		attributes: {
			selectedForm,
			displayTitle,
		},
		setAttributes,
		isSelected
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
	let smMsg = (formEntryObjs && formEntryObjs.length > 1 ) ? __('Choose the form to display with the dropdown below.', 'constant-contact-forms' ) : __('Please create a Constant Contact Form.', 'constant-contact-forms');

	const getURL = (form) => {
		const adminRoot = ajaxurl.replace(/\/admin-ajax\.php$/, '/post.php');
		return addQueryArgs(
			adminRoot,
			{
				post  : form,
				action: 'edit',
			}
		)
	}

	return (
		<div {...blockProps}>
			{!formEntryObjs && <Spinner />}
			{isSelected ? (
				<div className="ctct-block-container-edit">
					<div className="ctct-block-container--header">
						<img
							alt={__('Constant Contact Forms', 'constant-contact-forms')}
							src={logo}
						/>
					</div>
					<div className="ctct-block-container--selection">
						<div className="ctct-block-container--component">
							<SelectControl
								label={__('Display Form Title', 'constant-contact-forms')}
								value={displayTitle}
								options={[
									{label: __('Display Title', 'constant-contact-forms'), value: 'true'},
									{label: __('Hide Title', 'constant-contact-forms'), value: 'false'},
								]}
								onChange={(displayTitle) => setAttributes({displayTitle})}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
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
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</div>
					</div>
				</div>
			) : (
				<div className="ctct-block-container-preview">
					<ServerSideRender
						block="constant-contact/single-contact-form"
						attributes={{
							selectedForm,
							displayTitle
						}}
					/>
				</div>
			)
			}
			<InspectorControls>
				<PanelBody
					title={__('Form settings', 'constant-contact-forms')}
				>
						{(Number.isInteger(parseInt(selectedForm)) && parseInt(selectedForm) > 0
								?
								<ExternalLink href={getURL(selectedForm)}>{__('Edit selected form', 'cptuiext')}</ExternalLink>
								: <div>{__('Please select a form', 'constant-contact-forms')}</div>
						)}
				</PanelBody>
			</InspectorControls>
		</div>
	)
}
