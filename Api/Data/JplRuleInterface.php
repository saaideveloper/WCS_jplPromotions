<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Api\Data;

/**
 * JplRule interface.
 *
 * @api
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
interface JplRuleInterface
{
    const TABLE_NAME             = 'wcs_jplpromotions_salesrule';
    const RULE_ID                = 'rule_id';
    const MAXIMUM_NUMBER_PRODUCT = 'maximum_number_product';
    const PRICE_RANGE            = 'price_range';
    const JPL_SKU                = 'wcs_jplpromotions_sku';
    const JPL_CUSTOMIZABLE_LABEL = 'wcs_jplpromotions_cutomizable_label_title';
    const JPL_CUSTOMIZABLE_VALUE = 'wcs_jplpromotions_cutomizable_value';
    

    /**
     * Rule type actions
     */
    const JPL_DISCOUNT_PRODUCT                 = 'jpl_discount_product';

    /**
     * Get the maximum number product.
     *
     * @return int
     */
    public function getMaximumNumberProduct();

    /**
     * Set the maximum number product.
     *
     * @param int $value Value
     * @return $this
     */
    public function setMaximumNumberProduct($value);

    /**
     * Get the price range.
     *
     * @return float
     */
    public function getPriceRange();

    /**
     * Set the price range.
     *
     * @param decimal $value Value
     * @return $this
     */
    public function setPriceRange($value);

    /**
     * Get the product sku to apply the discount.
     *
     * @return text
     */
    public function getWcsJplpromotionsSku();

    /**
     * Set the product sku to apply the discount.
     *
     * @param text $value Value
     * @return $this
     */
    public function setWcsJplpromotionsSku($value);

    /**
     * Get the product sku to apply the discount.
     *
     * @return text
     */
    public function getWcsJplpromotionsCutomizableLabelTitle();
    /**
     * Set the product sku to apply the discount.
     *
     * @param text $value Value
     * @return $this
     */
    public function setWcsJplpromotionsCutomizableLabelTitle($value);

    /**
     * Get the product sku to apply the discount.
     *
     * @return text
     */
    public function getWcsJplpromotionsCutomizableValue();
    /**
     * Set the product sku to apply the discount.
     *
     * @param text $value Value
     * @return $this
     */
    public function setWcsJplpromotionsCutomizableValue($value);
}
