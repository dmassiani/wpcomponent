<?php


// ******************************************************
//
// Génération des éditeurs par ajax
//
// ******************************************************


class wpcomponent_editors
{

	public $metabox_id = 1000;
	public $element_id = 1;

	// ID est uniquement défini lors d'une mise à jour du champ
	public $ID;

    public $template;
    public $structure;
    public $n_element;
    public $n_metabox;
    public $name;
    public $container_id;
    public $switch_id;
    public $content;
    public $type;
    public $folder_type;
    public $folder;
    public $slug;
    public $slug_ID;
    public $ajax;
    public $file;
    public $update = false;
    public $images_id;
    public $elementsRemove;
    public $postID;
    public $options;
    public $disable;


	public function getNewBox(){

		if( $this->n_metabox != 0 )$this->update = true;
		$this->metabox_id = $this->metabox_id * ( $this->n_metabox + 1 );

		$wpc_structure = new wpcomponent_Structure();

		$structureArray = $wpc_structure->wpcomponent_getFileStructure( $this->folder_type, $this->folder, $this->file );
		$slugsArray = $wpc_structure->wpcomponent_getFileSlugs( $this->folder_type, $this->folder, $this->file );
		$namesArray = $wpc_structure->wpcomponent_getFileNames( $this->folder_type, $this->folder, $this->file );
		$this->template = $wpc_structure->wpcomponent_getFileTemplate( $this->folder_type, $this->folder, $this->file );

		$this->openMetabox( $this->n_metabox );

			foreach ($structureArray as $key => $element):

				$this->element_id = $this->metabox_id + ( $key + 1 );
				$new_editor = "wpc_editor_" . $this->element_id;
				$this->container_id = $new_editor;
				$new_switch = "wpc_switch_" . $this->element_id;
				$this->switch_id = $new_switch;

				$this->slug = $slugsArray[ $key ];
				$this->name = $namesArray[ $key ];

				switch ( trim($element) ) {
					case 'image':
						$this->getNewImage();
						break;

					case 'editor':
						$this->getNewEditor();
						break;

					case 'title':
						$this->getNewTitle();
						break;

					case 'link':
						$this->getNewLink();
						break;

					case 'number':
						$this->getNewNumber();
						break;

					case 'option':
						$this->getNewOption();
						break;

					case 'option-number':
						$this->getNewOption('number');
						break;

					case 'option-switch':
						$this->getNewOption('switch');
						break;

					case 'option-select':
						$this->getNewOption('select');
						break;

					default:
						$this->getNewEditor();
				}


			endforeach;

		$this->closeMetabox();

	}

	public function getOptions() {
		if( $this->options === NULL ){
			echo 'No options';
		}else{
			echo $this->options;
		}
	}

	public function hasOptions() {
		if( !empty($this->options) ){
			return true;
		}else{
			return false;
		}
	}

