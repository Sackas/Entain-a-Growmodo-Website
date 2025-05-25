<?php
function growmodo_child_enqueue_styles() {
    wp_enqueue_style('growmodo-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('growmodo-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        ['growmodo-parent-style']
    );
}
add_action('wp_enqueue_scripts', 'growmodo_child_enqueue_styles');
