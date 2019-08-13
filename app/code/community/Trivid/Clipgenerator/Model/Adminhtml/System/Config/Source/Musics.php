<?php
/**
 * Music Source
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
 * Used in creating options for Clipgenerator Music config value selection
 *
 */
class Trivid_Clipgenerator_Model_Adminhtml_System_Config_Source_Musics {

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray() {
		$music = Mage::helper('clipgenerator')->getMusic();
		$musicArr = array();
		$musicArr[] = array('value' => '', 'label' => '');
		foreach ($music['songs'] as $k => $v) {
			$musicArr[] = array(
				'value' => $v['id'],
				'label' => $v['title']
			);
		}

		return $musicArr;
	}

	/**
	 * Get options in "key-value" format
	 *
	 * @return array
	 */
	public function toArray() {
		$music = Mage::helper('clipgenerator')->getMusic();
		$musicArr = array();
		$musicArr[0] = '';
		foreach ($music as $v) {
			$musicArr[$v['id']] = $v['title'];
		}

		return $musicArr;
	}
}
