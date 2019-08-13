<?php

class Video
{
    const FORMAT_240p = '240p';
    const FORMAT_360p = '360p';
    const FORMAT_480p = '480p';
    const FORMAT_720p = '720p';

    const RATIO_16x9 = '16x9';
    const RATIO_4x3 = '4x3';

    public $id;

    // video metadata
    public $title;
    public $producer;
    public $description;
    public $keywords;
    public $copyright;
    public $content;
    public $targetAudience;

    // video configuration
    public $songId;
    public $songUrl;

    public $designId;
    public $designUrl;

    public $filter;
    public $maxClipLength;

    // default font
    public $defaultFontFamily = 'cgFontDefault';
    public $defaultFontWeight = 'normal';
    public $defaultFontStyle = 'normal';

    // start page
    public $startPageEnabled;
    public $startPageDuration;
    public $startPageShowLowerThirds;

    // end page
    public $endPageEnabled;
    public $endPageDuration;
    public $endPageShowLowerThirds;
    public $endPageOnEndActionName;
    public $endPageOnEndActionParameter;

    // global lower thirds (will be shown on very frame)
    public $globalLowerThirds;

    // logo configuration
    public $logo;

    // video urls
    public $downloadUrl;
    public $thumbnailUrl;

    public $format = Video::FORMAT_360p;
    public $ratio = Video::RATIO_16x9;
    public $frames = array();

    public $fonts = array();

    function __construct($title = null, $designId = null, $songId = null, $format = Video::FORMAT_360p, $ratio = Video::RATIO_16x9)
    {
        $this->title = $title;
        $this->songId = $songId;
        $this->designId = $designId;

        $this->globalLowerThirds = array();

        if (!empty($format)) $this->format = $format;
        if (!empty($ratio)) $this->format = $ratio;
    }

    public function addGlobalLowerThird(LowerThird $lowerThird) {
        array_push($this->globalLowerThirds, $lowerThird);
    }

    public function clearGlobalLowerThirds() {
        $this->globalLowerThirds = array();
    }

    public function addFrame(Frame $frame)
    {
        array_push($this->frames, $frame);
    }

    public function clearFrames()
    {
        $this->log->debug("Clearing frames");
        $this->frames = array();
    }

    public function addFont($fontFamily, $weight='normal', $style='normal', $src='') {
        $font = array(
            "fontFamily" => $fontFamily,
            "fontWeight" => $weight,
            "fontStyle" => $style,
            "src" => $src
        );
        array_push($this->fonts, $font);
    }

    public static function fromXml($xmlString) {
        $element = new SimpleXMLElement($xmlString);
        $video = new Video();

        // video id
        $video->id = (string)$element->video->id;

        // video metadata
        $video->title = (string)$element->video->title;
        $video->producer = (string)$element->video->producer;
        $video->description = (string)$element->video->description;
        $video->keywords = (string)$element->video->keywords;
        $video->copyright = (string)$element->video->copyright;
        $video->content = (string)$element->video->content;
        $video->targetAudience = (string)$element->video->targetAudience;
        $video->thumbnailUrl = (string)$element->video->thumbnail['url'];
        $video->downloadUrl = (string)$element->video->download['url'];

        // video timeline
        $timeline = $element->timeline;
        $config = $timeline->config;
        $video->songId = (string)$config->song['id'];
        $video->songUrl = trim((string)$config->song);
        $video->designId = (string)$config->variation['id'];
        $video->designUrl = trim((string)$config->variation);

        $video->filter = (string)$config->filter;
        $video->ratio = (string)$config->ratio;
        $video->format = (string)$config->format['name'];

        $video->maxClipLength = (string)$config->clipMaxLenght;

        // start + end page
        $video->startPageEnabled = strtolower((string)$config->startPage->enabled) == 'true' ? true : false;
        $video->startPageShowLowerThirds = strtolower((string)$config->startPage->showLowerThirds) == 'true' ? true : false;
        $video->startPageDuration = (string)$config->startPage->duration;

        $video->endPageEnabled = strtolower((string)$config->endPage->enabled) == 'true' ? true : false;
        $video->endPageShowLowerThirds = strtolower((string)$config->endPage->showLowerThirds) == 'true' ? true : false;
        $video->endPageDuration = (string)$config->endPage->duration;
        $video->endPageOnEndActionName = (string)$config->endPage->onEndAction['name'];
        $video->endPageOnEndActionParameter = (string)$config->endPage->onEndAction['parameter'];

        // read global lower thirds

        if(isset($timeline->lowerThirds) && $timeline->lowerThirds->count() > 0) {
            foreach ($timeline->lowerThirds->lowerThird as $lt) {
                $lowerThirdString = $lt->asXml();
                $lowerThird = LowerThird::fromXml($lowerThirdString);
                $video->addGlobalLowerThird($lowerThird);
            }
        }

        // read frames

        if(isset($timeline->frames) && $timeline->frames->count() > 0) {
            foreach ($timeline->frames->frame as $f) {
                $frameString = $f->asXml();
                $frame = Frame::fromXml($frameString);
                $video->addFrame($frame);
            }
        }

        // read logo
        if(isset($element->logo)) {
            $logoString = $element->logo->asXml();
            $logo = Logo::fromXml($logoString);
            $video->logo = $logo;
        }

        // read fonts
        if(isset($element->fonts)) {
            foreach ($element->fonts->font as $f) {
                $video->addFont(
                    (string) $f['fontFamily'],
                    (string) $f['fontWeight'],
                    (string) $f['fontStyle'],
                    (string) $f['src']
                );
            }
        }


        return $video;
    }


