<?php

function custom_trip_fillter()
{
    $unuse_cat = get_term_by('slug', 'uncategorized', 'product_cat');
    $parent_cate = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'exclude'    => $unuse_cat ? [$unuse_cat->term_id] : []
    ]);

    if (empty($parent_cate)) return;

    ob_start();
?>
    <form method="GET">
        <div class="trip-shortcode-wrapper">
            <div class="trip-shortcode-filter">
                <?php foreach ($parent_cate as $parent): ?>
                    <?php
                    $child_cate = get_terms([
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => false,
                        'parent'     => $parent->term_id
                    ]);
                    $thumbnail_id = get_term_meta($parent->term_id, 'thumbnail_id', true);
                    $thumbnail = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                    ?>
                    <div class="filter-items">
                        <div class="filter-header">
                            <?php if ($thumbnail): ?>
                                <img src="<?php echo esc_url($thumbnail); ?>" alt="">
                            <?php endif; ?>
                        </div>
                        <div class="filter-content">
                            <p class="filter-title"><?php echo esc_html($parent->name); ?></p>
                            <select name="filter-value[<?php echo esc_attr($parent->slug); ?>]" class="filter-item">
                                <option value="">Choose Type</option>
                                <?php foreach ($child_cate as $child): ?>
                                    <option value="<?php echo esc_attr($child->slug); ?>">
                                        <?php echo esc_html($child->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="filter-button">
                <button type="submit" name="filter_submit" value="1">Find Trips</button>
            </div>
        </div>
    </form>
<?php
    return ob_get_clean();
}
add_shortcode('custom_trip_fillter', 'custom_trip_fillter');

add_action('template_redirect', function () {
    if (!isset($_GET['filter_submit'])) return;
    if (empty($_GET['filter-value']) || !is_array($_GET['filter-value'])) return;

    $parts = [];

    foreach ($_GET['filter-value'] as $parent => $child) {
        if (!empty($child)) {
            $child  = sanitize_title($child);
            $parts[] = $child;
        }
    }

    if (empty($parts)) {
        wp_safe_redirect(site_url('/shop'));
        exit;
    }

    $query = implode(';', $parts);

    wp_safe_redirect(site_url('/shop/?_c=' . $query));
    exit;
});

