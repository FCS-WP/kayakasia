<?php

function custom_filter_categories($atts)
{
    $atts = shortcode_atts([
        'hide_empty'      => 'true',
        'hide_uncategories' => 'true',
    ], $atts, 'custom_filter_categories');

    // handle to get and show categories data

    $hide_empty      = filter_var($atts['hide_empty'], FILTER_VALIDATE_BOOLEAN);
    $hide_uncat      = filter_var($atts['hide_uncategories'], FILTER_VALIDATE_BOOLEAN);

    $args = [
        'taxonomy'   => 'product_cat',       // for posts; use 'product_cat' for WooCommerce
        'hide_empty' => $hide_empty,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ];

    $categories = get_terms($args);

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>No categories found.</p>';
    }

    return  render_list_categories($categories, $hide_uncat);
}

add_shortcode('custom_filter_categories', 'custom_filter_categories');

function render_list_categories($categories, $hide_uncat)
{
    echo '<div class="custom-list-category">';
    foreach ($categories as $cat) {
        if ($hide_uncat && strtolower($cat->slug) === 'uncategorized') {
            continue;
        }
        // Hide all parent categories
        if ($cat->parent === 0) {
            continue;
        }
        $count = wc_get_products([
            'category' => [$cat->slug],
            'limit'    => 1,
            'status'   => 'publish',
        ]);

        if (empty($count)) {
            continue;
        }
        $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
        $image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : wc_placeholder_img_src();
?>
        <div class="category-item">
            <div class="category-info">
                <a href="<?php echo esc_url(get_term_link($cat)) ?>">
                    <?php echo esc_html($cat->name) ?>
                    (<?php echo intval($cat->count) ?>)
                </a>
            </div>
        </div>
<?php
    }
    echo '</div>';
}
