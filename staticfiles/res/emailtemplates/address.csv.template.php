<?php
if (isset($address) && is_array($address)) {
    $headers = array();
    $contents = array();
    foreach ($address as $key => $value) {
        if (trim($value['value']) != "" && $value['label'] != l10n('cart_field_attachment')) {
            $headers[] = $value['label'];
            $contents[] = $value['value'];
        }
    }
    echo @implode(";", $headers) . "\n" . @implode(";", $contents);
}
