<?php
// Copyright 2012 Trivid. All Rights Reserved.
//
// +---------------------------------------------------------------------------+
// | Clipgenerator PHP 5 Client                                     		   |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2012 Trivid GmbH                                            |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | 1. Redistributions of source code must retain the above copyright         |
// |    notice, this list of conditions and the following disclaimer.          |
// | 2. Redistributions in binary form must reproduce the above copyright      |
// |    notice, this list of conditions and the following disclaimer in the    |
// |    documentation and/or other materials provided with the distribution.   |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR      |
// | IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES |
// | OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.   |
// | IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT  |
// | NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF  |
// | THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.         |
// +---------------------------------------------------------------------------+
// | For help with this library, contact s.mueller@trivid.com          		   |
// +---------------------------------------------------------------------------+

class ClipgeneratorClient {
    const PACKAGE_TYPE_EDITOR = 'editor';
    const PACKAGE_TYPE_MUSIC = 'music';
    const PACKAGE_TYPE_DESIGN = 'design';

    const VIDEO_FORMAT_MP4 = 'mp4';
    const VIDEO_RESOLUTION_400x226 = '400x226';
    const VIDEO_RESOLUTION_640x360 = '640x360';

    const HTTP_CLIENT_ZEND = 'ZendClient';
    const HTTP_CLIENT_CURL = 'Curl';

    private $apiId;
    private $apiSecret;
    private $apiURL = 'http://api-v2.clipgenerator.com/';
    private $lang = 'de';
    private $error = null;
    private $errorCode = 0;
    private $debug = false;
    private $debugData = array();
    private $userId = null;
    private $curlCookieFile = null;
    private $callMethod = 'Curl';

    /**
     * Constructor
     *
     * @return
     */

    function __construct($apiId, $apiSecret, $userId=null, $lang='de', $apiURL = null, $curlCookieFile = null)
    {
        $this->apiId = $apiId;
        $this->apiSecret = $apiSecret;
        $this->userId = $userId;

        if (!is_null($lang)) $this->lang = $lang;
        if (!is_null($apiURL)) $this->apiURL = $apiURL;
        if (!is_null($curlCookieFile)) $this->curlCookieFile = $curlCookieFile;
    }

    /**
     * Sets the language for the current session. All subsequent api calls will return data in the configured language.
     *
     * @param $lang String The language identifier. Supported languages are 'en' for English and 'de' for German
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Configures the user id that is required to identify the user on the clipgenerator backend.
     * Typically this user id will be a hash of a unique user name from the application that uses this api.
     * @param $userId String User id that will uniquely identify the user inside the app.
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Configures the http client that will be used for communication with the backend.
     * The Zend Http Client is used as default.
     *
     * @param $callMethod String One 'ZendClient' or 'Curl'.
     */
    public function setCallMethod($callMethod)
    {
        if (in_array($callMethod, array('ZendClient', 'Curl')))
        {
            $this->callMethod = $callMethod;
        }
    }

    /**
     * Lists all videos of the current user
     *
     * @return bool false if error, list of videos otherwise.
     * @todo: this call should be moved to the api and return the corresponding json structure
     */

    public function getVideos($videoIdFilter=null) {
        $result = $this->call('getVideos', array(
            'videoIdFilter' => $videoIdFilter
        ));
        return $result;
    }

    /**
     * Loads a video with the $videoId in the current context. If no $videoId is supplied the id of the currently
     * loaded video will be used. In most cases this will reload the currently loaded video and reset all modifications
     * if the video was not saved using saveVideo().
     *
     * @param null $videoId String The id of the video that should be loaded in the current context.
     * If no $videoId is null the videoId of the current context will be used.
     *
     * @return false | xml string of the video webcart xml
     */
    public function loadVideo($videoId)
    {
        $result = $this->call('loadVideo', array(
            'videoId' => $videoId,
        ));

        if ($result !== false) {
            return $result['video'];
        }
        return false;
    }

