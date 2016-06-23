<?php


// ******************************************************
//
// Génération des éditeurs par ajax
//
// ******************************************************


class WPComponent__editors
{

	public $metabox__id = 1000;
	public $element__id = 1;

	// ID est uniquement défini lors d'une mise à jour du champ
	public $ID;

    public $template;
    public $structure;
    public $n__element;
    public $n__metabox;
    public $name;
    public $container__id;
    public $content;
    public $type;
    public $folder_type;
    public $folder;
    public $slug;
    public $ajax;
    public $file;
    public $update = false;
    public $images__id;
    public $elementsRemove;
    public $postID;
    public $options;


	public function getNewBox(){

		if( $this->n__metabox != 0 )$this->update = true;
		$this->metabox__id = $this->metabox__id * ( $this->n__metabox + 1 );

		$wpc__structure = new WPComponent__Structure();

		$structureArray = $wpc__structure->WPComponent__getFileStructure( $this->folder_type, $this->folder, $this->file );
		$slugsArray = $wpc__structure->WPComponent__getFileSlugs( $this->folder_type, $this->folder, $this->file );
		$namesArray = $wpc__structure->WPComponent__getFileNames( $this->folder_type, $this->folder, $this->file );
		$this->template = $wpc__structure->WPComponent__getFileTemplate( $this->folder_type, $this->folder, $this->file );

		$this->openMetabox( $this->n__metabox );

			foreach ($structureArray as $key => $element):

				$this->element__id = $this->metabox__id + ( $key + 1 );
				$new__editor = "wpc__editor__" . $this->element__id;	
				$this->container__id = $new__editor;

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

	public function openMetabox( $n__metabox ){

		$first = '';
		// $metaboxStory = '';

		if( $n__metabox === 0 )$first = ' wpc_container-first';
		if( $this->ajax === true )$metaboxStory = ' wpc';
		$this->metabox__id = 1000 * ( $n__metabox + 1 );

		?>


		<div class="wpc_container<?=$first?>" id="postbox-container-<?=$this->metabox__id?>">
	        <div class="meta-box-sortables" id="wpc__container--template--<?=$this->metabox__id?>">
	            <div class="postbox wpc closed">
	
					<!-- Wrapper -->
					<div class="wpc_wrapper">
						
						<header class="wpc_header">
							<h3 class="hndle">
								<span>
									<i class="icon -dragger"></i> <?=ucfirst($this->folder)?> : <strong><?=$this->template?></strong>
								</span>
							</h3>
						</header>

						<!-- inside -->
		                <div class="wpc_content inside">

							<input type="hidden" name="wpc__template__[]" value="<?=$this->template?>">
							<input type="hidden" name="wpc__folder_type__[]" value="<?=$this->folder_type?>">
							<input type="hidden" name="wpc__folder__[]" value="<?=$this->folder?>">
							<input type="hidden" name="wpc__file__[]" value="<?=$this->file?>">
				    		<input type="hidden" name="metabox__id[]" value="<?=$this->metabox__id?>">

							<?php
							wp_nonce_field( 'wpc__editor', 'wpcomponent__nonce' );
	} 
	public function closeMetabox(){ ?>
							<div class="clear"></div>
						</div>
						<!--  End inside -->

						<div class="wpc_settings">
							<?php $this->getOptions(); ?>
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
							if( $this->hasOptions() ){
							?>
							<a href="#" class="wpc_settings-handler">
								<i class="icon -settings"></i>
							</a>
							<?php
							}
							?>

		                	<!-- Metabox Remover -->
							<div class="wpc_sidebar-remover wpc__remove__element" data-elements="<?=$this->elementsRemove?>">
								<ul>
									<li class="remover"><a href="#" class="submitdelete deletion">
										<i class="icon -trash"></i>
									</a></li>
									<li class="confirm">
										<?=_e('Are you sur?')?>
										<a href="#" class="delete"><?=_e('Yes')?></a>
										<a href="#" class="cancel"><?=_e('No')?></a>
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
		<div class="wpc_element wpc__element__<?=$this->type?>">

			<h2>
				<i class="icon -title"></i> <?=$this->name?> 
			</h2>

			<input type="hidden" name="wpc__type__[]" value="<?=$this->type?>">
			<input type="hidden" name="wpc__slug__[]" value="<?=$this->slug?>">
			<input type="hidden" name="wpc__post__[]" value="<?=$this->container__id?>">

    	<?php
    	// pour une mise à jour du champ
    	if( $this->update === true ){
    		?>
    		<input type="hidden" name="wpc__ID[]" value="<?=$this->ID?>" />
    		<?php
    	}else{
    		?>
    		<input type="hidden" name="wpc__ID[]" />
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
		wp_editor( $this->content, $this->container__id );
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

					<input type="text" id="<?=$this->container__id?>_title" name="wpc__title__[]" value="<?=$this->content?>" class="text required">

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

    	if( !empty( $this->content ) ){
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

					<select id="<?=$this->container__id?>_link_selector" class="wpc__link__posttype__selector">
						<?=$optionsType?>
					</select>

					<select name="wpc__link__[]" id="<?=$this->container__id?>_link" class="disable">
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
		    	<input type="hidden" name="wpc__image__id[]" class="wpc__image__id" value="<?=$this->content?>" />
    		
				<input data-upload_image="<?=_e('Meta content Image')?>" data-upload_image_button="<?=_e('Select Image')?>" id="<?=$this->container__id?>_image" class="upload_image_button button<?=$hideUploader?>" type="button" value="Upload Image" />
				<div>
					<a href="#" class="wpc__imageRemover<?=$showRemover?>"><?php _e( 'Remove Image', 'macrocontenthammer' ) ?></a>
				</div>
			</div>

    	</div>
		<?php
		$this->closeElement();
 
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
								<input type="number" name="wpc__optionnumber__[]" value="<?=$this->content?>" class="text">
							<?php
								break;

							case 'switch':
							if( $this->content === 'on' ){
								$checked = 'checked';
							}else{
								$checked = '';
							}
							?>
								<input type="checkbox" name="wpc__optionswitch__[]" class="js-switch" <?=$checked?> />
							<?php
								break;

							case 'select':
								$wpc__structure = new WPComponent__Structure();
								$select_options = explode(',',$wpc__structure->WPComponent__getSelectOption( $this->folder_type, $this->folder, $this->file, $this->slug ) );
								$value = $this->content;
							?>
							<select name="wpc__optionselect__[]">
								<option>Select a value</option>
								<?php

									foreach ($select_options as $key => $val) {
										if( $value === $val ){
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
								<input type="text" name="wpc__option__[]" value="<?=$this->content?>" class="text">
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