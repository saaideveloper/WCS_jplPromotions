<?php
/**
 * @category  WCS
 * @package   WCS\JplSalesRule
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\JplPromotions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * JplRule search results data interface.
 *
 * @api
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
interface JplRuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get jplrule items.
     *
     * @return \WCS\jplPromotions\Api\Data\JplRuleInterface
     */
    public function getItems();

    /**
     * Set jplrule items.
     *
     * @param JplRuleInterface $items Jpl rule interface
     * @return $this
     */
    public function setItems(array $items);
}
