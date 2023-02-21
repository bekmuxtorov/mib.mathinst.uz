<?php

if (isset($orderData['products']) && is_array($orderData['products'])) {
    // Detect the required format
    $opt = false;
    $vat = $settings['vat_type'] != "none";
    $description = false;
    foreach ($orderData['products'] as $key => $value) {
        if (isset($value["option"]) && $value["option"] != "null" && strlen($value["option"])) {
            $opt = true;
        }
        if (isset($value['description'])) {
            $description = true;
        }
    }
    // Header
    echo $l10n->get("cart_name") . ";"
       . ($description ? $l10n->get("cart_descr") . ";" : "")
       . ($opt ? $l10n->get("product_option") . ";" : "")
       . $l10n->get("cart_qty") . ";"
       . $l10n->get("cart_price") . ";"
       . ($vat ? $l10n->get("cart_vat") .";" : "")
       . $l10n->get("cart_subtot");
    // Contents
    foreach ($orderData['products'] as $key => $value) {
        echo "\n" 
        . strip_tags(str_replace(array("\n", "\r"), "", $value["name"])) . ";" 
        . ($description ? strip_tags(str_replace(array("\n", "\r"), "", $value["description"])) . ";" : "")
        . (($opt && $value["option"] != "null") ? $value["option"] . ( $value["suboption"] != "null" ? " " . $value["suboption"] . ";" : "") : "")
        . $value["quantity"] . ";"
        . ($settings['vat_type'] == "excluded" ? $value["singlePrice"] : $value['singlePricePlusVat']) . ";"
        . ($vat ? $value["vat"] .";" : "")
        . ($settings['vat_type'] == "excluded" ? $value["price"] : $value['pricePlusVat']);
    }
}
