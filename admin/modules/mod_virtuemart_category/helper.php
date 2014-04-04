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
$config= VmConfig::loadConfig();
if (!class_exists( 'VirtueMartModelVendor' )) require(JPATH_VM_ADMINISTRATOR.'/models/vendor.php');
//if (!class_exists( 'VmImage' )) require(JPATH_VM_ADMINISTRATOR.'/helpers/image.php');
//if (!class_exists( 'shopFunctionsF' )) require(JPATH_SITE.'/components/com_virtuemart/helpers/shopfunctionsf.php');
if(!class_exists('TableMedias')) require(JPATH_VM_ADMINISTRATOR.'/tables/medias.php');
if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.'/tables/categories.php');
if (!class_exists( 'VirtueMartModelCategory' )) require(JPATH_VM_ADMINISTRATOR.'/models/category.php');

?>