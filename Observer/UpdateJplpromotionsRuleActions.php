<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as serializer;
use Magento\SalesRule\Api\Data\RuleInterface;
use WCS\jplPromotions\Helper\JplRule as JplRuleHelper;

/**
 * Class UpdateJplpromotionsRuleActions
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class UpdateJplpromotionsRuleActions implements ObserverInterface
{
    const QUOTE_ATTRIBUTE = 'quote_';

    /**
     * @var GiftRuleHelper
     */
    protected $jplRuleHelper;

    /**
     * @var serializer
     */
    protected $serializer;

    /**
     * UpdateGiftRuleActions constructor.
     *
     * @param JplRuleHelper $jplRuleHelper Jpl promotions rule helper
     * @param serializer     $serializer     Serializer
     */
    public function __construct(
        JplRuleHelper $jplRuleHelper,
        serializer $serializer
    ) {
        $this->jplRuleHelper = $jplRuleHelper;
        $this->serializer = $serializer;
    }

    /**
     * Remove quote condition if it's our rule  
     *
     * @param Observer $observer Observer
     */
    public function execute(Observer $observer)
    {
        /** @var RuleInterface $rule */
        $rule = $observer->getRule();
/*
        if ($this->jplRuleHelper->isJplRule($rule)) {
            if ($rule->getActions()) {
                $actions = $rule->getActions()->asArray();
                if (isset($actions['conditions'])) {
                    foreach ($actions['conditions'] as $index => $condition) {
                        if (strpos($condition['attribute'], self::QUOTE_ATTRIBUTE) !== false) {
                            // Remove quote condition for gift rule.
                            unset($actions['conditions'][$index]);
                        }
                    }

                    $rule->setActionsSerialized($this->serializer->serialize($actions));
                }
            }
        }
*/
    }
}
