<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */

namespace WCS\jplPromotions\Model\Rule\Action\Discount;

use Magento\Checkout\Model\Session as checkoutSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount;
use Magento\SalesRule\Model\Rule\Action\Discount\Data as DiscountData;
use Magento\SalesRule\Model\Rule\Action\Discount\DataFactory;
use Magento\SalesRule\Model\Validator;
use WCS\jplPromotions\Api\JplRuleRepositoryInterface;
use WCS\jplPromotions\Helper\Cache as JplRuleCacheHelper;
use WCS\jplPromotions\Model\JplRule;

/**
 * Class CustomizableProduct.php
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class CustomizableProduct extends AbstractDiscount
{
    /**
     * @var checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var JplRuleCacheHelper
     */
    protected $jplRuleCacheHelper;

    /**
     * @var JplRuleRepositoryInterface
     */
    protected $jplRuleRepository;

    /**
     * CustomizableProduct constructor.
     *
     * @param Validator                   $validator           Validator
     * @param DataFactory                 $discountDataFactory Discount data factory
     * @param PriceCurrencyInterface      $priceCurrency       Price currency
     * @param checkoutSession             $checkoutSession     Checkout session
     * @param JplRuleCacheHelper         $jplRuleCacheHelper Jpl rule cache helper
     * @param JplRuleRepositoryInterface $jplRuleRepository  Jpl rule repository
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        checkoutSession $checkoutSession,
        JplRuleCacheHelper $jplRuleCacheHelper,
        JplRuleRepositoryInterface $jplRuleRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->jplRuleCacheHelper = $jplRuleCacheHelper;
        $this->jplRuleRepository = $jplRuleRepository;

        parent::__construct(
            $validator,
            $discountDataFactory,
            $priceCurrency
        );
    }

    /**
     * @param Rule         $rule Rule
     * @param AbstractItem $item Item
     * @param float        $qty  Qty
     *
     * @return DiscountData
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $quote = $item->getQuote();

        $calculateId = 'calculate_jpl_rule_'.$rule->getRuleId();
        if (!$quote->getData($calculateId)) {
            $quote->setData($calculateId, true);

            $jplRule = $this->jplRuleRepository->getById($rule->getRuleId());

            $jplRule->setMaximumNumberProduct($jplRule->getMaximumNumberProduct());

            $jplRuleSessionData = $this->checkoutSession->getJplRules();
            $jplRuleSessionData[$rule->getRuleId()] = $rule->getRuleId();
            $this->checkoutSession->setGiftRules($jplRuleSessionData);

            $this->jplRuleCacheHelper->saveCachedJplRule(
                $rule->getRuleId(),
                $rule,
                $jplRule
            );

            //@TODO To Set it globally
            $optTitle = $jplRule->getWcsJplpromotionsCutomizableLabelTitle();
            $optValue = $jplRule->getWcsJplpromotionsCutomizableValue;
            $sku = $jplRule->getWcsJplpromotionsSku;

            //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
            //BEGIN CHECKING CUSTOMIZBLE OPTIONS
            //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

d
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                //BEGIN IF THE CART ITEM MATCH THE CUSTOM OPTION 
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $customOptions = $options['options'];
                if (!empty($customOptions)) {
                    foreach ($customOptions as $option) {
                        $optionTitle = $option['label'];
                //        $optionId = $option['option_id'];
                //        $optionType = $option['type'];
                        $optionValue = $option['value'];

                        if($optionTitle == $optTitle){
                            
                            if ($optionValue == $optValue){
                                return 1;
                            }
                        }
                    }
                }else{
                }
                
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                //END IF THE CART ITEM MATCH THE CUSTOM OPTION 
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@    


            //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
            //END CHECKING CUSTOMIZBLE OPTIONS
            //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

            //Total Discount to the whole Cart;
            $discountData->setAmount($item->getPriceInclTax());
        }


        return $discountData;
    }
}