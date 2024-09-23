
( function( blocks, editor, element ) {

	var el  = element.createElement
	var quizzes = fca_qc_gutenblock_script_data.quizzes

	blocks.registerBlockType( 'quiz-cat/gutenblock', {
		title: 'Quiz Cat Quiz',
		icon: 'welcome-learn-more',
		category: 'widgets',
		supports: {			
			customClassName: false,			
			html: false,
		},
		keywords: ['quiz', 'quizzes', 'test' ],
		edit: function( props ) {
			return [
				el(
					wp.blockEditor.InspectorControls,
					{ 
						key: 'qc-controls'
					},	
					el( wp.components.PanelBody, { },					
						el( wp.components.SelectControl,
							{	
								
								label: 'Select a Quiz',
								value: props.attributes.post_id,
								options: quizzes,
								onChange: function( newValue ){ props.setAttributes({ post_id: newValue }) }
							}
						)
					),
					el( wp.components.PanelBody, { },
						el( wp.components.ButtonGroup, {},
							props.attributes.post_id == 0 ? '' : 
							el(
								wp.components.Button,
								{	
									href: fca_qc_gutenblock_script_data.editurl + '?post=' + props.attributes.post_id + '&action=edit',
									target: '_blank',
									variant: "secondary",
									style: {
										margin: '0 6px 0 0'
									}
								},
								'Edit Quiz'
							),
							el(
								wp.components.Button,
								{	
									href: fca_qc_gutenblock_script_data.newurl,
									target: '_blank',
									variant: "primary"
									
								},
								'New Quiz'
							)
						)
					)
				),
				el( wp.serverSideRender, {
					key: 'qc_ssr',
					block: 'quiz-cat/gutenblock',
					attributes:  props.attributes,
				})
			]
		},

		save: function( props ) {
			return null
		},
	} )
}(
	window.wp.blocks,
	window.wp.editor,
	window.wp.element
))