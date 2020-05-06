<?php


class ToastSMS
{
    private $api_key; /* Api app key */
    private $user_agent; /* User-agent */
    private $host = "https://api-sms.cloud.toast.com"; /* Toast api domain */
    private $path; /* Url path */
    private $method; /* GET 0 / POST 1 */
    private $version; /* Api version */
    private $rejectionNumber; /* Rejection number(console > 080 rejection setting) */
    private $content; /* Data */
    private $result; /* Api Result */
    private $error; /* Error Message */
    private $authMsg = "인증"; /* Default = AUTH_REQUIRED_MSG_KR */
    private $language = "KR"; /* Default = KR */

    public const AUTH = "AUTH";
    public const AD = "AD";
    public const TAG = "TAG";

    public const KR = "KOREAN";
    public const EN = "ENGLISH";
    public const JP = "JAPANESE";

    /* Authentication Required Message */
    public const AUTH_REQUIRED_MSG_EN = "auth";
    public const AUTH_REQUIRED_MSG_KR = "인증";
    public const AUTH_REQUIRED_MSG_JP = "にんしょう";
    public const AUTH_REQUIRED_MSG_CN = "認証";
    public const AUTH_REQUIRED_MSG_VERIF = "verif";
    public const AUTH_REQUIRED_MSG_PASSWORD = "password";
    public const AUTH_REQUIRED_MSG_PASSWORD_KR = "비밀번호";

    public const MMS = "mms";
    public const SMS = "sms";

    /* Advertisement Required Message */
    private const AD_REQUIRED_MSG_FIRST_KR = "(광고)";
    private const AD_REQUIRED_MSG_SECOND_KR = "[무료 수신 거부]";
    private const AD_REQUIRED_MSG_FIRST_EN = "(Ad)";
    private const AD_REQUIRED_MSG_SECOND_EN = "[Reject receiving ads charge-free]";
    private const AD_REQUIRED_MSG_FIRST_JP = "(広告)";
    private const AD_REQUIRED_MSG_SECOND_JP = "[無料受信拒否]";

    public function __construct($apiKey, $sender, $version= "2.3")
    {
        $this->api_key = $apiKey;
        $this->content["sendNo"] = $sender;
        $this->version = $version;

        if(isset($_SERVER['HTTP_USER_AGENT']))
            $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * process curl
     */
    public function curlProcess()
    {
        $curl = curl_init();

        $host = sprintf("%s/sms/v%s/appKeys/%s%s",
            $this->host, $this->version, $this->api_key, $this->path);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->content, JSON_UNESCAPED_UNICODE));
        curl_setopt($curl, CURLOPT_URL, $host);
        curl_setopt($curl, CURLOPT_POST, $this->method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/json;charset=UTF-8"));
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $this->result = curl_exec($curl);

        $resultArray = json_decode($this->result, true);
        $resultHeader = $resultArray["header"];

        if($resultHeader["isSuccessful"] == false){
            $this->error["isSuccessful"] = $resultHeader["isSuccessful"];
            $this->error["resultCode"] = $resultHeader["resultCode"];
            $this->error["resultMessage"] = $resultHeader["resultMessage"];
        }

        if(curl_errno($curl)){
            $this->error = curl_errno($curl);
        }

        curl_close($curl);
    }

    /**
     * send Message
     * @param string $msgType
     * @param string $type
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @return $this
     */
    public function send($msgType, $type, array $recipientList, ToastSMSOption $option){

        $this->content["recipientList"] = $this->setRecipientToArray($recipientList);
        $option = $this->setBodyOfOptionsByType($type, $option);
        $this->setOptionsToContent($option);
        $this->setMethod($this->getPathByType($type, $msgType), 1);
        $this->curlProcess();

        return $this;
    }

    /**
     * send basic SMS
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @return $this
     */
    public function sendSMS(array $recipientList, ToastSMSOption $option){
        $this->send(self::SMS, self::SMS, $recipientList, $option);
        return $this;
    }

    /**
     * send SMS for Authentication(emergency)
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @param $authMsg
     * @return $this
     */
    public function sendAuthSMS(array $recipientList, ToastSMSOption $option, $authMsg){
        $this->authMsg = $authMsg;

        $this->send(self::SMS, self::AUTH, $recipientList, $option);
        return $this;
    }

    /**
     * send SMS for Advertisement
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @param $rejectionNumber
     * @return $this
     */
    public function sendAdSMS(array $recipientList, ToastSMSOption $option, $rejectionNumber){
        $this->rejectionNumber = $rejectionNumber;

        $this->send(self::SMS, self::AD, $recipientList, $option);
        return $this;
    }

    /**
     * send Tagged SMS
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @return $this
     */
    public function sendTagSMS(array $recipientList, ToastSMSOption $option){
        $this->send(self::SMS, self::TAG, $recipientList, $option);
        return $this;
    }

    /**
     * send basic MMS
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @return $this
     */
    public function sendMMS(array $recipientList, ToastSMSOption $option){
        $this->send(self::MMS, self::MMS, $recipientList, $option);
        return $this;
    }

