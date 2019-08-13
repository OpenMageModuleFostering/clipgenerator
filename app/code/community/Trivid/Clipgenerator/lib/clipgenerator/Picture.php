<?php

class Picture
{
    // using external picture urls is not implemented yet
    const INTERNAL_STORAGE = 'internal';
    const EXTERNAL_STORAGE = 'external';

    public $id;

    public $width;
    public $height;
    public $alpha;

    public $url;
    public $thumbnailUrl;
    public $webUrl;

    public $url_240p;
    public $url_360p;
    public $url_480p;
    public $url_720p;

    public $storage;

    function __construct($pictureId = null, $width = 1.0, $height = 1.0, $alpha = 1.0)
    {
        $this->id = $pictureId;
        $this->width = $width;
        $this->height = $height;
        $this->alpha = $alpha;
        $this->storage = Picture::INTERNAL_STORAGE;
    }

    public static function withInternalId($pictureId, $width = 1.0, $height = 1.0, $alpha = 1.0)
    {
        $pic = new Picture($pictureId, $width, $height, $alpha);
        $pic->setStorage(Picture::INTERNAL_STORAGE);
        return $pic;
    }

    public static function withPublicUrl($publicUrl, $width = 1.0, $height = 1.0, $alpha = 1.0)
    {
        $pic = new Picture(null, $width, $height, $alpha);
        $pic->storage = Picture::EXTERNAL_STORAGE;
        $pic->url = $publicUrl;
    }

    public static function fromXml($xmlString) {
        $pictureXml = new SimpleXMLElement($xmlString);
        $picture = new Picture();
        $picture->id = (string)$pictureXml->id;
        $picture->width = (string) $pictureXml['width'];
        $picture->height = (string) $pictureXml['height'];
        $picture->alpha = (string) $pictureXml['alpha'];
        $picture->url = (string) $pictureXml->url;
        $picture->webUrl = (string) $pictureXml->web_url;
        $picture->thumbnailUrl = (string) $pictureXml->thumb_url;
        $picture->url_240p = (string) $pictureXml->url_240p;
        $picture->url_360p = (string) $pictureXml->url_360p;
        $picture->url_480p = (string) $pictureXml->url_480p;
        $picture->url_720p = (string) $pictureXml->url_720p;
        $picture->storage = (string) $pictureXml->storage;
        return $picture;
    }

    public function asXml() {
        $xml = new SimpleXMLElement('<picture></picture>');
        $xml->addChild('id', $this->id);

        $xml->addChild('url', $this->url);
        $xml->addChild('thumb_url', $this->thumbnailUrl);
        $xml->addChild('web_url', $this->webUrl);
        $xml->addChild('url_240p', $this->url_240p);
        $xml->addChild('url_360p', $this->url_360p);
        $xml->addChild('url_480p', $this->url_480p);
        $xml->addChild('url_720p', $this->url_720p);
        $xml->addChild('storage', $this->storage);

        $xml->addAttribute('height', number_format($this->height, 4, '.', ''));
        $xml->addAttribute('width', number_format($this->width, 4, '.', ''));
        $xml->addAttribute('alpha', number_format($this->alpha, 4, '.', ''));
        return $xml->asXML();
    }
}

//    <picture width = "0.0-1.0" height = "0.0-1.0" alpha ="0.0-1.0" >
//      <id > unique_id</id >
//          <url > original_image_url</url >
//          <thumb_url > thumb_image_url</thumb_url >
//          <web_url > web_image_url</web_url >
//          <url_240p > http://user-data.clipgenerator.com/.../1c79deac_240p.jpg</url_240p>
//          <url_360p > http://user-data.clipgenerator.com/.../1c79deac_240p.jpg</url_360p>
//          <url_480p > http://user-data.clipgenerator.com/.../1c79deac_240p.jpg</url_480p>
//          <url_720p > http://user-data.clipgenerator.com/.../1c79deac_240p.jpg</url_720p>
//          <storage > external |internal </storage >
//      </picture >