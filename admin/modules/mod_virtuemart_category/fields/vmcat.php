<?php
/**
 * @package     Joomla.Platform
 * @subpackage  mod_virtuemart_category
 *
 * @copyright   Copyright (C) 2013 Studio 42 All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

if (!class_exists( 'VmConfig' )){
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
	VmConfig::loadConfig();
}
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php');

/**
 * Form Field class for the Joomla Platform.
 * a list of virtuemart categories.
 *
 * @package     Joomla.Platform
 * @subpackage  mod_virtuemart_category
 */
class JFormFieldvmcat extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'vmcat';
	protected $categories = array();
	protected $model = null;


	/**
	 * Method to get the field options for virtuemart category
	 * Use the extension attribute in a form to specify the.specific extension for
	 * which categories should be displayed.
	 * @return  array    The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$this->model = VmModel::getModel ('category');
		$this->model->_noLimit = TRUE;
		// set all categories
		$this->childrenCat();
		
		$this->categories = array_merge(parent::getOptions(), $this->categories);
		return $this->categories ;
	}
	/**
	 * Creates structured option fields for all vm2 categories
	 *
	 * @todo: Connect to vendor data
	 * @author Patrick Kohl 
	 * @param int 		$cid 		Internally used for recursion
	 * @param int 		$level 		Internally used for recursion
	 * @return 
	 */
	public function childrenCat ( $cid = 0, $level = 0) {

		$virtuemart_vendor_id = 1;

		$level++;
		
		$childs = $this->model->getCategories (true, $cid);
		if (!empty($childs)) {
			foreach ($childs as $category) {

				$childId = $category->category_child_id;

				if ($childId != $cid) {
					$text = str_repeat (' - ', ($level - 1)). $category->category_name;
					$this->categories[] = JHtml::_('select.option', $childId , $text );
				}

				if ($this->model->hasChildren ($childId)) {
					$this->childrenCat ( $childId, $level);
				}

			}
		}
	}
}
