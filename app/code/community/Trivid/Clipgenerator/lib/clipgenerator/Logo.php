<?php

class Logo {
    public $id;
    public $width;
    public $height;
    public $alpha;
    public $url;
    public $link;
    public $verticalPosition;
    public $horizontalPosition;
    public $showInStartPage;
    public $showInEndPage;
    public $showInTimeline;

    public static function fromXml($xmlString) {
        $element = new SimpleXMLElement($xmlString);
        $logo = new Logo();
        $logo->id = (string)$element->id;
        $logo->width = $element['width'];
        $logo->height = $element['height'];
        $logo->alpha = $element['alpha'];

        $logo->verticalPosition = $element->position['verticalAlign'];
        $logo->horizontalPosition = $element->position['horizontalAlign'];

        $logo->url = (string) $element->url;
        $logo->link = (string) $element->link;
        $logo->showInEndPage = strtolower((string) $element->showInEndPage ) == 'true' ? true : false;
        $logo->showInStartPage = strtolower((string) $element->showInStartPage) == 'true' ? true : false;
        $logo->showInTimeline = strtolower((string) $element->showInTimeline) == 'true' ? true : false;
        return $logo;
    }

    public function asXml() {
        $logo = new SimpleXMLElement('<logo></logo>');
        //print_r($logo);
        $logo->id = $this->id;
        $width = (string)$this->width;
        if($width=='') $width = 0;
        $height = (string)$this->height;
        if($height=='') $width = 0;
        if (!is_null($this->width))
            $logo->addAttribute('width', number_format($width, 4, '.', ''));
        if (!is_null($this->height))
            $logo->addAttribute('height', number_format($height, 4, '.', ''));
        $logo->addAttribute('alpha', number_format((string)$this->alpha, 4, '.', ''));
        $logo->url = $this->url;
        $logo->link = $this->link;
        $position = $logo->addChild('position');
        $position->addAttribute('verticalAlign', $this->verticalPosition);
        $position->addAttribute('horizontalAlign', $this->horizontalPosition);
        $logo->addChild('showInStartPage', $this->showInStartPage ? 'true' : 'false');
        $logo->addChild('showInEndPage', $this->showInEndPage ? 'true' : 'false');
        $logo->addChild('showInTimeline', $this->showInTimeline ? 'true' : 'false');
        $xmlString = $logo->asXML();
        return $xmlString;
    }
}



//<logo width="0.2" height="0.2" alpha="1">
//    <id />
//    <url />
//    <link />
//    <position verticalAlign="top" horizontalAlign="right" />
//    <showInStartPage>false</showInStartPage>
//    <showInEndPage>false</showInEndPage>
//    <showInTimeline>true</showInTimeline>
//</logo>