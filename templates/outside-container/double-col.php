<?php
/*
Template Name: Double col
Description: Section with 2 titles, 2 contents and 2 illustrations
---------------------------------------------------------------------
{"type": "title", "name": "Left title", "slug": "left_title"}
{"type": "editor", "name": "Left content", "slug": "left_content"}
---------------------------------------------------------------------
*/


// load css flexbox grid bootstrap compatible
wp_enqueue_style( 'flexboxgrid', '//cdn.jsdelivr.net/flexboxgrid/6.1.1/flexboxgrid.min.css' );
?>
<article class="hentry">

	<header class="entry-header">
		<h1 class="entry-title">
			<?php the_chapter_title( 'left_title' ) ?>
		</h1>
		<h2>Template Plugin</h2>
	</header>
	<div class="entry-content">
		<?php the_chapter( 'left_content' ) ?>
	</div>

</article>