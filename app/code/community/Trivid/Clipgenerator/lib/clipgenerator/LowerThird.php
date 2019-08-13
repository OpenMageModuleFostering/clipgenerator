<?php

class LowerThird {
    const ALIGN_LEFT = 'left';
    const ALIGN_CENTER = 'center';
    const ALIGN_RIGHT = 'right';
    const ALIGN_TOP = 'top';
    const ALIGN_MIDDLE = 'middle';
    const ALIGN_BOTTOM = 'bottom';

    const POSITION_TYPE_ABSOLUTE = 'absolute';
    const POSITION_TYPE_ALIGN = 'align';

    const COLOR_BLACK = 0x000000;
    const COLOR_RED = 0xFF0000;
    const COLOR_GREEN = 0x00FF00;
    const COLOR_BLUE = 0x0000FF;
    const COLOR_WHITE = 0xFFFFFF;

    public $width;
    public $height;
    public $margin;
    public $horizontalAlign;
    public $verticalAlign;
    public $alpha;
    public $backgroundColor = LowerThird::COLOR_WHITE;
    public $link = null;

    public $xPosition = 0;      // since 3.0.5
    public $yPosition = 0;      // since 3.0.5
    public $positioningType = self::POSITION_TYPE_ALIGN; // since 3.0.5

    public $text;
    public $textColor;
    public $textFontSize;
    public $textFontFamily;     // since 3.0.3
    public $textFontWeight;     // since 3.0.3
    public $textFontStyle;      // since 3.0.3
    public $textIsBold;         // replaced in 3.0.3 with fontWeight
    public $textIsItalic;       // replaced in 3.0.3 with fontStyle
    public $textIsUnderline;
    public $textIsHTML;

    public $textHorizontalAlign = self::ALIGN_CENTER;
    public $textVerticalAlign = self::ALIGN_MIDDLE;
    public $textOutlineColor = LowerThird::COLOR_WHITE;
    public $textOutlineWidth = 0;
    public $textMinFontSize = 30;
    public $textMargin = 0;

    public $picture_id;

    public $picture_width;
    public $picture_height;
    public $picture_alpha;

    public $picture_url;
    public $picture_thumbnailUrl;

    public $picture_url_240p;
    public $picture_url_360p;
    public $picture_url_480p;
    public $picture_url_720p;
    public $picture_storage;

    function __construct($width=1.0, $height=0.25, $horizontalAlign = LowerThird::ALIGN_CENTER, $verticalAlign = LowerThird::ALIGN_MIDDLE, $alpha = 0.6)
    {
        // width="0.0-1.0" height="0.0-1.0" horizontalAlign="left|center|right" verticalAlign="top|middle|bottom" alpha="0.0-1.0">
        $this->width = $width;
        $this->height = $height;
        $this->horizontalAlign = $horizontalAlign;
        $this->verticalAlign = $verticalAlign;
        $this->alpha = $alpha;
        $this->picture_url = '';

        $this->textFontFamily = "cgFontDefault";
    }

    public function setText($text, $color=LowerThird::COLOR_WHITE, $size=30, $bold=false, $italic=false, $underline=false,
                            $horizontalAlign=LowerThird::ALIGN_CENTER, $verticalAlign=LowerThird::ALIGN_MIDDLE) {
        $this->text = $text;
        $this->textColor = $color;
        $this->textFontSize = $size;
        $this->textIsBold = $bold;
        $this->textIsItalic = $italic;
        $this->textIsUnderline = $underline;

        $this->textFontWeight = $bold ? "bold" : "normal";
        $this->textFontStyle = $italic ? "italic" : "normal";

        $this->textHorizontalAlign = $horizontalAlign;
        $this->textVerticalAlign = $verticalAlign;
    }

    public static function fromXml($xmlString) {
        $element = new SimpleXMLElement($xmlString);

        $lowerThird = new LowerThird();
        $lowerThird->alpha = (string)$element['alpha'];
        $lowerThird->width = (string)$element['width'];
        $lowerThird->height = (string)$element['height'];
        $xpos = (string)$element['xPosition'];
        $ypos = (string)$element['yPosition'];
        $lowerThird->xPosition = !empty($xpos) ? floatval($xpos) : 0.0;
        $lowerThird->yPosition = !empty($ypos) ? floatval($ypos) : 0.0;
        $lowerThird->positioningType = (string)$element['positioningType'];
        if ($element['margin']) $lowerThird->margin = (string)$element['margin'];
        $lowerThird->horizontalAlign = (string)$element['horizontalAlign'];
        $lowerThird->verticalAlign = (string)$element['verticalAlign'];
        $lowerThird->backgroundColor = (string)$element->color;
        $lowerThird->text = (string)$element->text;
        $lowerThird->link = (string)$element->link;
        $lowerThird->textColor = (string)$element->text['color'];
        $lowerThird->textFontSize = (string)$element->text['fontSize'];
        if ($element->text['minFontSize']) $lowerThird->textMinFontSize = (string)$element->text['minFontSize'];
        $lowerThird->textIsBold = (string)$element->text['bold'] == 'true' ? true : false;
        $lowerThird->textIsItalic = (string)$element->text['italic']  == 'true' ? true : false;
        $lowerThird->textIsUnderline = (string)$element->text['underline']  == 'true' ? true : false;
        $lowerThird->textIsHTML = (string)$element->text['isHtml']  == 'false' ? false : true;

        $lowerThird->textHorizontalAlign = (string)$element->text['horizontalAlign'];
        $lowerThird->textVerticalAlign = (string)$element->text['verticalAlign'];
        $lowerThird->textOutlineColor = (string)$element->text['outlineColor'];
        $lowerThird->textOutlineWidth = (string)$element->text['outlineWidth'];

        $lowerThird->textFontFamily = (string)$element->text['fontFamily'];
        $lowerThird->textFontStyle = (string)$element->text['fontStyle'];
        $lowerThird->textFontWeight = (string)$element->text['fontWeight'];

        if ($element->text['margin']) $lowerThird->textMargin = (string)$element->text['margin'];

        if($element->picture){
            $lowerThird->picture_width = (string) $element->picture['width'];
            $lowerThird->picture_height = (string) $element->picture['height'];
            $lowerThird->picture_alpha = (string) $element->picture['alpha'];
            $lowerThird->picture_url = (string) $element->picture->url;
            $lowerThird->picture_thumbnailUrl = (string) $element->picture->thumb_url;
            $lowerThird->picture_url_240p = (string) $element->picture->url_240p;
            $lowerThird->picture_url_360p = (string) $element->picture->url_360p;
            $lowerThird->picture_url_480p = (string) $element->picture->url_480p;
            $lowerThird->picture_url_720p = (string) $element->picture->url_720p;
            $lowerThird->picture_storage = (string) $element->picture->storage;
        }

        return $lowerThird;
    }

