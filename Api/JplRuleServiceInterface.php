<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions 
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace WCS\jplPromotions\Api;

use Magento\Quote\Model\Quote;

/**
 * Class JplRuleService
 *
 * @api
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
interface JplRuleServiceInterface
{
    /**
     * Get available jpls
     *
     * @param Quote $quote Quote
     *
     * @return mixed[]
     *     {jpl_rule_id} => [
     *         maximum_number_product => {number}
     *         code => {jpl_rule_code}
     *         items => [
     *             {product_id} => [ {product_sku} ]
     *             ...
     *         ]
     *         quote_items => [
     *             {product_id} => {qty}
     *             ...
     *         ]
     *     ]
     */
    public function getAvailablejpls(Quote $quote);

    /**
     * Add jpl products
     *
     * @param Quote    $quote      Quote
     * @param array    $products   Products
     * @param string   $identifier Identifier
     * @param int|null $jplRuleId jpl rule id
     *
     * @return mixed
     */
    public function addJplProducts(Quote $quote, array $products, string $identifier, int $jplRuleId = null);

    /**
     * Replace jpl products
     *
     * @param Quote    $quote      Quote
     * @param array    $products   Products
     * @param string   $identifier Identifier
     * @param int|null $jplRuleId Jpl rule id
     *
     * @return mixed
     */
    public function replaceJplProducts(Quote $quote, array $products, string $identifier, int $jplRuleId = null);
}