    public function asXml() {
        $xml = new SimpleXMLElement('<webcart version="2.0"></webcart>');

        $video = $xml->addChild('video');
        $video->addChild('id', $this->id);
        $video->addChild('title', $this->title);
        $video->addChild('producer', $this->producer);
        $video->addChild('description', $this->description);
        $video->addChild('keywords', $this->keywords);
        $video->addChild('copyright', $this->copyright);
        $video->addChild('content', $this->content);
        $video->addChild('targetAudience', $this->targetAudience);
        $video->addChild('thumbnail');
        $video->thumbnail->addAttribute('url', $this->thumbnailUrl);
        $video->addChild('download');
        $video->download->addAttribute('url', $this->downloadUrl);

        $timeline = $xml->addChild('timeline');
        $config = $timeline->addChild('config');
        $config->addChild('song', $this->songUrl);
        $config->song->addAttribute('id', $this->songId);
        $config->addChild('variation', $this->designUrl);
        $config->variation->addAttribute('id', $this->designId);
        $config->addChild('filter', $this->filter);

        $config->addChild('format');
        $config->format->addAttribute('name', $this->format);
        $config->addChild('ratio', $this->ratio);

        $startPage = $config->addChild('startPage');
        $startPage->addChild('enabled', $this->startPageEnabled ? 'true' : 'false');
        $startPage->addChild('showLowerThirds', $this->startPageShowLowerThirds ? 'true' : 'false');
        $startPage->addChild('duration', $this->startPageDuration);

        $endPage = $config->addChild('endPage');
        $endPage->addChild('enabled', $this->endPageEnabled ? 'true' : 'false');
        $endPage->addChild('showLowerThirds', $this->endPageShowLowerThirds ? 'true' : 'false');
        $endPage->addChild('duration', $this->endPageDuration);
        $endPage->addChild('onEndAction');
        $endPage->onEndAction->addAttribute('name', $this->endPageOnEndActionName);
        $endPage->onEndAction->addAttribute('parameter', $this->endPageOnEndActionParameter);

        $config->addChild('clipMaxLenght', $this->maxClipLength);

        // add global lower thirds
        $lowerThirds = $timeline->addChild('lowerThirds');
        foreach($this->globalLowerThirds as $lowerThird) {
            $this->addChildFromString($lowerThirds, $lowerThird->asXml());
        }

        // add frames
        $frames = $timeline->addChild('frames');
        foreach($this->frames as $frame) {
            $this->addChildFromString($frames, $frame->asXml());
        }

        // add logo
        if (isset($this->logo))
            $this->addChildFromString($xml, $this->logo->asXml());

        // add fonts
        $fontsElement = $xml->addChild('fonts');
        $fontsElement->addAttribute("defaultFontFamily", $this->defaultFontFamily);
        $fontsElement->addAttribute("defaultFontWeight", $this->defaultFontWeight);
        $fontsElement->addAttribute("defaultFontStyle", $this->defaultFontStyle);

        if (count($this->fonts) > 0) {
            foreach($this->fonts as $font) {
                $fontElement = $fontsElement->addChild("font");
                $fontElement->addAttribute("fontFamily", $font['fontFamily']);
                $fontElement->addAttribute("fontWeight", $font['fontWeight']);
                $fontElement->addAttribute("fontStyle", $font['fontStyle']);
                $fontElement->addAttribute("src", $font['src']);
            }
        }

        // ignore formats, todo: really ignore this? formats will be injected by the backend when the xml is read
        // so it does not affect the creation of clips at the moment

        // todo: move this function to the backend, this is the wrong place
        return $xml->asXML();
    }

