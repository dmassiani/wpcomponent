<?php
/*
Template Name: Triple col
Description: Section with 2 titles, 2 contents and 2 illustrations
---------------------------------------------------------------------
{"type": "title", "name": "Left title", "slug": "left_title"}
{"type": "editor", "name": "Left content", "slug": "left_content"}
{"type": "title", "name": "Right title", "slug": "right_title"}
{"type": "editor", "name": "Right content", "slug": "right_content"}
---------------------------------------------------------------------
*/


// load css flexbox grid bootstrap compatible
wp_enqueue_style( 'flexboxgrid', '//cdn.jsdelivr.net/flexboxgrid/6.1.1/flexboxgrid.min.css' );
?>
<div class="row">
    <div class="col-xs-12
                col-sm-6
                col-md-6">
        <div class="box">
        	<h2><?php the_chapter_title( 'left_title' ) ?></h2>
        	<?php the_chapter( 'left_content' ) ?>
        </div>
    </div>
    <div class="col-xs-12
                col-sm-6
                col-md-6">
        <div class="box">
        	<h2><?php the_chapter_title( 'right_title' ) ?></h2>
        	<?php the_chapter( 'right_content' ) ?>
        </div>
    </div>
</div>