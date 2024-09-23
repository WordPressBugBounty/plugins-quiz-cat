<?php


// ADD OUR RECOMMENDED MENU
function fca_qc_featured_plugins_menu() {

	add_submenu_page( 'edit.php?post_type=fca_qc_quiz', __('Featured Plugins', 'quiz-cat'), __('Featured Plugins', 'quiz-cat'), 'manage_options', 'fca-featured-plugins', 'fca_qc_render_featured_plugins' );
	
}
add_action( 'admin_menu', 'fca_qc_featured_plugins_menu' );

function fca_qc_render_featured_plugins(){
	$content = '<figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/facebook-conversion-pixel/
</div></figure><figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/easy-pricing-tables/
</div></figure><figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/landing-page-cat/
</div></figure><figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/analytics-cat/
</div></figure>';

	?>
	<style>
		.fca-featured-plugins {
			
		}
		
		.fca-featured-plugins > figure {
			display: inline-block;
			margin: 0px 6px;
			vertical-align: top;
		}		
	</style>
	<div class="wrap">
		<h2><?php esc_html_e( 'Featured Plugins', 'quiz-cat' ) ?></h2>	
		<p><?php esc_html_e( 'Problems, Suggestions?', 'quiz-cat' ) ?> 
		<a href="https://wordpress.org/support/plugin/quiz-cat" target="_blank"><?php esc_html_e( 'Visit the support forum', 'quiz-cat' ) ?></a> | 
		<a href="https://fatcatapps.com/article-categories/quiz-cat/" target="_blank"><?php esc_html_e( 'Knowledge Base', 'quiz-cat' ) ?></a> | 
		<a href="https://youtu.be/CQe3VsX_Xag" target="_blank"><?php esc_html_e( 'Watch Demo', 'quiz-cat' ) ?></a> |
		<a href="http://fatcatapps.com/quizcat/" target="_blank"><?php esc_html_e( 'Get Quiz Cat Premium', 'quiz-cat' ) ?></a>
		</p>		
		<div class="fca-featured-plugins">
			<?php echo apply_filters( 'the_content', $content ) ?>
		</div>
	</div>	
	<?php
}

function fca_qc_admin_review_notice() {
	
	$action = empty( $_GET['fca_qc_review_notice'] ) ? false : sanitize_text_field( $_GET['fca_qc_review_notice'] );
	
	if( $action ) {
		
		$nonce = empty( $_GET['fca_qc_nonce'] ) ? false : sanitize_text_field( $_GET['fca_qc_nonce'] );
		$nonceVerified = wp_verify_nonce( $nonce, 'fca_qc_leave_review' );
		if( $nonceVerified == false ) {
			wp_die( "Unauthorized. Please try logging in again." );
		}
		
		update_option( 'fca_qc_show_review_notice', false );
		if( $action == 'review' ) {
			echo "<script>document.location='https://wordpress.org/support/plugin/quiz-cat/reviews/'</script>";
		}
				
		if( $action == 'later' ) {
			//MAYBE MAKE SURE ITS NOT ALREADY SET
			if( wp_next_scheduled( 'fca_qc_schedule_review_notice' ) == false ) {
				wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_qc_schedule_review_notice' );
			}
		}
		
		if( $action == 'dismiss' ) {
			//DO NOTHING
		}		
	}	
	
	$show_review_option = get_option( 'fca_qc_show_review_notice', null );
	if ( $show_review_option === null ) {
	
		//MAYBE MAKE SURE ITS NOT ALREADY SET
		if( wp_next_scheduled( 'fca_qc_schedule_review_notice' ) == false ) {
			wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_qc_schedule_review_notice' );
		}
		add_option( 'fca_qc_show_review_notice', false );
	}
	
	if( $show_review_option ) {

		$nonce = wp_create_nonce( 'fca_qc_leave_review' );
		$review_url = esc_url( add_query_arg( array( 'fca_qc_review_notice' => 'review', 'fca_qc_nonce' => $nonce ) ) );
		$postpone_url = esc_url( add_query_arg( array( 'fca_qc_review_notice' => 'later', 'fca_qc_nonce' => $nonce ) ) );
		$forever_dismiss_url = esc_url( add_query_arg( array( 'fca_qc_review_notice' => 'dismiss', 'fca_qc_nonce' => $nonce ) ) );

		echo '<div id="fca-qc-review-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
		
			echo '<p>' . __( "Hi! You've been using Quiz Cat for a while now, so who better to ask for a review than you? Would you please mind leaving us one? It really helps us a lot!", 'quiz-cat' ) . '</p>';
			
			echo "<a href='$review_url' class='button button-primary' style='margin-top: 2px;'>" . __( 'Leave review', 'quiz-cat' ) . "</a> ";
			echo "<a style='position: relative; top: 10px; left: 7px;' href='$postpone_url' >" . __( 'Maybe later', 'quiz-cat' ) . "</a> ";
			echo "<a style='position: relative; top: 10px; left: 16px;' href='$forever_dismiss_url' >" . __( 'No thank you', 'quiz-cat' ) . "</a> ";
			echo '<br style="clear:both">';
			
		echo '</div>';
	
	}

}
add_action( 'admin_notices', 'fca_qc_admin_review_notice' );

