<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);
	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';



	// $shopify = 'https://logicats-demo.myshopify.com/admin/themes/1458896922/assets.json';

	$url = file_get_contents("http://logicats-demo.myshopify.com/admin/themes/1458896922/assets.json");


	 $ch = curl_init();
	 $timeout = 5;
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 print_r($data);
	 return json_decode($data,true);
	
	// $shopify = get_data('https://logicats-demo.myshopify.com/admin/themes/1458896922/assets.json')
	// print_r($url);
	try
	{
		# Making an API request can throw an exception
		//$shop = $shopify('GET /admin/shop.json');
	
	}
	catch (shopify\ApiException $e)
	{
		# HTTP status code was >= 400 or response contained the key 'errors'
		echo $e;
		print_R($e->getRequest());
		print_R($e->getResponse());
	}
	catch (shopify\CurlException $e)
	{
		# cURL error
		echo $e;
		print_R($e->getRequest());
		print_R($e->getResponse());
	}
?>