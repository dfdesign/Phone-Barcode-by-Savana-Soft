<?php
/*
Plugin Name: Phone Barcode by Savana Soft
Description: Tired of copying the phone numbers to phone when you process your orders? Phone Barcode by Savana Soft will show barcodes on order admin pages for phone numbers and you can easily scan and directly call to your customers.
Version: 1.0
Author: Savana Soft
Author URI: https://savana-soft.com
License: GPL2
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Woocommerce_Phone_Barcode' ) ) {

    class Woocommerce_Phone_Barcode {

        public function __construct() {
				
            // Hook scripts and filters
            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			add_filter( 'woocommerce_shop_order_list_table_columns', [ $this, 'add_admin_order_list_phone' ], 11 );
			add_filter( 'manage_edit-shop_order_columns', [ $this, 'add_admin_order_list_phone' ], 999 );
            add_action( 'woocommerce_shop_order_list_table_custom_column', [ $this, 'display_admin_order_list_custom_column_content' ], 999, 2 );
            add_action( 'manage_shop_order_posts_custom_column', [ $this, 'display_admin_order_list_custom_column_content' ], 999, 2 );
            add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'add_phone_barcode_after_admin_order_phone' ], 10 );
        }

        /**
         * Enqueue necessary scripts only on WooCommerce order admin pages.
         */
        public function enqueue_scripts( $hook ) {
			$screen = get_current_screen();

			if ( isset( $screen->post_type ) ) {
			    
				$post_type = $screen->post_type;
				// Only load on WooCommerce shop order pages
				if ( $screen->post_type === 'shop_order' ) {
				    
					wp_register_script( 'qrcode', plugin_dir_url( __FILE__ ) . 'js/qrcode.min.js', [], '1.0.0', true );
					wp_enqueue_script( 'qrcode' );

					// Load specific script based on page context
					$script = isset( $_GET['action'] ) && $_GET['action'] === 'edit'
						? 'woocommerce-phone-barcode-product-scripts.js' 
						: 'woocommerce-phone-barcode-list-scripts.js';
				// 		print_r($script);die();

					wp_register_script( 'woocommerce-phone-barcode-scripts', plugin_dir_url( __FILE__ ) . 'js/' . $script );
					wp_enqueue_script( 'woocommerce-phone-barcode-scripts' );
				}
			}
        }

        /**
         * Add custom column for phone barcode to order list.
         */
        public function add_admin_order_list_phone( $columns ) {
            $new_columns = [];
            foreach ( $columns as $key => $column ) {
                $new_columns[ $key ] = $column;
                if ( $key === 'order_number' ) {
                    $new_columns['phone_barcode'] = __( 'Phone', 'woocommerce_phone_barcode' );
                }
            }

            return $new_columns;
        }

        /**
         * Display phone barcode content in the custom column on order list page.
         */
        public function display_admin_order_list_custom_column_content( $column, $post_id ) {
            if ( $column === 'phone_barcode' ) {
                $order = wc_get_order( $post_id );
                if ( $order ) {
                    echo '<div class="phone_number_for_barcode">' . esc_html( $order->get_billing_phone() ) . '</div>';
                }
            }
        }

        /**
         * Add barcode display after phone number in order details.
         */
        public function add_phone_barcode_after_admin_order_phone( $order ) {
            echo '<div id="phone-barcode" data-phone="' . esc_attr( $order->get_billing_phone() ) . '"></div>';
        }
    }

    // Initialize plugin
    new Woocommerce_Phone_Barcode();
}
