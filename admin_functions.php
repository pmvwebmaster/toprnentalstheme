<?php

function create_new_custom_admin_role() {
    // Check if the role already exists
    if (!get_role('custom_admin')) {
        // Get admin capabilities
        $admin = get_role('administrator');
        add_role('custom_admin', 'Custom Admin', $admin->capabilities);
    }

    // Ensure extra capabilities are correctly assigned
    $role = get_role('custom_admin');

    if ($role) {
        $role->add_cap('manage_options');
        $role->add_cap('update_plugins');
        $role->add_cap('install_plugins');
        $role->add_cap('activate_plugins');
        $role->add_cap('edit_plugins');
        $role->add_cap('delete_plugins');
        $role->add_cap('edit_theme_options');
    }
}
add_action('init', 'create_new_custom_admin_role');



// Create submenu in admin
function delivery_locations_admin_menu() {
    $user = wp_get_current_user();
    if (in_array('custom_admin', $user->roles) || in_array('shop_manager', $user->roles) || in_array('administrator', $user->roles)) {

        add_menu_page(
            'Delivery/Pickup Locations',
            'Delivery Locations',
            'edit_products',
            'delivery-pickup-locations',
            'delivery_locations_admin_page',
            'dashicons-location-alt',
            20
        );
    }
}
add_action('admin_menu', 'delivery_locations_admin_menu');

