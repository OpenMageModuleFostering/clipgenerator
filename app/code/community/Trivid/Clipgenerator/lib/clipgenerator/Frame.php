<?php

class Frame {
    public $number = 0;
    public $picture;
    public $lowerThirds = array();
    public $backgroundColor = null;
    public $backgroundPicture = null;

    function __construct(Picture $picture=null, $number=0) {
        $this->picture = $picture;
        $this->number = 0;
    }

    public static function withLowerThird(Picture $picture, LowerThird $lowerThird, $number = 0) {
        $frame = new Frame($picture, $number);
        $frame->addLowerThird($lowerThird);
        return $frame;
    }

    public function addLowerThird(LowerThird $lowerThird) {
        array_push($this->lowerThirds, $lowerThird);
    }

    public function getLowerThirds() {
        return $this->lowerThirds;
    }

    public function clearLowerThirds() {
        $this->lowerThirds = array();
    }

    public static function fromXml($xmlString) {
        $frameElement = new SimpleXMLElement($xmlString);
        $frame = new Frame();
        $frame->number = (string) $frameElement->number;
        $frame->backgroundColor = $frameElement->background ? (string)$frameElement->background->color : null;
        $pictureXmlString = $frameElement->picture->asXml();
        if (!empty($pictureXmlString)) {
            $frame->picture = Picture::fromXml($pictureXmlString);
        }

        if($frameElement->lowerThirds && $frameElement->lowerThirds->count() > 0) {
            foreach ($frameElement->lowerThirds->lowerThird as $lowerThirdElement) {
                $lowerThirdString = $lowerThirdElement->asXml();
                $lowerThird = LowerThird::fromXml($lowerThirdString);
                $frame->addLowerThird($lowerThird);
            }
        }

        return $frame;
    }

    public function asXml() {
        $frame = new SimpleXMLElement('<frame></frame>');
        $frame->addChild('number', $this->number);
        if ($this->backgroundColor) {
            $frame->addChild('background');
            $frame->background->addChild('color', $this->backgroundColor);
        }
        $this->addChildFromString($frame, $this->picture->asXml());
        $lowerThirds = $frame->addChild('lowerThirds');
        foreach($this->lowerThirds as $lowerTrhid) {
            $this->addChildFromString($lowerThirds, $lowerTrhid->asXml());
        }
        return $frame->asXML();
    }

    private function addChildFromString($parent, $childString) {
        $domparent = dom_import_simplexml($parent);
        $domchild  = dom_import_simplexml(new SimpleXMLElement($childString));
        $domchild  = $domparent->ownerDocument->importNode($domchild, true);
        $domparent->appendChild($domchild);
    }
}
//
//    <frame>
//<number>0</number>
//<picture width="1" height="1" alpha="1">
//    <id></id>
//</picture>
//<lowerThirds>
//    <lowerThird width="1" height="0.4" horizontalAlign="center" verticalAlign="bottom" alpha="0.7">
//        <color>0</color>
//        <link></link>
//        <text color="16777215" fontSize="31" bold="false" italic="false" underline="false"></text>
//    </lowerThird>
//</lowerThirds>
//</frame>