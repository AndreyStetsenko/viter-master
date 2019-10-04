<?php get_header(); ?>
<!-- основной контейнер -->
<div class="main">
<ul class="cat-post">
<!-- определение категории и количество записей -->
<?php $the_query = new WP_Query('cat=8&showposts=40'); ?>
<?php while ($the_query -> have_posts()) : $the_query -> the_post(); ?>
<li>
<!-- миниатюра записи -->
<a href="<?php the_permalink() ?>"><?php echo get_the_post_thumbnail( $post->ID, 'thumbnail'); ?></a>

<!-- заголовок записи -->
<h2><a href='<?php the_permalink() ?>'><?php the_title(); ?></a></h2>

<!-- количество слов в анонсе (необязательно) -->
<?php $content = get_the_content(); echo wp_trim_words( $content , '10' ); ?>

</li>
<?php endwhile; ?>

<!-- функция для правильной работы условных тегов -->
<?php wp_reset_query(); ?>
</ul>
</div>
<!-- функция вывода сайдбара -->
<?php get_sidebar(); ?>
<!-- функция вывода футера -->
<?php get_footer(); ?>
