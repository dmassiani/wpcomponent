
(function($, ajaxurl){

	$rapper 	= '·__rapper',
	$selector 	= 'wpc__selector';
	var n__post 		= 0;

	var n__metabox 		= 0;
	var n__element 		= 0;

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
	function getMetaboxs(){
		// retourne le nombre d'elements disponibles dans la page
		n__metabox = jQuery('.wpc_container').length;
	}
	function getElements(){
		// retourne le nombre d'elements disponibles dans la page
		n__element = jQuery('.wpc_element').length;
	}

	// ================================
	// requete template
	// ================================


	function getTemplate( type, folder, file, structure ){

		structure.replace(/ /g,'');
		var structureArray = structure.split(',');
		var contentLength = structureArray.length;


		var data = {
			'action': 'WPComponent__getNewBox',
			'type': type,
			'folder': folder,
			'file' : file,
			'n__metabox': n__metabox
		};

	    $.post( ajaxUrl, data, function(response) {

	    	$( '#post-body-content' ).append( response );

	    	window.setTimeout(function() {


				if( n__element === 0 )$('.wpc_container').first().addClass('wpc-first');

	    		// pour chaque structure de type content, on init un tinymce
	    		for (index = 0; index < contentLength; ++index) {


	    			var new__editor = "wpc__editor__" + parseInt( parseInt( n__metabox * 1000 ) + parseInt( index + 1 ) );


					if( $.trim(structureArray[ index ]) === "editor" ){

			    		// on test si l'editeur a déja été instancié
			    		instance = false;
			    		$.each( tinymce.editors, function(e){

			    			if( this.id === new__editor ){
			    				// deja instancié 
			    				instance = true;
			    			}
			    		});


			    		if( instance === true ){
			    			tinymce.EditorManager.execCommand('mceAddEditor',true, new__editor);
			    		}else{
							tinyMCEPreInit.mceInit[ 'content' ].selector = '#' + new__editor;
							tinyMCEPreInit.qtInit[ 'content' ].id = new__editor;
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
						'action': 'wpcomponent__setSetting',
						'option': jQuery(this).attr('name'),
						'value': jQuery(this).is(':checked')
					};

					jQuery.post( ajaxurl, data );
				})


	    	},100);

			n__metabox++;

	    });

	}

	// ================================
	// Choix du dossier
	// ================================
	$(document).on('change','#wpcomponent__folder__selector', function(e){

		$('#wpc__selector .inside ol').hide();
		$('#wpc__selector .inside ol#' + $(this).val()).show();

		console.log('test');

	});


	// ================================
	// instanciate image uploader
	// ================================
	var selectedButton, imageRemover;

	$(document).on('click','.wpc__element__image .upload_image_button', function(e) {
 
        e.preventDefault();
 
 		selectedButton = $(this);
 		imageRemover = $(this).closest('.wpc_element').find('.wpc__imageRemover');


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
			.find('.wpc__image__id').attr('value', attachment.id );

			selectedButton.hide();
			imageRemover.show();
	    });

	    // Finally, open the modal
	    file_frame.open();
 
    });

	// ================================
	// instanciate image remover
	// ================================

	$(document).on('click','.wpc__imageRemover', function(e) {

		e.preventDefault();

		$(this).closest('.wpc_element').find('img').remove();
		$(this).closest('.wpc_element').find('.upload_image_button').show();
		$(this).closest('.wpc_element').find('.wpc__image__id').attr('value','');
		$(this).hide();

	});

	// ================================
	// selector click
	// ================================

	$(document).on('click', '#wpc__selector a', function( e ){

		e.preventDefault();

		var type = $(this).data('type');
		var folder = $(this).data('folder');
		var file = $(this).data('file');
		var structure = $(this).data('structure');

		getTemplate( type, folder, file, structure );

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
		getMetaboxs();
		getElements();
    });

	// ================================
	// link post type selector
	// ================================
	$(document).on('change', '.wpc__link__posttype__selector', function(e){
		
		e.preventDefault();

		$selecteur = jQuery(this).closest('select');

		var data = {
			'action': 'WPComponent__getPosts_byType',
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
	$(document).on('click','.wpc__remove__element .remover a', function(e) {
		e.preventDefault();
		$(this).closest('.wpc__remove__element').find('.confirm').show();
		$(this).hide();
	});
	// confirm
	$(document).on('click','.wpc__remove__element .confirm .delete', function(e) {
		e.preventDefault();

		var buttonRemove = $(this);
		var elements = buttonRemove.closest('.wpc__remove__element').data('elements');

		var data = {
			'action': 'WPComponent__deleteElements',
			'elements': encodeURIComponent(elements),
			'parent': jQuery('#post_ID').val()
		};

		if( elements != '' ){

			$.post(ajaxurl , data, function(response) {

					buttonRemove.closest('.wpc_container').remove();
					
					// on supprimer aussi tout les editeurs tiny mce
					$.each( buttonRemove.closest('.wpc_container').find('input[name="wpc__post__[]"]'), function(e){

						tinymce.EditorManager.execCommand('mceRemoveEditor',true, $(this).val() );
			
					});

					getElements();

			});

		}else{
			buttonRemove.closest('.wpc_container').remove();
		}


	});
	// cancel
	$(document).on('click','.wpc__remove__element .confirm .cancel', function(e) {
		e.preventDefault();
		$(this).closest('.wpc__remove__element').find('.confirm').hide();
		$(this).closest('.wpc__remove__element').find('.remover a').show();
	});


})(jQuery, ajaxurl);