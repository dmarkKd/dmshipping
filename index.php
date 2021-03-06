<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$_db = 'stores.json';

require_once( __DIR__ . '/inc/func.php' );

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

require __DIR__.'/conf.php';

if(!empty($_REQUEST)){

	if(

		isset($_REQUEST['hmac']) &&
		isset($_REQUEST['shop']) &&
		isset($_REQUEST['timestamp'])
		)

	{
		
		//define the SHop CONST

		$hmac = $_REQUEST['hmac'];
		$store = $_REQUEST['shop'];
		$storeURL = 'http://'.$_REQUEST['shop'];
		$timestamp = $_REQUEST['timestamp'];

	
		//Check if Store already exists in our DB
        if(file_exists($_db)) $shopifyStores = (array) json_decode(file_get_contents( $_db ));
        else $shopifyStores = array();
		
		if( !array_key_exists($store, $shopifyStores) ){

			$shopifyStores[$store]['hmac'] = $hmac;
			$shopifyStores[$store]['store'] = $store;
			$shopifyStores[$store]['storeURL'] = $storeURL;
			$shopifyStores[$store]['timestamp'] = $timestamp;

			//Write the DB updates
			write_file( $_db, json_encode($shopifyStores) );
			//print_r($shopifyStores);

			
			die();
		}

	}

}