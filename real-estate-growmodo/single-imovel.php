<?php get_header(); ?>

<?php
// Dados do imóvel
$location = get_post_meta(get_the_ID(), '_location', true);
$price = get_post_meta(get_the_ID(), '_price', true);
$bedrooms = get_post_meta(get_the_ID(), '_bedrooms', true);
$bathrooms = get_post_meta(get_the_ID(), '_bathrooms', true);
$area = get_post_meta(get_the_ID(), '_area', true);
$amenities = explode("\n", get_post_meta(get_the_ID(), '_amenities', true));
$photos = explode(',', get_post_meta(get_the_ID(), '_photos', true)); // IDs das imagens
?>

<style>
    .imovel-container {
        background-color: #111;
        color: #fff;
        font-family: 'Arial', sans-serif;
        padding: 40px 20px;
        max-width: 1200px;
        margin: auto;
    }
    .imovel-title {
        font-size: 32px;
        font-weight: bold;
    }
    .imovel-location {
        background: #222;
        display: inline-block;
        padding: 5px 15px;
        margin-left: 10px;
        border-radius: 20px;
        font-size: 14px;
    }
    .imovel-price {
        float: right;
        font-size: 24px;
        font-weight: bold;
    }
    .gallery {
        margin-top: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }
    .gallery img {
        width: 100%;
        border-radius: 8px;
    }
    .imovel-description {
        margin-top: 30px;
        line-height: 1.6;
    }
    .imovel-info {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        gap: 40px;
        flex-wrap: wrap;
    }
    .info-card {
        flex: 1;
        background: #1a1a1a;
        padding: 20px;
        border-radius: 8px;
    }
    .info-card h3 {
        margin-bottom: 20px;
        font-size: 20px;
        border-bottom: 1px solid #333;
        padding-bottom: 10px;
    }
    .details {
        display: flex;
        gap: 30px;
        font-size: 16px;
    }
    .amenities ul {
        padding-left: 20px;
        list-style: none;
    }
    .amenities li {
        margin-bottom: 10px;
        position: relative;
        padding-left: 20px;
    }
    .amenities li::before {
        content: "⚡";
        position: absolute;
        left: 0;
    }
</style>

<div class="imovel-container">

    <div class="imovel-header">
        <span class="imovel-title"><?php the_title(); ?></span>
        <?php if ($location): ?>
            <span class="imovel-location"><?php echo esc_html($location); ?></span>
        <?php endif; ?>
        <div class="imovel-price">Preço: R$ <?php echo number_format($price, 2, ',', '.'); ?></div>
        <div style="clear: both;"></div>
    </div>

    <div class="gallery">
        <?php foreach ($photos as $id): 
            $img = wp_get_attachment_image_src($id, 'large');
            if ($img): ?>
                <img src="<?php echo esc_url($img[0]); ?>" alt="Foto do imóvel">
            <?php endif;
        endforeach; ?>
    </div>

    <div class="imovel-info">

        <div class="info-card">
            <h3>Description</h3>
            <div class="imovel-description">
                <?php the_content(); ?>
            </div>
            <div class="details">
                <div><strong>Bedrooms:</strong><br> <?php echo esc_html($bedrooms); ?></div>
                <div><strong>Bathrooms:</strong><br> <?php echo esc_html($bathrooms); ?></div>
                <div><strong>Area:</strong><br> <?php echo esc_html($area); ?> sq ft</div>
            </div>
        </div>

        <div class="info-card amenities">
            <h3>Key Features and Amenities</h3>
            <ul>
                <?php foreach ($amenities as $item): ?>
                    <li><?php echo esc_html(trim($item)); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</div>

<?php get_footer(); ?>