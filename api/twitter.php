<?php

function twitter_call($TWITTER_USER, $TWITTER_PASS, $url, $type='GET')
{
	//cURL Handle erzeugen
	$ch = curl_init();

	//Festlegen ob ein GET- oder POST-Request gesendet wird
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

	//URL festlegen
	curl_setopt($ch, CURLOPT_URL, $url);

	//Daten als String zurückgeben und nicht direkt an den Browser senden
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	//Login-Informationen setzen
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $TWITTER_USER.":".$TWITTER_PASS);

	//URL aufrufen und XML interpretieren
	$data = simplexml_load_string(curl_exec($ch));

	//Resourcen freigeben
	curl_close($ch);

	return $data;
}
?>