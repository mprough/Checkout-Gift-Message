#!/usr/bin/env bash
set -euo pipefail

root="$(cd "$(dirname "$0")/.." && pwd)"
plugin="$root/files/zc_plugins/CheckoutGiftMessage/v2.0.5"

test -f "$plugin/manifest.php"
test -f "$plugin/Installer/ScriptedInstaller.php"
test -f "$plugin/catalog/includes/classes/observers/auto.checkout_gift_message.php"
test -f "$plugin/catalog/includes/modules/checkout_gift_message.php"
test -f "$plugin/admin/includes/classes/observers/auto.checkout_gift_message_admin.php"

! find "$root/files" -type f | grep -Eq '/(includes/classes/order.php|admin/orders.php|checkout_shipping/header_php.php)$'

grep -q "'pluginVersion' => 'v2.0.5'" "$plugin/manifest.php"
grep -q "NOTIFY_HTML_HEAD_END" "$plugin/catalog/includes/classes/observers/auto.checkout_gift_message.php"
grep -q "Checkout Gift Message v2.0.5" "$root/README.md"
grep -q "## v2.0.5" "$root/CHANGELOG.md"

find "$root/files" -type f -name '*.php' -print0 | xargs -0 -n1 php -l
php "$root/tests/runtime-test.php"
php "$root/tests/admin-runtime-test.php"
echo 'Package structure and PHP syntax checks passed.'
