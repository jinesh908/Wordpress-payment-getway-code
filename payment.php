add_action('woocommerce_available_payment_gateways', 'select_only_phonepe_if_needed');
add_action('woocommerce_checkout_update_order_review', 'auto_select_phonepe_if_needed');
add_action('woocommerce_review_order_before_payment', 'show_cod_limit_message');

function restrict_cod_payment_method() {
    if (is_checkout()) {
        $total = WC()->cart->total;
        $max_cod_amount = 10000; // Maximum amount for COD

        if ($total > $max_cod_amount) {
            wc_add_notice(__('Cash on Delivery is not available for orders above ₹10,000. Please choose a different payment method.'), 'error');
        }
    }
}

function select_only_phonepe_if_needed($available_gateways) {
    if (is_admin()) return $available_gateways;

    $total = WC()->cart->total;
    $max_cod_amount = 10000; // Maximum amount for COD

    if ($total > $max_cod_amount) {
        // Ensure only PhonePe is available
        foreach ($available_gateways as $gateway_id => $gateway) {
            if ($gateway_id !== 'phonepe') {
                unset($available_gateways[$gateway_id]);
            }
        }

        // Set PhonePe as the chosen payment method
        if (isset($available_gateways['phonepe'])) {
            WC()->session->set('chosen_payment_method', 'phonepe');
        }
    }

    return $available_gateways;
}

function show_cod_limit_message() {
    $total = WC()->cart->total;
    $max_cod_amount = 10000; // Maximum amount for COD

    if ($total > $max_cod_amount) {
        echo '<div class="woocommerce-info">';
        echo __('Cash on Delivery is not available for orders above ₹10,000. PhonePe is the only payment method available.');
        echo '</div>';
    }
}

function auto_select_phonepe_if_needed() {
    $total = WC()->cart->total;
    $max_cod_amount = 10000; // Maximum amount for COD

    if ($total > $max_cod_amount) {
        // Ensure PhonePe is the selected payment method
        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        if (isset($available_gateways['phonepe'])) {
            // Automatically set PhonePe as the selected payment method
            WC()->session->set('chosen_payment_method', 'phonepe');
        }
    }
}
