<?php
use Twilio\Rest\Client;
function send_sms($receiverNumber,$message){
    try {
			$account_sid = Config::get('constants.TWILIO_SID');
			$auth_token = Config::get('constants.TWILIO_TOKEN');
			$twilio_number = Config::get('constants.TWILIO_FROM');
			$msg_service_sid = Config::get('constants.Twillio_messagingServiceSid');
			$client = new Client($account_sid, $auth_token);
			$res = $client->messages->create($receiverNumber, [
				'from' => $twilio_number, 
				"messagingServiceSid" => $msg_service_sid,
				'body' => $message]);
			return 'done';
  
		} catch (\Exception $e){
			if($e->getCode() == 21211)
			{      
				// $message = $e->getMessage();
				// $show_msg = $receiverNumber. ' is not a valid number';
				// $validator->getMessageBag()->add('phone', $show_msg);
				// return back()->withInput()
				// ->withErrors($validator);
			}
			return 'error';
		}    
}
function send_notification($body_text,$expo_token,$title='new_order') {
				$data_arr = array(
				"to" => $expo_token,
				"sound" => "default",
				"body" => $body_text,
				"remote" => true,
				"content" => array(
				  "autoDismiss" => true,
				  "badge" => 2,
				  "body" => "connent",
				  "sound" => "default",
				  "sticky" => false,
				  "subtitle" => null,
				  "title" =>$title
				)
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://exp.host/--/api/v2/push/send");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_arr));
			curl_setopt($ch, CURLOPT_POST, 1);

			$headers = array();
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			return $result = curl_exec($ch);
}