    /**
     * send MMS for Advertisement
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @param $rejectionNumber
     * @return $this
     */
    public function sendAdMMS(array $recipientList, ToastSMSOption $option, $rejectionNumber){
        $this->rejectionNumber = $rejectionNumber;

        $this->send(self::MMS, self::AD, $recipientList, $option);
        return $this;
    }

    /**
     * send Tagged LMS
     * @param ToastSMSRecipient[] $recipientList
     * @param ToastSMSOption $option
     * @return $this
     */
    public function sendTagLMS(array $recipientList, ToastSMSOption $option){
        $this->send(self::MMS, self::TAG, $recipientList, $option);
        return $this;
    }

    /**
     * set language(KR, EN ..)
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * set rejection number(console > 080 rejection setting)
     * @param mixed $rejectionNumber
     */
    public function setRejectionNumber($rejectionNumber): void
    {
        $this->rejectionNumber = $rejectionNumber;
    }

    /**
     * set required auth message
     * @param string $authMsg
     */
    public function setAuthMsg(string $authMsg): void
    {
        $this->authMsg = $authMsg;
    }

    /**
     * set required content by type(auth, ad ...)
     * @param $type
     * @param ToastSMSOption $option
     * @return ToastSMSOption
     */
    private function setBodyOfOptionsByType($type, ToastSMSOption $option){
        $text = $option->getBody();
        if(empty($option->getTemplateId())){
            if(strcmp($type, self::AD) == 0){
                if(strcmp($this->language, self::JP) == 0){
                    $text = self::AD_REQUIRED_MSG_FIRST_JP.$text."\n"
                        .self::AD_REQUIRED_MSG_SECOND_JP.$this->rejectionNumber;
                } else if(strcmp($this->language, self::EN) == 0){
                    $text = self::AD_REQUIRED_MSG_FIRST_EN.$text."\n"
                        .self::AD_REQUIRED_MSG_SECOND_EN.$this->rejectionNumber;
                } else{
                    $text = self::AD_REQUIRED_MSG_FIRST_KR.$text."\n"
                        .self::AD_REQUIRED_MSG_SECOND_KR.$this->rejectionNumber;
                }
            } else if(strcmp($type,self::AUTH) == 0){
                $text = "(".$this->authMsg.") ".$text;
            }
        }

        $option->setBody($text);
        return $option;
    }

    /**
     * get url path by type(auth, ad ..)
     * @param string $type
     * @return string
     */
    private function getPathByType($type, $msgType){
        if(strcmp($type, self::AUTH) == 0){
            $path = "/sender/auth/";
        } else if(strcmp($type, self::AD) == 0){
            $path = "/sender/ad-";
        } else if(strcmp($type, self::TAG) == 0){
            $path = "/tag-sender/";
        } else {
            $path = "/sender/";
        }
        $path .= $msgType;
        return $path;
    }

    /**
     * set url path, method, version
     * @param string $path
     * @param int $method
     * @param string $version
     */
    private function setMethod($path, $method, $version="2.3")
    {
        $this->path = $path;
        $this->method = $method;
        $this->version = $version;
    }

    /**
     * set option data to content(array)
     * @param ToastSMSOption $options
     */
    private function setOptionsToContent(ToastSMSOption $options){
        $this->content = array_merge($this->content, $options->objectToArray());
    }

    /**
     * set required recipient variable to array
     * @param ToastSMSRecipient[] $recipientList
     * @return ToastSMSRecipient[]
     */
    private function setRecipientToArray(array $recipientList){
        $formattedRecipientList = array();
        foreach ($recipientList as $recipient){
            array_push($formattedRecipientList, $recipient->objectToArray());
        }

        return $formattedRecipientList;
    }
}

class ToastSMSOption
{
    private $title;
    private $body;
    private $templateId; /* Template ID */
    private $requestDate; /* Request date and time  */
    private $senderGroupingKey; /* Sender's group key */
    private $attachFileIdList; /* Attached file included, for MMS*/
    private $userId; /* User ID */
    private $statsId; /* Statistics ID */
    private $tagExpression; /* Tag expression, for TAG message, type(Array) */
    private $adYn; /* Ad or not (default: N), for MMS, for TAG message*/
    private $autoSendYn; /* Auto delivery or not (immediate delivery) (default: Y), for MMS, for TAG message */

    public function __construct($text, $title = null)
    {
        $this->body = $text;

        if(!empty($title)){
            $this->title = $title;
        }
    }

