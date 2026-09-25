<?php

function fca_qc_share_string ( $quiz_id = 0, $result_title = '{{MY_QUIZ_RESULT}}' ) {
	
	$quiz_text_strings = fca_qc_set_quiz_text_strings( $quiz_id );
	$quiz_title = get_the_title( $quiz_id );	
	return esc_attr( str_replace ( "'",  "&#39;", ( apply_filters( 'fca_qc_share_text', $quiz_text_strings['i_got'] . " \"$result_title\" - $quiz_title" ) ) )  );
}


function fca_qc_convert_entities ( $array ) {
	$array = is_array($array) ? array_map('fca_qc_convert_entities', $array) : html_entity_decode( $array, ENT_QUOTES );

	return $array;
}

//INFO SPAN
function fca_qc_info_span( $text = '', $link = '' ) {
	if ( empty( $link ) ) {
		return "<span class='fca_qc_info_span'>" . esc_attr( $text ) . "</span>";
	} else {
		return "<span class='fca_qc_info_span'><a class='fca_qc_api_link' href='" . esc_url( $link ) . "' target='_blank'>" . esc_attr( $text ) . "</a></span>";
	}
}

//OUTPUTS HTML FOR IMAGE ADD/CHANGE
function fca_qc_add_image_input( $img = '', $name = '', $id = '', $alt = '' ) {
	$id_attr = empty( $id ) ? '' : "id='" . esc_attr( $id ) . "'";
	ob_start(); ?>	
	<div class="fca_qc_image_input_div fca_qc_image_input_div_<?php echo esc_attr( $name ); ?>" >
		<?php echo fca_qc_input( $name, '', $img, 'hidden', $id_attr ) ?>
		<a href='#' 
			title='<?php esc_attr_e('Adds an image (optional).  For best results, use images at least 250px wide and use the same image resolution for each image you add to an answer.', 'quiz-cat') ?>' 
			class='fca_qc_quiz_image_upload_btn'><?php esc_attr_e('Add Image', 'quiz-cat') ?></a>
		<?php 
		if( $name === 'quiz_description_image_src' ) {
			echo fca_qc_input( 'description_image_alt', '', $alt, 'hidden' );
		} else {
			echo fca_qc_input( 'alt', '', $alt, 'hidden' );
		}			
		?>
		<img class='fca_qc_image' style='max-width: 252px' src='<?php echo esc_attr( $img )?>'>
			
		<div class='fca_qc_image_hover_controls'>
			<button type='button' class='button-secondary fca_qc_quiz_image_change_btn'><?php esc_attr_e('Change', 'quiz-cat') ?></button>
			<button type='button' class='button-secondary fca_qc_quiz_image_revert_btn'><?php esc_attr_e('Remove', 'quiz-cat') ?></button>
		</div>	
	</div>
	<?php
	
	return ob_get_clean();
}

//RETURN GENERIC INPUT HTML
function fca_qc_input( $name = '', $placeholder = '', $value = '', $type = 'text', $atts = '' ) {

	$name = esc_attr( $name );
	$placeholder = esc_attr( $placeholder );
	$value = esc_attr( $value );

	switch ( $type ) {

		case 'checkbox':
			$checked = !empty( $value ) ? "checked='checked'" : '';

			$html = "<div class='onoffswitch'>";
				$html .= "<input $atts style='display:none;' type='checkbox' id='fca_qc[$name]' class='onoffswitch-checkbox fca-qc-input-$type fca-qc-$name' name='fca_qc[$name]' $checked>";
				$html .= "<label class='onoffswitch-label' for='fca_qc[$name]'><span class='onoffswitch-inner' data-content-on='ON' data-content-off='OFF'><span class='onoffswitch-switch'></span></span></label>";
			$html .= "</div>";
			break;

		case 'shortcode':
			$html = "<input $atts type='text' placeholder='$placeholder' class='fca_qc_input_wide fca_qc_shortcode_input' name='fca_qc_shortcode_input' value='$value'></input>";
			break;

		case 'textarea':
			$html = "<textarea placeholder='$placeholder' class='fca_qc_question_texta' $atts >$value</textarea>";
			break;

		case 'translation':
			$html = "<input $atts type='text' placeholder='$placeholder' class='fca_qc_translation_title' name='fca_qc_" . $name . "_translation' value='$value'>";
			break;

		default:
			$html = "<input $atts type='$type' placeholder='$placeholder' class='fca-qc-input-$type fca-qc-$name' name='fca_qc_$name' value='$value'>";
	}

	return $html;
}

