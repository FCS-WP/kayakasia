<?php

add_filter('woocommerce_get_price_html', 'show_price_range_for_combo', 20, 2);
function show_price_range_for_combo($price_html, $product)
{
    if(is_front_page()) {
        return '<span>From </span>' . $price_html;
    }
    return $price_html;
}