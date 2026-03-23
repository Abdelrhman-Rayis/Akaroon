<?php get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'content', 'single' );
		$html = lbs_shelveBooks( get_the_id() );
		echo wp_kses(
			$html,
			array(
				'div' => array(
					'class' 		   => true,
					'id'			   => true,
					'style' 		   => true,
					'data-slick' 	   => true,
					'data-slick-index' => true,
					'aria-hidden'	   => true,
					'aria-describedby' => true,
					'role'			   => true,
					'tabindex'		   => true
				),
				'p' => array(),
				'a'	=> array(
					'href'	   => true,
					'target'   => true,
					'class'	   => true,
					'tabindex' => true
				),
				'span' => array(),
				'img' => array(
					'src' => true,
					'alt' => true
				),
				'button' => array(
					'id'			=> true,
					'class'			=> true,
					'aria-label'	=> true,
					'aria-controls' => true,
					'tabindex'		=> true,
					'type'			=> true,
					'style'			=> true,
					'role'			=> true
				),
				'ul' => array(
					'class'	=> true,
					'style'	=> true,
					'role'	=> true
				),
				'li' => array(
					'class'	=> true,
					'role'	=> true
				),
				'script' => array()
			)
		);
	endwhile;
	?>
	</main>
</div>

<?php get_footer(); ?>
