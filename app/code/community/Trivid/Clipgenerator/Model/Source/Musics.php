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
 * @copyright  2013 Trivid GmbH
 * @license    http://www.clipgenerator.com/static/public/legal.php Clipgenerator - End User License Agreement
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
/**
 * Class Trivid_Clipgenerator_Model_Source_Musics
 * @package Trivid
 */
class Trivid_Clipgenerator_Model_Source_Musics extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     * Get all music options as array.
     * @return array
     */
    public function getAllOptions() {
		$music = Mage::helper('clipgenerator')->getMusic();
		$musicArr = array();
		foreach ($music['songs'] as $k => $v) {
			$musicArr[] = array(
				'value' => $v['id'],
				'label' => $v['title']
			);
		}

		return $musicArr;
	}
}