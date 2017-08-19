<?php

/**
 *  ___            __                  
 * | _ \__ _ _ _  / _|_  _   _ __  ___ 
 * |  _/ _` | ' \|  _| || |_| '  \/ -_)
 * |_| \__,_|_||_|_|  \_,_(_)_|_|_\___|
 *
 * @author		Robin Heidrich <robin@panfu.me>
 * @copyright	Goodbeans GmbH
 * @version		AmfPHP 2.2.2
 */

session_start();

date_default_timezone_set("Europe/Berlin");

error_reporting(0);

require_once dirname(__FILE__) . '/ClassLoader.php';

$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway();

$gateway->service();
$gateway->output();