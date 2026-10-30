<?php

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

class zcObserverCheckoutGiftMessage extends base
{
    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_HEADER_START_CHECKOUT_SHIPPING',
            'NOTIFY_HEADER_START_CHECKOUT_PAYMENT',
            'NOTIFY_HEADER_START_CHECKOUT_CONFIRMATION',
            'NOTIFY_HEADER_START_CHECKOUT_ONE',
            'NOTIFY_HEADER_START_CHECKOUT_ONE_CONFIRMATION',
            'NOTIFY_HTML_HEAD_END',
            'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER',
            'NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS',
            'NOTIFY_CHECKOUT_PROCESS_BEFORE_CART_RESET',
        ]);
    }

    public function update(&$class, $eventID, $paramsArray = [], &$param1 = null, &$param2 = null)
    {
        if (!$this->enabled()) {
            return;
        }

        switch ($eventID) {
            case 'NOTIFY_HEADER_START_CHECKOUT_SHIPPING':
            case 'NOTIFY_HEADER_START_CHECKOUT_PAYMENT':
            case 'NOTIFY_HEADER_START_CHECKOUT_CONFIRMATION':
            case 'NOTIFY_HEADER_START_CHECKOUT_ONE':
            case 'NOTIFY_HEADER_START_CHECKOUT_ONE_CONFIRMATION':
                $this->capturePost();
                break;

            case 'NOTIFY_HTML_HEAD_END':
                global $current_page_base;
                require dirname(__DIR__, 2) . '/modules/checkout_gift_message.php';
                break;

            case 'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER':
                $this->saveToOrder((int)$param1);
                break;

            case 'NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS':
                $this->addToEmail($param1, $param2);
                break;

            case 'NOTIFY_CHECKOUT_PROCESS_BEFORE_CART_RESET':
                unset($_SESSION['checkout_gift_message']);
                break;
        }
    }

    private function enabled(): bool
    {
        return defined('CHECKOUT_GIFT_MESSAGE_STATUS') && CHECKOUT_GIFT_MESSAGE_STATUS === 'true';
    }

    private function maxLength(): int
    {
        $length = defined('CHECKOUT_GIFT_MESSAGE_MAX_LENGTH') ? (int)CHECKOUT_GIFT_MESSAGE_MAX_LENGTH : 500;
        return max(1, min(5000, $length));
    }

    private function capturePost(): void
    {
        if (!array_key_exists('checkout_gift_message', $_POST)) {
            return;
        }

        $posted = is_scalar($_POST['checkout_gift_message']) ? (string)$_POST['checkout_gift_message'] : '';
        $message = trim((string)zen_db_prepare_input($posted));
        $_SESSION['checkout_gift_message'] = function_exists('mb_substr')
            ? mb_substr($message, 0, $this->maxLength())
            : substr($message, 0, $this->maxLength());
    }

    private function saveToOrder(int $ordersId): void
    {
        global $db;

        if ($ordersId < 1) {
            return;
        }

        $message = (string)($_SESSION['checkout_gift_message'] ?? '');
        $db->Execute(
            "UPDATE " . TABLE_ORDERS . " SET gift_message = '" . zen_db_input($message) . "' WHERE orders_id = " . $ordersId . " LIMIT 1"
        );
    }

    private function addToEmail(&$textEmail, &$htmlEmail): void
    {
        $message = (string)($_SESSION['checkout_gift_message'] ?? '');
        if ($message === '') {
            return;
        }

        $name = defined('CHECKOUT_GIFT_MESSAGE_NAME') && trim(CHECKOUT_GIFT_MESSAGE_NAME) !== ''
            ? CHECKOUT_GIFT_MESSAGE_NAME
            : 'Gift message';
        $safeName = zen_output_string_protected($name);
        $safeMessage = zen_output_string_protected($message);
        $textEmail .= $safeName . ":\n" . $safeMessage . "\n\n";
        $htmlEmail['ORDER_COMMENTS'] = ($htmlEmail['ORDER_COMMENTS'] ?? '')
            . (($htmlEmail['ORDER_COMMENTS'] ?? '') !== '' ? '<br><br>' : '')
            . '<strong>' . $safeName . ':</strong><br>' . nl2br($safeMessage);
    }
}
