<?php
/**
* 2007-2017 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Phlikeboxwithcounter extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'phlikeboxwithcounter';
        $this->tab = 'social_networks';
        $this->version = '1.0.2';
        $this->author = 'PrestaHeroes';
        $this->need_instance = 1;
        //$this->module_key = '';

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->secure_key = Tools::encrypt($this->name);

        $this->displayName = $this->l('Facebook Like Button for the Web');
        $this->description = $this->l(
            'Module supports all Facebook Like Button Layouts and Actions.'
        );

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }


    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (_PS_MODE_DEMO_) {
            return false;
        }

        //---Update Configuration for Devices
        $this->updateConfiguration('PC');
        $this->updateConfiguration('MB');
        $this->updateConfiguration('TB');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        if (_PS_MODE_DEMO_) {
            return false;
        }

        //include(dirname(__FILE__).'/sql/uninstall.php');

        //---Delete the configuration for Devices
        $this->deleteConfiguration('PC');
        $this->deleteConfiguration('MB');
        $this->deleteConfiguration('TB');

        return parent::uninstall();
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = $this->displayMessage(1, array('name' => $this->displayName));

        if (_PS_MODE_DEMO_) {
            $output .= $this->displayMessage(2);
        }

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPhlikeboxwithcounter')) == true) {
            if (!_PS_MODE_DEMO_) {
                $output .= $this->postProcess();
            }
        }


        $this->context->smarty->assign('module_dir', $this->_path);

        $this->context->smarty->assign('demo_mode', _PS_MODE_DEMO_);

        //$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    protected function displayMessage($message, $params = null)
    {
        if ($message) {
            $this->smarty->assign(array(
                'message' => $message,
                'params' => $params
            ));
            return $this->display(__FILE__, 'views/templates/admin/html_info.tpl');
        }
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPhlikeboxwithcounter';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /* returns most front office hooks */
    public function getFrontHooks()
    {
        $hooks = Hook::getHooks(true);
        $words = array('action', 'Admin', 'BackOffice', 'dashboard', 'shoppingCartExtra','displayProductExtraContent');
        foreach ($hooks as $index => $hook) {
            foreach ($words as $word) {
                if ((Tools::strpos($hook['name'], $word) !== false) || ($hook['name'] == 'Header')) {
                    unset($hooks[$index]);
                }
            }
        }
        return $hooks;
    }

    /**
     * Create the structure of your form.
     */
    public function returnInputsByDevice($device)
    {
        $hooks = $this->getFrontHooks();

        $options_type_action = array(
            array(
                'id_option' => 'like',
                'name' => 'like'
            ),
            array(
                'id_option' => 'recommend',
                'name' => 'recommend'
            )
        );

        $options_type_button = array(
            array(
                'id_option' => 'standard',
                'name' => 'standard'
            ),
            array(
                'id_option' => 'box_count',
                'name' => 'box_count'
            ),
            array(
                'id_option' => 'button_count',
                'name' => 'button_count'
            ),
            array(
                'id_option' => 'button',
                'name' => 'button'
            )
        );

        $options_button_size = array(
            array(
                'id_option' => 'small',
                'name' => 'small'
            ),
            array(
                'id_option' => 'large',
                'name' => 'large'
            )
        );

        return array(
            array(
                'type' => 'switch',
                'tab' => $device,
                'label' => $this->l('Display'),
                'hint' => $this->l('Display Widget'),
                'name' => 'PH_FLB_DISPLAY_MODULE_'.$device.'',
                'is_bool' => true,
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
                ),
            ),
            array(
                'type' => 'select',
                'tab' => $device,
                'label' => $this->l('Type Button:'),
                'name' => 'PH_TYPE_BUTTON_HOOK_'.$device.'',
                'multiple' => false,
                'options' => array(
                    'query' => $options_type_button,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'tab' => $device,
                'label' => $this->l('Type action'),
                'name' => 'PH_TYPE_ACTION_HOOK_'.$device.'',
                'multiple' => false,
                'options' => array(
                    'query' => $options_type_action,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'select',
                'tab' => $device,
                'label' => $this->l('Button Size:'),
                'name' => 'PH_BOX_SIZE_HOOK_'.$device.'',
                'multiple' => false,
                'options' => array(
                    'query' => $options_button_size,
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'switch',
                'tab' => $device,
                'label' => $this->l('Show Friends Faces:'),
                'name' => 'PH_SHOW_FACES_HOOK_'.$device.'',
                'is_bool' => true,
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
                'type' => 'switch',
                'tab' => $device,
                'label' => $this->l('Display Share Button:'),
                'name' => 'PH_SHARE_BUTTON_HOOK_'.$device.'',
                'is_bool' => true,
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
                'type' => 'select',
                'tab' => $device,
                'class' => 'chosen fixed-width-xl',
                'label' => $this->l('Hook into:'),
                'name' => 'PH_FLB_HOOK_'.$device.'[]',
                'hint' => $this->l(
                    'Choose one or more hooks to show widget.'
                ),
                'multiple' => true,
                'options' => array(
                    'query' => $hooks,
                    'id' => 'id_hook',
                    'name' => 'name'
                )
            )
        );
    }

    protected function getConfigForm()
    {
        $inputs_pc = $this->returnInputsByDevice('PC');
        $inputs_mb = $this->returnInputsByDevice('MB');
        $inputs_tb = $this->returnInputsByDevice('TB');

        $config_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'tabs' => array(
                    'PC' => $this->l('Desktop Widget'),
                    'MB' => $this->l('Mobile Widget'),
                    'TB' => $this->l('Tablet Widget'),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        foreach ($inputs_pc as $input_pc) {
            $config_form['form']['input'][] = $input_pc;
        }
        foreach ($inputs_mb as $input_mb) {
            $config_form['form']['input'][] = $input_mb;
        }
        foreach ($inputs_tb as $input_tb) {
            $config_form['form']['input'][] = $input_tb;
        }
        return $config_form;
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $config_pc = $this->getConfigFormValuesByDevice('PC');
        $config_mobile = $this->getConfigFormValuesByDevice('MB');
        $config_tablet = $this->getConfigFormValuesByDevice('TB');

        $config = array_merge($config_pc, $config_mobile, $config_tablet);
        return $config;
    }

    protected function getConfigFormValuesByDevice($device)
    {
        $config_fields = array(
            'PH_FLB_DISPLAY_MODULE_'.$device => (bool) Configuration::get('PH_FLB_DISPLAY_MODULE_'.$device),
            'PH_FLB_HOOK_'.$device.'[]' => explode(';', Configuration::get('PH_FLB_HOOK_'.$device)),
            'PH_TYPE_BUTTON_HOOK_'.$device =>  Configuration::get('PH_TYPE_BUTTON_HOOK_'.$device),
            'PH_TYPE_ACTION_HOOK_'.$device =>  Configuration::get('PH_TYPE_ACTION_HOOK_'.$device),
            'PH_BOX_SIZE_HOOK_'.$device => Configuration::get('PH_BOX_SIZE_HOOK_'.$device),
            'PH_SHOW_FACES_HOOK_'.$device => Configuration::get('PH_SHOW_FACES_HOOK_'.$device),
            'PH_SHARE_BUTTON_HOOK_'.$device => Configuration::get('PH_SHARE_BUTTON_HOOK_'.$device),
        );

        return $config_fields;
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (_PS_MODE_DEMO_) {
            return $this->displayError($this->l('This functionality has been disabled.'));
        }

        return $this->hookProcess();
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }


    public function unregisterHooksByDevice($device)
    {
        // Unregister old hooks
        $old_hooks = explode(';', Configuration::get('PH_FLB_HOOK_'.$device));
        foreach ($old_hooks as $old_hook) {
            $id_footer_hook = (int)Hook::getIdByName('displayFooter');
            if ($id_footer_hook != (int)$old_hook) {
                $this->unregisterHook((int)$old_hook);
            }
        }
    }
    public function registerHooksByDevice($device)
    {
        //register new hook
        $new_hooks = explode(';', Configuration::get('PH_FLB_HOOK_'.$device));
        foreach ($new_hooks as $new_hook) {
            $hook_name = Hook::getNameById((int)$new_hook);
            if (Validate::isHookName($hook_name) && !$this->isRegisteredInHook($hook_name)) {
                $this->registerHook($hook_name);
            }
        }
    }

    public function updateConfiguration($device)
    {
        Configuration::updateValue('PH_FLB_DISPLAY_MODULE_'.$device, false);
        Configuration::updateValue('PH_FLB_HOOK_'.$device, (int)Hook::getIdByName('displayNav1'));

        Configuration::updateValue('PH_TYPE_BUTTON_HOOK_'.$device, 'box_count');
        Configuration::updateValue('PH_TYPE_ACTION_HOOK_'.$device, 'like');
        Configuration::updateValue('PH_BOX_SIZE_HOOK_'.$device, 'small');
        Configuration::updateValue('PH_SHOW_FACES_HOOK_'.$device, false);
        Configuration::updateValue('PH_SHARE_BUTTON_HOOK_'.$device, false);
    }

    public function deleteConfiguration($device)
    {
        Configuration::deleteByName('PH_FLB_DISPLAY_MODULE_'.$device);
        Configuration::deleteByName('PH_FLB_HOOK_'.$device);

        Configuration::deleteByName('PH_TYPE_BUTTON_HOOK_'.$device);
        Configuration::deleteByName('PH_TYPE_ACTION_HOOK_'.$device);
        Configuration::deleteByName('PH_BOX_SIZE_HOOK_'.$device);
        Configuration::deleteByName('PH_SHOW_FACES_HOOK_'.$device);
        Configuration::deleteByName('PH_SHARE_BUTTON_HOOK_'.$device);
    }

    //----Save or delete hook from the DB
    public function hookProcess()
    {
        $message = null;
        // Unregister old hooks
        $this->unregisterHooksByDevice('PC');
        $this->unregisterHooksByDevice('MB');
        $this->unregisterHooksByDevice('TB');

        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            switch ($key) {
                case 'PH_FLB_HOOK_PC[]':
                    $ph_va_hook_pc_val = (Tools::getValue('PH_FLB_HOOK_PC')) ?
                        implode(';', Tools::getValue('PH_FLB_HOOK_PC')) :
                        null;
                    Configuration::updateValue(
                        'PH_FLB_HOOK_PC',
                        $ph_va_hook_pc_val
                    );
                    $message = $this->displayMessage(3);
                    break;
                case 'PH_FLB_HOOK_MB[]':
                    $ph_va_hook_mb_val = (Tools::getValue('PH_FLB_HOOK_MB')) ?
                        implode(';', Tools::getValue('PH_FLB_HOOK_MB')) :
                        null;
                    Configuration::updateValue(
                        'PH_FLB_HOOK_MB',
                        $ph_va_hook_mb_val
                    );
                    $message = $this->displayMessage(3);
                    break;
                case 'PH_FLB_HOOK_TB[]':
                    $ph_va_hook_tp_val = (Tools::getValue('PH_FLB_HOOK_TB')) ?
                        implode(';', Tools::getValue('PH_FLB_HOOK_TB')) :
                        null;
                    Configuration::updateValue(
                        'PH_FLB_HOOK_TB',
                        $ph_va_hook_tp_val
                    );
                    $message = $this->displayMessage(3);
                    break;
                default:
                    Configuration::updateValue($key, Tools::getValue($key));
                    break;
            }
        }

        //register new hook
        $this->registerHooksByDevice('PC');
        $this->registerHooksByDevice('MB');
        $this->registerHooksByDevice('TB');

        return $message;
    }

    // magic method
    public function __call($name, $arguments)
    {
        if (!Validate::isHookName($name)) {
            return false;
        }

        $hook_name = str_replace('hook', '', $name);
        $hook_id = (int)Hook::getIdByName($hook_name);


        $device = 'PC';
        $mobile_detect = $this->context->getMobileDetect();
        if ($mobile_detect->isMobile()) {
            $device = 'MB';
        }
        if ($mobile_detect->isTablet()) {
            $device = 'TB';
        }
        $config = $this->getConfigFormValues();
        $device_hooks = $config['PH_FLB_HOOK_'.$device.'[]'];

        $is_hooked = false;
        foreach ($device_hooks as $device_hook_id) {
            if ($hook_id == $device_hook_id) {
                $is_hooked = true;
                break;
            }
        }
        if (!$is_hooked) {
            return '';
        }

        return $this->displayWidget($hook_name, $arguments);
    }

    public function displayWidget($hookName, array $params)
    {
        if (!$this->prepareHook()) {
            return;
        }

        $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        $this->smarty->assign('module_dir', $this->_path);

        if (!isset($this->context->controller->php_self) ||
            !in_array($this->context->controller->php_self, array('product'))
        ) {
            return;
        }

        return $this->fetch('module:'.$this->name.'/views/templates/hook/displayWidget.tpl');
    }


    public function getWidgetVariables($hookName, array $params)
    {
        return array();
    }

    public function renderWidget($hookName, array $params)
    {
        if (!Validate::isHookName($hookName) || count($params) == 0) {
            return false;
        }
        $hook_name = str_replace('hook', '', $hookName);
        $hook_id = (int)Hook::getIdByName($hook_name);

        $device = 'PC';
        $mobile_detect = $this->context->getMobileDetect();
        if ($mobile_detect->isMobile()) {
            $device = 'MB';
        }
        if ($mobile_detect->isTablet()) {
            $device = 'TB';
        }
        $config = $this->getConfigFormValues();
        $device_hooks = $config['PH_FLB_HOOK'.$device.'[]'];

        $is_hooked = false;
        foreach ($device_hooks as $device_hook_id) {
            if ($hook_id == $device_hook_id) {
                $is_hooked = true;
                break;
            }
        }
        if (!$is_hooked) {
            return '';
        }

        return $this->displayWidget($hook_name, $params);
    }

    protected function prepareHook()
    {

        $device = 'PC';
        $mobile_detect = $this->context->getMobileDetect();
        if ($mobile_detect->isMobile()) {
            $device = 'MB';
        }
        if ($mobile_detect->isTablet()) {
            $device = 'TB';
        }

        $config = $this->getConfigFormValues();

        /**
         * If module is hidden return/
         * Return False
         */
        if (!$config['PH_FLB_DISPLAY_MODULE_'.$device]) {
            return false;
        }
        return true;
    }

    public function hookDisplayHeader($params)
    {

        if (!isset($this->context->controller->php_self) ||
            !in_array($this->context->controller->php_self, array('product'))
        ) {
            return;
        }

        $device = 'PC';
        $mobile_detect = $this->context->getMobileDetect();
        if ($mobile_detect->isMobile()) {
            $device = 'MB';
        }
        if ($mobile_detect->isTablet()) {
            $device = 'TB';
        }

        $config = $this->getConfigFormValues();

        // If module is visible
        if ($config['PH_FLB_DISPLAY_MODULE_'.$device]) {
            $this->context->controller->addJS($this->_path.'views/js/front.js');
            $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        }

        if ($this->context->controller->php_self == 'product') {
            $product = $this->context->controller->getProduct();
            $link = new Link();

            if (!Validate::isLoadedObject($product)) {
                return;
            }

            /**
             * We format the language iso_code to have the same format used by facebook
             * Format: ll_cc
             * ll = iso language code
             * cc = iso country code
             */
            $language_code = $this->context->language->language_code;
            $language_code_format = explode("-", $language_code);
            $language_iso_code = $language_code_format[0].'_'.Tools::strtoupper($language_code_format[1]);

            $this->context->smarty->assign(array(
                'link_rewrite' => isset($product->link_rewrite) &&
                $product->link_rewrite ? $product->link_rewrite : '',
                'ph_product_link' => $link->getProductLink($product),
                'ph_language_iso_code' => $language_iso_code
            ));
            $this->getConfigValues();
        }
        return $this->display(
            __FILE__,
            'phlikeboxwithcounter_header.tpl'
        );
    }

    public function getConfigValues()
    {
        $device = 'PC';
        $mobile_detect = $this->context->getMobileDetect();
        if ($mobile_detect->isMobile()) {
            $device = 'MB';
        }
        if ($mobile_detect->isTablet()) {
            $device = 'TB';
        }

        $type_button = Configuration::get('PH_TYPE_BUTTON_HOOK_'.$device);
        $type_action = Configuration::get('PH_TYPE_ACTION_HOOK_'.$device);
        $box_size = Configuration::get('PH_BOX_SIZE_HOOK_'.$device);
        $show_faces = Configuration::get('PH_SHOW_FACES_HOOK_'.$device);
        $share_button = Configuration::get('PH_SHARE_BUTTON_HOOK_'.$device);

        $product = $this->context->controller->getProduct();

        /**
         * TOdo
         * remove
         */
        /*if (Tools::getIsset(Tools::getValue("id_product_attribute"))) {
            $id_product_attribute = Tools::getValue("id_product_attribute");

        } else {
            $id_product_attribute = 0;
        }*/
        /*$product_url = $this->context->link->getProductLink(
            $product,
            null,
            null,
            null,
            null,
            null,
            $id_product_attribute
        );*/
        $product_url = $this->context->link->getProductLink($product);

        $image_cover_id = $product->getCover($product->id);
        if (is_array($image_cover_id) && isset($image_cover_id['id_image'])) {
            $image_cover_id = (int)$image_cover_id['id_image'];
        } else {
            $image_cover_id = 0;
        }
        $ph_cover_img = addcslashes($this->context->link->getImageLink($product->link_rewrite, $image_cover_id), "'");

        if (version_compare(_PS_VERSION_, '1.7.0.6', '<=')) {
            $ph_product_cover = $ph_cover_img;
        } else {
            $ph_product_cover = null;
        }

        $this->context->smarty->assign(array(
            'type_button' => $type_button,
            'type_action' => $type_action,
            'box_size' => $box_size,
            'show_faces' => ($show_faces) ? "true" : "false",
            'share_button' => ($share_button) ? "true": "false",
            //'ph_product_link' => $product_url,
            'ph_cover_img' => $ph_product_cover,
        ));
    }
}
