<?php
/**
 * @author    Mirko Laruina
 * @copyright 2019 Mirko Laruina
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreationRecaptcha extends Module
{
    public function __construct()
    {
        $this->name = "creationrecaptcha";
        $this->tab = "front_office_features";
        $this->version = '1.0.2';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7.99.99');
        $this->author = 'Mirko Laruina';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Add reCAPTCHA to account creation form');
        $this->description = $this->l('No more spam accounts');
    }

    public function install()
    {
        return parent::install() &&
        $this->registerHook('createAccountForm') &&
        Configuration::updateValue('CREATIONRECAPTCHA_ACTIVE', 1) &&
        Configuration::updateValue('CREATIONRECAPTCHA_KEY', '') &&
        Configuration::updateValue('CREATIONRECAPTCHA_PRIVKEY', '') &&
        Configuration::updateValue('CREATIONRECAPTCHA_LABEL', 'Select "I\'m not a robot" to continue') &&
        Configuration::updateValue('CREATIONRECAPTCHA_ERRTXT', 'CAPTCHA is not valid') &&
        Configuration::updateValue('CREATIONRECAPTCHA_SUBDISABLE', 1);
    }

    public function uninstall()
    {
        return Configuration::deleteByName('CREATIONRECAPTCHA_ACTIVE') &&
        Configuration::deleteByName('CREATIONRECAPTCHA_KEY') &&
        Configuration::deleteByName('CREATIONRECAPTCHA_PRIVKEY') &&
        Configuration::deleteByName('CREATIONRECAPTCHA_LABEL') &&
        Configuration::deleteByName('CREATIONRECAPTCHA_ERRTXT') &&
        Configuration::deleteByName('CREATIONRECAPTCHA_SUBDISABLE') &&
        parent::uninstall();
    }

    public function getContent()
    {
        $output = null;
    
        if (Tools::isSubmit('submit'.$this->name)) {
            $status = Tools::getValue('CREATIONRECAPTCHA_ACTIVE');
            $key = Tools::getValue('CREATIONRECAPTCHA_KEY');
            $privkey = Tools::getValue('CREATIONRECAPTCHA_PRIVKEY');
            $errtxt = Tools::getValue('CREATIONRECAPTCHA_ERRTXT');
            $label = Tools::getValue('CREATIONRECAPTCHA_LABEL');
            $subdisable = Tools::getValue('CREATIONRECAPTCHA_SUBDISABLE');

            Configuration::updateValue('CREATIONRECAPTCHA_ACTIVE', $status);
            Configuration::updateValue('CREATIONRECAPTCHA_KEY', $key);
            Configuration::updateValue('CREATIONRECAPTCHA_PRIVKEY', $privkey);
            Configuration::updateValue('CREATIONRECAPTCHA_ERRTXT', $errtxt);
            Configuration::updateValue('CREATIONRECAPTCHA_LABEL', $label);
            Configuration::updateValue('CREATIONRECAPTCHA_SUBDISABLE', $subdisable);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
    
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Init Fields form array
        $fieldsForm = array();
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show reCAPTCHA'),
                    'name' => 'CREATIONRECAPTCHA_ACTIVE',
                    'is_bool' => true,
                    'required' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Label'),
                    'name' => 'CREATIONRECAPTCHA_LABEL',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Error message'),
                    'name' => 'CREATIONRECAPTCHA_ERRTXT',
                    'required' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Disable submit'),
                    'name' => 'CREATIONRECAPTCHA_SUBDISABLE',
                    'is_bool' => true,
                    'required' => true,
                    'values' => array(
                        array(
                            'id' => 'sub_on',
                            'value' => true,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'sub_off',
                            'value' => false,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'desc' => $this->l('Disable submit button until reCAPTCHA is checked')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Site key'),
                    'name' => 'CREATIONRECAPTCHA_KEY',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Private key'),
                    'name' => 'CREATIONRECAPTCHA_PRIVKEY',
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['CREATIONRECAPTCHA_ACTIVE'] = Configuration::get('CREATIONRECAPTCHA_ACTIVE');
        $helper->fields_value['CREATIONRECAPTCHA_KEY'] = Configuration::get('CREATIONRECAPTCHA_KEY');
        $helper->fields_value['CREATIONRECAPTCHA_PRIVKEY'] = Configuration::get('CREATIONRECAPTCHA_PRIVKEY');
        $helper->fields_value['CREATIONRECAPTCHA_ERRTXT'] = Configuration::get('CREATIONRECAPTCHA_ERRTXT');
        $helper->fields_value['CREATIONRECAPTCHA_LABEL'] = Configuration::get('CREATIONRECAPTCHA_LABEL');
        $helper->fields_value['CREATIONRECAPTCHA_SUBDISABLE'] = Configuration::get('CREATIONRECAPTCHA_SUBDISABLE');

        return $helper->generateForm($fieldsForm);
    }

    public function hookCreateAccountForm()
    {
        $active = (int)Configuration::get('CREATIONRECAPTCHA_ACTIVE');
        $creationrecaptcha_key = Configuration::get('CREATIONRECAPTCHA_KEY');
        $creationrecaptcha_label = Configuration::get('CREATIONRECAPTCHA_LABEL');
        $creationrecaptcha_subdisable = Configuration::get('CREATIONRECAPTCHA_SUBDISABLE');

        $this->context->smarty->assign(array(
            'creationrecaptcha_key' => $creationrecaptcha_key,
            'creationrecaptcha_label' => $creationrecaptcha_label,
            'creationrecaptcha_subdisable' => $creationrecaptcha_subdisable
        ));

        if ($active == 1) {
            return $this->display(dirname(__FILE__), '/views/templates/hook/recaptcha.tpl');
        }
    }
}
