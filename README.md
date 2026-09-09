# Checkout Gift Message for Zen Cart

Checkout Gift Message adds a configurable message field to Zen Cart checkout without replacing core files or template files. It supports standard checkout, guest checkout, and One Page Checkout (OPC), including physical and virtual orders.

## Features

- Custom field name
- Custom directions
- Custom placeholder
- Configurable maximum entry length from 1 through 5,000 characters
- Browser and server length enforcement
- Checkout confirmation review
- Order email display
- Admin order display
- Existing `orders.gift_message` data preserved during migration
- Encapsulated Plugin Manager installation with no core edits

## Compatibility

- Zen Cart 2.0.x with the PHP versions supported by that Zen Cart branch
- Zen Cart 2.1.x with the PHP versions supported by that Zen Cart branch
- Zen Cart 2.2.x with the PHP versions supported by that Zen Cart branch
- PHP 8.0 through PHP 8.5 within those Zen Cart limits
- One Page Checkout, including OPC guest checkout
- Standard Zen Cart checkout, including guest checkout

## Installation

1. Back up the store files and database.
2. Copy the contents of `files` to the store root.
3. In admin, open Modules, Plugin Manager.
4. Install Checkout Gift Message v2.0.2.
5. Open Configuration, Customer Details to set the field name, directions, placeholder, and maximum length.

The installer adds the `gift_message` column only when it is absent. A legacy installation is detected through that existing column, and its order data is left intact.

## Upgrade

Remove the old loose files by comparing them with `legacy/v1.1`. Do not copy the archived legacy files into a current store. Install v2.0.2 through Plugin Manager. The new package uses observers and the existing database column.

## Uninstall

Uninstall the plugin in Modules, Plugin Manager. Its configuration settings are removed. The `orders.gift_message` column and saved order messages remain because they are store business records.

## Database changes

The plugin adds a nullable `gift_message` text column to the Zen Cart `orders` table when needed. No separate tables are created.

## Support and license

Maintained by Melanie Prough, [PRO-Webs, Inc.](https://pro-webs.net/). Report bugs through the [PRO-Webs helpdesk](https://prowebsinc.zohodesk.com/portal/en/newticket).

Free distribution is provided under GPL 2.0 without warranty. Installation, configuration, customization, and compatibility work for third party checkout replacements are not included.

Original Zen Cart listing: [Checkout Gift Message](https://www.zen-cart.com/plugins/checkout-gift-message-vb1678)

Source repository: [mprough/Checkout-Gift-Message](https://github.com/mprough/Checkout-Gift-Message)
