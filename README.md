# toast-sms-notifier

🔗 Toast SMS api Document : https://docs.toast.com/ko/Notification/SMS/ko/api-guide/
<br><br>


```
/* case1 */ 
$toastSMS = new toastSMS($api_key, $sendNo); // toast api key, calling number

/* case2 : you can set api version, default = 2.3 */ 
$version = "2.3";
$toastSMS = new toastSMS($api_key, $sendNo, $version);
```
```
/* recipient number is required as array */
$phoneNumList = array("010********"); 
```
<br>

- Send Short SMS
```
  $toastSMS->sendSMS($text, $phoneNumList, $options=null);
```
```
  $toastSMS->sendSMS("text", $phoneNumList);
  
  /* Send SMS for Authentication */
  // The text about authentication is required to send AUTH message(constant), declared in ToastSMS
  $toastSMS->sendAuthSMS("text", $phoneNumList, toastSMS::AUTH_REQUIRED_MSG_KR);
  
  /* Send SMS for Advertisement */
  $rejectionNumber = "080********"; // required, rejection number(console > 080 rejection setting)
  $toastSMS->sendAdSMS("text", $phoneNumList, $rejectionNumber);
  
  /* Send Tagged SMS */
  $tagExpression = array("tag1", "tag2"); // required, tag array
  $toastSMS->sendTagSMS("text", $phoneNumList, $tagExpression);
```

<br>

- Send Long MMS
```
  $toastSMS->sendSMS($title, $text, $phoneNumList, $options=null);
```
```
  $toastSMS->sendMMS("title", "text", $phoneNumList);
  
  /* Send MMS for Advertisement */
  $rejectionNumber = "080********"; // required, rejection number(console > 080 rejection setting)
  $toastSMS->sendAdMMS("title", "text", $phoneNumList, $rejectionNumber);
  
  /* Send Tagged MMS */
  $tagExpression = array("tag1", "tag2"); // required, tag array
  $toastSMS->sendTagLMS("title", "text", $phoneNumList, $tagExpression);
```
<br>

- Send SMS/MMS with options

```
  $options = new StdClass();
  $options->templateId = "Template ID";
  
  $toastSMS->sendSMS("", $phoneNumList, $options);
  $toastSMS->sendMMS("", "", $phoneNumList, $options);
  $toastSMS->sendAdSMS("", $phoneNumList, $rejectionNumber, $options);
                              .
                              .
                              .
```

<br>

- Send SMS/MMS by type<br>
  Type(constant) is declared in ToastSMS
```
  $toastSMS->send($msgType, $type, $text, $phoneNumList, $options=null);
```
```
 $toastSMS->send(ToastSMS::SMS, ToastSMS::SMS, "text", $phoneNumList);
 
 /* Send SMS for Authentication */
 $toastSMS->send(ToastSMS::SMS, ToastSMS::AUTH, "text", $phoneNumList);
 
 /* Send SMS for Advertisement */
 $toastSMS->send(ToastSMS::SMS, ToastSMS::AD, "text", $phoneNumList);
 
 /* When you send MMS, required title in options */
 $options = new StdClass();
 $options->title = "title";
 
 $toastSMS->send(ToastSMS::MMS, ToastSMS::MMS, "text", $phoneNumList, $options);
 
 /* Send MMS for Advertisement */
 $toastSMS->send(ToastSMS::MMS, ToastSMS::AD, "text", $phoneNumList, $options);
                               .
                               .
                               .
```
