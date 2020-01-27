<?php 

require _PS_MODULE_DIR_.'apiwebhook/Traits/DisplayTrait.php';
require _PS_MODULE_DIR_.'apiwebhook/Traits/InstallTrait.php';
require _PS_MODULE_DIR_.'apiwebhook/Traits/QueueTrait.php';

use Traits\DisplayTrait;
use Traits\InstallTrait;
use Traits\QueueTrait;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApiWebHook extends Module
{
    use InstallTrait, DisplayTrait, QueueTrait;


    const STATUS_QUEUE = 'queue';
    const STATUS_SENDED = 'sended';
    const STATUS_FAILED = 'failed';


    public function __construct()
    {
        $this->name = 'apiwebhook';
        $this->tab = '';
        $this->version = '1.0.0';
        $this->author = 'Gabriel Gonzalez';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Api WebHook Module');
        $this->description = $this->l('Module to send updates through post requests.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('WEBHOOK_URL'))
            $this->warning = $this->l('No URL provided.');
    }

}

?>