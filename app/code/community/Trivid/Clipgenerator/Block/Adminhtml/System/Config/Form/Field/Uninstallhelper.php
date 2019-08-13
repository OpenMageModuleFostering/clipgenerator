<?php
/**
 * Uninstall Field
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
 * Class Trivid_Clipgenerator_Block_Adminhtml_System_Config_Form_Field_Contingentlabel
 *
 * Uninstall label handles uninstall helper configuration.
 * @package Trivid
 */
class Trivid_Clipgenerator_Block_Adminhtml_System_Config_Form_Field_Uninstallhelper extends Mage_Adminhtml_Block_System_Config_Form_Field {
	/**
	 * Renders the uninstall template with the given uninstall information
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
		$block = Mage::getSingleton('core/layout')
				->createBlock('adminhtml/template', '', array('template' => 'clipgenerator/uninstall.phtml'))
				->setIsRenderToJsTemplate(TRUE);

		return $block->toHtml();
	}
}