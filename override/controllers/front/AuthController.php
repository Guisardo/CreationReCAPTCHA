<?php
/**
 * @author    Mirko Laruina
 * @copyright 2019 Mirko Laruina
 * @license   LICENSE.txt
 */

class AuthController extends AuthControllerCore
{

    protected function processSubmitAccount()
    {
        if (Configuration::get('CREATIONRECAPTCHA_ACTIVE')) {
            $secret = Configuration::get('CREATIONRECAPTCHA_PRIVKEY');
            $response = Tools::getValue('g-recaptcha-response');
            $verifyurl = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array(
                'secret' => $secret,
                'response' => $response
            );
            $options = array(
                'http' => array (
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $out = Tools::file_get_contents($verifyurl, false, $context);
            $res = json_decode($out);

            if ($res == null || !$res->success) {
                $error = Configuration::get('CREATIONRECAPTCHA_ERRTXT');
                $this->errors[] = $error;
            }
        }

        parent::processSubmitAccount();
    }
}
