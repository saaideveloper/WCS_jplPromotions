<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @autdhor    Sergio Abad <saaideveloper@gmail.com>
 */
namespace WCS\jplPromotions\Plugin\Model\Rule\Metadata;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Metadata\ValueProvider;

use WCS\jplPromotions\Api\Data\JplRuleInterface;
use WCS\jplPromotions\Api\JplRuleRepositoryInterface;
use WCS\jplPromotions\Model\JplRuleFactory;

/**
 * Add jpl sales rule
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class ValueProviderPlugin
{
    /**
     * Jpl rule repository
     *
     * @var JplRuleRepositoryInterface
     */
    protected $jplRuleRepository;

    /**
     * Jpl rule factory
     *
     * @var JplRuleFactory
     */
    protected $jplRuleFactory;

    /**
     * UpdateRuleDataObserver constructor.
     *
     * @param JplRuleRepositoryInterface $jplRuleRepository Jpl rule repository
     * @param JplRuleFactory             $jplRuleFactory    Jpl rule factory
     */
    public function __construct(
        JplRuleRepositoryInterface $jplRuleRepository,
        JplRuleFactory $jplRuleFactory
    ) {
        $this->jplRuleRepository = $jplRuleRepository;
        $this->jplRuleFactory    = $jplRuleFactory;
    }
    
    
    /**
     * Add jpl sales rule label with rule type actions
     *
     * @param ValueProvider $subject Subject
     * @param array         $result  Result
     * @param Rule          $rule    Rule
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMetadataValues(
        ValueProvider $subject,
        $result,
        Rule $rule
    ) {
        $extensionAttributes = $rule->getExtensionAttributes();

        $result['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
            'label' => __('jpl new discount per item line'),
            'value' => JplRuleInterface::JPL_DISCOUNT_PRODUCT,
        ];

        $result['actions']['children']['maximum_number_product']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['jpl_rule'][JplRuleInterface::MAXIMUM_NUMBER_PRODUCT],
        ];

        $result['actions']['children']['wcs_jplpromotions_sku']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['jpl_rule'][JplRuleInterface::JPL_SKU],
        ];

        $result['actions']['children']['wcs_jplpromotions_cutomizable_label_title']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['jpl_rule'][JplRuleInterface::JPL_CUSTOMIZABLE_LABEL],
        ];

        $result['actions']['children']['wcs_jplpromotions_cutomizable_value']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['jpl_rule'][JplRuleInterface::JPL_CUSTOMIZABLE_VALUE],
        ];

        $result['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
            'label' => __('to offer product per price range'),
            'value' => JplRuleInterface::OFFER_PRODUCT_PER_PRICE_RANGE,
        ];

        $result['actions']['children']['price_range']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['jpl_rule'][JplRuleInterface::PRICE_RANGE],
        ];

        return $result;

    }
}
