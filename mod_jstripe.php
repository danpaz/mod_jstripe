<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );
 
// retrieve the amount ($), default value is 1000
$amount = $params->get('amount', '1000'); 
$amount = $amount*100; //Convert price to cents
$itemname = $params->get('itemname', 'Name'); 
$itemdesc = $params->get('itemdesc', 'Description'); 
$currency = $params->get('currency', 'usd'); 
 
require( JModuleHelper::getLayoutPath( 'mod_jstripe' ) );
?>