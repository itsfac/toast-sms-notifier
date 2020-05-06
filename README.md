# toast-sms-notifier

🔗 Toast SMS api Document : https://docs.toast.com/ko/Notification/SMS/ko/api-guide/
<br><br>

<h4>1. Create instance of ToastSMS class </h4>

```
$toastSMS = new ToastSMS("api-key", "sender phone number");
```

<h4>2. Create array of recipient's information </h4>

```
$recipientList = array();

// recipient's information, available to set options
$recipient = new ToastSMSRecipient("01012345678");
array_push($recipientList, $recipient);
```
<h4>3. Send message </h4>

- SMS
```
/* Toast SMS Options, required text(message content), available to set other options */
$smsOption = new ToastSMSOption("text");

/* Send SMS */
$toastSMS->sendSMS($recipientList, $smsOption);

/* Send Auth SMS, required auth message */
$toastSMS->sendAuthSMS($recipientList, $smsOption, ToastSMS::AUTH);

/* Send Ad SMS, required rejection number */
$rejectionNumber = "08012345678"; // rejection number (console > 080 rejection setting)
$toastSMS->sendAdSMS($recipientList, $smsOption, $rejectionNumber);

/* Send Tag SMS, tag expression array */
$tagExpressionList = array("tag1", "tag2");
$smsOption->setTagExpression($tagExpressionList);
$toastSMS->sendTagSMS($recipientList, $smsOption);
```

- MMS
```
/* Toast MMS Options, required text(message content), title, available to set other options */
$mmsOption = new ToastSMSOption("text", "title");

/* Send MMS */
$toastSMS->sendMMS($recipientList, $mmsOption);

/* Send Ad MMS, required rejection number */
$rejectionNumber = "08012345678"; // rejection number (console > 080 rejection setting)
$toastSMS->sendAdMMS($recipientList, $mmsOption, $rejectionNumber);

/* Send Tag LMS, tag expression array */
$tagExpressionList = array("tag1", "tag2");
$mmsOption->setTagExpression($tagExpressionList);
$toastSMS->sendTagLMS($recipientList, $mmsOption);
```

<br><hr><br>
- Auth message list 
```
// auth
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_EN);

// 인증 (default)
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_KR);

// にんしょう
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_JP);

// 認証
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_CN);

// verif
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_VERIF);

// password
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_PASSWORD);

// 비밀번호
$toastSMS->setAuthMsg(ToastSMS::AUTH_REQUIRED_MSG_PASSWORD_KR);
```

- Language
  (default : KR)
  
  Available to set language, AD message set by language 
```
$toastSMS->setLanguage(ToastSMS::KR);
$toastSMS->setLanguage(ToastSMS::EN);
$toastSMS->setLanguage(ToastSMS::JP);
```
