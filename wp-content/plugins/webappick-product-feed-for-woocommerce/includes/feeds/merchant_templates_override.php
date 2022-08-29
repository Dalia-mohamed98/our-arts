<?php
/**
 * Created by PhpStorm.
 * User: wahid
 * Date: 5/23/20
 * Time: 10:07 AM
 */
################ Spartoo #################
/**
 * Modify Spartoo feed Parent/Child attribute value.
 *
 * @param $attribute_value
 * @param $product
 * @param $feed_config
 * @return string
 */
function woo_feed_spartoo_attribute_value_modify( $attribute_value, $product, $feed_config ) {
    if ( 'spartoo.fi' === $feed_config['provider'] ) {
        if ( 'variation' === $attribute_value ) {
            return "child";
        }

        return "parent";
    }
    return $attribute_value;
}
add_filter('woo_feed_get_type_attribute','woo_feed_spartoo_attribute_value_modify',10,3);
################ Availability ################# title
/**
 * Modify  Availability value
 *
 * @param $attribute_value
 * @param $product
 * @param $feed_config
 * @return string
 */
function woo_feed_availability_attribute_value_modify( $attribute_value, $product, $feed_config ) {
    if ( 'skroutz' === $feed_config['provider'] || 'bestprice' === $feed_config['provider'] ) {
        if ( 'in stock' === $attribute_value ) {
            return "Y";
        }

        return "N";
    }

    if ( 'pricerunner' === $feed_config['provider'] ) {
        if ( 'in stock' === $attribute_value ) {
            return "Yes";
        }

        return "No";
    }

    if ( 'google' === $feed_config['provider']
        || 'pinterest' === $feed_config['provider'] ) {
        if ( 'on backorder' === $attribute_value ) {
            return 'preorder';
        }
    }

    if ( 'facebook' === $feed_config['provider'] && 'on backorder' === $attribute_value ) {
        return 'available for order';
    }

    return $attribute_value;
}
add_filter('woo_feed_get_availability_attribute','woo_feed_availability_attribute_value_modify',10,3);
################ Best Price #################
/**
 * Replace BestPrice categoryPath value from > to ,
 *
 * @param $attribute_value
 * @param $product
 * @param $feed_config
 * @return string
 */
function woo_feed_get_bestprice_categoryPath_attribute_value_modify( $attribute_value, $product, $feed_config ) {
    $attribute_value = str_replace('>',', ',$attribute_value);
    return $attribute_value;
}
add_filter('woo_feed_get_bestprice_product_type_attribute','woo_feed_get_bestprice_categoryPath_attribute_value_modify',10,3);

/**
 * Modify wight value
 *
 * @param $attribute_value
 * @param $product
 * @param $feed_config
 * @return string
 */
if ( ! function_exists( 'woo_feed_modify_weight_attribute_value' ) ) {
    function woo_feed_modify_weight_attribute_value( $attribute_value, $product, $feed_config ) {
        if ( in_array($feed_config['provider'], [ 'google', 'facebook', 'pinterest', 'bing', 'snapchat' ]) ) {
            if ( isset( $feed_config['attributes'] ) ) {
                $attributes = $feed_config['attributes'];
                $key = array_search ('weight', $attributes);
                if ( isset( $feed_config['suffix'] ) && ! empty($key) ) {
                    if ( array_key_exists( $key, $feed_config['suffix'] ) ) {
                        $weight_suffix_unit = $feed_config['suffix'][ $key ];

                        if ( empty($weight_suffix_unit) && ! empty($attribute_value) ) {
                            $attribute_value = $attribute_value . ' ' . get_option('woocommerce_weight_unit');
                        }                    
}
                }
            }
        }

        return $attribute_value;
    }
    add_filter( 'woo_feed_get_weight_attribute', 'woo_feed_modify_weight_attribute_value', 10, 3 );
}
