<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Model\ResourceModel\JplRule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use WCS\jplPromotions\Api\Data\JplRuleInterface;

/**
 * JplRule collection.
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray(JplRuleInterface::RULE_ID, JplRuleInterface::RULE_ID);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \WCS\jplPromotions\Model\JplRule::class,
            \WCS\jplPromotions\Model\ResourceModel\JpltRule::class
        );
    }
}
