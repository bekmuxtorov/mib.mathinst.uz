<?php

echo $l10n->get('cart_order_no') . ": " . $orderData['order']['id'] . "\n\n";

echo $opening . "\n\n";

echo $l10n->get('cart_product_list') . "\n\n";

foreach ($products as $value) {
    $url = $baseurl . "cart/x5cart.php?download=" . $value['download_hash'];
    echo $value['name'] . ": " . $url . "\n";
    if (isset($value['description']) && strlen($value['description'])) {
        echo $value['description'] . "\n";
    }
    echo "\n";
}

echo $closing;
