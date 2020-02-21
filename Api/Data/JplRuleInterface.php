<?php
/**
 * @category  WCS
 * @package   WCS\JplPromotions
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
    //const TABLE_NAME             = 'smile_gift_salesrule';
    //const RULE_ID                = 'rule_id';
    const MAXIMUM_NUMBER_PRODUCT = 'maximum_number_product';
    //const PRICE_RANGE            = 'price_range';

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
}
