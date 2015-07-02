Constant Contact
===============
Add Magento users to Constant Contact contacts and lists.

Description
-----------
Add Magento users to Constant Contact contacts and lists. Users
can subscribe to lists by submitting a form on this page:

/constantcontact/form/subscribe

<img src="md/subscribe.png" />

or by subscribing to the Magento newsletter.

Installation Instructions
-------------------------

Requires the Constant Contact PHP-SDK: https://github.com/constantcontact/php-sdk. 
You will need to install composer to install the SDK. Composer will place the SDK 
and autoload.php under the vendor/constantcontact. 

<pre>
    {
        "require": {
            "constantcontact/constantcontact": "2.0.*"
        }
    }
</pre>

This extension requires vendor/autoload.php to be in your Magento site's include path. 
Without the SDK, this extension WILL NOT work.

Upload this module's files to root of your 
Magento install. Let the setup script run. After the setup is completed, several new 
tables will be created:

<pre>
aydus_constantcontact_config
aydus_constantcontact_customer_list
aydus_constantcontact_subscriber_contact
</pre>

In the admin under System -> Configuration -> General -> Constant Contact,
configure the extension:

<img src="md/configuration.png" />
