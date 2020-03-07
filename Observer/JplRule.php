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

/**
 * Jpl rule helper
 *
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
     * JplRule constructor.
     *
     * @param Context                     $context            Context
     * @param array                       $jplRule           Jpl rule
     */
    public function __construct(
        Context $context,
        array $jplRule = []
    ) {
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
