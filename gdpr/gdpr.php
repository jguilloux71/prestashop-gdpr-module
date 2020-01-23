<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Zido <jguilloux@gmail.com>
 *  @copyright 2019 Zido
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  https://github.com/jguilloux71/
 */

if (!defined('_PS_VERSION_')) {
    exit;
}


class Gdpr extends Module {

    public function __construct() {
        $this->name = 'gdpr';
        $this->tab = 'administration';
        $this->version = '1.0';
        $this->author = 'Zido';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7');
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('GDPR');
        $this->description = $this->l('Add a cookies consent banner and text about privacy data.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('COOKIES_CONSENT_TEXT')) {
            $this->warning = $this->l('GDPR module need to be configured');
        }

        if (!Configuration::get('PRIVACY_DATA_LINK') && !Configuration::get('PRIVACY_DATA_TEXT')) {
            $this->warning = $this->l('GDPR module need to be configured');
        }
    }


    public function install() {
        return parent::install()
            && $this->registerHook('header')
            && Configuration::updateValue('COOKIES_CONSENT_TEXT', $this->l('This website uses cookies to ensure you get the best experience on our website.'))
            && Configuration::updateValue('COOKIES_PALETTE_BACKGROUND_COLOR', '#8A2F29')
            && Configuration::updateValue('COOKIES_BUTTON_BACKGROUND_COLOR', '#101010')
            && Configuration::updateValue('PRIVACY_DATA_RADIO', 'link')
            && Configuration::updateValue('PRIVACY_DATA_LINK', 'http://my.url/privacy-data')
            && Configuration::updateValue('PRIVACY_DATA_TEXT', $this->l('Privacy data text here'), true);
    }


    public function uninstall() {
        return parent::uninstall()
            && Configuration::deleteByName('COOKIES_CONSENT_TEXT')
            && Configuration::deleteByName('COOKIES_PALETTE_BACKGROUND_COLOR')
            && Configuration::deleteByName('COOKIES_BUTTON_BACKGROUND_COLOR')
            && Configuration::deleteByName('PRIVACY_DATA_RADIO')
            && Configuration::deleteByName('PRIVACY_DATA_LINK')
            && Configuration::deleteByName('PRIVACY_DATA_TEXT');
    }


    /**
     * Get HTML link for 'learn more'
     *
     * HTML link depending of selected radio button
     *
     * If privacy data radio button is 'HTML link' then link will be PRIVACY_DATA_LINK
     * Else link will be the autogenerated URL by template 'privacydata'
     */
    private function _getPrivacyDataLink() {
        $privacy_data_link = Configuration::get('PRIVACY_DATA_LINK');

        if (Configuration::get('PRIVACY_DATA_RADIO') == 'custom-text') {
            $privacy_data_link = $this->context->link->getModuleLink('gdpr', 'privacydata');
        }

        return $privacy_data_link;
    }


    public function hookDisplayHeader() {
        // Specific CSS for 'cookies consent', in <HEAD> tag
        $this->context->controller->addCSS($this->_path . 'views/css/cookies-consent.css', 'all');

        // JS for cookies consent (from https://www.osano.com/cookieconsent), in <HEAD> tag
        $this->context->controller->addJS($this->_path . 'views/js/cookies-consent.js', 'all');

        $this->context->smarty->assign(
            array(
                'gdpr_cookies_consent_text' => Configuration::get('COOKIES_CONSENT_TEXT'),
                'gdpr_cookies_palette_background_color' => Configuration::get('COOKIES_PALETTE_BACKGROUND_COLOR'),
                'gdpr_cookies_button_background_color' => Configuration::get('COOKIES_BUTTON_BACKGROUND_COLOR'),
                'gdpr_privacy_data_link' => $this->_getPrivacyDataLink(),
                'gdpr_privacy_data_text' => Configuration::get('PRIVACY_DATA_TEXT')
            )
        );
        return $this->display(__FILE__, 'gdpr.tpl');
    }


