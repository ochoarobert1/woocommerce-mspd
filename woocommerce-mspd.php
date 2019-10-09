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
    DISABLE IF WOOCOMMERCE IS NOT CURRENTLY ACTIVE
-------------------------------------------------------------- */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}