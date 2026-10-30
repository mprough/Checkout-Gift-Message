<?php
define('IS_ADMIN_FLAG', true);
define('TABLE_ORDERS', 'orders');
define('CHECKOUT_GIFT_MESSAGE_NAME', 'Card <message>');
define('CHECKOUT_GIFT_MESSAGE_STATUS', 'false');
class base {
    public array $events = [];
    public function attach($observer, array $events): void { $this->events = $events; }
}
class TestDatabase {
    public ?string $message = "Happy <birthday>!\nLove & hugs";
    public array $queries = [];
    public function Execute(string $sql): object {
        $this->queries[] = $sql;
        return (object)['EOF' => $this->message === null, 'fields' => ['gift_message' => $this->message]];
    }
}
function zen_output_string_protected(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function check(bool $ok, string $message): void {
    if (!$ok) { throw new RuntimeException($message); }
}
require dirname(__DIR__) . '/files/zc_plugins/CheckoutGiftMessage/v2.0.5/admin/includes/classes/observers/auto.checkout_gift_message_admin.php';
$db = new TestDatabase();
$observer = new zcObserverCheckoutGiftMessageAdmin();
$source = null;
foreach ($observer->events as $event) {
    $params = $event === 'NOTIFY_ADMIN_ORDERS_CONTENT_UNDER_PRODUCTS' ? ['oID' => 42] : '42';
    $output = false;
    $observer->update($source, $event, $params, $output);
    check(str_contains($output, 'Happy &lt;birthday&gt;!<br'), "$event did not render escaped multiline text");
    check(str_contains($output, 'Card &lt;message&gt;'), "$event did not escape the configured label");
    check(str_contains(end($db->queries), 'orders_id = 42 LIMIT 1'), "$event queried the wrong order");
    $output = '<p>Other plugin</p>';
    $observer->update($source, $event, $params, $output);
    check(str_starts_with($output, '<p>Other plugin</p>'), "$event replaced other output");
    foreach (['', null] as $empty) {
        $db->message = $empty;
        $output = false;
        $observer->update($source, $event, $params, $output);
        check($output === false, "$event rendered an empty or missing message");
    }
    $db->message = "Happy <birthday>!\nLove & hugs";
}
$count = count($db->queries);
$output = false;
$observer->update($source, 'NOTIFY_ADMIN_ORDERS_INVOICE_ADDITIONAL_DATA_BOTTOM', 0, $output);
check(count($db->queries) === $count && $output === false, 'Invalid order ID was not ignored');
echo "Admin order, invoice, and packing slip runtime checks passed.\n";