function fca_qc_add_wysiwyg ( $value = '', $name = '' ) {
	ob_start();?>
		<div class='fca-wysiwyg-nav' style='display:none'>
			<div class="fca-wysiwyg-group fca-wysiwyg-text-group">
				<button type="button" data-wysihtml5-command="bold" class="fca-nav-bold fca-nav-rounded-left" ><span class="dashicons dashicons-editor-bold"></span></button>
				<button type="button" data-wysihtml5-command="italic" class="fca-nav-italic fca-nav-no-border" ><span class="dashicons dashicons-editor-italic"></span></button>
				<button type="button" data-wysihtml5-command="underline" class="fca-nav-underline fca-nav-rounded-right" ><span class="dashicons dashicons-editor-underline"></span></button>
			</div>
			<div class="fca-wysiwyg-group fca-wysiwyg-alignment-group">
				<button type="button" data-wysihtml5-command="justifyLeft" class="fca-nav-justifyLeft fca-nav-rounded-left" ><span class="dashicons dashicons-editor-alignleft"></span></button>
				<button type="button" data-wysihtml5-command="justifyCenter" class="fca-nav-justifyCenter fca-nav-no-border" ><span class="dashicons dashicons-editor-aligncenter"></span></button>
				<button type="button" data-wysihtml5-command="justifyRight" class="fca-nav-justifyRight fca-nav-rounded-right" ><span class="dashicons dashicons-editor-alignright"></span></button>
			</div>			
			<div class="fca-wysiwyg-group fca-wysiwyg-link-group">
				<button type="button" data-wysihtml5-command="createLink" style="border-right: 0;" class="fca-wysiwyg-link-group fca-nav-rounded-left"><span class="dashicons dashicons-admin-links"></span></button>
				<button type="button" data-wysihtml5-command="unlink" class="fca-wysiwyg-link-group fca-nav-rounded-right"><span class="dashicons dashicons-editor-unlink"></span></button>
				<div class="fca-wysiwyg-url-dialog" data-wysihtml5-dialog="createLink" style="display: none">
					<input data-wysihtml5-dialog-field="href" value="http://">
					<a class="button button-secondary button-small" data-wysihtml5-dialog-action="cancel"><?php esc_attr_e('Cancel', 'quiz-cat') ?></a>
					<a class="button button-primary button-small" data-wysihtml5-dialog-action="save"><?php esc_attr_e('OK', 'quiz-cat') ?></a>
				</div>
			</div>			
			<button class="fca-wysiwyg-view-html action" type="button" data-wysihtml5-action="change_view">HTML</button>	
		</div>
		<textarea class='fca-wysiwyg-html fca-qc-input-wysi fca-qc-<?php echo esc_attr( $name )?>' name='<?php echo esc_attr( $name )?>'><?php echo esc_attr( $value ) ?></textarea>
	<?php
	return ob_get_clean();
}

function fca_qc_sanitize_text( $data ) {
	
	if ( is_array ( $data ) ) {
		forEach ( $data as $k => $v ) {
			$data[ $k ] = fca_qc_sanitize_text( $v );
		}
		return $data;
	}
	
	$data = sanitize_text_field( $data );
		
	return $data;

}

function fca_qc_kses_html( $data ) {
	$allowed_tags = wp_kses_allowed_html( 'post' );
	//ADD VIDEO/EMBEDS
	$allowed_tags['iframe'] = array( 'src' => true, 'width' => true, 'height' => true, 'frameborder' => true );
	
	if ( is_array ( $data ) ) {
		forEach ( $data as $k => $v ) {
			$data[ $k ] = fca_qc_kses_html( $v );
		}
		return $data;
	}
	
	$data = wp_kses( $data, $allowed_tags );
		
	return $data;

}
	
function fca_qc_tooltip( $text = 'Tooltip', $icon = 'dashicons dashicons-editor-help' ) {
	return "<span class='$icon fca_qc_tooltip' title='" . htmlentities($text) . "'></span>";
}

