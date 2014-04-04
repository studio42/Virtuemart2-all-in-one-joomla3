<?php
defined('_JEXEC') or  die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
* Module Helper
*
* @package VirtueMart
* @copyright (C) 2010 - Patrick Kohl
* @ Email: cyber__fr|at|hotmail.com
*
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();
if (!class_exists( 'VmImage' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/image.php');
if(!class_exists('TableMedias')) require(JPATH_VM_ADMINISTRATOR.'/tables/medias.php');
if(!class_exists('TableManufacturer_medias')) require(JPATH_VM_ADMINISTRATOR.'/tables/manufacturer_medias.php');
if(!class_exists('TableManufacturers')) require(JPATH_VM_ADMINISTRATOR.'/tables/manufacturers.php');
if (!class_exists( 'VirtueMartModelManufacturer' )){
   JLoader::import( 'manufacturer', JPATH_ADMINISTRATOR .'/components/com_virtuemart/models' );
}
?>