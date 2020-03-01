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
use WCS\jplPromotions\Model\JplRule as JplRuleModel;

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
     * @var JplRuleModel
     */
    protected $jplRuleModel;

    /**
     * CustomizableProduct constructor.
     *
     * @param Validator                   $validator           Validator
     * @param DataFactory                 $discountDataFactory Discount data factory
     * @param PriceCurrencyInterface      $priceCurrency       Price currency
     * @param checkoutSession             $checkoutSession     Checkout session
     * @param JplRuleCacheHelper         $jplRuleCacheHelper Jpl rule cache helper
     * @param JplRuleRepositoryInterface $jplRuleRepository  Jpl rule repository
     * @param JplRuleModel                $jplRuleModel         Jpl rule Model
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        checkoutSession $checkoutSession,
        JplRuleCacheHelper $jplRuleCacheHelper,
        JplRuleRepositoryInterface $jplRuleRepository,
        JplRuleModel $jplRuleModel
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->jplRuleCacheHelper = $jplRuleCacheHelper;
        $this->jplRuleRepository = $jplRuleRepository;
        $this->jplRuleModel =$jplRuleModel;

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

	    $x = $rule->getDiscountStep();
	    $y = $rule->getDiscountAmount();

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

            //@TODO Refactor To Set it globally
            $optTitle = $jplRule->getWcsJplpromotionsCutomizableLabelTitle();
            $optValue = $jplRule->getWcsJplpromotionsCutomizableValue();
            $sku = $jplRule->getWcsJplpromotionsSku();

            $discount =  $jplRule->getMaximumNumberProduct() * $item->getPriceInclTax(); 

                //@TODO Refactor To Move to A helper function
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

                $customOptions = $options['options'];
                if (!empty($customOptions)) {
                    foreach ($customOptions as $option) {
                        $optionTitle = $option['label'];
                        $optionValue = $option['value'];

                        if($optionTitle == $optTitle){
                            
                            if ($optionValue == $optValue){
                                 //Total Discount to the whole Cart;
                                 $discountData->setAmount($discount);
                            }
                        }
                    }
                }else{
                }
        }


        return $discountData;
    }
}
