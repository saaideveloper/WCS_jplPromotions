<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright WEb Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Model;

use Exception;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use WCS\jplPromotions\Api\Data\JplRuleDataInterface;
use WCS\jplPromotions\Api\Data\JplRuleDataInterfaceFactory;
use WCS\jplPromotions\Api\JplRuleServiceInterface;
use WCS\jplPromotions\Helper\Cache as JplRuleCacheHelper;

/**
 * Class JplRuleService
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright WEb Cloud Solutions Ltd
 */
class JplRuleService implements JplRuleServiceInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var JplRuleCacheHelper
     */
    protected $jplRuleCacheHelper;

    /**
     * @var JplRuleDataInterfaceFactory
     */
    protected $jplRuleDataFactory;

    /**
     * JplRuleService constructor.
     *
     * @param CheckoutSession              $checkoutSession     Checkout session
     * @param Cart                         $cart                Cart
     * @param CacheInterface               $cache               Cache
     * @param JplRuleCacheHelper          $jplRuleCacheHelper Jpl rule cache helper
     * @param JplRuleDataInterfaceFactory $jplRuleDataFactory Jpl rule data factory
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Cart $cart,
        CacheInterface $cache,
        JplRuleCacheHelper $jplRuleCacheHelper,
        JplRuleDataInterfaceFactory $jplRuleDataFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->cache = $cache;
        $this->jplRuleCacheHelper = $jplRuleCacheHelper;
        $this->jplRuleDataFactory = $jplRuleDataFactory;
    }

    /**
     * Get available jpls
     *
     * @param Quote $quote Quote
     *
     * @return JplRuleDataInterface[]
     */
    public function getAvailableJpls(Quote $quote)
    {
        /** @var array $jpls */
        $jpls = [];

        /** @var array $quoteItems */
        $quoteItems = [];

        /** @var array $jplRules */
        $jplRules = $this->checkoutSession->getJplRules();

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            /** @var Option $option */
            $option = $item->getOptionByCode('option_jpl_rule');

            if ($option) {
                $quoteItems[$option->getValue()][$item->getProductId()] = $item->getQty();
            }
        }

        if (is_array($jplRules)) {
            foreach ($jplRules as $jplRuleId => $jplRuleCode) {
                $jpls[$jplRuleId] = $this->jplRuleCacheHelper->getCachedJplRule($jplRuleCode);
                $jpls[$jplRuleId][JplRuleDataInterface::RULE_ID] = $jplRuleId;
                $jpls[$jplRuleId][JplRuleDataInterface::CODE] = $jplRuleCode;
                $jpls[$jplRuleId][JplRuleDataInterface::REST_NUMBER]
                    = $jpls[$jplRuleId][JplRuleDataInterface::NUMBER_OFFERED_PRODUCT];
                $jpls[$jplRuleId][JplRuleDataInterface::QUOTE_ITEMS] = [];
                if (isset($quoteItems[$jplRuleId])) {
                    $jpls[$jplRuleId][JplRuleDataInterface::QUOTE_ITEMS] = $quoteItems[$jplRuleId];
                    $jpls[$jplRuleId][JplRuleDataInterface::REST_NUMBER]
                        -= count($jpls[$jplRuleId][JplRuleDataInterface::QUOTE_ITEMS]);
                }
                /** @var JplRuleDataInterface $jplRuleData */
                $jplRuleData = $this->jplRuleDataFactory->create();
                $jpls[$jplRuleId] = $jplRuleData->populateFromArray($jpls[$jplRuleId]);
            }
        }

        return $jpls;
    }

    /**
     * Add jpl product
     *
     * @param Quote    $quote      Quote
     * @param array    $products   Products
     * @param string   $identifier Identifier
     * @param int|null $jplRuleId Jpl rule id
     *
     * @return mixed|void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function addJplProducts(Quote $quote, array $products, string $identifier, int $jplRuleId = null)
    {
        if ($jplRuleId == null) {
            $jplRuleId = $identifier;
        }

        $jplRuleData = $this->jplRuleCacheHelper->getCachedJplRule($identifier);

        foreach ($products as $product) {
            if (!(isset($product['id']) && isset($product['qty']))) {
                throw new Exception(__('We found an invalid request for adding jpl product.'));
            }

            if ($this->isAuthorizedJplProduct($product['id'], $jplRuleData, $product['qty'])) {
                $product['jpl_rule'] = $jplRuleId;
                $this->cart->addProduct($product['id'], $product);
            } else {
                throw new Exception(__('We can\'t add this jpl item to your shopping cart.'));
            }
        }
    }

    /**
     * Replace jpl product
     *
     * @param Quote    $quote      Quote
     * @param array    $products   Product
     * @param string   $identifier Identifier
     * @param int|null $jplRuleId Jpl rule id
     *
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function replaceJplProducts(Quote $quote, array $products, string $identifier, int $jplRuleId = null)
    {
        if ($jplRuleId == null) {
            $jplRuleId = $identifier;
        }

        $jplRuleData   = $this->jplRuleCacheHelper->getCachedJplRule($identifier);
        $quoteJplItems = $this->getQuoteJplItems($quote, $jplRuleId);

        foreach ($products as $product) {
            if (!(isset($product['id']) && isset($product['qty']))) {
                throw new Exception(__('We found an invalid request for adding jpl product.'));
            }
            if ($this->isAuthorizedJplProduct($product['id'], $jplRuleData, $product['qty'])) {
                $quoteItem = false;

                $productId = $product['id'];
                if (isset($product['super_attribute'])) {
                    $productId = $product['id'].json_encode($product['super_attribute']);
                }

                if (isset($quoteJplItems[$productId])) {
                    /** @var Item $quoteItem */
                    $quoteItem = $quoteJplItems[$productId];
                    unset($quoteJplItems[$productId]);
                }

                if ($quoteItem) {
                    $quoteItem->setQty($product['qty']);
                } else {
                    $product['jpl_rule'] = $jplRuleId;
                    $this->cart->addProduct($product['id'], $product);
                }
            } else {
                throw new Exception(__('We can\'t add this jpl item to your shopping cart.'));
            }
        }

        // Remove old jpl items.
        if (count($quoteJplItems) > 0) {
            /** @var Item $quoteJplItem */
            foreach ($quoteJplItems as $quoteJplItem) {
                $this->cart->removeItem($quoteJplItem->getId());
            }
        }
    }

    /**
     * Check if is authorized jpl product
     *
     * @param int   $productId    Product id
     * @param array $jplRuleData Jpl rule data
     * @param int   $qty          Qty
     *
     * @return bool
     */
    protected function isAuthorizedJplProduct($productId, $jplRuleData, $qty)
    {
        $isAuthorizedJplProduct = false;
        if (array_key_exists($productId, $jplRuleData[JplRuleCacheHelper::DATA_PRODUCT_ITEMS])
            && $qty <= $jplRuleData[JplRuleCacheHelper::DATA_NUMBER_OFFERED_PRODUCT]) {
            $isAuthorizedJplProduct = true;
        }

        return $isAuthorizedJplProduct;
    }

    /**
     * Get quote jpl item
     *
     * @param Quote $quote      Quote
     * @param int   $jplRuleId Jpl rule id
     *
     * @return array
     */
    protected function getQuoteJplItems(Quote $quote, int $jplRuleId)
    {
        $quoteItem = [];

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            /** @var Option $option */
            $option = $item->getOptionByCode('option_jpl_rule');
            if ($option && $option->getValue() == $jplRuleId) {
                $attributesOptionValue = '';
                /** @var Option $attributesOption */
                $attributesOption = $item->getOptionByCode('attributes');
                if ($attributesOption) {
                    $attributesOptionValue = $attributesOption->getValue();
                }
                $quoteItem[$item->getProductId()  . $attributesOptionValue] = $item;
            }
        }

        return $quoteItem;
    }
}