    public function asXml() {
        $xml = new SimpleXMLElement('<lowerThird></lowerThird>');
        $xml->addAttribute('positioningType', $this->positioningType);
        $xml->addAttribute('xPosition', number_format($this->xPosition, 4, '.', ''));
        $xml->addAttribute('yPosition', number_format($this->yPosition, 4, '.', ''));
        $xml->addAttribute('width', number_format($this->width, 4, '.', ''));
        $xml->addAttribute('height', number_format($this->height, 4, '.', ''));
        $xml->addAttribute('alpha', number_format((float)$this->alpha, 4, '.', ''));
        $xml->addAttribute('margin', number_format($this->margin, 4, '.', ''));
        $xml->addAttribute('horizontalAlign', $this->horizontalAlign);
        $xml->addAttribute('verticalAlign', $this->verticalAlign);
        $xml->addChild('color', $this->backgroundColor);
        $xml->addChild('link', $this->link);

        $node = $xml->addChild('text'); // fix: add child does not automatically encode ampersands. meh.
        $node = dom_import_simplexml($node);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($this->text));

        $xml->text->addAttribute('color', $this->textColor);
        $xml->text->addAttribute('fontSize', number_format(($this->textFontSize=='')?0:$this->textFontSize, 4, '.', ''));
        $xml->text->addAttribute('minFontSize', number_format(($this->textMinFontSize=='')?0:$this->textMinFontSize, 4, '.', ''));
        $xml->text->addAttribute('bold', $this->textIsBold ? 'true' : 'false');
        $xml->text->addAttribute('italic', $this->textIsItalic ? 'true' : 'false');
        $xml->text->addAttribute('underline', $this->textIsUnderline? 'true' : 'false');
        $xml->text->addAttribute('isHtml', $this->textIsHTML? 'true' : 'false');

        $xml->text->addAttribute('horizontalAlign', $this->textHorizontalAlign);
        $xml->text->addAttribute('verticalAlign', $this->textVerticalAlign);

        $xml->text->addAttribute('outlineColor', $this->textOutlineColor);
        $xml->text->addAttribute('outlineWidth', number_format(($this->textOutlineWidth=='')?0:$this->textOutlineWidth, 4, '.', ''));
        $xml->text->addAttribute('margin', number_format(($this->textMargin=='')?0:$this->textMargin, 4, '.', ''));

        $xml->text->addAttribute('fontFamily', $this->textFontFamily);
        $xml->text->addAttribute('fontWeight', $this->textFontWeight);
        $xml->text->addAttribute('fontStyle', $this->textFontStyle);

        if($this->picture_id > 0){
            $xml->addChild('picture');
            $xml->picture->addAttribute('width', number_format($this->picture_width, 4, '.', ''));
            $xml->picture->addAttribute('height', number_format($this->picture_height, 4, '.', ''));
            $xml->picture->addAttribute('alpha', number_format((float)$this->picture_alpha, 4, '.', ''));
            $xml->picture->addChild('id', $this->picture_id);
            $xml->picture->addChild('url', $this->picture_url);
            $xml->picture->addChild('url_240p', $this->picture_url_240p);
            $xml->picture->addChild('url_360p', $this->picture_url_360p);
            $xml->picture->addChild('url_480p', $this->picture_url_480p);
            $xml->picture->addChild('url_720p', $this->picture_url_720p);
            $xml->picture->addChild('thumb_url', $this->picture_thumbnailUrl);
            $xml->picture->addChild('storage', $this->picture_storage);
        }

        return $xml->asXml();
    }
}

//    <lowerThird width="1" height="0.4" horizontalAlign="center" verticalAlign="bottom" alpha="0.7">
//        <color>0</color>
//        <link></link>
//        <text color="16777215" fontSize="31" bold="false" italic="false" underline="false"></text>
//    </lowerThird>