<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Helper;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\SalesRule\Model\Rule;
use WCS\jplPromotions\Api\Data\JplRuleInterface;
use WCS\jplPromotions\Api\JplRuleRepositoryInterface;

/**
 * Rule helper
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class Cache extends AbstractHelper
{
    /**
     * Cache
     */
    const CACHE_DATA_TAG   = "jpl_rule_cache";
    const CACHE_IDENTIFIER = "jpl_rule_product_";

    const DATA_NUMBER_OFFERED_PRODUCT = "number_offered_product";
    const DATA_PRODUCT_ITEMS          = "product_items";
    const DATA_LABEL                  = "label";

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var JplRuleRepositoryInterface
     */
    protected $jplRuleRepository;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Builder
     */
    protected $sqlBuilder;

    /**
     * JplSalesRuleCache constructor.
     *
     * @param Context                     $context                  Context
     * @param CacheInterface              $cache                    Cache
     * @param JplRuleRepositoryInterface $jplRuleRepository       Jpl rule repository
     * @param CollectionFactory           $productCollectionFactory Product collection factory
     * @param Builder                     $sqlBuilder               Sql builder
     */
    public function __construct(
        Context $context,
        CacheInterface $cache,
        JplRuleRepositoryInterface $jplRuleRepository,
        CollectionFactory $productCollectionFactory,
        Builder $sqlBuilder
    ) {
        $this->cache = $cache;
        $this->jplRuleRepository = $jplRuleRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sqlBuilder = $sqlBuilder;

        parent::__construct($context);
    }

    /**
     * Save cached jpl rule
     *
     * @param string                $identifier Identifier
     * @param Rule                  $rule       Rule
     * @param int|JplRuleInterface $jplRule   Jpl rule
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveCachedJplRule($identifier, $rule, $jplRule)
    {
        $jplRuleData = $this->cache->load(self::CACHE_IDENTIFIER . $identifier);
        if (!$jplRuleData) {
            if (is_int($jplRule)) {
                /**
                 * Rules load by collection => extension attributes not present in rule entity
                 */
                /** @var JplRuleInterface $jplRule */
                $jplRule = $this->jplRuleRepository->getById($jplRule);
            }

            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();

            $collection->addStoreFilter();

            $actions = $rule->getActions();
            $actions->collectValidatedAttributes($collection);
            $this->sqlBuilder->attachConditionToCollection($collection, $actions);

            $items = [];
            $productCacheTags = [];
            foreach ($collection->getItems() as $item) {
                $items[$item->getId()] = $item->getSku();
                $productCacheTags[] = Product::CACHE_TAG . '_' . $item->getEntityId();
            }
            $jplRuleData = [
                self::DATA_LABEL => $rule->getStoreLabel(),
                self::DATA_NUMBER_OFFERED_PRODUCT => $jplRule->getNumberOfferedProduct(),
                self::DATA_PRODUCT_ITEMS => $items,
            ];

            $this->cache->save(
                serialize($jplRuleData),
                self::CACHE_IDENTIFIER . $identifier,
                array_merge([self::CACHE_DATA_TAG], $productCacheTags),
                3600
            );
        }

        if (!is_array($jplRuleData)) {
            $jplRuleData = unserialize($jplRuleData);
        }

        return $jplRuleData;
    }

    /**
     * Get cached jpl rule
     *
     * @param int|string $jplRuleCode Jpl rule code
     *
     * @return array
     */
    public function getCachedJplRule($jplRuleCode)
    {
        return unserialize($this->cache->load(self::CACHE_IDENTIFIER . $jplRuleCode));
    }

    /**
     * Flush cached jpl rule
     */
    public function flushCachedJplRule()
    {
        $this->cache->clean(self::CACHE_DATA_TAG);
    }
}
