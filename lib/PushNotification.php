<?php

class PushNotification()
{
	public function __construct()
	{
		$this->endpoint = "https://android.googleapis.com/gcm/send";
		$this->key = __GCM_API_KEY;
	}

	/**
	 * Process CURL request
	 *
	 * @param json   $json   Registration ID returned after user agreed to receive notification
	 *
	 * @return array
	 */
	public function sendCurl($json)
	{
		/* sample */
		$json = '{"registration_ids":["evW-PMpHxcw:APA91bFaQ4tzyxz5sN1ojJpd1TAAI5V5A3jTWWfOzWGfXzaxbebTY7mkhkgMpqB7YdGqI1yeD2-qJk_TVyDVBBWMYrGsXwVSnJIA7eNRD9uDHjB5YnigGqqOEEW5D1YQoutWD7K7oljx"]}';

		$header = array();
		$header[] = 'Content-type: application/json';
		$header[] = "Authorization: key={$this->key}";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->endpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		// Receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($ch);

		curl_close ($ch);

		$response = json_decode($server_output);

		return $response;
	}

	/**
	 * Sends a push notification to Google Cloud Messaging
	 *
	 * @return array
	 */
	public function notify()
	{	
		// verify the subscription first
		if ($this->hadSubscribed()) {
			// send the notification
		}
	}

	/**
	 * Checks if the current user had subscribed to push notification
	 *
	 * @return bool
	 */
	public function hadSubscribed()
	{
		if (get_option('sentinel_push_notification') !== false || !empty(get_option('sentinel_push_notification'))) {
			return true;
		}

		return false;
	}

	/**
	 * Saves the user's registration ID to database when user agreed to be 
	 * notified with push notification
	 *
	 * @param string $registrationID

	 */
	public function subscribe($registrationID)
	{
		//
	}

	public function unsubscribe()
	{
		//
	}
}