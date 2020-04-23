# toast-sms-notifier

```
$toastSMS = new toastSMS($api_key, $sendNo); // toast api key, calling number
$phoneNumList = array("01082250640"); // Recipient phone number array 
```
<br>

- Send Short SMS

```
  $toastApi->sendSMS("text", phoneNumList);
```

<br>

- Send Long MMS
```
  $toastSMS->sendMMS("title", "text", phoneNumList);
```
<br>

- Send SMS/MMS with options
```
  $options = new StdClass();
  $options->templateId = "Template ID";
  
  $toastSMS->sendSMS("", $phoneNumList, $options);
  $toastSMS->sendMMS("", "", $phoneNumList, $options);
```
