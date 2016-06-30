
(function($, ajaxurl){

	$selector 		= 'wpc_selector';
	var n_post 		= 0;
	var n_metabox 	= 0;
	var n_element 	= 0;

	var file_frame;

	// ajaxurl est fourni par WP grâce à wp_localize_script()
	if( typeof ajaxurl != 'string'){
		var ajaxUrl = '/wp-admin/admin-ajax.php';
	}else{
		var ajaxUrl = ajaxurl;
	}


	// ================================
	// get total post of wpc
	// ================================
	function wpc_getMetaboxs(){
		// retourne le nombre d'elements disponibles dans la page
		n_metabox = jQuery('.wpc_container').length;
	}
	function wpc_getElements(){
		// retourne le nombre d'elements disponibles dans la page
		n_element = jQuery('.wpc_element').length;
	}

	// ================================
	// requete template
	// ================================


	function wpc_getTemplate( type, folder, file, structure ){

		structure.replace(/ /g,'');
		var structureArray = structure.split(',');
		var contentLength = structureArray.length;

		var data = {
			'action': 'wpcomponent_getNewBox',
			'type': type,
			'folder': folder,
			'file' : file,
			'n_metabox': n_metabox
		};

	    $.post( ajaxUrl, data, function(response) {

	    	$( '#post-body-content' ).append( response );

	    	window.setTimeout(function() {


				if( n_element === 0 )$('.wpc_container').first().addClass('wpc-first');

	    		// pour chaque structure de type content, on init un tinymce
	    		for (index = 0; index < contentLength; ++index) {


	    			var new_editor = "wpc_editor_" + parseInt( parseInt( n_metabox * 1000 ) + parseInt( index + 1 ) );


					if( $.trim(structureArray[ index ]) === "editor" ){

			    		// on test si l'editeur a déja été instancié
			    		instance = false;
			    		$.each( tinymce.editors, function(e){

			    			if( this.id === new_editor ){
			    				// deja instancié 
			    				instance = true;
			    			}
			    		});


			    		if( instance === true ){
			    			tinymce.EditorManager.execCommand('mceAddEditor',true, new_editor);
			    		}else{
							tinyMCEPreInit.mceInit[ 'content' ].selector = '#' + new_editor;
							tinyMCEPreInit.qtInit[ 'content' ].id = new_editor;
							tinymce.init(tinyMCEPreInit.mceInit[ 'content' ]);
							quicktags( tinyMCEPreInit.qtInit[ 'content' ] );
			    		}
			    		instance = false;


					}

					
				}

				// ----------------------------------------------
				// Initialisation des switchers
				// ----------------------------------------------
				var elems = jQuery('.js-switch');

				for (var i = 0; i < elems.length; i++) {
					var switchery = new Switchery(elems[i], { color: '#3A56F3',size: 'small' });
				}
				// ----------------------------------------------
				// On change on envoit l'option ajax
				// ----------------------------------------------
				elems.on('change',function(){

					var data = {
						'action': 'wpcomponent_setSetting',
						'option': jQuery(this).attr('name'),
						'value': jQuery(this).is(':checked')
					};

					jQuery.post( ajaxurl, data );
				})


	    	},100);

			n_metabox++;

	    });

	}

	// ================================
	// Choix du dossier
	// ================================
	$(document).on('change','#wpcomponent_folder_selector', function(e){

		$('#wpc_selector .inside ol').hide();
		$('#wpc_selector .inside ol#' + $(this).val()).show();

		console.log('test');

	});


	// ================================
	// instanciate image uploader
	// ================================
	var selectedButton, imageRemover;

	$(document).on('click','.wpc_element_image .upload_image_button', function(e) {
 
        e.preventDefault();
 
 		selectedButton = $(this);
 		imageRemover = $(this).closest('.wpc_element').find('.wpc_imageRemover');


        //If the uploader object has already been created, reopen the dialog
        if ( file_frame ) {
	      file_frame.open();
	      return;
	    }
 
	    // Create the media frame.
	    file_frame = wp.media.frames.file_frame = wp.media({
	      title: jQuery( this ).data( 'upload_image' ),
	      button: {
	        text: jQuery( this ).data( 'upload_image_button' ),
	      },
	      multiple: false  // Set to true to allow multiple files to be selected
	    });


	    // When an image is selected, run a callback.
	    file_frame.on( 'select', function() {

			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();

			$('<img>', {
			    src: attachment.sizes.thumbnail.url
			}).insertBefore( selectedButton );


			selectedButton.closest('.wpc_element')
			.find('.wpc_image_id').attr('value', attachment.id );

			selectedButton.hide();
			imageRemover.show();
	    });

	    // Finally, open the modal
	    file_frame.open();
 
    });

	// ================================
	// instanciate image remover
	// ================================

	$(document).on('click','.wpc_imageRemover', function(e) {

		e.preventDefault();

		$(this).closest('.wpc_element').find('img').remove();
		$(this).closest('.wpc_element').find('.upload_image_button').show();
		$(this).closest('.wpc_element').find('.wpc_image_id').attr('value','');
		$(this).hide();

	});

	// ================================
	// selector click
	// ================================

	$(document).on('click', '#wpc_selector a', function( e ){

		e.preventDefault();

		var type = $(this).data('type');
		var folder = $(this).data('folder');
		var file = $(this).data('file');
		var structure = $(this).data('structure');

		wpc_getTemplate( type, folder, file, structure );

		return false;

	});


	// fix iframe height
	// todo : cibling wpcomponent handlediv
	$(document).on('click', '.postbox-container .postbox .handlediv', function(){

		var postbox = $(this).closest('.postbox');
		
		if( !postbox.hasClass('closed') )
		{
			jQuery.each( tinyMCE.editors , function(i,e){
				$newHeight = jQuery(e.contentDocument.activeElement).outerHeight()
				tinyMCE.editors[i].theme.resizeTo('100%', $newHeight);
			});
		}

	});


    jQuery(window).load(function($){
		var elems = document.querySelectorAll('.js-switch');

		for (var i = 0; i < elems.length; i++) {
			var switchery = new Switchery(elems[i], { color: '#3A56F3',size: 'small' });
		}
		wpc_getMetaboxs();
		wpc_getElements();
    });

	// ================================
	// link post type selector
	// ================================
	$(document).on('change', '.wpc_link_posttype_selector', function(e){
		
		e.preventDefault();

		$selecteur = jQuery(this).closest('select');

		var data = {
			'action': 'wpcomponent_getPosts_byType',
			'type': jQuery(this).val()
		};

	    $.post( ajaxUrl, data, function(response) {

	    	$post = $selecteur.closest('.inner').find('select').eq(1);
	    	// on supprime toute les options
	    	$post.find('option').remove();
	    	$post.append( response );

	    });

	});

// ------------------------------------------------------------------
// Actions
// ------------------------------------------------------------------

	// ================================
	// Metabox handler
	// ================================
	$(document).on('click', '.wpc_sidebar-handle', function(e){

		e.preventDefault();

		var postbox = $(this).closest('.wpc');

		if( postbox.hasClass('closed') )
		{
			// je réaffiche les contenus
			postbox.removeClass('closed');
		}
		else
		{
			postbox.addClass('closed');
		}

		// dans tous les cas, je ferme les settings
		postbox.removeClass('settings-active');
		e.stopPropagation();
		
	});

	// ================================
	// Settings
	// ================================
	// on regarde s'il y a des settings, autrement on disable le bouton
	$(document).ready(function(){
		$.each( $('.wpc'), function(e){
			if($(this).find('.wpc_settings .wp-core-ui').length === 0 ){
				$(this).find('.wpc_settings-handler').addClass('disable');
			}
		})
	})
	$(document).on('click','.wpc_sidebar-actions .wpc_settings-handler', function(e) {
		e.preventDefault();
		if( !$(this).hasClass('disable') ){
			$(this).closest('.wpc')
				.toggleClass('settings-active');
		}
	});

	// ================================
	// Remove wpc Element
	// ================================
	// delete
	$(document).on('click','.wpc_remove_element .remover a', function(e) {
		e.preventDefault();
		$(this).closest('.wpc_remove_element').find('.confirm').show();
		$(this).hide();
	});
	// confirm
	$(document).on('click','.wpc_remove_element .confirm .delete', function(e) {
		e.preventDefault();

		var isFirst = false;
		var buttonRemove = $(this);
		var elements = buttonRemove.closest('.wpc_remove_element').data('elements');

		var data = {
			'action': 'wpcomponent_deleteElements',
			'elements': encodeURIComponent(elements),
			'parent': jQuery('#post_ID').val()
		};


		if( elements != '' ){

			$.post(ajaxurl , data, function( response ) {

					if( buttonRemove.closest('.wpc_container').hasClass('wpc_container-first') ) isFirst = true;

					buttonRemove.closest('.wpc_container').remove();
					
					/** 
					 * Si c'est le premier on hérite la class first sur le suivant
					 *
					 */
					jQuery('#post-body-content')
						.find('.wpc_container')
						.first()
						.addClass('wpc_container-first');

					// on supprimer aussi tout les editeurs tiny mce
					$.each( buttonRemove.closest('.wpc_container').find('input[name="wpc_post_[]"]'), function(e){
						tinymce.EditorManager.execCommand('mceRemoveEditor',true, $(this).val() );
					});

					wpc_getElements();

			});

		}else{
			// buttonRemove.closest('.wpc_container').remove();
			buttonRemove.closest('.wpc_remove_element').find('.confirm').hide();
			buttonRemove.closest('.wpc_remove_element').find('.remover a').show();
		}


	});
	// cancel
	$(document).on('click','.wpc_remove_element .confirm .cancel', function(e) {
		e.preventDefault();
		$(this).closest('.wpc_remove_element').find('.confirm').hide();
		$(this).closest('.wpc_remove_element').find('.remover a').show();
	});


})(jQuery, ajaxurl);