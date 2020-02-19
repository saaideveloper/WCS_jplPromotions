<?php

namespace WCS\jplPromotions\Model\Rule\Condition;

use Exception;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;

use Magento\Catalog\Model\ProductFactory;

/**
 * Class Customer
 */
class Customizable extends \Magento\Rule\Model\Condition\AbstractCondition
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
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Constructor
     * @param CheckoutSession              $checkoutSession     Checkout session
     * @param Cart                         $cart                Cart
     * @param CacheInterface               $cache               Cache
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Config\Model\Config\Source\Yesno $sourceYesno
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Cart $cart,
        CacheInterface $cache,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        ProductFactory $productFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->cache = $cache;

        $this->sourceYesno = $sourceYesno;
        $this->productFactory = $productFactory;
    }

    /**
     * Load attribute options
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'customizable' => __('Custom JPL Discount For Some Foamex items not All')
        ]);
        return $this;
    }

    /**
     * Get input type
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * Get value element type
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get value select options
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_customizable_options')) {
            $this->setData(
                'value_select_customizable_options',
                $this->sourceYesno->toOptionArray()
            );
        }
        return $this->getData('value_select_customizable_options');
    }

    /**
     * Validate Customer First Order Rule Condition
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        //Get magento 2 model for the products to Discount
        $sku='Foamex';
        $optionTitle='Size';
        $optionValue='A1';

        $product = $this->productFactory->create();
        $product->load($product->getIdBySku($sku));

        if($this->isInCart($product)){
        //if($this->OptionIsInCart($product,$optionTitle,$optionValue)){
            $ApplyDiscount = 1;
        }else{
            $ApplyDiscount = 0;
        }

        $model->setData('customizable', $ApplyDiscount);
        //$model->setData('customizable', 0);
        return parent::validate($model);
    }

    public function isInCart($product)
    {
        $productId = $product->getId();
        $cartItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $itemsIds = array();
        foreach ($cartItems as $cartItem) {
            array_push($itemsIds, $cartItem->getProduct()->getId());
        }
//return in_array($productId, $itemsIds);
        if (in_array($productId, $itemsIds)){
//return 1;
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->getProduct();

                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                //BEGIN IF THE CART ITEM MATCH THE CUSTOM OPTION 
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

                $options = $cartItem->getProduct()->getTypeInstance(true)->getOrderOptions($cartItem->getProduct());
                $customOptions = $options['options'];
                if (!empty($customOptions)) {
                    foreach ($customOptions as $option) {
                        $optionTitle = $option['label'];
                //        $optionId = $option['option_id'];
                //        $optionType = $option['type'];
                        $optionValue = $option['value'];

                        if($optionTitle == 'Size'){
                            
                            if ($optionValue == 'A1'){
                                return 1;
                            }
                        }
                    }
                }else{
                }

                foreach ($cartItem->getOptions() as $o) {
                    if ($o->getTitle() == 'Size') { // or another title of option
                    }
                }
                
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                //END IF THE CART ITEM MATCH THE CUSTOM OPTION 
                //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

            }
        }
return 0;
        //return in_array($productId, $itemsIds);
    }

    public function OptionIsInCart($product,$optionTitle,$optionValue){
        foreach ($product->getOptions() as $o) {
            if ($o->getTitle() != $optionTitle) { // or another title of option
                continue;
            }else{
                foreach ($o->getValues() as $value) {
                    //print_r($value->getData());
                    if($value->getData() == $optionValue){
                        return 1;
                    }
                }
            }
        }

    }

}
