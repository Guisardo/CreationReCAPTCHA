# CreationReCAPTCHA
Add a reCAPTCHA v2 to Prestashop account creation form. It should work for Prestashop 1.5 to 1.7. Tested only on 1.6.1.3.
In the settings, site and private reCAPTCHA key have to be set for the module to work properly.

`Disable submit` feature will (generally) NOT work by default, `/views/templates/hook/recaptcha.tpl` should be edited in order to match the correct button in the theme.