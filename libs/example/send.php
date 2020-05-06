<?php
/* Create instance of ToastSMS class */
$toastSMS = new ToastSMS("api-key", "sender phone number");

/* recipient list */
$recipientList = array();

/* A recipient info, available to set options */
$recipient = new ToastSMSRecipient("01012345678");
array_push($recipientList, $recipient);

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

/* Toast MMS Options, required text(message content), title, available to set other options */
$mmsOption = new ToastSMSOption("text", "title");

/* Send MMS */
$toastSMS->sendMMS($recipientList, $mmsOption);

/* Send Ad MMS, required rejection number */
$rejectionNumber = "08012345678"; // rejection number (console > 080 rejection setting)
$toastSMS->sendAdMMS($recipientList, $mmsOption, $rejectionNumber);

/* Send Tag SMS, tag expression array */
$tagExpressionList = array("tag1", "tag2");
$mmsOption->setTagExpression($tagExpressionList);
$toastSMS->sendTagLMS($recipientList, $mmsOption);


