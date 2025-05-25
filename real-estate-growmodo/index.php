<?php get_header(); ?>
<main id="main">
    <?php if (have_posts()) : while (have_posts()) : the_post();
        get_template_part('template-parts/content', get_post_format());
    endwhile;
    else : ?>
        <p><?php esc_html_e('Nenhuma postagem encontrada.', 'real-estate-growmodo'); ?></p>
    <?php endif; ?>
</main>
<?php get_sidebar(); ?>
<?php get_footer(); ?>