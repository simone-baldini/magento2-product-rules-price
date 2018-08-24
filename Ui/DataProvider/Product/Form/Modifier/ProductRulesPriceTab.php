<?php
namespace SimoneBaldini\ProductRulesPrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\View\LayoutInterface;

class ProductRulesPriceTab extends AbstractModifier
{

    const PRODUCT_RULES_PRICE_TAB_INDEX = 'product_rules_price';
    const PRODUCT_RULES_PRICE_TAB_CONTENT = 'content';

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * Parent layout of the block
     *
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param LayoutInterface $layout
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        LayoutInterface $layout
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->layout = $layout;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $this->meta = array_merge_recursive(
            $meta,
            [
                self::PRODUCT_RULES_PRICE_TAB_INDEX => $this->getTabConfig(),
            ]
        );

        return $this->meta;
    }

    protected function getTabConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Rules Price'),
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'provider' => static::FORM_NAME . '.product_form_data_source',
                        'ns' => static::FORM_NAME,
                        'collapsible' => true,
                    ],
                ],
            ],
            'children' => [
                self::PRODUCT_RULES_PRICE_TAB_INDEX => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => null,
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'template' => 'ui/form/components/complex',
                                'content' => $this->getPricesBlock()->toHtml(),
                                'sortOrder' => 10,
                                'additionalClasses' => 'admin__data-grid-wrap-static'
                            ],
                        ],
                    ],
                    'children' => [],
                ],
            ],
        ];
    }

    protected function getPricesBlock()
    {
        return $this->layout->createBlock(
            \SimoneBaldini\ProductRulesPrice\Block\Adminhtml\Prices::class,
            'simonebaldini.product.productrulesprice'
        );
    }
}
