<?php

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

return [
    'pluginVersion' => 'v2.0.2',
    'pluginName' => 'Checkout Gift Message',
    'pluginDescription' => 'Adds a configurable gift message field to standard, guest, and one page checkout.',
    'pluginAuthor' => 'Melanie Prough, PRO-Webs, Inc.',
    'pluginId' => 1678,
    'zcVersions' => ['v200', 'v210', 'v220'],
    'changelog' => 'https://github.com/mprough/Checkout-Gift-Message/blob/main/CHANGELOG.md',
    'github_repo' => 'https://github.com/mprough/Checkout-Gift-Message',
    'pluginGroups' => [],
];
