<?php

// ******************************************************
//
// Remover des contenus
//
// ******************************************************

class wpcomponent_structure extends wpcomponent_kickstarter
{

	public $folder;
	public $files;
	public $fileHeader = array(
		'Name' => 'Template Name', 
		'Description' => 'Description'
	);
	public $folder_exist = false;

	// class access
	public $utility;

	public function __construct(){

		$this->themeFolder = get_stylesheet_directory() . '/' . WPCOMPONENT_FOLDER;
		$this->pluginFolder = WPCOMPONENT_DEFAULT_TEMPLATE;

		if( is_dir( $this->themeFolder ) ){
			$this->files = scandir( $this->themeFolder );
			$this->folder_exist = true;
		}else{
			$this->files = array();
			$this->folder_exist = false;
		}

		$this->utility = new wpcomponent_utility();
		$this->currentFolder = $this->themeFolder;
		
	}

	// ----------------------------------------------------------------
	// Cette fonction est utilisé pour connaitre la structure d'un dossier (fichier et dossier enfant)
	// ----------------------------------------------------------------

	public function dir_to_array($dir) { 

	   	$result = array(); 

		if( !is_dir($dir) ){
			return false;
		}

		$cdir = scandir($dir);

		foreach ($cdir as $key => $value) 
		{ 
		  if (!in_array($value,array(".",".."))) 
		  { 
		     if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
		     { 
		        $result[$value] = $this->dir_to_array($dir . DIRECTORY_SEPARATOR . $value); 
		     } 
		     else 
		     { 
		        $result[] = $value; 
		     } 
		  } 
		} 

		return $result; 
	} 


	// ---------------------------------------------------------------
	// Cette fonction scanne un dossier en particulier et retourne les json des contents
	// ---------------------------------------------------------------

