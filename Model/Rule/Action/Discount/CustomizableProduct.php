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
use WCS\jplPromotions\Model\Rule\Action\Discount\JplData;

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

        $jplData = new JplData;
        
        $itemPrice = $this->validator->getItemPrice($item);
        $baseItemPrice = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

        
        
        //@TODO Refactor To Set it globally
        $jplRule = $this->jplRuleRepository->getById($rule->getRuleId());

        $optTitle = $jplRule->getWcsJplpromotionsCutomizableLabelTitle();
        $optValue = $jplRule->getWcsJplpromotionsCutomizableValue();
        $sku = $jplRule->getWcsJplpromotionsSku();

	    $x = $rule->getDiscountStep();
        $y = $rule->getDiscountAmount();
        
        if (!$x || $y > $x) {
            return $discountData;
        }
        $buyAndDiscountQty = $x + $y;

        $fullRuleQtyPeriod = floor($qty / $buyAndDiscountQty);
        $freeQty = $qty - $fullRuleQtyPeriod * $buyAndDiscountQty;

        $discountQty = $fullRuleQtyPeriod * $y;
        if ($freeQty > $x) {
            $discountQty += $freeQty - $x;
        }


        $quote = $item->getQuote();

        $calculateId = 'calculate_jpl_rule_'.$rule->getRuleId();
        if (!$quote->getData($calculateId)) {
            $quote->setData($calculateId, true);

            
            $jplRule->setMaximumNumberProduct($jplRule->getMaximumNumberProduct());

            $jplRuleSessionData = $this->checkoutSession->getJplRules();
            $jplRuleSessionData[$rule->getRuleId()] = $rule->getRuleId();
            $this->checkoutSession->setGiftRules($jplRuleSessionData);

            $this->jplRuleCacheHelper->saveCachedJplRule(
                $rule->getRuleId(),
                $rule,
                $jplRule
            );

            

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
                                 //$discountData->setAmount($discount);
                   //$discountData->setAmount($qty);
                   $jplData->settotalQty($qty);
                                 //$discountData->setAmount($discountQty * $itemPrice);
                                 //$discountData->setBaseAmount($discountQty * $baseItemPrice);
                                 //$discountData->setOriginalAmount($discountQty * $itemOriginalPrice);
                                 //$discountData->setBaseOriginalAmount($discountQty * $baseItemOriginalPrice);
                            }
                        }
                    }
                }else{
			//$discountData->setAmount(0.50);
                }
        }else{
		//Discount for items that not follow the cart rule
		//$discountData->setAmount(3);

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
                                 //$discountData->setAmount(10);
                 //$discountData->setAmount($qty);
                 $jplData->settotalQty($qty);

                            }
                        }
                    }
                }else{
                }

	    }
//If the individual row has more than 21
if($jplData->gettotalQty() > 20){
    $discountData->setAmount(3.50);
}else{
    $discountData->setAmount($jplData->gettotalQty());
}
        return $discountData;
    }

}