	public function openMetabox( $n_metabox ){

		$first = '';
		// $metaboxStory = '';

		if( $n_metabox === 0 )$first = ' wpc_container-first';
		if( $this->ajax === true )$metaboxStory = ' wpc';
		$this->metabox_id = 1000 * ( $n_metabox + 1 );

		?>


		<div class="wpc_container<?=$first?>" id="postbox-container-<?=$this->metabox_id?>">
	        <div class="meta-box-sortables" id="wpc_container--template--<?=$this->metabox_id?>">
	            <div class="postbox wpc closed">

					<!-- Wrapper -->
					<div class="wpc_wrapper">

						<header class="wpc_header">
							<h3 class="hndle">
								<span>
									<i class="icon -dragger"></i> <?=ucfirst($this->folder)?> : <strong><?=$this->template?></strong>
									<?php
									/**
									 * set visually disable
									 *
									 */
									if( $this->disable === 'on' ){
									?>
									[ <?php _e('disable', 'wpcomponent') ?> ]
									<?php
									}
									?>
								</span>
							</h3>
						</header>

						<!-- inside -->
		                <div class="wpc_content inside">

							<input type="hidden" name="wpcomponent_template_[]" value="<?=$this->template?>">
							<input type="hidden" name="wpcomponent_folder_type_[]" value="<?=$this->folder_type?>">
							<input type="hidden" name="wpcomponent_folder_[]" value="<?=$this->folder?>">
							<input type="hidden" name="wpcomponent_file_[]" value="<?=$this->file?>">
				    		<input type="hidden" name="metabox_id[]" value="<?=$this->metabox_id?>">

							<?php
							wp_nonce_field( 'wpc_editor', 'wpcomponent_nonce' );
	}
	public function closeMetabox(){ ?>
							<div class="clear"></div>
						</div>
						<!--  End inside -->

						<div class="wpc_settings">
							<?php
								// we adding disable option
								$this->disableComponent();
								$this->getOptions();
							?>
						</div>

					</div>
					<!-- End Wrapper -->

					<!-- Sidebar -->
					<aside class="wpc_sidebar">

						<!-- Handle -->
						<div type="button" class="wpc_sidebar-handle handlediv">
							<span class="screen-reader-text"><?=ucfirst($this->folder)?> : <?=$this->template?></span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</div>
<!-- 	                	<div class="wpc_sidebar-handle handlediv">
	                	</div> -->
	                	<!-- / Handle -->

						<div class="wpc_sidebar-actions">

							<?php
							// if( $this->hasOptions() ){
							?>
							<a href="#" class="wpc_settings-handler">
								<i class="icon -settings"></i>
							</a>
							<?php
							// }
							?>

		                	<!-- Metabox Remover -->
							<div class="wpc_sidebar-remover wpc_remove_element" data-elements="<?=$this->elementsRemove?>">
								<ul>
									<li class="remover"><a href="#" class="submitdelete deletion">
										<i class="icon -trash"></i>
									</a></li>
									<li class="confirm">
										<?=_e('Are you sur?', 'wpcomponent')?>
										<a href="#" class="delete"><?=_e('Yes', 'wpcomponent')?></a>
										<a href="#" class="cancel"><?=_e('No', 'wpcomponent')?></a>
									</li>
								</ul>
							</div>
							<!-- / Metabox Remover -->

						</div>


					</aside>
					<!--  / Sidebar -->


	            </div>
	        </div>
        </div>
        <?php
	}

	public function openElement(){

		?>
		<div class="wpc_element wpc_element_<?=$this->type?>">

			<h2>
				<i class="icon -title"></i> <?=$this->name?>
			</h2>

			<input type="hidden" name="wpcomponent_type_[]" value="<?=$this->type?>">
			<input type="hidden" name="wpcomponent_slug_[]" value="<?=$this->slug?>">
			<input type="hidden" name="wpcomponent_post_[]" value="<?=$this->container_id?>">

    	<?php
    	// pour une mise à jour du champ
    	if( $this->update === true ){
    		?>
    		<input type="hidden" name="wpcomponent_slug_ID[]" value="<?=$this->slug_ID?>" />
    		<?php
    	}else{
    		?>
    		<input type="hidden" name="wpcomponent_slug_ID[]" />
    		<?php
    	}

	}

	/* ---------------------------------------------------

		Ferme un element input

	/* --------------------------------------------------- */

	public function closeElement(){
		?>
		</div>
		<?php
	}


	/* ---------------------------------------------------

		Nouvel editeur

	/* --------------------------------------------------- */

    public function getNewEditor()
    {
    	$this->type = 'editor';
    	$this->openElement();

		ob_start();
		wp_editor( $this->content, $this->container_id );
        echo ob_get_clean();

        $this->closeElement();

    }


	/* ---------------------------------------------------

		Nouveau titre

	/* --------------------------------------------------- */

