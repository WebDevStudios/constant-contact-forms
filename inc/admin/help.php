<?php
/**
* Constant Contact Support Questions.
*
* @package ConstantContact
* @subpackage Support
* @author Pluginize
* @since 1.0.0
*/
function constant_contact_help_page() {
	?>
	<h2><?php esc_attr_e( 'Help / FAQ', 'constantcontact' ); ?></h2>
	<?php
	//constantcontact_admin()->page_tabs();

	$helps = array(
		array(
			'title' => __( 'Help', 'constantcontact' ),
			'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
		),
		array(
			'title' => __( 'Help 2', 'constantcontact' ),
			'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
		),
	);

	$faqs = array(
		array(
			'title' => __( 'Faq', 'constantcontact' ),
			'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
		),
		array(
			'title' => __( 'Faq 2', 'constantcontact' ),
			'content' => __( 'plugin is a of plugin by WebDevStudios', 'constantcontact' ),
		),
	);
	?>
	<style>
		#ctct-support li {
			padding: 0.8em 0;
		}

		#ctct-support ol li {
			list-style: none;
		}

		#ctct-support li {
			position: relative;
		}

		#ctct-support .answer {
			padding: 0.5em;
		}

		#ctct-support .question {
			font-size: 18px;
			font-weight: bold;
		}

		#ctct-support .question:before {
			content: "\f139";
			display: inline-block;
			font: normal 25px/1 'dashicons';
			margin-left: -25px;
			position: absolute;
			-webkit-font-smoothing: antialiased;
		}

		#ctct-support .question.active:before {
			content: "\f140";
		}
	</style>

	<div class="wrap">
		<table id="ctct-support" class="form-table cptui-table">
		<tr>
			<td class="outter" width="50%">
				<h2><?php esc_html_e( 'Help', 'constantcontact' ); ?></h2>
				<ol id="help_ctct">
				<?php foreach ( $helps as $help ) : ?>
					<li>
						<span tabindex="0" class="question" aria-controls="q1" aria-expanded="false"><?php echo esc_html( $help['title'] ); ?></span>
						<div class="answer"><?php echo esc_html( $help['content'] ); ?></div>
					</li>
				<?php endforeach; ?>
				</ol>
			</td>
			<td class="outter">
				<h2><?php esc_html_e( 'Faq', 'constantcontact' ); ?></h2>
				<ol id="faq_ctct">
				<?php foreach ( $faqs as $faq ) : ?>
					<li>
						<span tabindex="0" class="question" aria-controls="q1" aria-expanded="false"><?php echo esc_html( $faq['title'] ); ?></span>
						<div class="answer"><?php echo esc_html( $faq['content'] ); ?></div>
					</li>
				<?php endforeach; ?>
				</ol>
			</td>
		</tr>
		</table>
	</div>

	<?php wp_enqueue_script( 'ctct_form' );

}
constant_contact_help_page();