//GDPR STUFF
function fca_qc_is_gdpr_country( $accept_language = '' ) {
	$accept_language = empty( $accept_language ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : $accept_language;
	$gdpr_countries = array(
		"AT",
		"BE",
		"BG",
		"CY",
		"CZ",
		"DE",
		"DK",
		"EE",
		"EL",
		"ES",
		"FI",
		"FR",
		"HR",
		"HU",
		"IE",
		"IT",
		"LT",
		"LU",
		"LV",
		"MT",
		"NL",
		"PL",
		"PT",
		"RO",
		"SE",
		"SI",
		"SK",
		"UK",
		"GL",
		"GF",
		"PF",
		"TF",
		"GP",
		"MQ",
		"YT",
		"NC",
		"RE",
		"BL",
		"MF",
		"PM",
		"WF",
		"AW",
		"AN",
		"BV",
		"AI",
		"BM",
		"IO",
		"VG",
		"KY",
		"FK",
		"FO",
		"GI",
		"MS",
		"PN",
		"SH",
		"GS",
		"TC",
	);
		
	$code = '';
	//in some cases like "fr" or "hu" the language and the country codes are the same
	if ( strlen( $accept_language ) === 2 ){
		$code = strtoupper( $accept_language ); 
	} else if ( strlen( $accept_language ) === 5 ) {          
		$code = strtoupper( substr( $accept_language, 3, 5 ) ); 
	} 
	if ( in_array( $code, $gdpr_countries ) ) {
		return true;
	}
	
	if ( strlen( $accept_language ) > 5 ) {
		
		for ( $i=0; $i+2 < strlen( $accept_language ); $i++ ){
			$code = strtoupper( substr( $accept_language, $i, $i+2 ) );
			if ( in_array( $code, $gdpr_countries ) ) {
				return true;
			}
		}
	}
	return false;
}

function fca_qc_get_quiz_type( $quiz_id ){

	$settings = get_post_meta( $quiz_id, 'quiz_cat_settings', true );
	$quiz_type = empty ( $settings['quiz_type'] ) ? 'mc' : $settings['quiz_type'];
	
	switch ( $quiz_type ) {
		case 'pt':
			return '<span class="fca-qc-color2 fca_qc_quiz_type_label"><span class="dashicons dashicons-admin-users"></span> Personality Test</span>';
		case 'wq':
			return '<span class="fca-qc-color3 fca_qc_quiz_type_label"><span class="dashicons dashicons-chart-bar"></span> Weighted Answers</span>';
		default:
			return '<span class="fca-qc-color1 fca_qc_quiz_type_label"><span class="dashicons dashicons-clipboard"></span> Multiple Choice</span>';
	}
}

function fca_qc_show_gdpr_checkbox(){
	$gdpr_checkbox = get_option( 'fca_qc_gdpr_checkbox' );
	if ( !empty( $gdpr_checkbox ) ) {
		$gdpr_locale = get_option( 'fca_qc_gdpr_locale' );
		if ( empty( $gdpr_locale ) ) {
			return true;
		}
		return fca_qc_is_gdpr_country();
	}
	
	return false;
}

function fca_qc_clone_quiz( $to_duplicate ) {
			
	global $wpdb;
	$post = get_post( $to_duplicate );	
	
	if ( empty( $post ) ) {
		return;
	}	
	
	$new_post_id = wp_insert_post( array(
		'post_content'   => $post->post_content,
		'post_name'      => '',
		'post_status'    => 'publish',
		'post_title'     => $post->post_title . ' copy',
		'post_type'      => $post->post_type,
	) );
	
	if ( is_wp_error( $new_post_id ) || empty($new_post_id) ) {
		return;
	}

	$post_meta_infos = $wpdb->get_results( $wpdb->prepare(
		"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $to_duplicate
	) );

	$rows   = array();
	$values = array();

	foreach ( $post_meta_infos as $meta_info ) {
		if ( $meta_info->meta_key === '_wp_old_slug' ) continue;

		$rows[]   = '(%d, %s, %s)';
		$values[] = $new_post_id;
		$values[] = $meta_info->meta_key;
		$values[] = $meta_info->meta_value;
	}

	if ( $rows ) {
		$wpdb->query( $wpdb->prepare(
			"INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES " . implode( ', ', $rows ),
			$values
		) );
	}
	
	echo "<script>window.location='" . admin_url( 'post.php' ) . "?post=$new_post_id&action=edit" . "'</script>";
	exit;

}

function fca_qc_hex_to_rgb( $hex ) {
    $hex = ltrim( $hex, '#' );

    if ( strlen( $hex ) === 3 ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    if ( strlen( $hex ) !== 6 ) {
        return '0, 0, 0'; // fallback for bad input
    }

    $r = hexdec( substr( $hex, 0, 2 ) );
    $g = hexdec( substr( $hex, 2, 2 ) );
    $b = hexdec( substr( $hex, 4, 2 ) );

    return "$r, $g, $b";
}