    /**
     * Reads downloads pf the $videoId in the current context. If no $videoId is supplied then error.
     *
     * @param null $videoId String The id of the video that should be loaded in the current context.
     * If no $videoId is null the videoId of the current context will be used.
     *
     * @return false | array of downloads with params (status, date, etc.)
     */
    public function getDownloads($videoId)
    {
        $result = $this->call('getDownloads', array(
            'videoId' => $videoId,
        ));

        if ($result !== false) {
            return $result;
        }
        return false;
    }

    /**
     * Saves the current video configuration on the clipgenerator backend. If no $videoId is supplied the backend
     * will save the video as a new video using a new video id.
     * Note: Saving a new video will increase the available video slots for the current user.
     * @param null $videoId String Optional video id of an existing video that should be overwritten.
     * @return false if the call failed, videoId: string if the call succeeded
     * @todo fix the result handling
     */

    public function saveVideo($videoXmlString, $temporary = false)
    {
        $result = $this->call('saveVideo', array(
                'video' => $videoXmlString,
                'temporary' => $temporary ? 'true' : 'false',
            )
        );

        if ($result !== false) {
            $newVideoId = $result['id'];
            return $newVideoId;
        }
        return false;
    }

    /**
     * Deletes a video from the users video list.
     * @param $videoId array Array of string ids of the videos that should be deleted.
     * @return bool
     * @todo barely tested.
     * @todo add ids of the successfully deleted video to the response
     */
    public function deleteVideo($videoId)
    {
        $result = $this->call('deleteVideo', array('videoId' => $videoId));
        return $result !== false ? true : false;
    }

    /**
     * Uploads a local image file to the clipgenerator backend. The picture will be converted to
     * different resolutions and an result array with an unique picture identifier and link will
     * be returned.
     * @param $filePath
     * @return array|bool
     * @todo move getting the file information to the backend
     */
    public function uploadPicture($filePath, $forceNewId=true)
    {
        if(strpos($filePath, "http://") !== false)
            $result = $this->call('uploadPicture', array('forceNewId' => $forceNewId, 'pictureUrl' => $filePath));
        else
            $result = $this->call('uploadPicture', array('forceNewId' => $forceNewId), $filePath);
        return $result;
    }

    public function uploadLogo($filePath, $forceNewId=true)
    {
        if(strpos($filePath, "http://") !== false)
            $result = $this->call('uploadPicture', array('forceNewId' => $forceNewId, 'logoUrl' => $filePath));
        else
            $result = $this->call('uploadPicture', array('forceNewId' => $forceNewId), $filePath);
        return $result;
    }

    /**
     * Uploads a local mp3 file to the clipgenerator backend. An unique resource identifier and link will
     * be returned.
     * @param $filePath
     * @return array|bool
     * @todo move getting the file information to the backend
     */
    public function uploadMusic($filePath, $forceNewId=true)
    {
        $result = $this->call('uploadMusic', array('forceNewId' => $forceNewId), $filePath);
        return $result;
    }

    /**
     * Lists all pictures of the current user including their additional information.
     * @throws Exception
     * @todo implement this call in the backend.
     * note: this call has the same name as an existing call. The existing call should be replaced by this call.
     */

    public function getPictures() {
        throw new Exception('Not implemented yet: getPictures');
    }

    /**
     * Deletes uploaded pictures on the clipgenerator backend.
     * @param $pictureIds Array Array of picture ids that should be deleted.
     * @throws Exception
     * @todo implement this call in the backend.
     */
    public function deletePictures($pictureIds) {
        throw new Exception('Not implemented yet: getPictures');
    }

    /**
     * Lists all available video designs.
     * @todo rename the server api call from getVideoDesign to getDesigns
     * @todo not well tested yet
     */
    public function getDesigns()
    {
        $result = $this->call('getDesigns');
        return $result;
    }

