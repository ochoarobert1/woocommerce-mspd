<?php

/**
 * Woocommerce Simple Product Multisite Duplicator
 *
 * This is a Simple Product Multisite Duplicator for Woocommerce.
 *
 * @link              http://www.robertochoa.com.ve
 * @since             1.0.0
 * @package           woocommerce-mspd
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Simple Product Multisite Duplicator
 * Plugin URI:        http://www.robertochoa.com.ve
 * Description:       This is a Simple Product Multisite Duplicator for Woocommerce.
 * Version:           1.0.0
 * Author:            Robert Ochoa
 * Author URI:        http://www.robertochoa.com.ve
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-mspd
 * Domain Path:       /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/* --------------------------------------------------------------
    DEFINE CURRENT PLUGIN VERSION
-------------------------------------------------------------- */
define( 'WOOCOMMERCE_MSPD_VERSION', '1.0.0' );


/* --------------------------------------------------------------
    ADD CUSTOM SCRIPTS
-------------------------------------------------------------- */
function woocommerce_mspd_admin_scripts() {
    wp_register_style('woocommerce-mspd-css', plugins_url('css/woocommerce-mspd.css', __FILE__), false, '1.0.0', 'all');
    wp_enqueue_style('woocommerce-mspd-css');
    wp_enqueue_script('wooocommerce-mspd-js', plugins_url('js/woocommerce-mspd.js', __FILE__), array('jquery'));
    wp_localize_script( 'wooocommerce-mspd-js', 'admin_url', array( 'ajax_url' => admin_url('admin-ajax.php')));
}

add_action('admin_enqueue_scripts', 'woocommerce_mspd_admin_scripts');

/* --------------------------------------------------------------
    ADD MENU PAGE
-------------------------------------------------------------- */
function woocommerce_mspd_admin_menu() {
    add_management_page( __('Duplicate products', 'woocommerce-mspd'), __('Duplicate Woocommerce Products', 'woocommerce-mspd'), 'manage_options', 'woocommerce-mspd', 'woocommerce_mspd_admin_menu_callback' );
}

add_action('admin_menu', 'woocommerce_mspd_admin_menu', 99);

/* --------------------------------------------------------------
    ADD PLUGIN DASHBOARD
-------------------------------------------------------------- */
function woocommerce_mspd_admin_menu_callback() {
?>
<div class="woocommerce-mspd-main-title">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <img src="<?php echo plugins_url('img/woocommerce_logo_white.png', __FILE__); ?>" alt="Powered by woocommerce" class="img-logo" />
</div>

<div class="woocommerce-mspd-main-content">
    <div class="woocommerce-mspd-button-container">
        <button>
            <span class="dashicons dashicons-sort"></span>
            <h2><?php _e('Duplicate products', 'woocommerce-mspd'); ?></h2>
        </button>
    </div>
    <div class="woocommerce-mspd-products-container">
        <h2><?php _e('Products for duplication', 'woocommerce-mspd'); ?></h2>
        <?php $args = array('post_type' => 'product', 'posts_per_page' => -1, 'order' => 'DESC', 'orderby' => 'date'); ?>
        <?php $array_products = new WP_Query($args); ?>
        <?php $count_products = $array_products->found_posts; ?>
        <?php wp_reset_query(); ?>
        <h3><?php echo sprintf( esc_html__( "Right now, there's %s products ready to be duplicated", "woocommerce-mspd" ), $count_products); ?></h3>

        <div class="woocommerce-mspd-result-container">

        </div>
    </div>
</div>

<?php
                                                }

function Generate_Featured_Image( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}

function Generate_Gallery_Image( $image_url ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, 0 );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1 = wp_update_attachment_metadata( $attach_id, $attach_data );
    return $attach_id;
}

add_action('wp_ajax_woocommerce_mspd_main_action', 'woocommerce_mspd_main_action_handler');
add_action('wp_ajax_nopriv_woocommerce_mspd_main_action', 'woocommerce_mspd_main_action_handler');


function woocommerce_mspd_main_action_handler() {
    global $wpdb;
    $wpdb->show_errors();
    // Getting current blog id
    $original_blog_id = get_current_blog_id();
    // Getting Current Products
    $args = array('post_type' => 'product', 'posts_per_page' => -1, 'order' => 'DESC', 'orderby' => 'date');
    $array_products = new WP_Query($args);
    if ($array_products->have_posts()) :
    while ($array_products->have_posts()) : $array_products->the_post();
    $array_to_import[ get_the_ID()] = get_post_meta(get_the_ID(), '_sku', true);
    endwhile;
    endif;
    wp_reset_query();

    if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
        $sites = get_sites();
        foreach ( $sites as $site ) {
            if ($site->blog_id != $original_blog_id) {
                switch_to_blog( $site->blog_id );
                // Search current SKU
                foreach ($array_to_import as $key => $value) {
                    $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}{$site->blog_id}_postmeta WHERE meta_value = {$value}", OBJECT );
                    if (empty($results)) {
                        switch_to_blog( $original_blog_id );
                        if ($original_blog_id == 1) {
                            // get basic info
                            $original = $wpdb->get_results( "SELECT ID, post_content, post_title, post_excerpt FROM {$wpdb->prefix}posts WHERE ID LIKE ". $key, OBJECT );
                            $original = array_shift($original);
                            //Get all post_meta for products
                            $original_post_meta = $wpdb->get_results( "SELECT meta_key, meta_value FROM {$wpdb->prefix}postmeta WHERE post_id LIKE ". $key, OBJECT );
                            // GET CURRENT POST THUMBNAIL
                            $featured_img_url = get_the_post_thumbnail_url($key, 'full');
                            // GET CURRENT GALLERY
                            $media = get_post_meta($key, '_product_image_gallery', true);
                            if ($media !== '') {
                                $array_media[] = explode(',', $media);
                                $array_media = array_shift($array_media);
                                foreach ($array_media as $item) {
                                    $attachment_url[] = wp_get_attachment_url( $item );
                                }
                            }


                            //                            switch_to_blog( $site->blog_id );
                            //                            if (!empty($attachment_url)) {
                            //                                foreach ($attachment_url as $item) {
                            //                                    Generate_Gallery_Image( $item );
                            //                                }
                            //                            }
                            //                            var_dump($attach_gallery);
                            //                            $args_new_post = array(
                            //                                'post_title' => $original->post_title,
                            //                                'post_type' => 'product',
                            //                                'post_status' => 'publish',
                            //                                'post_content' => $original->post_content,
                            //                                'post_excerpt' => $original->post_excerpt,
                            //                            );
                            //                            $id = wp_insert_post($args_new_post);
                            //                            foreach ($original_post_meta as $item) {
                            //                            if ($item->meta_key != '_product_image_gallery') {
                            //                                update_post_meta($id, $item->meta_key, $item->meta_value);
                            //                                }
                            //                            }
                            //                            Generate_Featured_Image( $featured_img_url, $id );

                        } else {
                            $original = $wpdb->get_results( "SELECT POST_ FROM {$wpdb->prefix}{$site->blog_id}_posts WHERE post_type = 'product' and post_id = {$key}", OBJECT );
                        }
                        $wpdb->print_error();
                    }
                }
            }
            restore_current_blog();
        }
    }
    wp_die();
}
