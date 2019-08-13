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
 * @author     Another Author <another@example.com>
 * @copyright  2013 Trivid GmbH
 * @license    http://www.clipgenerator.com/static/public/legal.php Clipgenerator - End User License Agreement
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
class Trivid_Clipgenerator_Adminhtml_OrderformController extends Mage_Adminhtml_Controller_Action {
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