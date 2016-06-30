<?php
/*
Template Name: Triple col
Description: Section with 2 titles, 2 contents and 2 illustrations
---------------------------------------------------------------------
{"type": "title", "name": "Left title", "slug": "left_title"}
{"type": "editor", "name": "Left content", "slug": "left_content"}
---------------------------------------------------------------------
*/

?>
<div class="row">
    <div class="col-xs-12
                col-sm-6
                col-md-6">
        <div class="box">
        	<h2><?php the_wpcomponent( 'left_title' ) ?></h2>
        	<?php the_wpcomponent( 'left_content' ) ?>
        </div>
    </div>
    <div class="col-xs-12
                col-sm-6
                col-md-6">
        <div class="box">
        	<h2><?php the_wpcomponent( 'right_title' ) ?></h2>
        	<?php the_wpcomponent( 'right_content' ) ?>
        </div>
    </div>
</div>