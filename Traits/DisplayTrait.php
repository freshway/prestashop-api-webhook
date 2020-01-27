<?php

namespace Traits;

use PrestaShop\PrestaShop\Adapter\Entity\AdminController;
use PrestaShop\PrestaShop\Adapter\Entity\HelperForm;

trait DisplayTrait{

	public function getContent()
    {
        $output = null;

        if (\Tools::isSubmit('submit'.$this->name))
        {
            $endpoint = strval(\Tools::getValue('WEBHOOK_URL'));
            $attempts = strval(\Tools::getValue('WEBHOOK_ATTEMPTS'));
            $interval = strval(\Tools::getValue('WEBHOOK_INTERVAL'));


            if (
                 empty($endpoint) 
                || empty($attempts) 
                || empty($interval)
                || !\Validate::isUrl($endpoint)
                || !\Validate::isInt($attempts)
                || !\Validate::isInt($interval))
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else
            {
                \Configuration::updateValue('WEBHOOK_URL', $endpoint);
                \Configuration::updateValue('WEBHOOK_ATTEMPTS', $attempts);
                \Configuration::updateValue('WEBHOOK_INTERVAL', $interval);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        
        return $output.$this->displayForm();
    }

    public function displayForm()
    {

        // Get default language
        $default_lang = (int)\Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Webhook POST URL'),
                    'name' => 'WEBHOOK_URL',
                    'size' => 20,
                    'required' => true
                ),
            
                array(
                    'type' => 'text',
                    'label' => $this->l('Attempts (in case of error)'),
                    'name' => 'WEBHOOK_ATTEMPTS',
                    'size' => 20,
                    'required' => true
                ),
           

        
                array(
                    'type' => 'text',
                    'label' => $this->l('Minute interval (in case of error)'),
                    'name' => 'WEBHOOK_INTERVAL',
                    'size' => 20,
                    'required' => true
                ),


                array(
                    'type' => 'text',
                    'readonly' => 'readonly',
                    'label' => $this->l('Cron Job'),
                    'name' => 'CRON_URL',
                    'size' => 20,
                    
                )
            ),


            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = \Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.\Tools::getAdminTokenLite('AdminModules'),
                ),
                'back' => array(
                    'href' => AdminController::$currentIndex.'&token='.\Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Back to list')
                )
            );

        // Load current value
        $helper->fields_value['WEBHOOK_URL'] = \Configuration::get('WEBHOOK_URL');
        $helper->fields_value['WEBHOOK_ATTEMPTS'] = \Configuration::get('WEBHOOK_ATTEMPTS');
        $helper->fields_value['WEBHOOK_INTERVAL'] = \Configuration::get('WEBHOOK_INTERVAL');

        $helper->fields_value['CRON_URL'] = \Context::getContext()->link->getModuleLink($this->name, 'cronjob', array('token' => \Configuration::get('WEBHOOK_TOKEN')));


        


        return $helper->generateForm($fields_form);
    }

}