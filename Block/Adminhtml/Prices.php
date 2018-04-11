<?php

namespace SimoneBaldini\ProductRulesPrice\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\CatalogRule\Model\ResourceModel\Rule as CatalogRule;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;

class Prices extends \Magento\Backend\Block\Template
{
    /**
     * @var PricingHelper
     */
    protected $_princingHelper;

    /**
     * @var StoreManager
     */
    protected $_storeManager;

    /**
     * @var CatalogRule
     */
    protected $_catalogRule;

    /**
     * @var CatalogRuleRepositoryInterface
     */
    protected $_catalogRuleRepository;

    /**
     * @var CustomerGroupCollection
     */
    protected $_customerGroup;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_template = 'prices.phtml';

     /**
      * @inheritDoc
      *
      * @param Context $context
      * @param PricingHelper $pricingHelper
      * @param StoreManager $storeManager
      * @param CatalogRule $catalogRule
      * @param CatalogRuleRepositoryInterface $catalogRuleRepository
      * @param CustomerGroupCollection $customerGroup
      * @param Registry $registry
      * @param array $data
      */
    public function __construct(
        Context $context,
        PricingHelper $pricingHelper,
        StoreManager $storeManager,
        CatalogRule $catalogRule,
        CatalogRuleRepositoryInterface $catalogRuleRepository,
        CustomerGroupCollection $customerGroup,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_pricingHelper = $pricingHelper;
        $this->_storeManager = $storeManager;
        $this->_catalogRule = $catalogRule;
        $this->_catalogRuleRepository = $catalogRuleRepository;
        $this->_customerGroup = $customerGroup;
        $this->_coreRegistry = $registry;
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        $product = $this->getData('product');

        return $product;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getPriceData()
    {
        $data = [];
        $today = new \DateTime();
        $productId = $this->getProduct()->getId();
        $storeId = $this->getRequest()->getParam('store', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $store = $this->_storeManager->getStore($storeId);
        $websiteId = $store->getWebsiteId();
        $customerGroups = $this->_customerGroup->toOptionArray();
        foreach ($customerGroups as $customerGroup) {
            $customerGroupId = $customerGroup['value'];
            $prices = $this->_catalogRule->getRulesFromProduct(
                $today,
                $websiteId,
                $customerGroupId,
                $productId
            );

            usort($prices, function ($a, $b) {
                return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
            });

            $catalogRules = [];
            $skipNexts = false;
            foreach ($prices as $price) {
                if (!$skipNexts) {
                    $skipNexts = (bool) $price['action_stop'];
                    $href = $this->getUrl('catalog_rule/*/edit', ['id' => $price['rule_id']]);
                    $catalogRule = $this->_catalogRuleRepository->get($price['rule_id']);
                    $catalogRules[] = "<a href=\"{$href}\">{$catalogRule->getName()}</a>";
                }
            }

            if (count($catalogRules)) {
                $price = $this->_catalogRule->getRulePrice(
                    $today,
                    $websiteId,
                    $customerGroupId,
                    $productId
                );
                $data[$customerGroupId] = [
                    'customer_group' => $customerGroup['label'],
                    'price' => $this->_pricingHelper->currency(number_format($price, 2), true, false),
                    'catalog_rule' => implode(', ', $catalogRules)
                ];
            }
        }

        return $data;
    }

}