<?php
/**
 * video block
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
 * Class Trivid_Clipgenerator_Block_Video
 *
 * Video block for individual inclusion.
 * @package
 */
class Trivid_Clipgenerator_Block_Video extends Mage_Core_Block_Text {
    /**
     * Generates the video iframe for the block include opiton.
     * @return mixed|string
     */
    protected function _toHtml() {
		$_product = Mage::registry('current_product');
		$video = $_product->getData('clipgenerator_video_id');
		$show = $_product->getData('clipgenerator_show');
		if ($video && $show) {
			$html = '<iframe id="clipgeneratorvideo" style="width:400px; height:300px; border:0px none;" src="http://data.clipgenerator.com/player/v3/Player.swf?autoplay=off&webcartURL=http%3A%2F%2Fcg-v3.clipgenerator.com%2FgetWebcart%3FvideoId%3D' . $video . '"></iframe>';
		}

		return $html;
	}
}