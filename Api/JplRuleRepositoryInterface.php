<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use WCS\jplPromotions\Api\Data\JplRuleInterface;
use WCS\jplPromotions\Api\Data\JplRuleSearchResultsInterface;

/**
 * Jpl repository interface.
 *
 * @api
 * @author    Sergio Abad <saaideveloper@gmail.com>
 */
interface JplRuleRepositoryInterface
{
    /**
     * Get a giftrule by ID.
     *
     * @param int $entityId Entity id
     * @return \WCS\jplPromotions\Api\Data\JplRuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Get the giftrules matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria Search criteria
     * @return \WCS\jplPromotions\Api\Data\JplRuleSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * Save the JplRule.
     *
     * @param JplRuleInterface $jplRule Jpl rule
     * @return \WCS\jplPromotions\Api\Data\JplRuleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(JplRuleInterface $jplRule);

    /**
     * Delete a jplrule by ID.
     *
     * @param int $entityId Entity id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);
}