	public function wpcomponent_scan_folder($content, $folder){

		$filesFounded = [];

		foreach ($content as $value) {

			$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $value );


			if( isset($file_parts['extension']) && $file_parts['extension'] === "php" ){

				$default = get_file_data( $this->currentFolder .'/'. $folder .'/'. $value,  $this->fileHeader );
				$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $value );

				if( is_array($jsons) ):


					$elements = [];
					foreach( $jsons as $key => $json ):

							if( $this->utility->isJSON($json)):
								$elements[] = json_decode($json);
							endif;

					endforeach;


					$tJson = array(					
						'name'			=> 		$default[ 'Name' ], 
						'description'	=> 		$default[ 'Description' ],
						'file' 			=>		$file_parts['basename'],
						'elements' 		=> 		$elements
					);

					$filesFounded[] = json_encode( $tJson );

				endif;

			}

		}
		 
    	return $filesFounded;

	}


    // ============================================================
    // Load PLUGIN Templates
    // ============================================================

	public function wpcomponent_register_Plugin_folder(){


		// on prend on dossier et on le transforme en tableau de contenance dossier -> fichier
    	$pluginFolder = $this->dir_to_array($this->pluginFolder);

    	if( is_array( $pluginFolder ) ) {

			foreach ($pluginFolder as $key => $value) {

				if( gettype($value) === "array" ){

					// si dans le dossier scanner des dossiers enfant on retrouve les fichiers
					// et on les transforme en json contenant les informations du fichier
					// json utilisable ensuite pour générer les metabox de content
					$this->currentFolder = $this->pluginFolder;
					$pluginFolder[$key] = $this->wpcomponent_scan_folder( $value, $key );

				}

			}

			return $pluginFolder;	
    		
    	}else{
    		return false;
    	}
	}

    // ============================================================
    // Load THEMES Templates
    // ============================================================

	public function wpcomponent_register_Theme_folder(){

    	if( $this->folder_exist === false ){
    		return false;
    	}

		// on prend on dossier et on le transforme en tableau de contenance dossier -> fichier
    	$themesFolder = $this->dir_to_array($this->themeFolder);


		foreach ($themesFolder as $key => $value) {

			if( gettype($value) === "array" ){

				// si dans le dossier scanner des dossiers enfant on retrouve les fichiers
				// et on les transforme en json contenant les informations du fichier
				// json utilisable ensuite pour générer les metabox de content

				$this->currentFolder = $this->themeFolder;
				$themesFolder[$key] = $this->wpcomponent_scan_folder( $value, $key );

			}

		}

		return $themesFolder;	
	}

    public function wpcomponent_register_templates(){

		
		// on prend on dossier et on le transforme en tableau de contenance dossier -> fichier
    	$themesFolder = $this->dir_to_array($this->themeFolder);

		foreach ($themesFolder as $key => $value) {

			if( gettype($value) === "array" ){

				// si dans le dossier scanner des dossiers enfant on retrouve les fichiers
				// et on les transforme en json contenant les informations du fichier
				// json utilisable ensuite pour générer les metabox de content

				$themesFolder[$key] = $this->wpcomponent_scan_folder( $value, $key );

			}

		}

		return $themesFolder;

    }

    public function wpcomponent_realTemplates(){

    	$templates = $this->wpcomponent_register_templates();

        $elements = [];

        foreach ($templates as $template):
        	// pour chaque macro template

            $template = json_decode($template);

        	$name = sanitize_title( $template->name );
        	$elements[ $name ] = (array) $template->elements;


        endforeach;

        return $elements;

    }

    public function wpcomponent_get_template_structure( $name ){

    	if( $this->folder_exist === false ){
    		return false;
    	}

		foreach ($this->files as $value) {
			$file_parts = pathinfo( $value );

			if( $file_parts['extension'] === "php" ){

				$jsons 	 = $this->utility->get_file_data( $this->folder . '/' . $value );

				$elements = [];
				foreach( $jsons as $key => $json ):

						$elements[] = json_decode($json);

				endforeach;

			}

		}

		return $elements;
    }

    public function wpcomponent_get_fileStructure( $type, $folder, $file ){


    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );


   			if( $file_parts['extension'] === "php" ){

				$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );

				$element = [];

				foreach( $jsons as $key => $json ):

						$element[] = json_decode($json);

				endforeach;


			}	

		return $element;
    }

    public function wpcomponent_getFileStructure( $type, $folder, $file ){

    	// type = theme ou plugin : désigne si le template est situé dans le theme ou le plugin
    	// folder = dossier dans lequel est le template. Ex : plugin / folder / file ou theme / folder / file
    	// file est le nom du fichier/template

    	// return array of structure

    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );


   			if( $file_parts['extension'] === "php" ){

				$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );

				$element = [];
    			$structure = [];

				foreach( $jsons as $key => $json ):

						$element = json_decode($json);
						if( is_object($element) ):
							$structure[] = $element->type;
						endif;

				endforeach;


			}	

		return $structure;

    }

    public function wpcomponent_getFileSlugs( $type, $folder, $file ){

    	// return array of slugs

    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );



   			if( $file_parts['extension'] === "php" ){

				$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );

				$element = [];
    			$structure = [];

				foreach( $jsons as $key => $json ):

						$element = json_decode($json);
						if( is_object($element) ):
							$structure[] = $element->slug;
						endif;

				endforeach;


			}	

		return $structure;
    }

    public function wpcomponent_getSelectOption( $type, $folder, $file, $slug ){


    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );

    	$exist = false;


			if( $file_parts['extension'] === "php" ){

			$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );


			foreach( $jsons as $key => $json ):

					$element = json_decode($json);
					if( $element->slug === $slug ){
						return $element->choice;
					}

			endforeach;


		}

		return false;

    }

    public function wpcomponent_getFileNames( $type, $folder, $file ){

    	// return array of slugs

    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );



   			if( $file_parts['extension'] === "php" ){

				$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );

				$element = [];
    			$structure = [];

				foreach( $jsons as $key => $json ):

						$element = json_decode($json);
						if( is_object($element) ):
							$structure[] = $element->name;
						endif;

				endforeach;


			}	

		return $structure;
    }

    public function wpcomponent_getFileTemplate( $type, $folder, $file ){

    	// return array of slugs

    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );


		if( $file_parts['extension'] === "php" ){

			$default = get_file_data(  $this->currentFolder .'/'. $folder .'/'. $file,  $this->fileHeader );
			return $default[ 'Name' ];

		}	

    }

    public function wpcomponent_getNameFileSlug( $type, $folder, $file, $slug ){

    	// return array of slugs

    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );


			if( $file_parts['extension'] === "php" ){

			$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );


			foreach( $jsons as $key => $json ):

					$element = json_decode($json);
					if( $element->slug === $slug ){
						return $element->name;
					}

			endforeach;


		}	

    }

    public function wpcomponent_slugType( $type, $folder, $file, $slug ){


    	if( $type === 'theme' ){
    		$this->currentFolder = $this->themeFolder;
    	}else{
    		$this->currentFolder = $this->pluginFolder;
    	}

    	$file_parts = pathinfo( $this->currentFolder .'/'. $folder .'/'. $file );

    	$exist = false;


			if( $file_parts['extension'] === "php" ){

			$jsons 	 = $this->utility->get_file_data( $this->currentFolder .'/'. $folder .'/'. $file );


			foreach( $jsons as $key => $json ):

					$element = json_decode($json);
					if( $element->slug === $slug ){
						return $element->type;
					}

			endforeach;


		}

		return false;

    }

}