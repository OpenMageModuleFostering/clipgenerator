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
class Trivid_Clipgenerator_Model_Source_Designs extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

	public function getAllOptions() {
		$designs = Mage::helper('clipgenerator')->getDesigns();
		$designArr = array();
		foreach ($designs as $k => $v) {
			$designArr[] = array(
				'value' => $v['id'],
				'label' => $v['title']
			);
		}

		return $designArr;
	}
}