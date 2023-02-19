<?php

echo $l10n->get('cart_order_no') . ": " . $orderData['order']['id'] . "\n\n";

echo $opening . "\n\n";

echo $l10n->get('cart_product_list') . "\n\n";

foreach ($products as $value) {
    $url = $baseurl . "cart/x5cart.php?download=" . $value['download_hash'];
    echo $value['name'] . "\n\n";
    echo "\n";
}

echo $closing;
