<?php
/**
 * @category  WCS
 * @package   WCS\jplPromotions
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 */
namespace WCS\jplPromotions\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use WCS\jplPromotions\Api\Data\JplRuleInterface;

/**
 * JplRule model.
 *
 * @author    Sergio Abad <saaideveloper@gmail.com>
 * @copyright Web Cloud Solutions Ltd
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class JplRule extends AbstractModel implements JplRuleInterface, IdentityInterface
{
    const CACHE_TAG = 'wcs_jplpromotions_sales_rule_jpl_rule';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getMaximumNumberProduct()
    {
        return $this->getData(self::MAXIMUM_NUMBER_PRODUCT);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaximumNumberProduct($maximumNumberProduct)
    {
        return $this->setData(self::MAXIMUM_NUMBER_PRODUCT, $maximumNumberProduct);
    }

    /**
     * {@inheritdoc}
     */
    public function getWcsJplpromotionsSku()
    {
        return $this->getData(self::JPL_SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setWcsJplpromotionsSku($jplSku)
    {
        return $this->setData(self::JPL_SKU, $jplSku);
    }

    /**
     * {@inheritdoc}
     */
    public function getWcsJplpromotionsCutomizableLabelTitle()
    {
        return $this->getData(self::JPL_CUSTOMIZABLE_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setWcsJplpromotionsCutomizableLabelTitle($jplCustomizableLabelTitle)
    {
        return $this->setData(self::JPL_CUSTOMIZABLE_LABEL, $jplCustomizableLabelTitle);
    }

      /**
     * {@inheritdoc}
     */
    public function getWcsJplpromotionsCutomizableValue()
    {
        return $this->getData(self::JPL_CUSTOMIZABLE_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setWcsJplpromotionsCutomizableValue($jplCustomizableValue)
    {
        return $this->setData(self::JPL_CUSTOMIZABLE_VALUE, $jplCustomizableValue);
    }

    

    /**
     * {@inheritdoc}
     */
    public function getPriceRange()
    {
        return $this->getData(self::PRICE_RANGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRange($priceRange)
    {
        return $this->setData(self::PRICE_RANGE, $priceRange);
    }

    /**
     * Populate the object from array values
     * It is better to use setters instead of the generic setData method
     *
     * @param array $values values
     *
     * @return GiftRule
     */
    public function populateFromArray(array $values)
    {
        $this->setMaximumNumberProduct($values['maximum_number_product']);
        $this->setPriceRange($values['price_range']);
        $this->setWcsJplpromotionsSku($values['wcs_jplpromotions_sku']);
        $this->setWcsJplpromotionsCutomizableLabelTitle($values['wcs_jplpromotions_cutomizable_label_title']);
        $this->setWcsJplpromotionsCutomizableValue($values['wcs_jplpromotions_cutomizable_value']);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \WCS\jplPromotions\Model\ResourceModel\JplRule::class
        );
    }
}
