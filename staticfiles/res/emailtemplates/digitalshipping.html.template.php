<?php
    // This is the email separator line
    $separator = "<tr><td colspan=\"2\" style=\"margin: 10px 0; height: 10px; font-size: 0.1px; border-bottom: 1px solid [email:emailBackground];\">&nbsp;</td></tr>";
?>
<table border="0" width="100%" style="[email:contentStyle]">
    <tr>
        <td style="[email:contentFontFamily] text-align: center; font-weight: bold;font-size: 1.11em">
            <?php echo $l10n->get('cart_order_no') . ": " . $orderData['order']['id'] ?>
        </td>
    </tr>
    <!-- Opening Message -->
    <tr><td><?php echo $opening ?></td></tr>
    <?php echo $separator ?>
    <!-- Order Data -->
    <tr>
        <td style="padding: 5px 0 0 0;">
            <h3 style="font-size: 1.11em"><?php echo $l10n->get('cart_product_list') ?></h3>
            <table cellpadding="5" width="100%" style="[email:contentStyle] border-collapse: collapse;">
                <?php $i = 0; foreach ($products as $value): ?>
                    <?php $url = $baseurl . "cart/x5cart.php?download=" . $value['download_hash']; ?>
                    <tr valign="top" style="[email:contentFontFamily] vertical-align: top"<?php ($i%2 ? " bgcolor=\"#EEEEEE\"" : "") ?>>
                        <td>
                            <?php echo $value['name'] ?>
                            <?php if (isset($value['image'])): ?>
                            <img src="<?php echo $baseurl . $value['image'] ?>" alt="<?php echo $value['name'] ?>" style="max-width: 250px;" />
                            <?php endif; ?>
                        </td>
                        <td><a href="<?php echo $url ?>"><?php echo $url ?></a></td>
                    </tr>
                    <?php if (isset($value['description']) && strlen($value['description'])): ?>
                    <tr valign="top" style="[email:contentFontFamily] vertical-align: top"<?php ($i%2 ? " bgcolor=\"#EEEEEE\"" : "") ?>>
                        <td colspan="2"><?php echo $value['description'] ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
    <!-- Closing message -->
    <?php echo $separator ?>
    <tr><td style="padding: 5px 0 0 0;"><?php echo $closing ?></td></tr>
</table>
