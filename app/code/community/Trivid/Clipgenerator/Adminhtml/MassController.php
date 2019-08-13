<?php
/**
 * Adminhtml Mass Controller
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
 * Class Trivid_Clipgenerator_Adminhtml_MassController
 *
 * catches mass instruction to activate or deactivate at least 10 product
 * videos. After success the methods will return to list view.
 * @package Trivid
 */
class Trivid_Clipgenerator_Adminhtml_MassController extends Mage_Adminhtml_Controller_Action {

    /**
     * array of send products to mass controller.
     * @var array $products
     */
    protected $products;

    /**
     * initializes the class, checks if the count of the given products
     * is higher than the expected size of 10 and throws an error if
     * happend.
     *
     * @return void
     */
    protected function init() {
		$this->products = $this->getRequest()->getParam('product');
		if (count($this->products) > 10) {
			Mage::getSingleton('core/session')->addError(Mage::helper('clipgenerator')->__('Sie können maximal 10 Produkte gleichzeitig verarbeiten.'));
			$this->_redirectUrl(Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/index'));
		}
	}

    /**
     * activates the given products by calling the clipgenerator helper
     * which handle the activation request.
     *
     * @return void
     */
    public function activateAction() {
		$this->init();
		Mage::helper('clipgenerator')->activateVideos($this->products);
		$this->_redirectUrl(Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/index'));
	}

    /**
     * deactivates the given products by calling the clipgenerator helper
     * which handle the deactivation request.
     *
     * @return void
     */
	public function deactivateAction() {
		$this->init();
		Mage::helper('clipgenerator')->deactivateVideos($this->products);
		$this->_redirectUrl(Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/index'));
	}
}