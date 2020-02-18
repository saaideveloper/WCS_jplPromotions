<?php

namespace WCS\jplPromotions\Observer;

/**
 * Class CustomerConditionObserver
 */
class CustomerConditionObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer.
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = (array) $additional->getConditions();

        $conditions = array_merge_recursive($conditions, [
            $this->getCustomerFirstOrderCondition(),
            $this->getCustomizable()
        ]);

        $additional->setConditions($conditions);
        return $this;
    }

    /**
     * Get condition for customer first order.
     * @return array
     */
    private function getCustomerFirstOrderCondition()
    {
        return [
            'label'=> __('Overall size of all line-items that match'),
            'value'=> \WCS\jplPromotions\Model\Rule\Condition\Customer::class
        ];
    }

    /**
     * @return array
     */
    private function getCustomizable()
    {
        return [
            'label'=> __('Customizable'),
            'value'=> \WCS\jplPromotions\Model\Rule\Condition\Customizable::class
        ];
    }
}
