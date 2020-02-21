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
//use Smile\GiftSalesRule\Api\Data\GiftRuleInterface;
//use Smile\GiftSalesRule\Api\GiftRuleRepositoryInterface;

/**
 * Gift rule helper
 *
 * @author    Maxime Queneau <maxime.queneau@smile.fr>
 * @copyright 2019 Smile
 */
class GiftRule extends AbstractHelper
{
    /**
     * @var array
     */
    protected $jplRule = [];

    /**
     * @var GiftRuleRepositoryInterface
     */
    //protected $giftRuleRepository;

    /**
     * GiftRule constructor.
     *
     * @param Context                     $context            Context
     * @param array                       $jplRule           Jpl rule
     */
    public function __construct(
        Context $context,
        //GiftRuleRepositoryInterface $giftRuleRepository,
        array $jplRule = []
    ) {
        //$this->giftRuleRepository = $giftRuleRepository;
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
}
