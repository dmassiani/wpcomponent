(function($){

	// ajaxurl est fourni par WP grâce à wp_localize_script()
	if( typeof ajaxurl != 'string'){
		var ajaxUrl = '/wp-admin/admin-ajax.php';
	}else{
		var ajaxUrl = ajaxurl;
	}



	jQuery(document).ready(function(){

		//
		//
		// SWITCHERS

		// ----------------------------------------------
		// Initialisation des switchers
		// ----------------------------------------------
		var elems = jQuery('.js-switch-settings');

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
		
		// -----------------------------------------------

		//
		//
		// TABS

		// ----------------------------------------------
		// Initialisation des tabs de l'admin
		// ----------------------------------------------
		jQuery('.wpc_admin--main .wpc_admin_tabs').eq(0).show();
		jQuery('.wpc_admin--nav a').on('click', function(e){
			e.preventDefault();
			jQuery('.wpc_admin--nav a').removeClass('active');
			$tabs = jQuery(this).attr('href');
			jQuery(this).addClass('active');
			jQuery('.wpc_admin--main .wpc_admin_tabs').hide();
			jQuery('.wpc_admin--main').find('.wpc_admin_tabs--' + $tabs ).show();
		})

		// -----------------------------------------------

		//
		//
		// CHECKUP

		var checkupStart = function(){

			var data = {
				'action': 'wpcomponent_checkup'
			};

			$.post( ajaxUrl, data, function(response) {

				console.log(response);
				jQuery('.wpc_admin--reponse').append(response);

			});
		}

		// ----------------------------------------------
		// Initialisation du démarrage
		// ----------------------------------------------
		jQuery('#wpc-start').on('click', function(e){
			e.preventDefault();
			checkupStart();
		})
	})


})(jQuery);