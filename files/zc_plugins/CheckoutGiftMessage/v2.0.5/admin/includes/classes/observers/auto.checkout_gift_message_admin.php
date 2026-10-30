<?php

if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    die('Illegal Access');
}

class zcObserverCheckoutGiftMessageAdmin extends base
{
    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_ADMIN_ORDERS_CONTENT_UNDER_PRODUCTS',
            'NOTIFY_ADMIN_ORDERS_INVOICE_ADDITIONAL_DATA_BOTTOM',
            'NOTIFY_ADMIN_ORDERS_PACKINGSLIP_ADDITIONAL_DATA_BOTTOM',
        ]);
    }

    public function update(&$class, $eventID, $paramsArray = [], &$extraContent = null)
    {
        global $db;

        // The order screen supplies an array; print notifiers supply the order ID.
        $ordersId = (int)(is_array($paramsArray) ? ($paramsArray['oID'] ?? 0) : $paramsArray);
        if ($ordersId < 1) {
            return;
        }

        $result = $db->Execute("SELECT gift_message FROM " . TABLE_ORDERS . " WHERE orders_id = " . $ordersId . " LIMIT 1");
        $message = $result->EOF ? '' : (string)$result->fields['gift_message'];
        if ($message === '') {
            return;
        }

        $name = defined('CHECKOUT_GIFT_MESSAGE_NAME') && trim(CHECKOUT_GIFT_MESSAGE_NAME) !== ''
            ? CHECKOUT_GIFT_MESSAGE_NAME
            : 'Gift message';
        if ($eventID !== 'NOTIFY_ADMIN_ORDERS_CONTENT_UNDER_PRODUCTS') {
            // Print hooks start with false and may already contain another observer's output.
            $extraContent = (string)$extraContent . '<div class="checkout-gift-message" style="overflow-wrap: anywhere;">'
                . '<strong>' . zen_output_string_protected($name) . ':</strong><br>'
                . nl2br(zen_output_string_protected($message)) . '</div>';
            return;
        }

        $extraContent .= '<div class="row"><div class="col-12"><div class="card mb-3">'
            . '<div class="card-header"><strong>' . zen_output_string_protected($name) . '</strong></div>'
            . '<div class="card-body">' . nl2br(zen_output_string_protected($message)) . '</div>'
            . '</div></div></div>';
    }
}
