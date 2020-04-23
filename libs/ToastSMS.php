<?php


class ToastSMS
{
    private $api_key; /* Api app key */
    private $user_agent; /* User-agent */
    private $host = "https://api-sms.cloud.toast.com"; /* Toast api domain */
    private $path; /* url path */
    private $method; /* GET 0 / POST 1 */
    private $version; /* Api version */
    private $rejectionNumber; /* Rejection number(console > 080 rejection setting) */
    private $content; /* Data */
    private $result; /* Api Result */
    private $error; /* error Message */
    private $authMsg = "인증"; /* default = AUTH_REQUIRED_MSG_KR */
    private $language = "KR"; /* default = KR */

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

    public function __construct($api_key, $sendNo, $version= "2.3")
    {
        $this->api_key = $api_key;
        $this->content["sendNo"] = $sendNo;
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
     * @param string $text
     * @param array $phoneNumList
     * @param null | StdClass $options
     * @return $this
     */
    public function send($msgType, $type, $text, $phoneNumList, $options=null){
        if(!empty($options)){
            $this->setOptionsToContent($options);
        }

        $this->setRequiredContentByType($type, $phoneNumList, $text, $options);
        $this->setMethod($this->getPathByType($type, $msgType), 1);
        $this->curlProcess();

        return $this;
    }

    /**
     * send basic SMS
     * @param string $text
     * @param array $phoneNumList
     * @param null|StdClass $options
     * @return $this
     */
    public function sendSMS($text, $phoneNumList, $options=null){
        $this->send(self::SMS, self::SMS, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * send SMS for Authentication(emergency)
     * @param string $text
     * @param array $phoneNumList
     * @param string $authMsg
     * @param null | StdClass $options
     * @return $this
     */
    public function sendAuthSMS($text, $phoneNumList, $authMsg, $options=null){
        $this->authMsg = $authMsg;

        $this->send(self::SMS, self::AUTH, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * send SMS for Advertisement
     * @param string $text
     * @param array $phoneNumList
     * @param string $rejectionNumber
     * @param null | StdClass $options
     * @return $this
     */
    public function sendAdSMS($text, $phoneNumList, $rejectionNumber, $options=null){
        $this->rejectionNumber = $rejectionNumber;

        $this->send(self::SMS, self::AD, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * send Tagged SMS
     * @param string $text
     * @param array $phoneNumList
     * @param array $tagExpressionList
     * @param null | StdClass $options
     * @return $this
     */
    public function sendTagSMS($text, $phoneNumList, $tagExpressionList, $options=null){
        $this->content["tagExpression"] = $tagExpressionList;

        $this->send(self::SMS, self::TAG, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * send basic MMS
     * @param string $title
     * @param string $text
     * @param array $phoneNumList
     * @param null | string $options
     * @return $this
     */
    public function sendMMS($title, $text, $phoneNumList, $options=null){
        $this->content["title"] = $title;

        $this->send(self::MMS, self::MMS, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * send MMS for Advertisement
     * @param string $title
     * @param string $text
     * @param array $phoneNumList
     * @param string $rejectionNumber
     * @param null | StdClass $options
     * @return $this
     */
    public function sendAdMMS($title, $text, $phoneNumList, $rejectionNumber, $options=null){
        $this->content["title"] = $title;
        $this->rejectionNumber = $rejectionNumber;

        $this->send(self::MMS, self::AD, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * send Tagged LMS
     * @param string $title
     * @param string $text
     * @param array $phoneNumList
     * @param array $tagExpressionList
     * @param null | StdClass $options
     * @return $this
     */
    public function sendTagLMS($title, $text, $phoneNumList, $tagExpressionList, $options=null){
        $this->content["title"] = $title;
        $this->content["tagExpression"] = $tagExpressionList;

        $this->send(self::MMS, self::TAG, $text, $phoneNumList, $options);
        return $this;
    }

    /**
     * set recipientList(required content) by phoneNumber list
     * @param array $phoneNumList
     */
    public function setRecipientListByPhoneNum($phoneNumList){
        $recipientList = array();
        foreach ($phoneNumList as $phoneNum){
            $recipient = new StdClass();
            $recipient->recipientNo = $phoneNum;
            array_push($recipientList, $recipient);
        }
        $this->content["recipientList"] = $recipientList;
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
     * @param string $type
     * @param array $phoneNumList
     * @param string $text
     * @param null|StdClass $options
     */
    private function setRequiredContentByType($type, $phoneNumList, $text, $options=null){
        if(empty($options->templateId)){
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
        $this->content["body"] = $text;
        $this->setRecipientListByPhoneNum($phoneNumList);
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
     * @param StdClass $options
     */
    private function setOptionsToContent($options){
        foreach ($options as $key=>$value){
            $this->content[$key] = $value;
        }
    }
}