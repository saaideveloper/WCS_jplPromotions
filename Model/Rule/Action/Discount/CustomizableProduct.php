<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */

namespace WCS\jplPromotions\Model\Action\Discount;

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

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $item->getQuote();

        $calculateId = 'calculate_jpl_rule_'.$rule->getRuleId();
        if (!$quote->getData($calculateId)) {
            // Set only for performance (not save in DB).
            $quote->setData($calculateId, true);

            /** @var JplRule $jplRule */
            $jplRule = $this->jplRuleRepository->getById($rule->getRuleId());

            // Set number offered product.
            $jplRule->setNumberOfferedProduct($jplRule->getMaximumNumberProduct());

            // Save active jpl rule in session.
            $jplRuleSessionData = $this->checkoutSession->getJplRules();
            $jplRuleSessionData[$rule->getRuleId()] = $rule->getRuleId();
            $this->checkoutSession->setGiftRules($jplRuleSessionData);

            $this->jplRuleCacheHelper->saveCachedJplRule(
                $rule->getRuleId(),
                $rule,
                $jplRule
            );
        }

        return $discountData;
    }
}