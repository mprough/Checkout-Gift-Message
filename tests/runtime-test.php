<?php

define('IS_ADMIN_FLAG', false);
define('TABLE_ORDERS', 'orders');
define('CHECKOUT_GIFT_MESSAGE_STATUS', 'true');
define('CHECKOUT_GIFT_MESSAGE_NAME', 'Card message');
define('CHECKOUT_GIFT_MESSAGE_MAX_LENGTH', '10');
define('CHECKOUT_GIFT_MESSAGE_DIRECTIONS', 'Enter a message.');
define('CHECKOUT_GIFT_MESSAGE_PLACEHOLDER', 'Your message');

class base
{
    public function attach($observer, array $events): void
    {
    }
}

class TestDatabase
{
    public array $queries = [];

    public function Execute(string $sql): object
    {
        $this->queries[] = $sql;
        return (object)['EOF' => true, 'fields' => []];
    }
}

function zen_db_prepare_input($value)
{
    return $value;
}

function zen_db_input(string $value): string
{
    return addslashes($value);
}

function zen_output_string_protected(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$db = new TestDatabase();
$_SESSION = [];
$_POST = ['checkout_gift_message' => "  1234567890EXTRA  "];

require dirname(__DIR__) . '/files/zc_plugins/CheckoutGiftMessage/v2.0.5/catalog/includes/classes/observers/auto.checkout_gift_message.php';
$observer = new zcObserverCheckoutGiftMessage();
$nothing = null;
$current_page_base = 'checkout_one';
ob_start();
$observer->update($nothing, 'NOTIFY_HTML_HEAD_END');
$checkoutScript = ob_get_clean();
assertTrue(str_contains($checkoutScript, 'checkoutGiftMessage'), 'Checkout field script did not render.');
assertTrue(str_contains($checkoutScript, "comments.insertAdjacentElement('afterend', buildEditor())"), 'OPC comments placement is missing.');
assertTrue(str_contains($checkoutScript, 'hideForCheckoutTransition'), 'Checkout transition hiding is missing.');
assertTrue(str_contains($checkoutScript, "window.addEventListener('beforeunload'"), 'Page transition hiding is missing.');
assertTrue(str_contains($checkoutScript, 'checkout-gift-message-transition'), 'Transition CSS guard is missing.');

$observer->update($nothing, 'NOTIFY_HEADER_START_CHECKOUT_SHIPPING');
assertTrue($_SESSION['checkout_gift_message'] === '1234567890', 'Server length enforcement failed.');

$ordersId = 42;
$observer->update($nothing, 'NOTIFY_ORDER_DURING_CREATE_ADDED_ORDER_HEADER', [], $ordersId);
assertTrue(str_contains($db->queries[0] ?? '', 'orders_id = 42'), 'Order storage query was not created.');

$textEmail = '';
$htmlEmail = [];
$observer->update($nothing, 'NOTIFY_ORDER_EMAIL_BEFORE_PRODUCTS', [], $textEmail, $htmlEmail);
assertTrue(str_contains($textEmail, "Card message:\n1234567890"), 'Text email output failed.');
assertTrue(str_contains($htmlEmail['ORDER_COMMENTS'] ?? '', '<strong>Card message:</strong>'), 'HTML email output failed.');

$observer->update($nothing, 'NOTIFY_CHECKOUT_PROCESS_BEFORE_CART_RESET');
assertTrue(!isset($_SESSION['checkout_gift_message']), 'Checkout session cleanup failed.');

echo 'Runtime checks passed.' . PHP_EOL;