    public function getNewTitle()
    {
    	$this->type = 'title';
    	$this->openElement();

    	?>

    	<div class="wpc_element-input wp-core-ui wp-title-wrap">
    		<div class="inner">

					<input type="text" id="<?=$this->container_id?>_title" name="wpcomponent_title_[]" value="<?=$this->content?>" class="text">

			</div>
		</div>
    	<?php

        $this->closeElement();

    }

	/* ---------------------------------------------------

		Nouveau nombre

	/* --------------------------------------------------- */

    public function getNewNumber()
    {
    	$this->type = 'number';
    	$this->openElement();

    	?>

    	<div class="wpc_element-input wp-core-ui wp-title-wrap">
    		<div class="inner">

					<input type="number" id="<?=$this->container_id?>_number" name="wpcomponent_number_[]" value="<?=$this->content?>" class="text">

			</div>
		</div>
    	<?php

        $this->closeElement();

    }

	/* ---------------------------------------------------

		Nouveau swicth

	/* --------------------------------------------------- */

    public function getNewSwitch()
    {
    	$this->type = 'switch';
    	$this->openElement();

    	?>

    	<div class="wpc_element-input wp-core-ui wp-title-wrap">
    		<div class="inner">
				<?php
					if( $this->content === 'on' ){
						$checked = 'checked';
					}else{
						$checked = '';
					}
					?>
					<input type="checkbox" name="wpcomponent_switch_[]" class="js-switch" <?=$checked?> />
			</div>
		</div>
    	<?php

        $this->closeElement();

    }

	/* ---------------------------------------------------

		Nouveau lien

	/* --------------------------------------------------- */

    public function getNewLink()
    {
    	$this->type = 'link';
    	$this->openElement();
    	$optionsType = '<option>Select post type</option>';
    	$selectType = null;
    	$selectPage = null;
		$options = '<option>Select post</option>';

    	if( !empty( $this->content ) && $this->content != 'selectpost' ){
				$selectType = get_post_type( $this->content );
				$selectPage = get_post( $this->content );
				// sometimes have an error view debug log
				$selectedPage = $selectPage->post_name;
    	}

    		// get all custom post type
		$args = array(
		   'public'   => true,
		   '_builtin' => true
		);

		$output = 'objects'; // names or objects, note names is the default

		$post_types = get_post_types( $args, $output );

		foreach ( $post_types as $post_type ) {

			$selected = ( $post_type->name === $selectType ) ? 'selected' : '';
			$optionsType.= '<option value="' . $post_type->name . '"' . $selected . '>' . $post_type->name . '</option>';

		}

		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

		$post_types = get_post_types( $args, $output );

		foreach ( $post_types  as $post_type ) {

			$selected = ( $post_type->name == $selectType ) ? 'selected' : '';
			$optionsType.= '<option value="' . $post_type->name . '"' . $selected . '>' . $post_type->name . '</option>';

		}


		// si j'ai une valeur enregistré, je génère les options de page :
		if( !empty( $this->content ) ){

	        $args = array(
	            'posts_per_page'   => -1,
	            'orderby'          => 'name',
	            'order'            => 'ASC',
	            'post_type'        => $selectType,
	            'post_status'      => 'publish'
	        );
	        $posts_array = get_posts( $args );

	        foreach ( $posts_array as $post_type ) {
	        	$selected = ( $post_type->ID == $this->content ) ? 'selected' : '';
	            $options.= '<option value="' . $post_type->ID . '"' . $selected . '>' . $post_type->post_title . '</option>';
	        }

		}


    	?>

    	<div class="wpc_element-input wp-core-ui wp-title-wrap">
    		<div class="inner">

					<select id="<?=$this->container_id?>_link_selector" class="wpc_link_posttype_selector">
						<?=$optionsType?>
					</select>

					<select name="wpcomponent_link_[]" id="<?=$this->container_id?>_link" class="disable">
						<?=$options?>
					</select>

			</div>
		</div>
    	<?php

        $this->closeElement();

    }


	/* ---------------------------------------------------

		Nouvelle image

	/* --------------------------------------------------- */

