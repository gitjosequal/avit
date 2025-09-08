<?php
namespace Josequal\APIMobile\Model\V1;

class Checkout extends \Josequal\APIMobile\Model\AbstractModel
{

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    protected $customerSession;
    protected $_checkoutSession;
    protected $currencyHelper;
    protected $OnePageModel;
    protected $imageBuilder;
    protected $quoteManagement;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Model\Session $customerSession
    ) {

        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->eventManager = $eventManager;

        parent::__construct($context, $registry, $storeManager, $eventManager);

        $this->customerSession = $customerSession;

        //new
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->_checkoutSession = $this->objectManager->get('\Magento\Checkout\Model\Session');
        $this->currencyHelper = $this->objectManager->get('\Magento\Framework\Pricing\Helper\Data');
        $this->imageBuilder = $this->objectManager->get('\Magento\Catalog\Block\Product\ImageBuilder');
        $this->OnePageModel = $this->objectManager->get('\Magento\Checkout\Model\Type\Onepage');
        $this->quoteManagement = $this->objectManager->get('\Magento\Quote\Model\QuoteManagement');
        $this->urlInterface = $this->objectManager->get('\Magento\Framework\UrlInterface');
        $this->scopeConfig = $this->objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');

        date_default_timezone_set("Asia/Riyadh");

    }

    public function createOrder($data) {
        try {
            $quote = $this->_checkoutSession->getQuote();

            if (!$quote->hasItems()) {
                return $this->errorStatus(["Cart is empty"]);
            }

            // Set billing address
            if (isset($data['billing_address']) && is_array($data['billing_address'])) {
                $billingAddress = $data['billing_address'];
                // Ensure required fields are present
                $billingAddress['save_in_address_book'] = 1;

                // Handle region_id if region is provided as string
                if (isset($billingAddress['region']) && !isset($billingAddress['region_id'])) {
                    $billingAddress['region_id'] = $this->getRegionId($billingAddress['region'], $billingAddress['country_id']);
                }

                $quote->getBillingAddress()->addData($billingAddress);
            }

            // Set shipping address
            if (isset($data['shipping_address']) && is_array($data['shipping_address'])) {
                $shippingAddress = $data['shipping_address'];
                // Ensure required fields are present
                $shippingAddress['save_in_address_book'] = 1;

                // Handle region_id if region is provided as string
                if (isset($shippingAddress['region']) && !isset($shippingAddress['region_id'])) {
                    $shippingAddress['region_id'] = $this->getRegionId($shippingAddress['region'], $shippingAddress['country_id']);
                }

                $quote->getShippingAddress()->addData($shippingAddress);
            }

            // Get available shipping methods
            $availableMethods = $this->getAvailableShippingMethods();
            $availableMethodCodes = [];
            foreach ($availableMethods as $method) {
                if (isset($method['code'])) {
                    $availableMethodCodes[] = $method['code'];
                }
            }

            // Set shipping method
            if (isset($data['shipping_method'])) {
                if (in_array($data['shipping_method'], $availableMethodCodes)) {
                    $quote->getShippingAddress()->setShippingMethod($data['shipping_method']);
                } else {
                    $availableMethodsList = implode(', ', $availableMethodCodes);
                    return $this->errorStatus(["Shipping method '{$data['shipping_method']}' is not available. Available methods: {$availableMethodsList}"]);
                }
            } else {
                // Set default shipping method
                if (!empty($availableMethodCodes)) {
                    $defaultMethod = $availableMethodCodes[0];
                    $quote->getShippingAddress()->setShippingMethod($defaultMethod);
                } else {
                    return $this->errorStatus(["No shipping methods available"]);
                }
            }

            // Get available payment methods
            $availablePaymentMethods = $this->getAvailablePaymentMethods();
            $availablePaymentCodes = [];
            foreach ($availablePaymentMethods as $method) {
                if (isset($method['code'])) {
                    $availablePaymentCodes[] = $method['code'];
                }
            }

            // Set payment method
            if (isset($data['payment_method'])) {
                if (in_array($data['payment_method'], $availablePaymentCodes)) {
                    $quote->setPaymentMethod($data['payment_method']);
                    $quote->setInventoryProcessed(false);

                    // Import payment data to quote payment object (this is the correct way)
                    $quote->getPayment()->importData(['method' => $data['payment_method']]);
                } else {
                    $availablePaymentList = implode(', ', $availablePaymentCodes);
                    return $this->errorStatus(["Payment method '{$data['payment_method']}' is not available. Available methods: {$availablePaymentList}"]);
                }
            } else {
                return $this->errorStatus(["Payment method is required"]);
            }

            $quote->collectTotals();
            $quote->save();

            // Create order
            $order = $this->quoteManagement->submit($quote);

            if ($order) {
                $info = $this->successStatus('Order created successfully');
                $info['data'] = [
                    'order_id' => $order->getId(),
                    'increment_id' => $order->getIncrementId(),
                    'status' => $order->getStatus(),
                    'total' => $this->currencyHelper->currency($order->getGrandTotal(), true, false)
                ];
                return $info;
            } else {
                return $this->errorStatus(["Failed to create order"]);
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->errorStatus($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorStatus($e->getMessage());
        }
    }

    public function getPaymentMethods($data = []) {
        try {
            $quote = $this->_checkoutSession->getQuote();

            if (!$quote->hasItems()) {
                return $this->errorStatus(["Cart is empty"]);
            }

            $paymentMethods = $this->getAvailablePaymentMethods();

            $info = $this->successStatus('Payment methods retrieved successfully');
            $info['data'] = $paymentMethods;
            return $info;

        } catch (\Exception $e) {
            return $this->errorStatus($e->getMessage());
        }
    }

    public function getAvailableShippingMethods() {
        try {
            $quote = $this->_checkoutSession->getQuote();

            if (!$quote->hasItems()) {
                return [];
            }

            $shippingAddress = $quote->getShippingAddress();
            if (!$shippingAddress->getCountryId()) {
                // Set default country if not set
                $shippingAddress->setCountryId('US');
            }

            // Collect shipping rates
            $shippingAddress->setCollectShippingRates(true)->collectShippingRates();
            $shippingRates = $shippingAddress->getAllShippingRates();

            $shippingMethods = [];
            if ($shippingRates) {
                foreach ($shippingRates as $rate) {
                    if ($rate && $rate->getCode()) {
                        $shippingMethods[] = [
                            'code' => $rate->getCode(),
                            'title' => $rate->getMethodTitle() ?: $rate->getCarrierTitle(),
                            'description' => $rate->getMethodDescription() ?: $rate->getCarrierTitle(),
                            'price' => $rate->getPrice(),
                            'cost' => $rate->getCost()
                        ];
                    }
                }
            }

            // If no shipping methods found, add default ones
            if (empty($shippingMethods)) {
                $shippingMethods[] = [
                    'code' => 'freeshipping_freeshipping',
                    'title' => 'Free Shipping',
                    'description' => 'Free shipping for all orders',
                    'price' => 0,
                    'cost' => 0
                ];
            }

            return $shippingMethods;

        } catch (\Exception $e) {
            // Return default shipping methods if error occurs
            return [
                [
                    'code' => 'freeshipping_freeshipping',
                    'title' => 'Free Shipping',
                    'description' => 'Free shipping for all orders',
                    'price' => 0,
                    'cost' => 0
                ]
            ];
        }
    }

    public function getAvailablePaymentMethods() {
        try {
            $quote = $this->_checkoutSession->getQuote();

            if (!$quote->hasItems()) {
                return [];
            }

            $paymentMethods = [];

            // Get payment methods from quote using proper validation
            $quote->setTotalsCollectedFlag(false);
            $quote->collectTotals();

            // Use the proper method to get available payment methods for the quote
            $store = $quote->getStoreId();
            $paymentHelper = $this->objectManager->get('\Magento\Payment\Helper\Data');
            $availableMethods = $paymentHelper->getStoreMethods($store, $quote);

            foreach ($availableMethods as $method) {
                if ($method && $this->_canUsePaymentMethod($method, $quote)) {
                    try {
                        $paymentTitle = $this->scopeConfig->getValue('payment/' . $method->getCode() . '/title');
                        $paymentMethods[] = [
                            'code' => $method->getCode(),
                            'title' => $paymentTitle ?: $method->getTitle(),
                            'description' => $method->getDescription() ?: $method->getTitle()
                        ];
                    } catch (\Exception $e) {
                        // Skip this payment method if there's an error
                        continue;
                    }
                }
            }

            // If no payment methods found, add default ones
            if (empty($paymentMethods)) {
                $paymentMethods[] = [
                    'code' => 'checkmo',
                    'title' => 'Check / Money order',
                    'description' => 'Send check to store owner'
                ];
            }

            return $paymentMethods;

        } catch (\Exception $e) {
            // Return default payment methods if error occurs
            return [
                [
                    'code' => 'checkmo',
                    'title' => 'Check / Money order',
                    'description' => 'Send check to store owner'
                ]
            ];
        }
    }

    /**
     * Check if payment method can be used for the quote
     */
    protected function _canUsePaymentMethod($method, $quote)
    {
        if (!($method->isGateway() || $method->canUseCheckout())) {
            return false;
        }

        // Check if method can be used for the quote's currency
        if (!$method->canUseForCurrency($this->storeManager->getStore($quote->getStoreId())->getBaseCurrencyCode())) {
            return false;
        }

        /**
         * Checking for min/max order total for assigned payment method
         */
        $total = $quote->getBaseGrandTotal();
        $minTotal = $method->getConfigData('min_order_total');
        $maxTotal = $method->getConfigData('max_order_total');

        if ((!empty($minTotal) && ($total < $minTotal)) || (!empty($maxTotal) && ($total > $maxTotal))) {
            return false;
        }

        return true;
    }

    /**
     * Get region ID from region name and country ID
     */
    protected function getRegionId($regionName, $countryId)
    {
        try {
            $regionModel = $this->objectManager->get('\Magento\Directory\Model\Region');
            $region = $regionModel->loadByName($regionName, $countryId);
            return $region->getId();
        } catch (\Exception $e) {
            // If region not found, return null (Magento will handle this)
            return null;
        }
    }

    public function getShippingMethods($data = []) {
        try {
            $quote = $this->_checkoutSession->getQuote();

            if (!$quote->hasItems()) {
                return $this->errorStatus(["Cart is empty"]);
            }

            $shippingMethods = $this->getAvailableShippingMethods();

            $info = $this->successStatus('Shipping methods retrieved successfully');
            $info['data'] = $shippingMethods;
            return $info;

        } catch (\Exception $e) {
            return $this->errorStatus($e->getMessage());
        }
    }

    public function data() {
        $data = [];

        $customer = $this->customerSession->getCustomer();
        $quote = $this->_checkoutSession->getQuote();
        $quote->collectTotals();
        $quote->save();

        $coupon = $quote->getCouponCode();
        $data['has_cart'] = $quote->getItemsCount() > 0;
        $data['customer'] = [
            'name' => $customer->getName(),
            'phone' => $customer->getMobileNumber() ?? '',
        ];

        $data['coupon'] = $coupon ? $coupon : '';
        $data['has_coupon'] = $coupon != null;

        $data['total'] = (double) $quote->getGrandTotal();

        $list = [];
        $items = $quote->getAllVisibleItems();

        foreach ($items as $item) {
            $productData = $this->processProduct($item);
            $list[] = $productData;
        }

        $data['items'] = $list;

        return $data;
    }

    public function processProduct($item) {
        $product = $item->getProduct();

        $productData = [
            'id' => $item->getItemId(),
            'product_id' => $product->getId(),
            'name' => $item->getName(),
            'sku' => $item->getSku(),
            'qty' => $item->getQty(),
            'price' => $this->currencyHelper->currency($item->getPrice(), true, false),
            'row_total' => $this->currencyHelper->currency($item->getRowTotal(), true, false),
            'image' => $this->getImage($product, 'product_thumbnail_image'),
            'options' => $this->formatCartOptions($item->getOptions())
        ];

        return $productData;
    }

    public function getImage($product, $imageId, $attributes = []) {
        return $this->imageBuilder->setProduct($product)->setImageId($imageId)->setAttributes($attributes)->create();
    }

    private function formatCartOptions($options) {
        $formattedOptions = [];
        if ($options) {
            foreach ($options as $option) {
                $formattedOptions[] = [
                    'label' => $option['label'],
                    'value' => $option['value']
                ];
            }
        }
        return $formattedOptions;
    }
}