    /**
     * @return null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param mixed $templateId
     */
    public function setTemplateId($templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return mixed
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * @param mixed $requestDate
     */
    public function setRequestDate($requestDate): void
    {
        $this->requestDate = $requestDate;
    }

    /**
     * @return mixed
     */
    public function getSenderGroupingKey()
    {
        return $this->senderGroupingKey;
    }

    /**
     * @param mixed $senderGroupingKey
     */
    public function setSenderGroupingKey($senderGroupingKey): void
    {
        $this->senderGroupingKey = $senderGroupingKey;
    }

    /**
     * @return array
     */
    public function getAttachFileIdList(): array
    {
        return $this->attachFileIdList;
    }

    /**
     * @param array $attachFileIdList
     */
    public function setAttachFileIdList(array $attachFileIdList): void
    {
        $this->attachFileIdList = $attachFileIdList;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getStatsId()
    {
        return $this->statsId;
    }

    /**
     * @param mixed $statsId
     */
    public function setStatsId($statsId): void
    {
        $this->statsId = $statsId;
    }

    /**
     * @return array
     */
    public function getTagExpression(): array
    {
        return $this->tagExpression;
    }

    /**
     * @param array $tagExpression
     */
    public function setTagExpression(array $tagExpression): void
    {
        $this->tagExpression = $tagExpression;
    }

    /**
     * @return mixed
     */
    public function getAdYn()
    {
        return $this->adYn;
    }

    /**
     * @param mixed $adYn
     */
    public function setAdYn($adYn): void
    {
        $this->adYn = $adYn;
    }

    /**
     * @return mixed
     */
    public function getAutoSendYn()
    {
        return $this->autoSendYn;
    }

    /**
     * @param mixed $autoSendYn
     */
    public function setAutoSendYn($autoSendYn): void
    {
        $this->autoSendYn = $autoSendYn;
    }

    public function objectToArray ()
    {
        $smsOptionArray = array();

        if(!empty($this->title)){
            $smsOptionArray["title"] = $this->title;
        }

        if(!empty($this->body)){
            $smsOptionArray["body"] = $this->body;
        }

        if(!empty($this->templateId)){
            $smsOptionArray["templateId"] = $this->templateId;
        }

        if(!empty($this->requestDate)){
            $smsOptionArray["requestDate"] = $this->requestDate;
        }

        if(!empty($this->senderGroupingKey)){
            $smsOptionArray["senderGroupingKey"] = $this->senderGroupingKey;
        }

        if(!empty($this->attachFileIdList)){
            $smsOptionArray["attachFileIdList"] = $this->attachFileIdList;
        }

        if(!empty($this->userId)){
            $smsOptionArray["userId"] = $this->userId;
        }

        if(!empty($this->statsId)){
            $smsOptionArray["statsId"] = $this->statsId;
        }

        if(!empty($this->tagExpression)){
            $smsOptionArray["tagExpression"] = $this->tagExpression;
        }

        if(!empty($this->adYn)){
            $smsOptionArray["adYn"] = $this->adYn;
        }

        if(!empty($this->autoSendYn)){
            $smsOptionArray["autoSendYn"] = $this->autoSendYn;
        }

        return $smsOptionArray;
    }
}

class ToastSMSRecipient
{
    private $recipientNo; /* Recipient number, required */
    private $countryCode; /* Country code [Default: 82 (Korea)] */
    private $internationalRecipientNo; /* Recipient number including country code */
    private $templateParameter; /* Template parameter (with the input of template ID), key-value */
    private $recipientGroupingKey; /* Recipient group key */

    public function __construct($recipientNo)
    {
        $this->recipientNo = $recipientNo;
    }

    /**
     * @return mixed
     */
    public function getRecipientNo()
    {
        return $this->recipientNo;
    }

    /**
     * @param mixed $recipientNo
     */
    public function setRecipientNo($recipientNo): void
    {
        $this->recipientNo = $recipientNo;
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param mixed $countryCode
     */
    public function setCountryCode($countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return mixed
     */
    public function getInternationalRecipientNo()
    {
        return $this->internationalRecipientNo;
    }

    /**
     * @param mixed $internationalRecipientNo
     */
    public function setInternationalRecipientNo($internationalRecipientNo): void
    {
        $this->internationalRecipientNo = $internationalRecipientNo;
    }

    /**
     * @return mixed
     */
    public function getTemplateParameter()
    {
        return $this->templateParameter;
    }

    /**
     * @param mixed $templateParameter
     */
    public function setTemplateParameter($templateParameter): void
    {
        $this->templateParameter = $templateParameter;
    }

    /**
     * @return mixed
     */
    public function getRecipientGroupingKey()
    {
        return $this->recipientGroupingKey;
    }

    /**
     * @param mixed $recipientGroupingKey
     */
    public function setRecipientGroupingKey($recipientGroupingKey): void
    {
        $this->recipientGroupingKey = $recipientGroupingKey;
    }

    public function objectToArray ()
    {
        $recipientArray = array();

        if (!empty($this->recipientNo)) {
            $recipientArray["recipientNo"] = $this->recipientNo;
        }

        if (!empty($this->countryCode)) {
            $recipientArray["countryCode"] = $this->countryCode;
        }

        if (!empty($this->internationalRecipientNo)) {
            $recipientArray["internationalRecipientNo"] = $this->internationalRecipientNo;
        }

        if (!empty($this->templateParameter)) {
            $recipientArray["templateParameter"] = $this->templateParameter;
        }

        if (!empty($this->recipientGroupingKey)) {
            $recipientArray["recipientGroupingKey"] = $this->recipientGroupingKey;
        }

        return $recipientArray;
    }
}