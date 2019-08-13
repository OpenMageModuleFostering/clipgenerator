<?php
/**
 * Design Source
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
/**
 * Creates options for Clipgenerator Designs config value selection.
 * @package Trivid
 */
class Trivid_Clipgenerator_Model_Adminhtml_System_Config_Source_Designs {

	/**
	 * Creates options array of the designs.
	 * @return array
	 */
	public function toOptionArray() {
		$designs = Mage::helper('clipgenerator')->getDesigns();
		$designArr = array();
		$designArr[] = array('value' => '', 'label' => '');
		foreach ($designs as $k => $v) {
			$designArr[] = array(
				'value' => $v['id'],
				'label' => $v['title']
			);
		}

		return $designArr;
	}

	/**
	 * Creates options in "key-value" format of the designs.
	 * @return array
	 */
	public function toArray() {
		$designs = Mage::helper('clipgenerator')->getDesigns();
		$designArr = array();
		$designArr[0] = '';
		foreach ($designs as $v) {
			$designArr[$v['id']] = $v['title'];
		}

		return $designArr;
	}
}