    /**
     * Get packages,  genres and songs lists. With filtering by package and genre ids if set
     * @param package int (0,+)
     * @param genre int (0,+)
     *
     * @return bool false if error, arrays of packages, genres, songs
     * @TODO: rename the call in the backend to get
     * @TODO: later. implement filters on the backend for.
     * @TODO: package=x, only shows data for package x
     * @TODO: mood=x, only shows data for mood x
     * @TODO: speed=x, only shows data for speed x
     * @TODO: genre=x, only shows data for genre x
     * @TODO: song=x, only shows data for song x
     * @TODO: include_songs=true, if false songs will be ommited
     * @TODO: all filters should be combinable so package=x + mood=y would only return data that matches package x AND mood y
     */
    public function getMusic($purchasedOnly=false)
    {
        $result = $this->call('getMusic', array(
            'purchasedOnly' => $purchasedOnly
        ));
        return $result;
    }

    /**
     *
     * @param $user
     * @return bool
     */
    public function createUser($userId, $email, $password, $firstName='', $lastName='', $company='',
                               $street='', $zipCode='', $city='', $country='', $isActive='true',
                               $vatId='')
    {
        $result = $this->call('createUser',  array(
            "userId" => $userId,
            "email" => $email,
            "password" => $password,
            "firstName" => $firstName,
            "lastName" => $lastName,
            "company" => $company,
            "street" => $street,
            "zipCode" => $zipCode,
            "city" => $city,
            "country" => $country,
            "isActive" => $isActive,
            "vatId" => $vatId
        ));
        return $result !== false;
    }

    /**
     * Get account current user information (based on $this->userId)
     *
     * @return array
     */
    public function getUser()
    {
        $user = $this->call('getUser');
        return $user;
    }

    public function getUserList()
    {
        $userList = $this->call('getUserList');
        return $userList;
    }

    /**
     * Delete user information
     *
     * @return bool
     */
    public function deleteUser()
    {
        $result = $this->call('deleteUser');
        return $result !== false ? true : false;
    }

    /**
     * @depricated will be replaced by getMusic for music packages and getPlans() for complex application plans
     * @TODO: think this through again
     */
    public function getPackages()
    {
        $result = $this->call('getPackages');
        return $result;
    }

    /**
     * Add package to user packages
     * @param packageType string (editor | music)
     * @param packageId int
     * @return bool
     * @depricated will be removed by activateMusicPackage() and activatePlan()
     * @TODO: TBD
     */
    public function activatePackage($packageType, $packageId)
    {
        $result = $this->call('activatePackage', array(
            'packageType' => $packageType,
            'packageId' => $packageId,
        ));
        return $result !== false;
    }

    public function deactivatePackage($packageType, $packageId)
    {
        $result = $this->call('deactivatePackage', array(
            'packageType' => $packageType,
            'packageId' => $packageId,
        ));
        return $result !== false;
    }

    /**
     *
     * @param $videoId
     * @param $params
     * @return bool
     */

    public function scheduleDownloadJob($videoId, $resolution, $format, $playerVersion="")
    {
        $result = $this->call('scheduleDownloadJob', array(
                'videoId' => $videoId,
                'resolution' => $resolution,
                'format' => $format,
                'playerVersion' => $playerVersion
            )
        );

        if ($result !== false) {
            return $result;
        }
        return false;
    }

    /**
     * Get list of picture pool services
     *
     * @return bool false if error, array of services names otherwise
     */
    public function getPicturePoolServices()
    {
        $result = $this->call('getPicturePoolServices');
        return $result !== false ? $result['services'] : false;
    }

    /**
     * Get list of picture pool service galleries
     * @param service string
     *
     * @return bool false if error, array of galleries names otherwise
     */
    public function getPicturePoolGalleries($service)
    {
        $result = $this->call('getPicturePoolGalleries', array('service' => $service));
        return $result !== false ? $result['galleries'] : false;
    }