// Settings page
function delivery_locations_admin_page() {
    if (isset($_POST['locations_nonce']) && wp_verify_nonce($_POST['locations_nonce'], 'save_locations_nonce')) {
        if (current_user_can('edit_products')) {
            update_option('delivery_locations', sanitize_textarea_field($_POST['delivery_locations']));
            update_option('pickup_locations', sanitize_textarea_field($_POST['pickup_locations']));
            echo '<div class="updated"><p>Locations saved successfully.</p></div>';
        } else {
            echo '<div class="error"><p>You do not have permission to save.</p></div>';
        }
    }
?>
    <div class="wrap">
        <h1>Delivery and Pickup Locations</h1>
        <form method="post">
            <?php wp_nonce_field('save_locations_nonce', 'locations_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Delivery Location (1 Per Line)</th>
                    <td>
                        <textarea name="delivery_locations" rows="8" class="large-text"><?php echo esc_textarea(get_option('delivery_locations')); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pickup Locations (1 Per Line)</th>
                    <td>
                        <textarea name="pickup_locations" rows="8" class="large-text"><?php echo esc_textarea(get_option('pickup_locations')); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save'); ?>
        </form>
    </div>
<?php
}


// Register settings fields
function delivery_locations_register_settings() {
    register_setting('locations_options_group', 'delivery_locations');
    register_setting('locations_options_group', 'pickup_locations');

    add_settings_section('locations_section', '', null, 'delivery-pickup-locations');

    add_settings_field('delivery_locations', 'Delivery Location (1 Per Line)', 'delivery_locations_callback', 'delivery-pickup-locations', 'locations_section');
    add_settings_field('pickup_locations', 'Pickup Locations (1 Per Line)', 'pickup_locations_callback', 'delivery-pickup-locations', 'locations_section');
}
add_action('admin_init', 'delivery_locations_register_settings');

// Callbacks to display fields
function delivery_locations_callback() {
    $value = esc_textarea(get_option('delivery_locations'));
    echo "<textarea name='delivery_locations' rows='10' cols='50' class='large-text'>$value</textarea>";
}

function pickup_locations_callback() {
    $value = esc_textarea(get_option('pickup_locations'));
    echo "<textarea name='pickup_locations' rows='10' cols='50' class='large-text'>$value</textarea>";
}



/*add_action('admin_menu', 'add_orders_menu_for_custom_admin', 99);

function add_orders_menu_for_custom_admin() {
    // Check if the user has the 'custom_admin' role
    

    // Add the Orders menu (with the same link WooCommerce uses)
    add_menu_page(
        __('Orders'),                                // Page title
        __('Orders'),                                // Menu title
        'manage_woocommerce',                        // Required capability
        'edit.php?post_type=shop_order',             // Menu slug (link to orders)
        '',                                          // Callback function (not needed, WooCommerce handles it)
        'dashicons-cart',                            // Menu icon
        55                                           // Menu position (adjust as needed)
    );
}

add_action('admin_menu', function () {
   
    global $menu;

    // Look for the 'Orders' menu (inside edit.php?post_type=shop_order)
    foreach ($menu as $item) {
        if (
            isset($item[2]) &&
            $item[2] === 'edit.php?post_type=shop_order'
        ) {
            // Clone the item to another position (e.g., before the 'Comments' menu)
            $new_position = 55;
            $menu[$new_position] = $item;
            break;
        }
    }
}, 99);
function custom_admin_menu() {
    global $submenu;
    if (isset($submenu['edit.php?post_type=shop_order'])) {
        unset($submenu['edit.php?post_type=shop_order'][10]);
    }
}

add_action('admin_menu', 'custom_admin_menu', 99);  

*/

function custom_admin_css_file() {
    if (current_user_can('custom_admin')) {
        wp_enqueue_style('my-admin-css', get_template_directory_uri() . '/admin.css');
    }
}
add_action('admin_enqueue_scripts', 'custom_admin_css_file');



/*function restrict_custom_admin() {
    $role = get_role('custom_admin');
    if ($role) {
        $role->remove_cap('activate_plugins');
        $role->remove_cap('delete_plugins');
        $role->remove_cap('install_plugins');
        $role->remove_cap('update_plugins');
    }
}
add_action('init', 'restrict_custom_admin');*/

// Add menu for Unavailable Days
function unavailable_days_admin_menu() {
    if (current_user_can('custom_admin') || current_user_can('shop_manager') || current_user_can('administrator')) {
        add_menu_page(
            'Unavailable Days',
            'Unavailable Days',
            'edit_products',
            'unavailable-days',
            'unavailable_days_admin_page',
            'dashicons-calendar-alt',
            21
        );
    }
}
add_action('admin_menu', 'unavailable_days_admin_menu');

// Admin page for unavailable days
function unavailable_days_admin_page() {
    if (isset($_POST['unavailable_days_nonce']) && wp_verify_nonce($_POST['unavailable_days_nonce'], 'save_unavailable_days_nonce')) {
        if (current_user_can('edit_products')) {
            $dates = isset($_POST['unavailable_days']) ? array_map('sanitize_text_field', (array)$_POST['unavailable_days']) : [];
            update_option('unavailable_days', $dates);
            echo '<div class="updated"><p>Days saved successfully.</p></div>';
        } else {
            echo '<div class="error"><p>You do not have permission to save.</p></div>';
        }
    }
    $saved_dates = get_option('unavailable_days', []);
    if (!is_array($saved_dates)) $saved_dates = [];
    ?>
    <div class="wrap">
        <h1>Unavailable Days</h1>
        <form method="post">
            <?php wp_nonce_field('save_unavailable_days_nonce', 'unavailable_days_nonce'); ?>
            <p>Select the days when <b>there will be no operation</b> (pickup/delivery):</p>
            <input type="date" id="datePicker" />
            <button type="button" id="addDateBtn" class="button">Add Day</button>
            <ul id="selectedDates">
                <?php foreach ($saved_dates as $date): ?>
                    <li data-date="<?php echo esc_attr($date); ?>">
                        <?php echo esc_html(date_i18n('m/d/Y', strtotime($date))); ?>
                        <button type="button" class="removeDateBtn">Remove</button>
                        <input type="hidden" name="unavailable_days[]" value="<?php echo esc_attr($date); ?>" />
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php submit_button('Save Days'); ?>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const datePicker = document.getElementById('datePicker');
        const addDateBtn = document.getElementById('addDateBtn');
        const selectedDates = document.getElementById('selectedDates');
        addDateBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const date = datePicker.value;
            if (!date) return;
            if ([...selectedDates.querySelectorAll('input')].some(i => i.value === date)) return;
            const li = document.createElement('li');
            li.dataset.date = date;
            // Format date from YYYY-MM-DD to MM/DD/YYYY (US format) without using new Date
            const parts = date.split('-');
            const usDate = `${parts[1]}/${parts[2]}/${parts[0]}`;
            li.innerHTML = `${usDate} <button type='button' class='removeDateBtn'>Remove</button><input type='hidden' name='unavailable_days[]' value='${date}' />`;
            selectedDates.appendChild(li);
        });
        selectedDates.addEventListener('click', function(e) {
            if (e.target.classList.contains('removeDateBtn')) {
                e.target.closest('li').remove();
            }
        });
    });
    </script>
    <?php
}

// AJAX endpoint to return unavailable days for frontend JS
add_action('wp_ajax_get_unavailable_days', 'get_unavailable_days_callback');
add_action('wp_ajax_nopriv_get_unavailable_days', 'get_unavailable_days_callback');
function get_unavailable_days_callback() {
    $days = get_option('unavailable_days', []);
    if (!is_array($days)) $days = [];
    wp_send_json_success($days);
}