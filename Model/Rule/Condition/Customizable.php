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
        $sku='FOA001';
        $optionTitle='Size';
        $optionValue='A4';

        $product = $this->productFactory->create();
        $product->load($product->getIdBySku($sku));

        if($this->isInCart($product,$optionTitle,$optionValue)){
        //if($this->OptionIsInCart($product,$optionTitle,'AA')){
            $ApplyDiscount = 1;
        }else{
            $ApplyDiscount = 0;
        }

        $model->setData('customizable', $ApplyDiscount);
        //$model->setData('customizable', 0);
        return parent::validate($model);
    }

    public function isInCart($product,$optTitle,$optValue)
    {
        $productId = $product->getId();
        $cartItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
        $itemsIds = array();
        foreach ($cartItems as $cartItem) {
            array_push($itemsIds, $cartItem->getProduct()->getId());
        }

        if (in_array($productId, $itemsIds)){

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->getProduct();

                if($this->CustomOptionInCart($cartItem,$optTitle,$optValue,'Foamex')){
                    return 1;
                }


            }
        }
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

    public function CustomOptionInCart($cartItem,$optTitle,$optValue,$sku){

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

    }

}
