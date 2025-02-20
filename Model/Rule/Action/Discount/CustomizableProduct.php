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
use WCS\jplPromotions\Helper\JplRule as JplRuleHelper;
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
     * @var JplRuleCacheHelper
     */
    protected $jplRuleHelper;

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
     * @param JplRuleCacheHelper          $jplRuleCacheHelper Jpl rule cache helper
     * @param JplRuleHelper               $jplRuleHelper Jpl rule helper
     * @param JplRuleRepositoryInterface  $jplRuleRepository  Jpl rule repository
     * @param JplRuleModel                $jplRuleModel         Jpl rule Model
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        checkoutSession $checkoutSession,
        JplRuleCacheHelper $jplRuleCacheHelper,
        JplRuleHelper $jplRuleHelper,
        JplRuleRepositoryInterface $jplRuleRepository,
        JplRuleModel $jplRuleModel
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->jplRuleCacheHelper = $jplRuleCacheHelper;
        $this->jplRuleHelper = $jplRuleHelper;
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

        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //BEGIN VARIABLES
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        
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

        $addQty =0;
        

            $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

            $customOptions = $options['options'];
            if (!empty($customOptions)) {
                foreach ($customOptions as $option) {
                    $optionTitle = $option['label'];
                    $optionValue = $option['value'];

                    if($optionTitle == $optTitle){

                        if ($optionValue == $optValue){
                            $addQty=1;
                            $jplData->settotalQty($qty);
                        }
                    }
                }
            }
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //END VARIABLES
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

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
        }else{
        //@TODO TO CHECK WHY ONLY if (!$quote->getData($calculateId)) { ONLY APPLY FOR THE FIRST ITEM
        //Discount for items that not follow the cart rule

        }
        //@END Begin To Remove @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 
        
        //TOTAL QTY PER ROW IN CART
        if($jplData->gettotalQty() < 0){
            //$discountData->setAmount(3.50);
        }else{
            $totalInCart = 0;
            $regex='/\b'.$optValue.'\b/';
            //If($item->getProduct()->getSku() == $sku){
            if(preg_match($regex, $item->getProduct()->getSku() )){
                //if($totalInCart > $x){
                if($this->getTotalInCart($quote,$regex) > $x){
                    $discountData->setAmount($jplData->gettotalQty());
                }
            }
        }

        return $discountData;
    }

    /**
     * Function getTotalInCart()
     * 
     * @param Quote $quote
     * @param Regex $regex
     * @param Sku $sku
     * 
     * return int
     */
    public function getTotalInCart($quote,$regex){
        //@TODO To Move to Helper
        $totalInCart = 0;
        foreach($quote->getAllVisibleItems() as $_item) {
            //echo 'Sku: '.$_item->getSku().'<br/>';
            //echo 'Quantity: '.$_item->getQty().'<br/>';
            //if ($_item->getSku() == $sku ){
            if(preg_match($regex, $_item->getSku() )){
                $totalInCart = $totalInCart + $_item->getQty();
            }
        }

        return $totalInCart;
    }

}