function fca_qc_enable_review_notice(){
	update_option( 'fca_qc_show_review_notice', true );
	wp_clear_scheduled_hook( 'fca_qc_schedule_review_notice' );
}
add_action ( 'fca_qc_schedule_review_notice', 'fca_qc_enable_review_notice' );

//DEACTIVATION SURVEY
function fca_qc_admin_deactivation_survey( $hook ) {
	if ( $hook === 'plugins.php' ) {
		
		ob_start(); ?>
		
		<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
			<p style="font-size: 14px; font-weight: bold; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php esc_attr_e( 'Sorry to see you go', 'quiz-cat' ) ?></p>
			<p><?php esc_attr_e( 'Hi, this is David, the creator of Quiz Cat. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'quiz-cat' ) ?>
			</p>
			<p><?php esc_attr_e( 'I have a quick question that I hope you’ll answer to help us make Quiz Cat better: what made you deactivate?', 'quiz-cat' ) ?>
			</p>
			<p><?php esc_attr_e( 'You can leave me a message below. I’d really appreciate it.', 'quiz-cat' ) ?>
			</p>
			<p><b><?php esc_attr_e( 'If you\'re upgrading to Quiz Cat Premium and have questions or need help, click <a href=' . 'https://fatcatapps.com/article-categories/gen-getting-started/' . ' target="_blank">here</a></b>', 'quiz-cat' ) ?>
			</p>
			<p><textarea style='width: 100%;' id='fca-qc-deactivate-textarea' placeholder='<?php esc_attr_e( 'What made you deactivate?', 'quiz-cat' ) ?>'></textarea></p>
			<div style='float: right;' id='fca-deactivate-nav'>
				<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-qc-deactivate-skip'><?php esc_attr_e( 'Skip', 'quiz-cat' ) ?></button>
				<button type='button' class='button button-primary' id='fca-qc-deactivate-send'><?php esc_attr_e( 'Send Feedback', 'quiz-cat' ) ?></button>
			</div>
		
		</div>
		
		<?php
			
		$html = ob_get_clean();
		
		$data = array(
			'html' => $html,
			'nonce' => wp_create_nonce( 'fca_qc_uninstall_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);
					
		wp_enqueue_script('fca_qc_deactivation_js', FCA_QC_PLUGINS_URL . '/includes/notices/deactivation.min.js', false, FCA_QC_PLUGIN_VER, true );
		wp_localize_script( 'fca_qc_deactivation_js', "fca_qc", $data );
	}
	
	
}	
add_action( 'admin_enqueue_scripts', 'fca_qc_admin_deactivation_survey' );

//UNINSTALL ENDPOINT
function fca_qc_uninstall_ajax() {
	
	$msg = sanitize_text_field( $_POST['msg'] );
	$nonce = sanitize_text_field( $_POST['nonce'] );
	$nonceVerified = wp_verify_nonce( $nonce, 'fca_qc_uninstall_nonce') == 1;

	if ( $nonceVerified && !empty( $msg ) ) {
		
		$url =  "https://api.fatcatapps.com/api/feedback.php";
				
		$body = array(
			'product' => 'quizcat',
			'msg' => $msg,		
		);
		
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'body' => json_encode( $body ),	
			'blocking'    => true,
			'sslverify'   => false
		); 		
		
		$return = wp_remote_post( $url, $args );
		
		wp_send_json_success( $msg );

	}
	wp_send_json_error( $msg );

}
add_action( 'wp_ajax_fca_qc_uninstall', 'fca_qc_uninstall_ajax' );

function fca_qc_upgrade_menu() {
	$page_hook = add_submenu_page(
		'edit.php?post_type=fca_qc_quiz',
		esc_attr__('Upgrade to Premium', 'quiz-cat'),
		esc_attr__('Upgrade to Premium', 'quiz-cat'),
		'manage_options',
		'quiz-cat-upgrade',
		'fca_qc_upgrade_ob_start'
	);
	add_action('load-' . $page_hook , 'fca_qc_upgrade_page');
}
add_action( 'admin_menu', 'fca_qc_upgrade_menu' );

function fca_qc_upgrade_ob_start() {
    ob_start();
}

function fca_qc_upgrade_page() {
    wp_redirect('https://fatcatapps.com/quizcat/upgrade?utm_medium=plugin&utm_source=Quiz%20Cat%20Free&utm_campaign=free-plugin', 301);
    exit();
}

function fca_qc_upgrade_to_premium_menu_js() {
    ?>
    <script type="text/javascript">
    	jQuery(document).ready(function ($) {
            $('a[href="edit.php?post_type=fca_qc_quiz&page=quiz-cat-upgrade"]').click( function () {
        		$(this).attr('target', '_blank')
            })
        })
    </script>
    <style>
        a[href="edit.php?post_type=fca_qc_quiz&page=quiz-cat-upgrade"] {
            color: #6bbc5b !important;
        }
        a[href="edit.php?post_type=fca_qc_quiz&page=quiz-cat-upgrade"]:hover {
            color: #7ad368 !important;
        }
    </style>
    <?php 
}
add_action( 'admin_footer', 'fca_qc_upgrade_to_premium_menu_js');
