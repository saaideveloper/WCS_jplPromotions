<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Wed Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Helper;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Model\Rule;

use WCS\jplPromotions\Api\Data\JplRuleInterface;
use WCS\jplPromotions\Api\JplRuleRepositoryInterface;

/**
 * Jpl rule helper *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class JplRule extends AbstractHelper
{
    /**
     * @var array
     */
    protected $jplRule = [];

    /**
     * @var JplRuleRepositoryInterface
     */
    protected $jplRuleRepository;

    /**
     * GiftRule constructor.
     *
     * @param Context                     $context            Context
     * @param JplRuleRepositoryInterface  $jplRuleRepository  jplRuleRepository
     * @param array                       $jplRule           Jpl rule
     */
    public function __construct(
        Context $context,
        JplRuleRepositoryInterface $jplRuleRepository,
        array $jplRule = []
    ) {
        $this->jplRuleRepository = $jplRuleRepository;
        $this->jplRule = $jplRule;

        parent::__construct($context);
    }

    /**
     * Is jpl sales rule
     *
     * @param Rule $rule Rule
     *
     * @return bool
     */
    public function isJplRule(Rule $rule)
    {
        $isJplRule = false;
        if (in_array($rule->getSimpleAction(), $this->jplRule)) {
            $isJplRule = true;
        }

        return $isJplRule;
    }

    /**
     * Check if is valid gift rule for quote
     *
     * @param Rule  $rule  Rule
     * @param Quote $quote Quote
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isValidJplRule(Rule $rule, Quote $quote)
    {
        $valid = true;

        /**
         * Check if quote has at least one quote item (no gift rule item) in quote
         */
        
        $hasProduct = false;
        foreach ($quote->getAllItems() as $item) {
            if (!$item->getOptionByCode('option_jpl_rule')) {
                $hasProduct = true;
                break;
            }
        }
        if (!$hasProduct) {
            $valid = false;
        }

        return $valid;
    }

    /** checkIfMatchCustomOptions function 
     * Set a The Qtotal qty of products With the same Title and Value in 
     * the Cart and store it in JplData
     * 
     * @param Item          $item           Magento Row item in Cart
     * @param OptionTitle   $optionTitle    WCS jplPromotions Customizable Option Title
     * @param OptionValue   $optionValue    WCS jplPromotions Customizable Option Value
     * 
     * @return boolean 
     */
    public function checkIfMatchCustomOptions($item,$optionTitle,$optionValue){

        $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

        $customOptions = $options['options'];
        
        if (!empty($customOptions)) {
            foreach ($customOptions as $option) {
                $optionTitle = $option['label'];
                $optionValue = $option['value'];

                if($optionTitle == $optTitle){
                            
                    if ($optionValue == $optValue){
                        //Setting Total Qty based on optTitle And optValue to the whole Cart;
                        //$jplData->settotalQty($qty);
                        return 1;
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }
            }
        }else{
            //IF CUSTOMOPTIONS IS EMPTY
            return 0;
        }

    }
}
