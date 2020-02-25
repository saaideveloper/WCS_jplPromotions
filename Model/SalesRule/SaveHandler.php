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
use WCS\jplPromotions\Model\JplRuleFactory;
use WCS\jplPromotions\Model\JplRuleRepository;

/**
 * Class SaveHandler
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class SaveHandler implements ExtensionInterface
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
     * @var JplRuleFactory
     */
    protected $jplRuleFactory;

    /**
     * ReadHandler constructor.
     *
     * @param MetadataPool       $metadataPool       Metadata pool
     * @param JplRuleRepository $jplRuleRepository Jpl rule repository
     * @param JplRuleFactory    $jplRuleFactory    Jpl rule factory
     */
    public function __construct(
        MetadataPool $metadataPool,
        JplRuleRepository $jplRuleRepository,
        JplRuleFactory $jplRuleFactory
    ) {
        $this->metadataPool = $metadataPool;
        $this->jplRuleRepository = $jplRuleRepository;
        $this->jplRuleFactory = $jplRuleFactory;
    }

    /**
     * Save jpl rule value from Sales Rule extension attributes
     *
     * @param object $entity    Entity
     * @param array  $arguments Arguments
     *
     * @return bool|object
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $metadata = $this->metadataPool->getMetadata(RuleInterface::class);
        $ruleId = $entity->getData($metadata->getLinkField());
        if ($ruleId) {
            $extensionAttributes = $entity->getExtensionAttributes();
            if (isset($extensionAttributes['jpl_rule'])) {
                try {
                    /** @var JplRuleInterface $jplRule */
                    $jplRule = $this->jplRuleRepository->getById($ruleId);
                } catch (NoSuchEntityException $exception) {
                    // Create jpl rule if not exist.
                    $jplRule = $this->jplRuleFactory->create();
                    $jplRule->setId($ruleId);
                }

                $jplRule->addData($extensionAttributes['jpl_rule']);

                $this->jplRuleRepository->save($jplRule);
            }
        }

        return $entity;
    }
}
