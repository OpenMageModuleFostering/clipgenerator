<?php
/**
 * Orderform Controller
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
 * Class Trivid_Clipgenerator_Adminhtml_OrderformController
 *
 * Handles the orderform view and request.
 * @package Trivid
 */
class Trivid_Clipgenerator_Adminhtml_OrderformController extends Mage_Adminhtml_Controller_Action {
    /**
     * User get the orderform if the api
     * credentials not set anyway. On submitting the form to Trivid order web-
     * service it receives the result via ajax and sets the return to the
     * magento configuration.
     * @return void
     */
    public function indexAction() {
		$post = $this->getRequest()->getPost();
		if($post['isAjax']) {
			if($post['apiUser'] && $post['apiSecret']) {
				$response_array['status'] = 'success';
				echo json_encode($response_array);
				Mage::getModel('core/config')->saveConfig('clipgenerator/general/clipgenerator_api_user_id', $post['apiUser']);
				Mage::getModel('core/config')->saveConfig('clipgenerator/general/clipgenerator_api_secret', $post['apiSecret']);
				Mage::getModel('core/config')->saveConfig('clipgenerator/general/clipgenerator_email', $post['email']);
				Mage::getSingleton('core/session')->addSuccess(Mage::helper('clipgenerator')->__('Die Bestellung war erfolgreich!'));
			}
			die();
		}
		$this->loadLayout()->renderLayout();
	}
}