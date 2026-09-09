=== Zen Cart Checkout Shipping Gift Message Field ===

Tested on Zen Cart 1.5.0/1.5.1 and php 5.2.17 as well as PHP 5.3.13
License GPL ==

This module is a advanced level installation.

This customization adds a gift message field to the checkout shipping step of checkout.
This new field is in addition to the comments section which already exists.
The data entered in to the Gift Message text area is also available on the checkout
confirmation page for review. Additionally, the provided message is available in the
Zen Cart admin orders screen.

Module release 010/24/2012 http://pro-webs.net/ 

Please provide bug fixes and issues to support@pro-webs.net

This module can be purchased for installation on your store at 
http://pro-webs.net/store/index.php?main_page=product_info&cPath=10_21&products_id=386


===Database Changes===
Create new field in orders table.

===Core File Edits===
1) admin/includes/orders.php
2) admin/includes/classes/order.php
3) includes/classes/order.php
4) includes/modules/pages/checkout_shipping/header.php

===Override Files===
1) includes/templates/your_template/templates/tpl_checkout_confirmation_default.php
2) includes/templates/your_template/templates/tpl_checkout_shipping_default.php

===Basic Installation===
1. !!!! Backup your files and database before beginning !!!!
   
2. Upload if unchanged, merge is customized or unsure the following core files.
      admin/includes/orders.php
      admin/includes/classes/order.php
      includes/classes/order.php
      includes/modules/pages/checkout_shipping/header.php

3. Upload or if override already exists the following override files.
      includes/templates/your_template/templates/tpl_checkout_confirmation_default.php
      includes/templates/your_template/templates/tpl_checkout_shipping_default.php    

4. In your Zen Cart admin under Tools >> Install SQL Patches run the included 
      install.sql

===EOF Basic Installation===		
       
===Change History===
Date       Version  Who             Why
===============================================================================
10/24/2012  1.1	  PRO-Webs.net	Initial Release 
				
				