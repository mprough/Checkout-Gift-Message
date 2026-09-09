# Gift Message in Checkout SQL Install. Please backup your database first!
ALTER TABLE orders ADD gift_message text NULL default NULL after delivery_address_format_id;