    /**
     * Get pictures from gallery
     * @param service string
     *
     * @param gallery string
     *
     * @return bool false if error, array of pictures with different formats otherwise
     */
    public function getPicturePool($service, $gallery)
    {
        $result = $this->call('getPicturePool', array(
            'service' => $service,
            'gallery' => $gallery,
        ));
        return $result !== false ? $result['pictures'] : false;
    }

    /**
     * Get error
     *
     * @return string error.
     */
    public function getError()
    {
        if (!empty($this->error)) {
            return "({$this->errorCode}) {$this->error}";
        } else {
            return "Unknown error.";
        }
    }

    /**
     * Enable debug
     *
     */
    public function enableDebug()
    {
        $this->debug = true;
    }

    /**
     * Get debug data
     *
     * @return array debug data
     */
    public function getDebugData()
    {
        return $this->debugData;
    }

    private function call($controller, $params = array(), $postFilePath = '')
    {
        $url = $this->apiURL . $controller;
        foreach ($params as $key => $value) {
            // todo: remove the json part, it's strange
            if (is_array($value)) $params[$key] = json_encode($value);
            elseif (is_bool($value)) $params[$key] = $value == true ? '1' : '0';
        }
        $postFields = array_merge($params, array(
            'apiId' => $this->apiId,
            'apiSecret' => $this->apiSecret,
            'lang' => $this->lang,
        ));

        if (!is_null($this->userId))
            $postFields['userId'] = $this->userId;

        if ($this->debug) $this->debugData = array(
            'url' => $url,
            'postFields' => $postFields,
        );

        switch ($this->callMethod) {
            case 'ZendClient':
                $responce = $this->makeZendClientRequest($url, $postFields, $postFilePath);
                break;
            case 'Curl':
                $responce = $this->makeCurlRequest($url, $postFields, $postFilePath);
                break;
        }

        $result = json_decode($responce, true);

        if (is_null($result)) {
            $e = json_last_error();
            $this->error = 'JSON ERROR: '.$e;
            if ($e == JSON_ERROR_SYNTAX) {
                $len = strlen($responce);
                $this->error = "JSON_ERROR_SYNTAX " .$len;
            }
            return false;
        }

        if ($this->debug) $this->debugData['result'] = $result;
        if ($result['code'] != 200) {
            $this->error = isset($result['message']) ? $result['message'] : 'Unknown error.';
            $this->errorCode = isset($result['code']) ? (int) $result['code'] : -1;
            return false;
        }
        $this->error = null;
        return isset($result['result']) ? $result['result'] : array();
    }

    private function makeCurlRequest($url, $postFields, $postFilePath = '')
    {
        if ($postFilePath != '') $postFields['postFile']='@'.$postFilePath;

        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
        ));
        if (!is_null($this->curlCookieFile)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->curlCookieFile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->curlCookieFile);
        }
        try {
            $responce = curl_exec($ch);
        } catch (Exception $e) {
            $this->error = 'connection error';
            if ($this->debug) $this->debugData['error'] = $e->getMessage();
            return false;
        }
        if ($this->debug) $this->debugData['response'] = $responce;
        curl_close($ch);
        if (!$responce) {
            $this->error = 'connection error';
            if ($this->debug) $this->debugData['error'] .= '   curl_exec returns false';
            return false;
        }
        return $responce;
    }

    private function makeZendClientRequest($url, $postFields, $postFilePath = '')
    {
        $client = new Zend_Http_Client($url);
        $client->setCookieJar();
        $client->setParameterPost($postFields);
        if ($postFilePath != '') $client->setFileUpload($postFilePath, basename($postFilePath));
        try {
            $response = $client->request('POST');
        } catch (Zend_Http_Client_Adapter_Exception $e) {
            $this->error = 'connection error';
            if ($this->debug) $this->debugData['error'] = $e->getMessage();
            return false;
        }
        if ($this->debug) $this->debugData['response'] = $response->getBody();
        return $response->getBody();
    }
}