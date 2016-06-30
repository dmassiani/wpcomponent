<?php
/*
Template Name: Double col
Description: Section with 2 titles, 2 contents and 2 illustrations
---------------------------------------------------------------------
{"type": "title", "name": "Left title", "slug": "left_title"}
{"type": "editor", "name": "Left content", "slug": "left_content"}
---------------------------------------------------------------------
*/

?>
<article class="hentry">

	<header class="entry-header">
		<h1 class="entry-title">
			<?php the_wpcomponent( 'left_title' ) ?>
		</h1>
	</header>
	<div class="entry-content">
		<?php the_wpcomponent( 'left_content' ) ?>
	</div>

</article>