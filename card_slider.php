<?php
/*
Plugin Name: Latest Products Horizontal Slider 
Description: Display the most recent WooCommerce products using a horizontal slideshow.[A1_Product_Slider] [A1_Product_Slider num_products="5"]
Version: 1.0
Author: Hassan Naqvi
*/

// Enqueue styles and scripts
function card_slider_acf_enqueue_scripts() {
    wp_enqueue_style('typekit', 'https://use.typekit.net/vxy2fer.css');
	wp_enqueue_script('webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array(), null, true);
   
    wp_enqueue_style('demo', plugin_dir_url(__FILE__) . 'main-set.css');

    wp_enqueue_script('demo', plugin_dir_url(__FILE__) . 'slider-js.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'card_slider_acf_enqueue_scripts');

function enqueue_custom_styles_if_shortcode_present() {
    global $post;

    // Check if the current post content contains the specified shortcode
    if (has_shortcode($post->post_content, 'A1_Product_Slider')) {
        // Enqueue custom stylesheet for the specified styles from the plugin folder
        wp_enqueue_style('custom-styles', plugin_dir_url(__FILE__) . 'custom-styles.css');
    }
}

add_action('wp_enqueue_scripts', 'enqueue_custom_styles_if_shortcode_present');



function custom_a1_product_slider($atts) {
    // Check if we are in the Elementor editor
    if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
        return ''; // Return an empty string to hide the shortcode in Elementor editor
    }

    // Parse the shortcode attributes
    $atts = shortcode_atts(
        array(
            'num_products' => -1, // Default to show all products
        ),
        $atts,
        'A1_Product_Slider'
    );

    ob_start();

    // Query WooCommerce for the latest products
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $atts['num_products'],
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $latest_products = new WP_Query($args);

    // Check if there are any products
    if ($latest_products->have_posts()) :
        ?>

        <main data-scroll-container="">
            <div class="content-base">
                <div class="gallery" id="gallery">
                    <div class="gallery__text"><span class="gallery__text-inner" data-scroll="" data-scroll-speed="3" data-scroll-direction="vertical"></span><span data-scroll="" data-scroll-speed="-4" data-scroll-direction="vertical" class="gallery__text-inner"></span></div>

                    <?php
                    $counter = 1;
                    while ($latest_products->have_posts()) : $latest_products->the_post();
                        $product_id = get_the_ID();
                        $product_title = get_the_title();
                        $product_tags = get_the_terms($product_id, 'product_tag');
                        $product_image = get_the_post_thumbnail_url($product_id, 'full');
                        $product_price = get_post_meta($product_id, '_price', true);
                        ?>

                        <figure class="gallery__item" data-scroll="" data-scroll-speed="1">
                            <div class="gallery__item-img">
                                <div class="gallery__item-imginner" style="background-image: url(<?php echo esc_url($product_image); ?>)"></div>
                            </div>
                            <figcaption class="gallery__item-caption">
                                <h2 class="gallery__item-title"><?php echo esc_html($product_title); ?></h2>
                            <span class="gallery__item-number"><?php echo '#' . sprintf('%02d', $counter); ?></span>

                                <p class="gallery__item-tags">
                                    <?php
                                    if ($product_tags) {
                                        foreach ($product_tags as $tag) {
                                            echo '<span>#' . esc_html($tag->name) . '</span>';
                                        }
                                    }
                                    ?>
                                </p>
                                <a href="<?php the_permalink(); ?>" class="gallery__item-link">Buy Now</a>
                            </figcaption>
                        </figure>

                    <?php
                    $counter++;
                    endwhile;
                    wp_reset_postdata(); // Reset the loop data
                    ?>

                    <div class="gallery__text two-text"><span class="gallery__text-inner" data-scroll="" data-scroll-speed="-4" data-scroll-direction="vertical"></span><span data-scroll="" data-scroll-speed="3" data-scroll-direction="vertical" class="gallery__text-inner"></span></div>
                </div>
            </div>
        </main>
        <svg class="cursor" width="20" height="20" viewBox="0 0 20 20">
            <circle class="cursor__inner" cx="10" cy="10" r="5"></circle>
        </svg>

    <?php else :
        echo '<p>No products found</p>';
    endif;

    $output = ob_get_clean();
    return $output;
}

add_shortcode('A1_Product_Slider', 'custom_a1_product_slider');

/**
 * Free Product Carousel Shortcodes Plugin
 *
 * Description: Adds a settings page for the Custom Swiper plugin and provides examples of shortcode usage.
 */
function free_product_carousel_shortcodes_settings_page() {
    ?>
 <div class="wrap">
    <h1>Free Product Carousel Shortcodes</h1>
    <p>Welcome to the settings page of the Free Product Carousel Or Slider Shortcodes plugin.</p>

    <h2>How to Use Shortcodes</h2>
    <p>Below are examples of shortcodes.</p>

    <p>Display all products with default settings:</p>
    <pre>[A1_Product_Slider]</pre>

    <p>Show the latest products with a number count:</p>
    <pre>[latest_products_gallery num_products="5"]</pre>
    
   
</div>
    <?php
}
/**
 * Function to add the settings page to the admin menu
 */
function free_product_carousel_shortcodes_add_menu() {
    add_options_page('Free Product Carousel Shortcodes Settings', 'Free Product Carousel', 'manage_options', 'free-product-carousel-settings', 'free_product_carousel_shortcodes_settings_page');
}

// Hook to add the settings page
add_action('admin_menu', 'free_product_carousel_shortcodes_add_menu');
