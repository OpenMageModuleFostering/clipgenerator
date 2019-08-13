<?php
/**
 * insert video block
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
class Trivid_Clipgenerator_Block_InsertVideo extends Mage_Core_Block_Text {
	protected $_nameInLayout = 'clipgenerator.insertvideo';
	protected $_alias = 'insertvideo';
	protected $videoId;
	protected $show;

	function __construct($videoId = NULL, $show = FALSE) {
		$this->videoId = $videoId;
		$this->show = $show;
	}

	public function setPassingTransport($transport) {
		$this->setData('text', $transport . $this->_insertVideoHtml());
	}

	private function _insertVideoHtml() {
		$html = '';
		if ($this->videoId && $this->show) {
			$html = '<iframe id="clipgeneratorvideo" style="width:400px; height:300px; border:0px none;" src="http://data.clipgenerator.com/player/v2/Player.swf?autoplay=off&webcartURL=http%3A%2F%2Fcg-v3.clipgenerator.com%2FgetWebcart%3FvideoId%3D' . $this->videoId . '"></iframe>';
		}

		return $html;
	}
}