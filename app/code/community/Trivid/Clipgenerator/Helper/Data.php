<?php
/**
 * Main Helper Class
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
require_once(Mage::getModuleDir('', 'Trivid_Clipgenerator') . '/lib/clipgenerator/ClipgeneratorClient.php');
require_once(Mage::getModuleDir('', 'Trivid_Clipgenerator') . '/lib/clipgenerator/Video.php');
require_once(Mage::getModuleDir('', 'Trivid_Clipgenerator') . '/lib/clipgenerator/Picture.php');
require_once(Mage::getModuleDir('', 'Trivid_Clipgenerator') . '/lib/clipgenerator/Frame.php');
require_once(Mage::getModuleDir('', 'Trivid_Clipgenerator') . '/lib/clipgenerator/Logo.php');
require_once(Mage::getModuleDir('', 'Trivid_Clipgenerator') . '/lib/clipgenerator/LowerThird.php');
/**
 * Class Trivid_Clipgenerator_Helper_Data
 *
 * Main helper class for handling video generation, activation and so on.
 * @package Trivid
 */
class Trivid_Clipgenerator_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Instance of the clipgenerator client.
     * @var ClipgeneratorClient
     */
    protected $clipgeneratorClient;

	/**
	 * Initalize clipgenerator api with given api credentials from.
	 * clipgenerator konfiguration
	 * @return void
	 */
	private function initClipgeneratorApi() {
		$apiUserId = Mage::getStoreConfig('clipgenerator/general/clipgenerator_api_user_id', Mage::app()->getStore());
		$apiSecret = Mage::getStoreConfig('clipgenerator/general/clipgenerator_api_secret', Mage::app()->getStore());
		$apiId = 'magento-' . $apiUserId;
		$apiUrl = 'http://cg-v3.clipgenerator.com/';
		$this->clipgeneratorClient = new ClipgeneratorClient(
			$apiId,
			$apiSecret,
			$apiUserId,
			'de',
			$apiUrl
		);
	}

    /**
     * Method for calling requests to clipgenerator api direct via curl.
     * @param $method
     * @param array $params
     * @return mixed
     */
    private function curlCall($method, $params = array()) {
		if ($method) {
			$apiCredentials = array(
				'userId' => Mage::getStoreConfig('clipgenerator/general/clipgenerator_api_user_id', Mage::app()->getStore()),
				'apiSecret' => Mage::getStoreConfig('clipgenerator/general/clipgenerator_api_secret', Mage::app()->getStore()),
				'apiId' => 'magento-' . Mage::getStoreConfig('clipgenerator/general/clipgenerator_api_user_id', Mage::app()->getStore())
			);
			$params = array_merge($params, $apiCredentials);
			$apiUrl = 'http://cg-v3.clipgenerator.com/';
			$curl = $apiUrl . $method . '?' . http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $curl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$output = curl_exec($ch);
			curl_close($ch);

			return $output;
		}
	}

    /**
     * Uploads the given picture url to the current api account.
     * @param $file
     * @return array|bool
     */
    public function uploadPicture($file) {
		$this->initClipgeneratorApi();

		return $this->clipgeneratorClient->uploadPicture($file);
	}

    /**
     * Save the generated xml to the given video or creates a new video.
     * @param $xml
     * @return false
     */
    public function saveVideo($xml) {
		$this->initClipgeneratorApi();

		return $this->clipgeneratorClient->saveVideo($xml);
	}

	/**
	 * Returns a array of the clipgenerator designs.
	 * @return array
	 */
	public function getDesigns() {
		$this->initClipgeneratorApi();
		$designs = $this->clipgeneratorClient->getDesigns();

		return $designs;
	}

	/**
	 * Returns a array of the clipgenerator music.
	 * @return array
	 */
	public function getMusic() {
		$this->initClipgeneratorApi();
		$designs = $this->clipgeneratorClient->getMusic();

		return $designs;
	}

	/**
	 * Returns the current user if api credentials match.
	 * @return array
	 */
	public function getUser() {
		$this->initClipgeneratorApi();
		$user = $this->clipgeneratorClient->getUser();

		return $user;
	}

	/**
     * Activates the given products.
	 * @param $products
     * @return void
	 */
	public function activateVideos($products) {
		foreach ($products as $product_id) {
			$product = Mage::getModel('catalog/product')->load($product_id);
			$cid = $product->getData('clipgenerator_video_id');
			if ($cid) {
				$productIdArray[] = $product->getData('clipgenerator_video_id');
				$productArray[$product->getData('clipgenerator_video_id')] = $product;
			} else {
				$product->setData('clipgenerator_show', 1);
				$this->createVideo($product);
				$productIdArray[] = $product->getData('clipgenerator_video_id');
				$productArray[$product->getData('clipgenerator_video_id')] = $product;
			}
		}
		if (!empty($productIdArray)) {
			$params['idList'] = json_encode($productIdArray);
			$params['visibility'] = 1;
			$return = json_decode($this->curlCall('setClipVisibilityBulk', $params));
			$success = $return->result->idList;
		}
		if (!empty($success)) {
			$count = 0;
			Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
			foreach ($success as $id) {
				$productArray[$id]->setData('clipgenerator_show', 1);
				$productArray[$id]->save();
				$count++;
			}
			Mage::getSingleton('core/session')->addSuccess(sprintf(Mage::helper('clipgenerator')->__('%s Video/s wurde/n aktivert.'), $count));
		} else {
			Mage::getSingleton('core/session')->addError(Mage::helper('clipgenerator')->__('Die gewählten Videos konnten nicht aktiviert werden. Bitte versuchen Sie es noch einmal.'));
		}
	}

	/**
     * Deactivates the given products.
	 * @param $products
     * @return void
	 */
	public function deactivateVideos($products) {
		foreach ($products as $product_id) {
			$product = Mage::getModel('catalog/product')->load($product_id);
			$cid = $product->getData('clipgenerator_video_id');
			if ($cid) {
				$productIdArray[] = $product->getData('clipgenerator_video_id');
				$productArray[$product->getData('clipgenerator_video_id')] = $product;
			}
		}
		if (!empty($productIdArray)) {
			$params['idList'] = json_encode($productIdArray);
			$params['visibility'] = 0;
			$return = json_decode($this->curlCall('setClipVisibilityBulk', $params));
			$success = $return->result->idList;
		}
		if (!empty($success)) {
			$count = 0;
			Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
			foreach ($success as $id) {
				$productArray[$id]->setData('clipgenerator_show', 0);
				$productArray[$id]->save();
				$count++;
			}
			Mage::getSingleton('core/session')->addSuccess(sprintf(Mage::helper('clipgenerator')->__('%s Video/s wurde/n deaktivert.'), $count));
		} else {
			Mage::getSingleton('core/session')->addError(Mage::helper('clipgenerator')->__('Die gewählten Videos konnten nicht deaktiviert werden.'));
		}
	}

    /**
     * Creates a video by the given product with the product configuration,
     * if there are no settings given or missing, it sets randomized value.
     * @param $_product
     * @return void
     */
    public function createVideo($_product) {
		$this->initClipgeneratorApi();
		if ($_product->getData('clipgenerator_show') && $this->getUser()) {
			$video = new Video($_product->getData('clipgenerator_title'));
			//set video design
			if ($_product->getData('clipgenerator_design')) {
				$video->designId = $_product->getData('clipgenerator_design');
			} else {
				$designs = $this->getDesigns();
				$dKey = array_rand($designs);
				$video->designId = $designs[$dKey]['id'];
				$_product->setData('clipgenerator_design', $designs[$dKey]['id']);
			}
			//set video song
			if ($_product->getData('clipgenerator_song')) {
				$video->songId = $_product->getData('clipgenerator_song');
			} else {
				$music = $this->getMusic();
				$mKey = array_rand($music['songs']);
				$video->designId = $music['songs'][$mKey]['id'];
				$_product->setData('clipgenerator_design', $music['songs'][$mKey]['id']);
			}
			$video->keywords = $_product->getData('clipgenerator_keywords') ? $_product->getData('clipgenerator_keywords') : $_product->getData('meta_keywords');
			$video->title = $_product->getData('clipgenerator_title') ? $_product->getData('clipgenerator_title') : $_product->getData('name');
			$video->description = $_product->getData('clipgenerator_description') ? $_product->getData('clipgenerator_description') : $_product->getData('description');
			$logoUrl = $_product->getData('clipgenerator_logo') ? $_product->getData('clipgenerator_logo') : Mage::getStoreConfig('clipgenerator/settings/clipgenerator_logo_url', Mage::app()->getStore());
			if ($logoUrl) {
				if ($logo = $this->clipgeneratorClient->uploadLogo($logoUrl)) {
					$logoId = $logo['id'];
					$logoUrl = $logo['url'];
					$logo = Logo::fromXml('<logo width="0.2" height="0.2" alpha="1">
                                                <id>' . $logoId . '</id>
                                                <url>' . htmlspecialchars($logoUrl) . '</url>
                                                <link />
                                                <position verticalAlign="top" horizontalAlign="right" />
                                                <showInStartPage>false</showInStartPage>
                                                <showInEndPage>false</showInEndPage>
                                                <showInTimeline>true</showInTimeline>
                                            </logo>');
					$video->logo = $logo;
				}
			}
			$images = explode(';', trim($_product->getData('clipgenerator_images_select'), ';'));
			$pImgs = $_product->getMediaGalleryImages();
			$newImgSelect = '';
			$imgCount = 0;
			if ($_product->getData('clipgenerator_images_select')) {
				foreach ($images as $img) {
					foreach ($pImgs as $pImg) {
						if ($pImg->getUrl() == $img) {
							$pic = $this->clipgeneratorClient->uploadPicture($img);
							$picture = new Picture($pic['id']);
							$frame = new Frame($picture);
							if ($pImg->getData('label')) {
								$l3 = new LowerThird();
								$l3->verticalAlign = 300;
								$l3->setText($pImg->getData('label'));
								$l3->textOutlineWidth = 2;
								$l3->textOutlineColor = 0x333333;
								$l3->link = $_product->getProductUrl();
								$frame->addLowerThird($l3);
							}
							$video->addFrame($frame);
							$newImgSelect .= ';' . $img;
							$imgCount++;
							break;
						}
					}
					// limitation to 22 images
					if ($imgCount == 22) {
						break;
					}
				}
			} else {
				foreach ($pImgs as $pImg) {
					$pic = $this->clipgeneratorClient->uploadPicture($pImg->getUrl());
					$picture = new Picture($pic['id']);
					$frame = new Frame($picture);
					if ($pImg->getData('label')) {
						$l3 = new LowerThird();
						$l3->verticalAlign = 300;
						$l3->setText($pImg->getData('label'));
						$l3->textOutlineWidth = 2;
						$l3->textOutlineColor = 0x333333;
						$l3->link = $_product->getProductUrl();
						$frame->addLowerThird($l3);
					}
					$video->addFrame($frame);
					$newImgSelect .= ';' . $pImg->getUrl();
					$imgCount++;
					// limitation to 22 images
					if ($imgCount == 22) {
						break;
					}
				}
				if ($newImgSelect) {
					$_product->setData('clipgenerator_images_select', $newImgSelect);
				}
			}
			$video->format = '360p';
			if ($_product->getData('clipgenerator_video_id')) {
				$video->id = $_product->getData('clipgenerator_video_id');
			}
			$videoXml = $video->asXml();
			$videoId = $this->clipgeneratorClient->saveVideo($videoXml);
			Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));
			if (!$_product->getData('clipgenerator_video_id')) {
				$_product->setData('clipgenerator_video_id', $videoId);
				$_product->save();
			} else {
				if ($newImgSelect != $_product->getData('clipgenerator_images_select')) {
					$_product->setData('clipgenerator_images_select', $newImgSelect);
					$_product->save();
				}
			}
		}
	}
}