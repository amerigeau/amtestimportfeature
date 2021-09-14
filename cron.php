<?php
/**
 * AMTESTIMPORT
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 */

/* This file can be called using a cron to generate product feed */
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');

/* Check security token */
if (!Tools::isPHPCLI()) {
    if (Tools::substr(Tools::encrypt('amtestimportfeature/cron'), 0, 10) != Tools::getValue('token') || !Module::isInstalled('amtestimportfeature')) {
        die('Bad token');
    }
}

/* Check if the module is enabled */
$module = Module::getInstanceByName('amtestimportfeature');
if ($module->active) {
	$module->cronTest();
}
