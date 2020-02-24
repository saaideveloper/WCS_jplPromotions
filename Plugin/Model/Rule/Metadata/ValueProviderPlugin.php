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

//use Smile\GiftSalesRule\Api\Data\GiftRuleInterface;
use WCS\jplPromotions\Api\Data\JplRuleInterface;
//use Smile\GiftSalesRule\Api\GiftRuleRepositoryInterface;

//!!!! IMPORTANT !!!!! THE FOLLOWING LOAD THE MODEL
//!!!! IMPORTANT !!!!! THE FOLLOWING LOAD THE MODEL
//!!!! IMPORTANT !!!!! THE FOLLOWING LOAD THE MODEL
//use Smile\GiftSalesRule\Model\GiftRuleFactory;

/**
 * Add gift sales rule
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class ValueProviderPlugin
{
    /**
     * Gift rule repository
     *
     * @var GiftRuleRepositoryInterface
     */
    //protected $giftRuleRepository;

    /**
     * Gift rule factory
     *
     * @var GiftRuleFactory
     */
    //protected $giftRuleFactory;

    /**
     * UpdateRuleDataObserver constructor.
     *
     * @param GiftRuleRepositoryInterface $giftRuleRepository Gift rule repository
     * @param GiftRuleFactory             $giftRuleFactory    Gift rule factory
     */
    public function __construct(
        //GiftRuleRepositoryInterface $giftRuleRepository
        //GiftRuleFactory $giftRuleFactory
    ) {
        //$this->giftRuleRepository = $giftRuleRepository;
       // $this->giftRuleFactory    = $giftRuleFactory;
    }
    
    
    /**
     * Add gift sales rule label with rule type actions
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
            //'value' =>'',
            'value' => JplRuleInterface::JPL_DISCOUNT_PRODUCT,
        ];

        return $result;
/*
        $result['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
            'label' => __('to offer product per price range'),
            'value' => GiftRuleInterface::OFFER_PRODUCT_PER_PRICE_RANGE,
        ];
*/

        $result['actions']['children']['maximum_number_product']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['jpl_rule'][JplRuleInterface::MAXIMUM_NUMBER_PRODUCT],
        ];
/*
        $result['actions']['children']['price_range']['arguments']['data']['config'] = [
            'value' => $extensionAttributes['gift_rule'][GiftRuleInterface::PRICE_RANGE],
        ];

        return $result;
*/
    }
}
