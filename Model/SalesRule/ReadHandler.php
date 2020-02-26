<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Model\SalesRule;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\Rule;
use WCS\jplPromotions\Api\Data\JplRuleInterface;
use WCS\jplPromotions\Model\JplRuleRepository;

/**
 * Class ReadHandler
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var JplRuleRepository
     */
    protected $jplRuleRepository;

    /**
     * ReadHandler constructor.
     *
     * @param MetadataPool       $metadataPool       Metadata pool
     * @param JplRuleRepository $jplRuleRepository Jpl rule repository
     */
    public function __construct(
        MetadataPool $metadataPool,
        JplRuleRepository $jplRuleRepository
    ) {
        $this->metadataPool = $metadataPool;
        $this->jplRuleRepository = $jplRuleRepository;
    }

    /**
     * Fill Sales Rule extension attributes with jpl rule attributes
     *
     * @param Rule|object $entity    Entity
     * @param array       $arguments Arguments
     *
     * @return Rule
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $attributes = $entity->getExtensionAttributes() ?: [];
        $metadata = $this->metadataPool->getMetadata(RuleInterface::class);
        if ($entity->getData($metadata->getLinkField())) {
            try {
                /** @var JplRuleInterface $jplRule */
                $jplRule = $this->jplRuleRepository->getById($entity->getData($metadata->getLinkField()));

                $maximumNumber = $jplRule->getMaximumNumberProduct();
                $attributes['jpl_rule'][JplRuleInterface::MAXIMUM_NUMBER_PRODUCT] = $maximumNumber;
                $attributes['jpl_rule'][JplRuleInterface::PRICE_RANGE] = $jplRule->getPriceRange();
                $attributes['jpl_rule'][JplRuleInterface::JPL_SKU] = $jplRule->getWcsJplpromotionsSku();               
                $attributes['jpl_rule'][JplRuleInterface::JPL_CUSTOMIZABLE_LABEL] = $jplRule->getWcsJplpromotionsCutomizableLabelTitle();
                $attributes['jpl_rule'][JplRuleInterface::JPL_CUSTOMIZABLE_VALUE] = $jplRule->getWcsJplpromotionsCutomizableValue();               
            } catch (NoSuchEntityException $exception) {
                $attributes['jpl_rule'][JplRuleInterface::MAXIMUM_NUMBER_PRODUCT] = null;
                $attributes['jpl_rule'][JplRuleInterface::PRICE_RANGE] = null;
                $attributes['jpl_rule'][JplRuleInterface::JPL_SKU] = null;
                $attributes['jpl_rule'][JplRuleInterface::JPL_CUSTOMIZABLE_LABEL] = null;
                $attributes['jpl_rule'][JplRuleInterface::JPL_CUSTOMIZABLE_VALUE] = null;
            }
        }
        $entity->setExtensionAttributes($attributes);

        return $entity;
    }
}
