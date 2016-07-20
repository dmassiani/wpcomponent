<?php
// ******************************************************
//
// Checkup
//
// ******************************************************

class wpcomponent_checkup
{

    // ============================================================
    // Init // current status : DEV
    // ============================================================


	public function init() {



		foreach ($theme_template as $key => $value) {


			// $key = folder
			// $value = component

			if( gettype($value) === "array" ){

				// log_it($value);
				// on affiche une hiérarchie
				echo '<h3>' . $key . '</h3>';

		        foreach ($value as $template):
		        	// pour chaque macro template

					$template = json_decode($template);

					// available
						// $template->name
						// $template->description
						// $template->file
						// $template->elements (liste des contenu et slug)

		        	echo '<h4>' . $template->name . '</h4>';


		        endforeach;
			}

		}

		die();

	}


}
