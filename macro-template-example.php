<?php
/*
Template Name: Section Beautiful Product
Description: Section with 2 titles, 2 contents and 2 illustrations
---------------------------------------------------------------------
{"type": "title", "name": "Left title", "slug": "left_title"}
{"type": "editor", "name": "Left content", "slug": "left_content"}
{"type": "image", "name": "Left illustration", "slug": "left_illustration"}
{"type": "title", "name": "Right title", "slug": "right_title"}
{"type": "editor", "name": "Right content", "slug": "right_content"}
{"type": "image", "name": "Right illustration", "slug": "right_illustration"}
{"type": "link", "name": "Page", "slug": "page"}
{"type": "option", "name": "Option couleur", "slug": "color"}
{"type": "option-number", "name": "Option couleur en chiffre", "slug": "colorNumber"}
{"type": "option-switch", "name": "Option couleur en switch", "slug": "colorSwitch"}
{"type": "option-select", "name": "Option couleur en selecteur", "slug": "colorSelect", "choice": "test, test2, test3"}
---------------------------------------------------------------------
*/
/*
==============================================================================

	Place this file into --yourtheme--/wpcomponent/macro-template-name.php 
	and go to admin to see your new content management

	LINK 
	link to any content
	
	OPTION
	add simple input text

	OPTION-NUMBER
	add simple input type number

	OPTION-SWITCH
	add boolean switcher
	return on / off

	OPTION-SELECT
	add selectbox
	list of <option> : "choice": "first option, second option, third option"

==============================================================================
*/
?>
Option : 

<?php the_wpcomponent( 'color' ) ?>

Option Not Echo :

<?php $option = get_wpcomponent( 'color' ) ?>

<?php echo $option ?>

<section> 
	<h1>		<?php the_wpcomponent( 'left_title' ) ?></h1>
	<article>	<?php the_wpcomponent( 'left_content' ) ?></article>
	<aside>		<?php the_wpcomponent( 'left_illustration' ) ?></aside>
	<article>	<?php the_wpcomponent( 'right_content' ) ?></article>
	<aside>		<?php the_wpcomponent( 'right_illustration' ) ?></aside>
</section>