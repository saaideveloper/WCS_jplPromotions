<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WCS\jplPromotions\Model\Rule\Action\Discount;

use Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface;
abstract class JplAbstractDiscount implements DiscountInterface
{
    /**
     * @var \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory
     */
    protected $discountFactory;

    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $validator;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->validator = $validator;
        $this->discountFactory = $discountDataFactory;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    abstract public function calculate($rule, $item, $qty);

    /**
     * @param float $qty
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return float
     */
    public function fixQuantity($qty, $rule)
    {
        return $qty;
    }
}
