<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Plugin\Model\Rule\Condition\Product;

use Magento\Framework\Model\AbstractModel;
use Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory;
use Magento\SalesRule\Model\Rule\Condition\Product\Combine;
use WCS\jplPromotions\Helper\JplRule as JplRuleHelper;

/**
 * Class CombinePlugin
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class CombinePlugin
{
    /**
     * @var JplRuleHelper
     */
    protected $jplRuleHelper;

    /**
     * CombinePlugin constructor.
     *
     * @param JplRuleHelper $jplRuleHelper Jpl rule helper
     */
    public function __construct(
        JplRuleHelper $jplRuleHelper
    ) {
        $this->jplRuleHelper = $jplRuleHelper;
    }

    /**
     * Return true if rule is a gift sales rule
     *
     * @param Combine       $subject Subject
     * @param callable      $proceed Proceed
     * @param AbstractModel $model   Model
     *
     * @return bool
     */
    public function aroundValidate(
        Combine $subject,
        callable $proceed,
        AbstractModel $model
    ) {
        if ($this->jplRuleHelper->isJplRule($subject->getRule())) {
            if (!$this->jplRuleHelper->isValidJplRule($subject->getRule(), $model->getQuote())) {
                return false;
            }

            return true;
        }

        return $proceed($model);
    }
}
