<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace WCS\jplPromotions\Model\Rule\Action\Discount;

/**
 *  Jpl Discount Data
 *
 */
class JplData {
    /**
     * @var float
     */
    protected $totalQty;


    /**
     * Constructor
     */
    public function __construct()
    {
        //$this->settotalQty(0);
    }

    /**
     * Set total Qty
     *
     * @param float $totalQty
     * @return $this
     */
    public function settotalQty($totalQty)
    {
        $this->totalQty = $this->totalQty + $totalQty;
        return $this;
    }

    /**
     * Get total Qty
     *
     * @return float
     */
    public function gettotalQty()
    {
        return $this->totalQty;
    }

}
