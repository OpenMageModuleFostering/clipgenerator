<?php
/**
 * Module Observer
 *
 * PHP version 5
 *
 * LICENSE: is available through the world-wide-web at the following URI:
 * http://www.clipgenerator.com/static/public/legal.php If you did not receive a copy of
 * the Clipgenerator - End User License Agreement and are unable to obtain it through the web, please
 * send a note to info@trivid.com so we can mail you a copy immediately.
 *
 * @package    Trivid
 * @author     Trivid GmbH <author@example.com>
 * @copyright  2013 Trivid GmbH
 * @license    http://www.clipgenerator.com/static/public/legal.php Clipgenerator - End User License Agreement
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
/**
 * Class Trivid_Clipgenerator_Model_Observer
 *
 * @package Trivid
 */
class Trivid_Clipgenerator_Model_Observer {
    /**
     * module name
     */
    const MODULE_NAME = 'Trivid_Clipgenerator';

    /**
     * Creates or updates video after saving new product information
     * @param Varien_Event_Observer $observer
     */
    public function catalog_product_save_after(Varien_Event_Observer $observer) {
		$_product = $observer->getProduct();
		Mage::helper('clipgenerator')->createVideo($_product);
	}

    /**
     * Inserts the video block html after the product description if activated in
     * the magento clipgenerator configuration.
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function insertVideo(Varien_Event_Observer $observer = NULL) {
		if (!$observer) {
			return;
		}
        $block = Mage::getStoreConfig('clipgenerator/settings/clipgenerator_block', Mage::app()->getStore());
        $currentBlock = $observer->getEvent()->getBlock()->getNameInLayout();
		if (($block == $currentBlock || ($block == '' && 'product.description' == $currentBlock)) && Mage::getStoreConfig('clipgenerator/settings/clipgenerator_output', Mage::app()->getStore())) {
			if (!Mage::getStoreConfig('advanced/modules_disable_output/' . self::MODULE_NAME) && Mage::helper('clipgenerator')->getUser() && $observer->getEvent()->getBlock()->getProduct()->getData('clipgenerator_video_id') && $observer->getEvent()->getBlock()->getProduct()->getData('clipgenerator_show')) {
				$event = $observer->getEvent();
				$product = $event->getProduct();
				$transport = $observer->getEvent()->getTransport();
				$block = new Trivid_Clipgenerator_Block_InsertVideo($event->getBlock()->getProduct()->getData('clipgenerator_video_id'), $event->getBlock()->getProduct()->getData('clipgenerator_show'));
				$block->setPassingTransport($transport['html']);
				$block->toHtml();
			}
		}

		return $this;
	}

    /**
     * Creates mass action options to the product list dropdown
     * and sets the url which will be called after choosing the action.
     * @param $observer
     */
    public function addMassAction($observer) {
		$block = $observer->getEvent()->getBlock();
		if (Mage::helper('clipgenerator')->getUser() && get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction' && $block->getRequest()->getControllerName() == 'catalog_product') {
			$block->addItem('video_activate', array(
				'label' => Mage::helper('clipgenerator')->__('Videos aktivieren'),
				'url' => Mage::helper("adminhtml")->getUrl("clipgenerator/adminhtml_mass/activate"),
			));
			$block->addItem('video_deactivate', array(
				'label' => Mage::helper('clipgenerator')->__('Videos deaktivieren'),
				'url' => Mage::helper("adminhtml")->getUrl("clipgenerator/adminhtml_mass/deactivate"),
			));
		}
	}
}