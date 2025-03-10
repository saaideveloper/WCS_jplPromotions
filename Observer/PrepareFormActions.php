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
use Magento\Framework\Registry;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\RegistryConstants;
use WCS\jplPromotions\Helper\JplRule as JplRuleHelper;

/**
 * Class PrepareFormActions
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
class PrepareFormActions implements ObserverInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var JplRuleHelper
     */
    protected $JplRuleHelper;

    /**
     * PrepareFormActionsObserver constructor.
     *
     * @param Registry        $registry       Registry
     * @param JplRuleHelper $jplRuleHelper Jpl rule helper
     */
    public function __construct(
        Registry $registry,
        JplRuleHelper $jplRuleHelper
    ) {
        $this->registry       = $registry;
        $this->JplRuleHelper = $jplRuleHelper;
    }

    /**
     * Change fieldset legend
     *
     * @param Observer $observer Observer
     */
    public function execute(Observer $observer)
    {

        if ($this->JplRuleHelper->isJplRule($this->getCurrentSalesRule())) {
            $form = $observer->getData('form');
            $fieldset = $form->getElement('actions_fieldset');

            $fieldset->setData('legend', __('Select jpl products to Apply Discounts:'));
        }
    
    }

    /**
     * Get current sales rule
     *
     * @return RuleInterface
     */
    protected function getCurrentSalesRule()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_SALES_RULE);
    }
}