    public function getNewImage(){

    	$this->type = 'image';
    	$showRemover = '';
    	$hideUploader = '';
    	$this->openElement();

    	?>
    	<div class="wpc_element-input wp-core-ui wp-image-wrap">
    		<div class="inner">

		    	<?php

		    	if( !empty( $this->content ) && is_numeric( $this->content ) ){
		    		$showRemover = ' show';
		    		$hideUploader = ' hide';
		    		echo wp_get_attachment_image( $this->content, 'medium' );
		    	}

		    	?>
		    	<input type="hidden" name="wpcomponent_image_id[]" class="wpc_image_id" value="<?=$this->content?>" />

				<input data-upload_image="<?=_e('Meta content Image', 'wpcomponent')?>" data-upload_image_button="<?=_e('Select Image', 'wpcomponent')?>" id="<?=$this->container_id?>_image" class="upload_image_button button<?=$hideUploader?>" type="button" value="<?php _e('Upload Image', 'wpcomponent') ?>" />
				<div>
					<a href="#" class="wpc_imageRemover<?=$showRemover?>"><?php _e( 'Remove Image', 'wpcomponent' ) ?></a>
				</div>
			</div>

    	</div>
		<?php
		$this->closeElement();

    }


	/* ---------------------------------------------------

		Disable component

	/* --------------------------------------------------- */

    public function disableComponent()
    {
		ob_start();
		if( $this->disable === 'on' ){
			$disable = 'checked';
		}else{
			$disable = '';
		}
		?>
		<div class="wpc_element">

			<h2>
				<i class="icon -title"></i> <?php _e('Disable component', 'wpcomponent') ?>
			</h2>

	    	<div class="wpc_element-input wp-core-ui wp-title-wrap">
	    		<div class="inner">

	    				<input type="checkbox" name="wpcomponent_disable_<?php echo $this->metabox_id ?>" class="js-switch" <?php echo $disable ?>/>

				</div>
			</div>
		</div>
		<?php
        $this->options.= ob_get_contents();

		ob_get_clean();


    }

	/* ---------------------------------------------------

		Nouvelle option

	/* --------------------------------------------------- */

    public function getNewOption( $type = null )
    {


    	$this->type = 'option';
		ob_start();

    	$this->openElement();
    	?>
    	<div class="wpc_element-input wp-core-ui wp-title-wrap">
    		<div class="inner">

					<?php

						switch ( trim($type) ) {
							case 'number':
							?>
								<input type="number" name="wpcomponent_optionnumber_[]" value="<?=$this->content?>" class="text">
							<?php
								break;

							case 'switch':
							if( $this->content === 'on' ){
								$checked = 'checked';
							}else{
								$checked = '';
							}
							?>
								<input type="checkbox" name="wpcomponent_optionswitch_[]" class="js-switch" <?=$checked?> />
							<?php
								break;

							case 'select':
								$wpc_structure = new wpcomponent_Structure();
								$select_options = explode(',',$wpc_structure->wpcomponent_getSelectOption( $this->folder_type, $this->folder, $this->file, $this->slug ) );
								$value = $this->content;
							?>
							<select name="wpcomponent_optionselect_[]">
								<option><?php _e('Select a value', 'wpcomponent')?></option>
								<?php

									foreach ($select_options as $key => $val) {
										if( $value === trim($val) ){
										?>
										<option value="<?=$val?>" selected><?=ucfirst($val)?></option>
										<?php
										}else{
										?>
										<option value="<?=$val?>"><?=ucfirst($val)?></option>
										<?php
										}
									}

								?>
							</select>
							<?php
								break;

							default:
							?>
								<input type="text" name="wpcomponent_option_[]" value="<?=$this->content?>" class="text">
							<?php
						}

					?>


			</div>
		</div>
		<?php
        $this->closeElement();

        $this->options.= ob_get_contents();

		ob_get_clean();


    }


}