    private function addChildFromString($parent, $childString) {
        $domparent = dom_import_simplexml($parent);
        $domchild  = dom_import_simplexml(new SimpleXMLElement($childString));
        $domchild  = $domparent->ownerDocument->importNode($domchild, true);
        $domparent->appendChild($domchild);
    }

    private function compactFonts($webcartXml) {
        // list of system provided fonts
        $SYSTEM_FONTS = array(
            array('family'=> 'cgFontDefault', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/DefaultRegularFont.swf'),
            array('family'=> 'cgFontDefault', 'weight'=>'bold', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/DefaultBoldFont.swf'),
            array('family'=> 'cgFontDefault', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/DefaultItalicFont.swf'),
            array('family'=> 'cgFontDefault', 'weight'=>'bold', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/DefaultBoldItalicFont.swf'),
            array('family'=> 'cgFontActionMan', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/ActionManRegularFont.swf'),
            array('family'=> 'cgFontActionMan', 'weight'=>'bold', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/ActionManBoldFont.swf'),
            array('family'=> 'cgFontActionMan', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/ActionManItalicFont.swf'),
            array('family'=> 'cgFontActionMan', 'weight'=>'bold', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/ActionManBoldItalicFont.swf'),
            array('family'=> 'cgFontAnonymousPro', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/AnonymousProRegularFont.swf'),
            array('family'=> 'cgFontAnonymousPro', 'weight'=>'bold', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/AnonymousProBoldFont.swf'),
            array('family'=> 'cgFontAnonymousPro', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/AnonymousProItalicFont.swf'),
            array('family'=> 'cgFontAnonymousPro', 'weight'=>'bold', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/AnonymousProBoldItalicFont.swf'),
            array('family'=> 'cgFontSpecialElite', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/SpecialEliteRegularFont.swf'),
            array('family'=> 'cgFontNobile', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/NobileRegularFont.swf'),
            array('family'=> 'cgFontNobile', 'weight'=>'bold', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/NobileBoldFont.swf'),
            array('family'=> 'cgFontNobile', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/NobileItalicFont.swf'),
            array('family'=> 'cgFontNobile', 'weight'=>'bold', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/NobileBoldItalicFont.swf'),
            array('family'=> 'cgFontAlegreya', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/AlegreyaRegularFont.swf'),
            array('family'=> 'cgFontAlegreya', 'weight'=>'bold', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/AlegreyaBoldFont.swf'),
            array('family'=> 'cgFontAlegreya', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/AlegreyaItalicFont.swf'),
            array('family'=> 'cgFontAlegreya', 'weight'=>'bold', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/AlegreyaBoldItalicFont.swf'),
            array('family'=> 'cgFontLeagueGothic', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/LeagueGothicRegularFont.swf'),
            array('family'=> 'cgFontLeagueGothic', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/LeagueGothicItalicFont.swf'),
            array('family'=> 'cgFontDancingScript', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/DancingScriptRegularFont.swf'),
            array('family'=> 'cgFontClaireHand', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/ClaireHandRegularFont.swf'),
            array('family'=> 'cgFontClaireHand', 'weight'=>'bold', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/ClaireHandBoldFont.swf'),
            array('family'=> 'cgFontCicleGordita', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/CicleGorditaRegularFont.swf'),
            array('family'=> 'cgFontCicleGordita', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/CicleGorditaItalicFont.swf'),
            array('family'=> 'cgFontSortsMillGoudy', 'weight'=>'normal', 'style'=>'normal', 'src'=>'http://data.clipgenerator.com/fonts/v3/SortsMillGoudyRegularFont.swf'),
            array('family'=> 'cgFontSortsMillGoudy', 'weight'=>'normal', 'style'=>'italic', 'src'=>'http://data.clipgenerator.com/fonts/v3/SortsMillGoudyItalicFont.swf'),
        );

        // create lookup list of system fonts
        $systemFonts = array();
        foreach ($SYSTEM_FONTS as $font) {
            $key = $font['family'] . '.' . $font['weight'] . '.' . $font['style'] ;
            $systemFonts[$key] = $font;
        }

        // create list of used fonts in the webcart
        $usedFonts = array();
        foreach ($webcartXml->xpath('//text') as $node) {
            // todo: when isHTML is introduced in the backend, scan the insides of the text too.
            $text = (string)$node;
            $family = (string)$node['fontFamily'];
            $weight = (string)$node['fontWeight'];
            $style = (string)$node['fontStyle'];
            $key = "$family.$weight.$style";
            $font = array("family" => $family, "weight" => $weight, "style" => $style);
            $usedFonts[$key] = $font;
        }

        // create list of already existing fonts in the webcart (may include custom fonts)
        $suppliedFonts = array();
        foreach ($webcartXml->xpath('/webcart/fonts//font') as $node) {
            $family = (string)$node['fontFamily'];
            $weight = (string)$node['fontWeight'];
            $style = (string)$node['fontStyle'];
            $src = (string)$node['src'];
            $key = "$family.$weight.$style";
            $font = array("family" => $family, "weight" => $weight, "style" => $style, "src" => $src);
            $suppliedFonts[$key] = $font;
        }

        // check which font to include, and which not
        $includeFonts = array();
        foreach ($usedFonts as $key => $font) {
            if (array_key_exists($key, $systemFonts)) {
                array_push($includeFonts, $systemFonts[$key]);
            } else if (array_key_exists($key, $suppliedFonts)) {
                array_push($includeFonts, $suppliedFonts[$key]);
            } else {
                // ignore this case, if we cannot find the correct font, so it should be,
                // in this case the player should fall back to the default font hopefully
                // todo: log that a font was used that was not found
            }
        }

        // actually modify the fonts element in webcart, to include
        // new correct font list
        $defaultFontFamily = $webcartXml->fonts['defaultFontFamily'];
        $defaultFontWeight = $webcartXml->fonts['defaultFontWeight'];
        $defaultFontStyle = $webcartXml->fonts['defaultFontStyle'];
        // remove existing node
        $webcartXml->fonts = null;
        // add new one
        $fontsNode = $webcartXml->addChild('fonts');
        $fontsNode->addAttribute("defaultFontFamily", $defaultFontFamily);
        $fontsNode->addAttribute("defaultFontWeight", $defaultFontWeight);
        $fontsNode->addAttribute("defaultFontStyle", $defaultFontStyle);

        foreach ($includeFonts as $font) {
            $fontNode = $fontsNode->addChild('font');
            $fontNode->addAttribute("fontFamily", $font['family']);
            $fontNode->addAttribute("fontWeight", $font['weight']);
            $fontNode->addAttribute("fontStyle", $font['style']);
            $fontNode->addAttribute("src", $font['src']);
        }

        return $webcartXml;
    }
}
//<webcart version="2.0">
//    <video>
//        <id>fzuUT5DsCG6b80xLC5B3s5iq4dfL4CLj</id>
//        <title>super text</title>
//        <producer>sergej</producer>
//        <description>bezeichnung</description>
//        <keywords>keywork</keywords>
//        <copyright>copy</copyright>
//        <content>inhalt</content>
//        <targetAudience>zielgruppe</targetAudience>
//        <thumbnail url="http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_thumb.jpg"/>
//        <download url=""/>
//    </video>
//    <timeline>
//        <config>
//            <song id="Land_aus_Glas_Clipgenerator_5_27_333.mp3" copyright="">
//                http://data.clipgenerator.com/music/65/SUEp1qj3837vy3GH.mp3
//            </song>
//            <variation id="ce_showtime">http://data.clipgenerator.com/variations/v2/xml/ce_showtime.xml</variation>
//            <filter>none</filter>
//            <ratio>16x9</ratio>
//            <format name="360p"/>
//            <startPage>
//                <enabled>false</enabled>
//                <duration>5</duration>
//                <showLowerThirds>false</showLowerThirds>
//            </startPage>
//            <endPage>
//                <enabled>false</enabled>
//                <duration>5</duration>
//                <onEndAction name="showIntro" parameter=""/>
//                <showLowerThirds>false</showLowerThirds>
//            </endPage>
//            <clipMaxLenght>0</clipMaxLenght>
//        </config>
//        <lowerThirds>
//            <lowerThird width="0.3" height="0.1" horizontalAlign="left" verticalAlign="top" alpha="0.7">
//                <color>0</color>
//                <link>http://</link>
//                <text color="16777215" fontSize="14" bold="false" italic="false" underline="false">SERGEJ</text>
//            </lowerThird>
//        </lowerThirds>
//        <frames>
//            <frame>
//            <number>0</number>
//            <picture width="1" height="1" alpha="1">
//                <id>16441</id>
//                <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f.jpg</url>
//                <thumb_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_thumb.jpg
//                </thumb_url>
//                <web_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_web.jpg</web_url>
//                <url_240p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_240p.jpg</url_240p>
//                <url_360p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_360p.jpg</url_360p>
//                <url_480p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_480p.jpg</url_480p>
//                <url_720p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/07b0aa3f_720p.jpg</url_720p>
//            </picture>
//            <background>
//                <color>65433</color>
//            </background>
//            <lowerThirds>
//                <lowerThird width="0.45" height="0.39555555555556" horizontalAlign="center" verticalAlign="middle"
//                            alpha="0.7">
//                    <color>0</color>
//                    <link>http://</link>
//                    <text color="16777215" fontSize="31" bold="false" italic="false" underline="false">SUPER TEXT
//                    </text>
//                </lowerThird>
//            </lowerThirds>
//            </frame>
//            <frame>
//            <number>1</number>
//            <picture width="1" height="1" alpha="1">
//                <id>18572</id>
//                <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63.jpg</url>
//                <thumb_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63_thumb.jpg
//                </thumb_url>
//                <web_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63_web.jpg</web_url>
//                <url_240p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63_240p.jpg</url_240p>
//                <url_360p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63_360p.jpg</url_360p>
//                <url_480p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63_480p.jpg</url_480p>
//                <url_720p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/28f73d63_720p.jpg</url_720p>
//            </picture>
//            </frame>
//            <frame>
//            <number>2</number>
//            <picture width="1" height="1" alpha="1">
//                <id>18573</id>
//                <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2.jpg</url>
//                <thumb_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2_thumb.jpg
//                </thumb_url>
//                <web_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2_web.jpg</web_url>
//                <url_240p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2_240p.jpg</url_240p>
//                <url_360p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2_360p.jpg</url_360p>
//                <url_480p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2_480p.jpg</url_480p>
//                <url_720p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/b84820f2_720p.jpg</url_720p>
//            </picture>
//            </frame>
//            <frame>
//            <number>3</number>
//            <picture width="1" height="1" alpha="1">
//                <id>18574</id>
//                <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064.jpg</url>
//                <thumb_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064_thumb.jpg
//                </thumb_url>
//                <web_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064_web.jpg</web_url>
//                <url_240p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064_240p.jpg</url_240p>
//                <url_360p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064_360p.jpg</url_360p>
//                <url_480p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064_480p.jpg</url_480p>
//                <url_720p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/cf4f1064_720p.jpg</url_720p>
//            </picture>
//            <background>
//                <color>204</color>
//            </background>
//            </frame>
//            <frame>
//            <number>4</number>
//            <picture width="1" height="1" alpha="1">
//                <id>18575</id>
//                <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de.jpg</url>
//                <thumb_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de_thumb.jpg
//                </thumb_url>
//                <web_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de_web.jpg</web_url>
//                <url_240p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de_240p.jpg</url_240p>
//                <url_360p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de_360p.jpg</url_360p>
//                <url_480p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de_480p.jpg</url_480p>
//                <url_720p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/564641de_720p.jpg</url_720p>
//            </picture>
//            <background>
//                <color>16711935</color>
//            </background>
//            </frame>
//            <frame>
//            <number>5</number>
//            <picture width="1" height="1" alpha="1">
//                <id>18576</id>
//                <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811.jpg</url>
//                <thumb_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811_thumb.jpg
//                </thumb_url>
//                <web_url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811_web.jpg</web_url>
//                <url_240p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811_240p.jpg</url_240p>
//                <url_360p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811_360p.jpg</url_360p>
//                <url_480p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811_480p.jpg</url_480p>
//                <url_720p>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/picture/d954d811_720p.jpg</url_720p>
//            </picture>
//            <background>
//                <color>65382</color>
//            </background>
//            </frame>
//        </frames>
//    </timeline>
//    <logo width="0.1" height="0.1" alpha="1">
//        <id>18194</id>
//        <url>http://user-data.clipgenerator.com/l/g/5/j/127a00f2/logo/4ad842bc.png</url>
//        <link>http://</link>
//        <position verticalAlign="top" horizontalAlign="right"/>
//        <showInStartPage>false</showInStartPage>
//        <showInEndPage>false</showInEndPage>
//        <showInTimeline>true</showInTimeline>
//    </logo>
//    <formats>
//        <format name="240p" ratio="4x3" resolution="320x240" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="360p" ratio="4x3" resolution="480x360" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="480p" ratio="4x3" resolution="640x480" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="720p" ratio="4x3" resolution="960x720" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="1080p" ratio="4x3" resolution="1440x1080" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="240p" ratio="16x9" resolution="426x240" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="360p" ratio="16x9" resolution="640x360" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="480p" ratio="16x9" resolution="853x480" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="720p" ratio="16x9" resolution="1280x720" isPreviewVisible="true" canPreview="true"
//                isDownloadVisible="true" canDownload="true"/>
//        <format name="1080p" ratio="16x9" resolution="1920x1080" isPreviewVisible="false" canPreview="false"
//                isDownloadVisible="true" canDownload="true"/>
//    </formats>
//    <branding branded="false"/>
//</webcart>
