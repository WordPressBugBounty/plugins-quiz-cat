<?php

class QuizCat_List_Table extends WP_List_Table {

	public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'quiz',
			'plural'   => 'quizzes',
			'ajax'     => false,
		) );
	}

	public function get_columns() {
		$columns = array(
			'title'    => _x( 'Quiz Name', 'Column label', 'quiz-cat' ),
			'shortcode'   => _x( 'Shortcode', 'Column label', 'quiz-cat' ),
			'type'   => _x( 'Quiz Type', 'Column label', 'quiz-cat' ),
			'questions'   => _x( 'Question Count', 'Column label', 'quiz-cat' ),
			'date' => _x( 'Date', 'Column label', 'quiz-cat' ),
		);
		
		if( function_exists( 'fca_qc_stats_page' ) &&  !defined( 'fca_qc_disable_activity' ) ) {
			$columns = array(
				'title'    => _x( 'Quiz Name', 'Column label', 'quiz-cat' ),
				'shortcode'   => _x( 'Shortcode', 'Column label', 'quiz-cat' ),
				'type'   => _x( 'Quiz Type', 'Column label', 'quiz-cat' ),
				'stats'   => _x( 'Activity (Starts/Optins/Shares)', 'Column label', 'quiz-cat' ),
				
				'questions'   => _x( 'Question Count', 'Column label', 'quiz-cat' ),
				'date' => _x( 'Date', 'Column label', 'quiz-cat' ),
			);
		}
		
		return $columns;
	}

	protected function column_default( $item, $column_name ) {
		$post_id = $item->ID;
		$activity = array();
		if( function_exists('fca_qc_get_activity') && !defined( 'fca_qc_disable_activity' ) ) {
			$activity = fca_qc_get_activity( $post_id, 'stats' );
			$activity['starts'] = empty ( $activity['starts'] ) ? 0 : $activity['starts'];
			$activity['optins'] = empty ( $activity['optins'] ) ? 0 : $activity['optins'];
			$activity['completions'] = empty ( $activity['completions'] ) ? 0 : $activity['completions'];
			$activity['shares'] = empty ( $activity['shares'] ) ? 0 : $activity['shares'];
		}
		
		switch ( $column_name ) {
			case 'shortcode':
				return '<input type="text" readonly="readonly" onclick="this.select()" value="[quiz-cat id=&quot;'. $post_id . '&quot;]"/>';
			case 'type':
				return fca_qc_get_quiz_type( $post_id );
			case 'starts':
				return "<a href='" . admin_url( "options.php?page=quiz-cat-stats") . "&quiz=$post_id" . "'>" . $activity['starts'] . "</a>";	
			case 'optins':
				return "<a href='" . admin_url( "options.php?page=quiz-cat-stats") . "&quiz=$post_id" . "'>" . $activity['optins'] . "</a>";				
			case 'shares':
				return "<a href='" . admin_url( "options.php?page=quiz-cat-stats") . "&quiz=$post_id" . "'>" . $activity['shares'] . "</a>";
			
			case 'stats':
				return "<a href='" . admin_url( "options.php?page=quiz-cat-stats") . "&quiz=$post_id" . "'>" . "{$activity['starts']}  - {$activity['optins']} - {$activity['shares']}" . "</a>";				
			case 'questions':
				$questions = get_post_meta( $post_id, 'quiz_cat_questions', true );
				return empty( $questions ) ? 0 : count( $questions );
			case 'date':
				$date_format = get_option( 'links_updated_date_format', 'Y/m/d \a\t g:i a' );
				return '<span>Published</span></br>' . date_i18n( $date_format, strtotime( $item->post_date ) );
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}
	
	protected function column_title( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// Build edit row action.
		$edit_query_args = array(
			'post'  => $item->ID,
			'action' => 'edit',
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( add_query_arg( $edit_query_args, 'post.php' ), $item->post_title ),
			_x( 'Edit', 'List table row action', 'quiz-cat' )
		);
		
		$clone_query =  add_query_arg( array(
			'post'  => $item->ID,
			'action' => 'clone',
			'fca_qc_nonce' => wp_create_nonce( 'fca_qc_clone' )
		));
		
		$actions['duplicate'] = "<a href='" . esc_url( $clone_query ) . "'>" . __('Copy', 'quiz-cat') . "</a>";
		
		// Build delete row action.
		$delete_query_args = array(
			'post'  => $item->ID,
			'action' => 'trash',
			'fca_qc_nonce' => wp_create_nonce( 'fca_qc_delete' )
		);
		
		if( function_exists( 'fca_qc_stats_page' ) &&  !defined ( 'fca_qc_disable_activity' ) ) {
			$actions['view_stats'] = "<a href='" . admin_url( "options.php?page=quiz-cat-stats") . "&quiz=$item->ID" . "'>" . esc_attr__( 'Stats', 'quiz-cat' ) . "</a>";
		}
		
		$actions['trash'] = sprintf(
			'<a class="ept-trash" href="%1$s" onclick="confirm( \'Are you sure?\' ) == false ? event.preventDefault() : null",>%2$s</a>',
			esc_url( add_query_arg( $delete_query_args ), $item->ID ),
			_x( 'Delete', 'List table row action', 'quiz-cat' ),
			
		);
		
		$view_link = get_permalink( $item );
		$actions['view'] = "<a target='_blank' href='$view_link'>" . __('View', 'quiz-cat') . "</a>";
		
		$title = empty( $item->post_title ) ? __("(Untitled Quiz)", 'quiz-cat') : $item->post_title;
		// Return the title contents.
		return sprintf( '<a class="row-title" href="' . esc_url( add_query_arg( $edit_query_args, 'post.php' ), $title ) . '">%1$s</a> <span style="display:none;">(id:%2$s)</span>%3$s',
			$title,
			$item->ID,
			$this->row_actions( $actions )
		);
	}

	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
		if ( 'trash' === $this->current_action() ) {
			$postID = empty( $_GET['post'] ) ? '' : intval( $_GET['post'] );
			$nonce = empty( $_GET['fca_qc_nonce'] ) ? '' : sanitize_text_field( $_GET['fca_qc_nonce'] );
			$post_type = get_post_type( $postID );
			
			if( wp_verify_nonce( $nonce, 'fca_qc_delete' ) && $post_type == 'fca_qc_quiz' && current_user_can( 'edit_post', $postID ) ){
				wp_delete_post( $postID );
			} else {
				wp_die( 'Not authorized, please try logging in again' );
			}
		}
		
		if ( 'clone' === $this->current_action() ) {
			$postID = empty( $_GET['post'] ) ? '' : intval( $_GET['post'] );
			$nonce = empty( $_GET['fca_qc_nonce'] ) ? '' : sanitize_text_field( $_GET['fca_qc_nonce'] );
			$post_type = get_post_type( $postID );
			
			if( wp_verify_nonce( $nonce, 'fca_qc_clone' ) && $post_type == 'fca_qc_quiz' && current_user_can( 'edit_post', $postID ) ){
				fca_qc_clone_quiz( $postID );
			} else {
				wp_die( 'Not authorized, please try logging in again' );
			}
		}
		
	}

	function prepare_items() {

		$post_status = empty( $_GET['post_status'] ) ? '' : sanitize_text_field( $_GET['post_status'] );
		$search = empty( $_REQUEST['s'] ) ? '' : sanitize_text_field( wp_unslash( $_REQUEST['s'] ) );
		$quiz_type = empty( $_REQUEST['quiz_type'] ) ? '' : sanitize_text_field( wp_unslash( $_REQUEST['quiz_type'] ) );
		$month = empty( $_REQUEST['m'] ) ? '' : sanitize_text_field( wp_unslash( $_REQUEST['m'] ) );
	
		$per_page = 20;

		$columns  = $this->get_columns();
		$hidden   = array();

		$this->_column_headers = array( $columns, $hidden );

		$this->process_bulk_action();

    	$args = array(
			'post_status'	 => $post_status,
			'post_type'      => 'fca_qc_quiz',			
			'posts_per_page' => '-1'
        );
		
		if ( !empty( $search ) ) {
			$args['s'] = $search;
		}
		
		if ( !empty( $month ) && strlen( $month ) === 6 ) {
			$args['year']     = (int) substr( $month, 0, 4 );
			$args['monthnum'] = (int) substr( $month, 4, 2 );
		}

		$data = get_posts( $args );
		
		if ( !empty( $quiz_type ) ) {
			$data = array_filter( $data, function( $post ) use ( $quiz_type ) {
					$settings = get_post_meta( $post->ID, 'quiz_cat_settings', true );
					return !empty( $settings['quiz_type'] ) && $settings['quiz_type'] === $quiz_type;
				}
			);
		}

		$current_page = $this->get_pagenum();

		$total_items = count( $data );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}

	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'title'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}
	
	protected function extra_tablenav( $which ) {

		if ( 'top' !== $which ) {
			return;
		}

		global $wpdb;

		$current_type = empty( $_GET['quiz_type'] )	? '' : sanitize_text_field( wp_unslash( $_GET['quiz_type'] ) );
		$current_month = empty( $_GET['m'] ) ? '' : sanitize_text_field( wp_unslash( $_GET['m'] ) );
				
		$months = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
				FROM {$wpdb->posts}
				WHERE post_type = %s
				ORDER BY post_date DESC
				",
				'fca_qc_quiz'
			)
		);

		?>
		<div class="alignleft actions">
			<select name="m">
				<option value=""><?php esc_html_e( 'All dates' ); ?></option>
				<?php foreach ( $months as $month ) {

						$value = sprintf(
							'%04d%02d',
							$month->year,
							$month->month
						);

						$label = date_i18n(
							'F Y',
							mktime( 0, 0, 0, $month->month, 1, $month->year )
						);
						?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_month, $value ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
				<?php } ?>
			</select>
			<select name="quiz_type">
				<option value=""><?php esc_html_e( 'All Quiz Types', 'quiz-cat' ); ?></option>
				<option value="pt" <?php selected( $current_type, 'pt' ); ?>>
					<?php esc_html_e( 'Personality', 'quiz-cat' ); ?>
				</option>
				<option value="mc" <?php selected( $current_type, 'mc' ); ?>>
					<?php esc_html_e( 'Multiple Choice', 'quiz-cat' ); ?>
				</option>
				<option value="wq" <?php selected( $current_type, 'wq' ); ?>>
					<?php esc_html_e( 'Weighted Answers', 'quiz-cat' ); ?>
				</option>
			</select>

			<?php
			submit_button(
				__( 'Filter', 'quiz-cat' ),
				'secondary',
				'filter_action',
				false
			);			
			?>
		</div>
		<?php $this->search_box( __( 'Search Quizzes', 'quiz-cat' ), 'quiz-search' ); ?>		
	<?php
	}
}