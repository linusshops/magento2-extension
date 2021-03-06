<?php
/**
 * StoreFront Bazaarvoice Extension for Magento
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to commercial source code license
 * of StoreFront Consulting, Inc.
 *
 * @category  SFC
 * @package   Bazaarvoice_Ext
 * @author    Dennis Rogers <dennis@storefrontconsulting.com>
 * @copyright 2016 StoreFront Consulting, Inc
 * @license   http://www.storefrontconsulting.com/media/downloads/ExtensionLicense.pdf StoreFront Consulting Commercial License
 * @link      http://www.StoreFrontConsulting.com/bazaarvoice-extension/
 */

namespace Bazaarvoice\Connector\Block\ProductList;

class Widget extends Item
{
    protected $_type = \Bazaarvoice\Connector\Model\Source\ProductList::WIDGET;

	// @codingStandardsIgnoreStart
	public function beforeGetProductPriceHtml(
		/** @noinspection PhpUnusedParameterInspection */
		$subject, $product)
	{
		// @codingStandardsIgnoreEnd
		if ($this->isEnabled()) {
			$this->_product = $product;
		}
	}

	// @codingStandardsIgnoreStart
	public function afterGetProductPriceHtml(
		/** @noinspection PhpUnusedParameterInspection */
		$subject, $result)
	{
		// @codingStandardsIgnoreEnd
		if ($this->isEnabled()) {
			$productIdentifier = $this->helper->getProductId($this->_product);
			$this->_productIds[$productIdentifier] = array('url' => $this->_product->getProductUrl());
			$result = '<div id="BVRRInlineRating_' . $this->_type . '-' . $productIdentifier . '"></div>' . $result;
		}
		return $result;
	}

}