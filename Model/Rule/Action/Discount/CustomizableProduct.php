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

/**
 * Class CustomizableProduct
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
     * @var GiftRuleCacheHelper
     */
    //protected $giftRuleCacheHelper;

    /**
     * @var GiftRuleRepositoryInterface
     */
    //protected $giftRuleRepository;

    /**
     * OfferProduct constructor.
     *
     * @param Validator                   $validator           Validator
     * @param DataFactory                 $discountDataFactory Discount data factory
     * @param PriceCurrencyInterface      $priceCurrency       Price currency
     * @param checkoutSession             $checkoutSession     Checkout session
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        checkoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;

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
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $item->getQuote();

        $calculateId = 'calculate_gift_rule_'.$rule->getRuleId();
        if (!$quote->getData($calculateId)) {
            // Set only for performance (not save in DB).
            $quote->setData($calculateId, true);
/*
            $giftRule = $this->giftRuleRepository->getById($rule->getRuleId());

            $giftRule->setNumberOfferedProduct($giftRule->getMaximumNumberProduct());
*/
            $giftRuleSessionData = $this->checkoutSession->getGiftRules();
            $giftRuleSessionData[$rule->getRuleId()] = $rule->getRuleId();
            $this->checkoutSession->setGiftRules($giftRuleSessionData);

            $this->giftRuleCacheHelper->saveCachedGiftRule(
                $rule->getRuleId(),
                $rule,
                $giftRule
            );
        }

        return $discountData;
    }
}
