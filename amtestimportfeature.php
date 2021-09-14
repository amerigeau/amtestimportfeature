<?php
/**
 * AMTESTIMPORT
 *
 * @author    LATOUTFRANCAIS | Arnaud Merigeau <contact@arnaud-merigeau.fr> - https://www.arnaud-merigeau.fr
 * @copyright Arnaud Merigeau 2020 - https://www.arnaud-merigeau.fr
 * @license   Commercial
 *
 */

if (!defined('_PS_VERSION_')) {
	exit;
}

class amtestimportfeature extends Module
{   
	public function __construct()
	{
		$this->name = 'amtestimportfeature';
		$this->tab = 'administration';
		$this->version = '1.0.0';
		$this->author = 'LATOUTFRANCAIS - https://www.arnaud-merigeau.fr';
		$this->need_instance = 0;
		$this->context = Context::getContext();
		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('(AM) amtestimportfeature');
		$this->description = $this->l('Test module');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
	}

	public function install()
	{
		return parent::install();
	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	public function getContent()
	{
		$output = null;
		return $output . $this->displayCron();
	}

	public function displayCron()
	{
		if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			$store_url = $this->context->link->getBaseLink();
		}else{
			$store_url = Tools::getHttpHost(true).__PS_BASE_URI__;
		}
		$this->context->smarty->assign(array(
			'cron' => $store_url . 'modules/amtestimportfeature/cron.php?token=' . Tools::substr(Tools::encrypt('amtestimportfeature/cron'), 0, 10) . '&id_shop=' . $this->context->shop->id,
			'prestashop_ssl' => Configuration::get('PS_SSL_ENABLED'),
			'shop' => $this->context->shop,
			'action' => AdminController::$currentIndex.'&configure='.$this->name.'&integrate&token='.Tools::getAdminTokenLite('AdminModules')
		));
		return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');   
	}

	/**
	 * Cron test
	 * @param -
	 * @return -
	 */
	public function cronTest()
	{
		// we suppose there is a product id 1 and a feature id 1 in the test shop
		$idProduct = 1;
		$idFeature = 1;

		$langs = Language::getLanguages();
	    $featureValName = array();
	    foreach($langs as $k => $lang){
	    	if($k == 0){
	    		$featureValName[$k] = 'sable';
	    	}elseif($k == 1){
	    		$featureValName[$k] = 'sand';
	    	}
		}

	    foreach($langs as $k => $lang){
	    	// foreach on lang id 1 first : french in my test shop
	    	if ($lang['id_lang'] == 1){
	    		$idFeatureValue = (int)FeatureValue::addFeatureValueImport( 
				  $idFeature,
				  $featureValName[$k],
				  $idProduct,
				  $lang['id_lang'],
				  0
				);
			// foreach on lang id 2 second : english in my test shop
	    	}elseif ($lang['id_lang'] == 2){
	    		Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'feature_value_lang` SET `id_feature_value` = ' . $idFeatureValue . ',`value` = "' . $featureValName[$k] . '",`id_lang` = ' . $lang['id_lang'] . ' WHERE id_feature_value = ' . $idFeatureValue . ' AND id_lang = ' . $lang['id_lang']);
	    	}
		}
		Product::addFeatureProductImport($idProduct, $idFeature, $idFeatureValue);
		die('Done');
	}
}
