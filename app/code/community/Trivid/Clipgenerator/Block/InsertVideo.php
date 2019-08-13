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
 * @copyright  2013 Trivid GmbH
 * @license    http://www.clipgenerator.com/static/public/legal.php Clipgenerator - End User License Agreement
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */
/**
 * Class Trivid_Clipgenerator_Block_InsertVideo
 *
 * Handles automatically output to the product detail view.
 * @package Trivid
 */
class Trivid_Clipgenerator_Block_InsertVideo extends Mage_Core_Block_Text {
    /**
     * Name of the new layout part.
     * @var string
     */
    protected $_nameInLayout = 'clipgenerator.insertvideo';
    /**
     * Alias of the new layout part.
     * @var string
     */
    protected $_alias = 'insertvideo';
    /**
     * Current video id.
     * @var int
     */
    protected $videoId;
    /**
     * Products show option.
     * @var boolean
     */
    protected $show;

    /**
     * Initializes the given product attributes videoid and show.
     * @param null $videoId
     * @param bool $show
     */
    function __construct($videoId = NULL, $show = FALSE) {
		$this->videoId = $videoId;
		$this->show = $show;
	}

    /**
     * Expands the current transport part by adding the video iframe html.
     * @param $transport
     */
    public function setPassingTransport($transport) {
		$this->setData('text', $transport . $this->_insertVideoHtml());
	}

    /**
     * Creates video iframe.
     * @return string
     */
    private function _insertVideoHtml() {
		$html = '';
		if ($this->videoId && $this->show) {
			$html = '<iframe id="clipgeneratorvideo" style="width:400px; height:300px; border:0px none;" src="http://data.clipgenerator.com/player/v3/Player.swf?autoplay=off&webcartURL=http%3A%2F%2Fcg-v3.clipgenerator.com%2FgetWebcart%3FvideoId%3D' . $this->videoId . '"></iframe>';
		}

		return $html;
	}
}