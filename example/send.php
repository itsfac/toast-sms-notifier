<?php
/* -------------- 1. Create instance of ToastSMS class -------------- */
$toastSMS = new ToastSMS("api-key", "sender phone number");

/* --------------  2. Create array of recipient's information -------------- */
/* case 1 : array(phone number array) */
$recipientList = array("01012345678", "01022222222");

/* case 2 : a phone number */
//$recipientList = "01012345678";

/* case 3 : Object Array(ToastSMSRecipient class) */
//$recipientList = array();
//$recipient = new ToastSMSRecipient("01012345678");
// $recipient->setCountryCode(82); // available to set options
//array_push($recipientList, $recipient);

/* -------------- 3. Send message -------------- */
/* Toast SMS Options, available to set ToastSMS options */
$smsOption = new ToastSMSOption();
//$smsOption->setTemplateId(1);

/* ------------- SMS ------------- */
/* Send SMS */
$toastSMS->sendSMS("text", $recipientList);
$toastSMS->sendSMS("text", $recipientList, $smsOption); // with options

/* Send Auth SMS, required auth message */
$toastSMS->sendAuthSMS("text", $recipientList, ToastSMS::AUTH);
$toastSMS->sendAuthSMS("text", $recipientList, ToastSMS::AUTH, $smsOption); // with options

/* Send Ad SMS, required rejection number */
$rejectionNumber = "08012345678"; // rejection number (console > 080 rejection setting)
$rejectionNumber = "08012345678"; // rejection number (console > 080 rejection setting)
$toastSMS->sendAdSMS("text", $recipientList, $rejectionNumber);
$toastSMS->sendAdSMS("text", $recipientList, $rejectionNumber, $smsOption); // with options

/* Send Tag SMS, tag expression array */
$tagExpressionList = array("tag1", "tag2");
$smsOption->setTagExpression($tagExpressionList);
$toastSMS->sendTagSMS("text", $recipientList, $smsOption);

/* ------------- MMS ------------- */
$mmsOption = new ToastSMSOption();

/* Send MMS */
$toastSMS->sendMMS("title", "text", $recipientList);
$toastSMS->sendMMS("title", "text", $recipientList, $mmsOption); // with options

/* Send Ad MMS, required rejection number */
$rejectionNumber = "08012345678"; // rejection number (console > 080 rejection setting)
$toastSMS->sendAdMMS("title", "text", $recipientList, $rejectionNumber);
$toastSMS->sendAdMMS("title", "text", $recipientList, $rejectionNumber, $mmsOption); // with options

/* Send Tag LMS, tag expression array */
$tagExpressionList = array("tag1", "tag2");
$mmsOption->setTagExpression($tagExpressionList);
$toastSMS->sendTagLMS("title", "text", $recipientList, $mmsOption);