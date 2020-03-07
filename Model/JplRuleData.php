<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Model;

use Magento\Framework\DataObject;
use WCS\jplPromotions\Api\Data\JplRuleDataInterface;

/**
 * JplRuleData model.
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class JplRuleData extends DataObject implements JplRuleDataInterface
{
    /**
     * Get the maximum number product.
     *
     * @return int
     */
    public function getNumberOfferedProduct()
    {
        return $this->getData(self::NUMBER_OFFERED_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setNumberOfferedProduct($numberOfferedProduct)
    {
        return $this->setData(self::NUMBER_OFFERED_PRODUCT, $numberOfferedProduct);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($value)
    {
        return $this->setData(self::CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($value)
    {
        return $this->setData(self::RULE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel($value)
    {
        return $this->setData(self::LABEL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductItems()
    {
        return $this->getData(self::PRODUCT_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductItems($items)
    {
        return $this->setData(self::PRODUCT_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteItems()
    {
        return $this->getData(self::QUOTE_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteItems($items)
    {
        return $this->setData(self::QUOTE_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getRestNumber()
    {
        return $this->getData(self::REST_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setRestNumber($value)
    {
        return $this->setData(self::REST_NUMBER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromArray(array $values)
    {
        $this->setLabel($values['label']);
        $this->setNumberOfferedProduct($values['number_offered_product']);
        $this->setRestNumber($values['rest_number']);
        $this->setQuoteItems($values['quote_items']);
        $this->setProductItems($values['product_items']);
        $this->setCode($values['code']);
        $this->setRuleId($values['rule_id']);

        return $this;
    }
}
