<?php

namespace Traits;



trait InstallTrait{

	public function install()
    {
        if (\Shop::isFeatureActive()) {
            \Shop::setContext(\Shop::CONTEXT_ALL);
        }

        return parent::install() &&
            $this->registerHook('actionProductAdd') &&
            $this->registerHook('actionProductDelete') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('actionAttributeCombinationSave') &&
            $this->registerHook('actionAttributeCombinationDelete') &&
            $this->installDb() &&
            \Configuration::updateValue('WEBHOOK_URL', 'http://www.example.com/endpoint') &&
            \Configuration::updateValue('WEBHOOK_ATTEMPTS', 5)  &&
            \Configuration::updateValue('WEBHOOK_INTERVAL', 1) &&/* minutes */
            \Configuration::updateValue('WEBHOOK_TOKEN',  sha1(mt_rand(1, 90000)));


            

    }
    public function installDb()
    {
        return (\Db::getInstance()->execute('
        CREATE TABLE `'._DB_PREFIX_.'webhook_queue` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `data` longtext NOT NULL,
            `status` VARCHAR(50) NOT NULL DEFAULT \''.self::STATUS_QUEUE.'\',
            `created_at` TIMESTAMP NOT NULL DEFAULT NOW(),
            `reserved_at` timestamp NULL DEFAULT NULL,
            `sended_at` timestamp NULL DEFAULT NULL,
            `attempt` int(10) unsigned DEFAULT NULL,
            PRIMARY KEY (`id`)
        )
        ENGINE = '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci;'));



    }
    
    public function uninstall()
    {
        /*if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }*/

        parent::uninstall() && 
        $this->uninstallDb() &&
        $this->unregisterHook('actionValidateOrder')&&
        $this->unregisterHook('actionProductDelete')&&
        $this->unregisterHook('actionProductUpdate')&&
        $this->unregisterHook('actionAttributeCombinationSave')&&
        $this->unregisterHook('actionAttributeCombinationDelete')&&
        \Configuration::deleteByName('WEBHOOK_URL') &&
        \Configuration::deleteByName('WEBHOOK_ATTEMPTS') &&
        \Configuration::deleteByName('WEBHOOK_INTERVAL') &&
        \Configuration::deleteByName('WEBHOOK_TOKEN');

        return true;
    }


    protected function uninstallDb()
    {
        \Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'webhook_queue`');
        return true;
    }

}