    public function getContent() {
        $output = null;
        $errors = 0;
 
        if (Tools::isSubmit('submit' . $this->name)) {
            $gdpr_cookies_consent_text = strval(Tools::getValue('COOKIES_CONSENT_TEXT'));
            $gdpr_cookies_palette_background_color = strval(Tools::getValue('COOKIES_PALETTE_BACKGROUND_COLOR'));
            $gdpr_cookies_button_background_color = strval(Tools::getValue('COOKIES_BUTTON_BACKGROUND_COLOR'));
            $gdpr_privacy_data_radio = strval(Tools::getValue('PRIVACY_DATA_RADIO'));
            $gdpr_privacy_data_link = strval(Tools::getValue('PRIVACY_DATA_LINK'));
            $gdpr_privacy_data_text = strval(Tools::getValue('PRIVACY_DATA_TEXT'));

            if (!$gdpr_cookies_consent_text || empty($gdpr_cookies_consent_text)) {
                $errors += 1;
                $output .= $this->displayError( $this->l('Invalid cookies consent message') );
            }

            if (!$gdpr_cookies_palette_background_color || empty($gdpr_cookies_palette_background_color)) {
                $errors += 1;
                $output .= $this->displayError( $this->l('Invalid value for cookies banner background color') );
            }

            if (!$gdpr_cookies_button_background_color || empty($gdpr_cookies_button_background_color)) {
                $errors += 1;
                $output .= $this->displayError( $this->l('Invalid value for cookies button background color') );
            }

            if (($gdpr_privacy_data_radio == 'link') && ((!gdpr_privacy_data_link) || empty($gdpr_privacy_data_link))) {
                $errors += 1;
                $output .= $this->displayError( $this->l('Invalid privacy data link'));
            }

            if (($gdpr_privacy_data_radio == 'custom-text') && ((!gdpr_privacy_data_text) || empty($gdpr_privacy_data_text))) {
                $errors += 1;
                $output .= $this->displayError( $this->l('Invalid privacy data custom text'));
            }

            if ($errors == 0) {
                Configuration::updateValue('COOKIES_CONSENT_TEXT', $gdpr_cookies_consent_text);
                Configuration::updateValue('COOKIES_PALETTE_BACKGROUND_COLOR', $gdpr_cookies_palette_background_color);
                Configuration::updateValue('COOKIES_BUTTON_BACKGROUND_COLOR', $gdpr_cookies_button_background_color);
                Configuration::updateValue('PRIVACY_DATA_RADIO', $gdpr_privacy_data_radio);
                Configuration::updateValue('PRIVACY_DATA_LINK', $gdpr_privacy_data_link);
                Configuration::updateValue('PRIVACY_DATA_TEXT', $gdpr_privacy_data_text, true);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
            elseif ($errors == 1) {
                $output .= $this->displayError('1 ' . $this->l('error found'));
            }
            else {
                $output .= $this->displayError($errors . ' ' . $this->l('errors found'));
            }
        }

        return $output . $this->displayForm();
    }


    public function displayForm() {
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'tabs' => array(
                'cookies' => $this->l('Cookies consent settings'),
                'privacy-data' => $this->l('Privacy data settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Consent message'),
                    'name' => 'COOKIES_CONSENT_TEXT',
                    'size' => 100,
                    'required' => true,
                    'tab' => 'cookies'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Banner background color'),
                    'name' => 'COOKIES_PALETTE_BACKGROUND_COLOR',
                    'size' => 8,
                    'required' => true,
                    'tab' => 'cookies'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Button background color'),
                    'name' => 'COOKIES_BUTTON_BACKGROUND_COLOR',
                    'size' => 8,
                    'required' => true,
                    'tab' => 'cookies'
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Select where is stored your privacy data policy'),
                    'name' => 'PRIVACY_DATA_RADIO',
                    'required' => true,
                    'class' => 't',
                    'tab' => 'privacy-data',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'privacy-data-radio-link',
                            'value'=> 'link',
                            'label'=> $this->l('From the link below'),
                        ),
                        array(
                            'id' => 'privacy-data-radio-text',
                            'value'=> 'custom-text',
                            'label'=> $this->l('From your custom text below'),
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Privacy data link'),
                    'name' => 'PRIVACY_DATA_LINK',
                    'size' => 8,
                    'tab' => 'privacy-data'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Privacy data text'),
                    'name' => 'PRIVACY_DATA_TEXT',
                    'lang' => 0,
                    'class' => 'rte',
                    'row' => 32,
                    'autoload_rte' => true,
					'hint' => $this->l('Invalid characters: ') . ' <>;=#{}',
                    'tab' => 'privacy-data'
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button btn btn-default pull-right'
            )
        );
     
        return $this->_helperForm($fields_form);
    }


    private function _helperForm($form) {
        // Get default Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
     
        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
     
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value['COOKIES_CONSENT_TEXT'] = Configuration::get('COOKIES_CONSENT_TEXT');
        $helper->fields_value['COOKIES_PALETTE_BACKGROUND_COLOR'] = Configuration::get('COOKIES_PALETTE_BACKGROUND_COLOR');
        $helper->fields_value['COOKIES_BUTTON_BACKGROUND_COLOR'] = Configuration::get('COOKIES_BUTTON_BACKGROUND_COLOR');
        $helper->fields_value['PRIVACY_DATA_RADIO'] = Configuration::get('PRIVACY_DATA_RADIO');
        $helper->fields_value['PRIVACY_DATA_LINK'] = Configuration::get('PRIVACY_DATA_LINK');
        $helper->fields_value['PRIVACY_DATA_TEXT'] = Configuration::get('PRIVACY_DATA_TEXT');

        return $helper->generateForm($form);
    }
}
?>
