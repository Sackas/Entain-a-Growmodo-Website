<?php
function growmodo_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'growmodo_theme_setup');

function growmodo_enqueue_scripts() {
    wp_enqueue_style('growmodo-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'growmodo_enqueue_scripts');

function growmodo_elementor_compatibility() {
    add_theme_support('elementor');
}
add_action('after_setup_theme', 'growmodo_elementor_compatibility');




function registrar_post_type_imovel() {
    $labels = array(
        'name' => 'Imóveis',
        'singular_name' => 'Imóvel',
        'add_new' => 'Adicionar Novo',
        'add_new_item' => 'Adicionar Novo Imóvel',
        'edit_item' => 'Editar Imóvel',
        'new_item' => 'Novo Imóvel',
        'view_item' => 'Ver Imóvel',
        'search_items' => 'Buscar Imóveis',
        'not_found' => 'Nenhum imóvel encontrado',
        'not_found_in_trash' => 'Nenhum imóvel na lixeira',
        'menu_name' => 'Imóveis'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-building',
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'imoveis'),
        'show_in_rest' => true,
    );

    register_post_type('imovel', $args);
}
add_action('init', 'registrar_post_type_imovel');



function adicionar_campos_personalizados_imovel() {
    add_meta_box(
        'detalhes_imovel',
        'Detalhes do Imóvel',
        'renderizar_campos_imovel',
        'imovel',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'adicionar_campos_personalizados_imovel');

function renderizar_campos_imovel($post) {
    // Valores atuais
    $location = get_post_meta($post->ID, '_location', true);
    $price = get_post_meta($post->ID, '_price', true);
    $bedrooms = get_post_meta($post->ID, '_bedrooms', true);
    $bathrooms = get_post_meta($post->ID, '_bathrooms', true);
    $area = get_post_meta($post->ID, '_area', true);
    $amenities = get_post_meta($post->ID, '_amenities', true);
    $photos = get_post_meta($post->ID, '_photos', true); // IDs das imagens

    wp_nonce_field('salvar_detalhes_imovel', 'detalhes_imovel_nonce');

    ?>
    <p><label>Localização:</label><br><input type="text" name="location" value="<?php echo esc_attr($location); ?>" style="width: 100%;"></p>
    <p><label>Preço (R$):</label><br><input type="number" name="price" value="<?php echo esc_attr($price); ?>" style="width: 100%;"></p>
    <p><label>Quartos:</label><br><input type="number" name="bedrooms" value="<?php echo esc_attr($bedrooms); ?>"></p>
    <p><label>Banheiros:</label><br><input type="number" name="bathrooms" value="<?php echo esc_attr($bathrooms); ?>"></p>
    <p><label>Área (m²):</label><br><input type="number" name="area" value="<?php echo esc_attr($area); ?>"></p>
    <p><label>Características e Comodidades:</label><br>
        <textarea name="amenities" rows="4" style="width: 100%;"><?php echo esc_textarea($amenities); ?></textarea>
    </p>
<p><label>Fotos:</label></p>
<div id="galeria-preview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;"></div>
<input type="hidden" id="fotos_ids" name="photos" value="<?php echo esc_attr($photos); ?>">
<button type="button" class="button" id="selecionar-fotos">Selecionar Fotos</button>

<script>
jQuery(document).ready(function($){
    const frame = wp.media({
        title: 'Selecionar Fotos do Imóvel',
        multiple: true,
        library: { type: 'image' },
        button: { text: 'Usar essas fotos' }
    });

    const input = $('#fotos_ids');
    const preview = $('#galeria-preview');

    function atualizarPreview(ids) {
        preview.empty();
        if (!ids) return;
        ids.split(',').forEach(function(id){
            wp.media.attachment(id).fetch().then(function(){
                const url = wp.media.attachment(id).get('url');
                preview.append(`<div style="position:relative">
                    <img src="${url}" style="width:100px;height:auto;border-radius:4px;">
                    <span class="remover-foto" data-id="${id}" style="position:absolute;top:-5px;right:-5px;background:red;color:white;padding:2px 5px;border-radius:50%;cursor:pointer;">&times;</span>
                </div>`);
            });
        });
    }

    atualizarPreview(input.val());

    $('#selecionar-fotos').on('click', function(){
        frame.open();
    });

    frame.on('select', function(){
        const selection = frame.state().get('selection');
        const ids = [];
        selection.each(function(attachment){
            ids.push(attachment.id);
        });
        input.val(ids.join(','));
        atualizarPreview(ids.join(','));
    });

    preview.on('click', '.remover-foto', function(){
        const idRemover = $(this).data('id');
        const ids = input.val().split(',').filter(id => id != idRemover);
        input.val(ids.join(','));
        atualizarPreview(ids.join(','));
    });
});
</script>

    <?php
}


function salvar_detalhes_imovel($post_id) {
    if (!isset($_POST['detalhes_imovel_nonce']) || !wp_verify_nonce($_POST['detalhes_imovel_nonce'], 'salvar_detalhes_imovel')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $campos = ['location', 'price', 'bedrooms', 'bathrooms', 'area', 'amenities', 'photos'];

    foreach ($campos as $campo) {
        if (isset($_POST[$campo])) {
            update_post_meta($post_id, "_$campo", sanitize_text_field($_POST[$campo]));
        }
    }
}
add_action('save_post', 'salvar_detalhes_imovel');

function habilitar_media_script_admin() {
    if (is_admin()) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'habilitar_media_script_admin');

function shortcode_carrossel_imoveis($atts) {
    ob_start();

    $args = [
        'post_type' => 'imovel',
        'posts_per_page' => 10,
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        ?>
        <!-- Carrossel HTML -->
        <div class="carrossel-imoveis swiper">
            <div class="swiper-wrapper">
                <?php while ($query->have_posts()) : $query->the_post();
    $id = get_the_ID();
	
    $price = get_post_meta($id, '_price', true);
    $price_formatted = is_numeric($price) ? number_format((float)$price) : 'N/A';
    $bedrooms = get_post_meta($id, '_bedrooms', true);
//	echo var_dump($bedrooms);
    $bathrooms = get_post_meta($id, '_bathrooms', true);
    $photos = get_post_meta($id, '_photos', true);
    $thumb_id = explode(',', $photos)[0];
    $thumb_url = wp_get_attachment_image_url($thumb_id, 'medium');
?>
    <div class="swiper-slide">
        <div class="card-imovel">
            <img src="<?= esc_url($thumb_url); ?>" alt="<?= esc_attr(get_the_title()); ?>">
            <h3><?= get_the_title(); ?></h3>
            <p><?= wp_trim_words(get_the_content(), 15); ?></p>
            <div class="info-icons">
                🛏️ <?= esc_html($bedrooms); ?>-Bedroom
                &nbsp;&nbsp;🛁 <?= esc_html($bathrooms); ?>-Bathroom
                &nbsp;&nbsp;🏠 Villa
            </div>
            <div class="preco">Price <strong>$<?= esc_html($price_formatted); ?></strong></div>
            <a href="<?= get_permalink(); ?>" class="botao-detalhes">View Property Details</a>
        </div>
    </div>
<?php endwhile; ?>

            </div>

            <!-- Botões -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <?php
        wp_reset_postdata();
    else :
        echo "<p>No properties found.</p>";
    endif;

    return ob_get_clean();
}
add_shortcode('carrossel_imoveis', 'shortcode_carrossel_imoveis');



function carregar_swiper_para_imoveis() {
    if (!is_admin()) {
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', [], null, true);
wp_add_inline_script('swiper-js', '
    function iniciarSwiperImoveis() {
        new Swiper(".carrossel-imoveis", {
            slidesPerView: 3,
            spaceBetween: 20,
            loop: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                1024: { slidesPerView: 3 },
                768: { slidesPerView: 2 },
                480: { slidesPerView: 1 }
            }
        });
    }

    if (typeof elementorFrontend !== "undefined") {
        jQuery(window).on("elementor/frontend/init", function () {
            elementorFrontend.hooks.addAction("frontend/element_ready/global", iniciarSwiperImoveis);
        });
    } else {
        document.addEventListener("DOMContentLoaded", iniciarSwiperImoveis);
    }
');

    }
}/**/
add_action('wp_enqueue_scripts', 'carregar_swiper_para_imoveis');







