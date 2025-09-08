<?php
namespace Josequal\APIMobile\Model\V1;

class Points extends \Josequal\APIMobile\Model\AbstractModel
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
    protected $pointsModel;
    protected $quoteRepository;
    protected $registry;
    protected $logger;
    protected $helper;
    protected $_rewardsQuote;
    protected $objectManager;

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

        $this->pointsModel = $this->objectManager->get('\Amasty\Rewards\Model\Rewards');
        $this->_rewardsQuote = $this->objectManager->get('\Amasty\Rewards\Model\Quote');
        $this->_registry = $registry;
        $this->quoteRepository = $this->objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');;
        $this->logger = $this->objectManager->get('\Psr\Log\LoggerInterface\Proxy');
        $this->helper = $this->objectManager->get('\Amasty\Rewards\Helper\Data');
        $this->_checkoutSession = $this->objectManager->get('\Magento\Checkout\Model\Session');
        $this->cart = $this->objectManager->get('\Josequal\APIMobile\Model\V1\Cart');

    }

    /**
     * Get customer points with detailed information
     *
     * @return array
     */
    function getCustomerPoints(){

        $info = $this->successStatus('Points Total');

        $customerId = $this->customerSession->getCustomerId();
        $points = (int) $this->pointsModel->getPoints($customerId);

        // Get customer information
        $customer = $this->objectManager->get('\Magento\Customer\Model\CustomerFactory')->create()->load($customerId);

        // Determine loyalty level
        $loyaltyLevel = $this->getLoyaltyLevel($points);
        $nextLevelPoints = $this->getNextLevelPoints($loyaltyLevel);
        $pointsToNextLevel = $nextLevelPoints - $points;

        // Get points history
        $pointsHistory = $this->getPointsHistory($customerId);

        $info['data'] = [
            'points' => $points,
            'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
            'loyalty_level' => $loyaltyLevel,
            'next_level_points' => $nextLevelPoints,
            'points_to_next_level' => $pointsToNextLevel,
            'progress_percentage' => $this->calculateProgressPercentage($points, $nextLevelPoints),
            'points_history' => $pointsHistory
        ];

        return $info;
    }

    /**
     * Get loyalty level based on points
     *
     * @param int $points
     * @return string
     */
    private function getLoyaltyLevel($points) {
        if ($points >= 3000) {
            return 'Platinum';
        } elseif ($points >= 1000) {
            return 'Gold';
        } else {
            return 'Silver';
        }
    }

    /**
     * Get next level points requirement
     *
     * @param string $currentLevel
     * @return int
     */
    private function getNextLevelPoints($currentLevel) {
        switch ($currentLevel) {
            case 'Silver':
                return 1000;
            case 'Gold':
                return 3000;
            case 'Platinum':
                return 5000; // أو أي قيمة تريدها للمستوى الأعلى
            default:
                return 1000;
        }
    }

    /**
     * Calculate progress percentage to next level
     *
     * @param int $currentPoints
     * @param int $nextLevelPoints
     * @return int
     */
    private function calculateProgressPercentage($currentPoints, $nextLevelPoints) {
        if ($nextLevelPoints <= 0) return 100;
        return min(100, round(($currentPoints / $nextLevelPoints) * 100));
    }

    /**
     * Get points history for customer
     *
     * @param int $customerId
     * @return array
     */
    private function getPointsHistory($customerId) {
        $db = $this->objectManager->get('Magento\Framework\App\ResourceConnection');

        $select = $db->getConnection()->select()
            ->from(
                'amasty_rewards_rewards',
                ['amount', 'created_at', 'action', 'comment']
            )
            ->where('customer_id = ?', $customerId)
            ->where('amount > 0')
            ->order('created_at DESC')
            ->limit(10);

        $results = $db->getConnection()->fetchAll($select);

        $history = [];
        foreach ($results as $row) {
            $history[] = [
                'points' => '+' . $row['amount'],
                'action' => $this->getActionDescription($row['action']),
                'description' => $row['comment'] ?: 'Online Order',
                'date' => date('d/m/Y • H:i', strtotime($row['created_at'])),
                'icon' => $this->getActionIcon($row['action'])
            ];
        }

        return $history;
    }

    /**
     * Get action description based on action type
     *
     * @param string $action
     * @return string
     */
    private function getActionDescription($action) {
        switch ($action) {
            case 'order':
                return 'Online Order';
            case 'review':
                return 'Product Review';
            case 'signup':
                return 'Account Registration';
            case 'birthday':
                return 'Birthday Bonus';
            default:
                return 'Points Earned';
        }
    }

    /**
     * Get action icon based on action type
     *
     * @param string $action
     * @return string
     */
    private function getActionIcon($action) {
        switch ($action) {
            case 'order':
                return 'shopping_cart';
            case 'review':
                return 'star';
            case 'signup':
                return 'person_add';
            case 'birthday':
                return 'cake';
            default:
                return 'percent';
        }
    }

    /**
     * Apply points to cart or remove them
     *
     * @param array $data
     * @return array
     */
    function applyPoints($data){
        if(!isset($data['remove']) && !isset($data['points'])){
			return $this->errorStatus(["Number of points is required"]);
		}

        $applyCode = isset($data['remove']) && $data['remove'] == 1 ? 0 : 1;
        $data['points'] = isset($data['points']) ? (int) $data['points'] : 0;

        $cartQuote = $this->_checkoutSession->getQuote();
        $usedPoints = $this->helper->roundPoints($data['points']);
        $pointsLeft = $this->pointsModel->getPoints($this->customerSession->getCustomerId());
        if($pointsLeft <= 0){
            return $this->errorStatus(["You don't have points to apply"]);
        }
        $msg = '';
        try {
            if ($applyCode) {

                $coupon = $cartQuote->getCouponCode();
                if($coupon != null){
                    return $this->errorStatus(["Please remove coupon so you can use points."]);
                }

                if ($usedPoints > $pointsLeft) {
                    return $this->errorStatus(["Too much point(s) used."]);
                }

                if ($usedPoints < 0) {
                    $usedPoints = $usedPoints * -1;
                    if ($usedPoints < 0) {
                        return $this->errorStatus(["Enter correct points number."]);
                    }
                }
                $itemsCount = $cartQuote->getItemsCount();
                if ($itemsCount) {
                    $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                    $cartQuote->setData('amrewards_point', $usedPoints);

                    $cartQuote->setDataChanges(true);
                    $cartQuote->collectTotals();
                    $this->_rewardsQuote->addReward(
                        $cartQuote->getId(),
                        $cartQuote->getData('amrewards_point')
                    );
                    $this->quoteRepository->save($cartQuote);
                    $msg = __('You used %1 point(s)', $data['points']);
                    $totals = $this->objectManager->get('\Magento\Quote\Model\Quote\Address\Total');
                    $this->pointsModel->calculateDiscount($cartQuote->getItems(), $totals, $data['points']);
                }else{
                    return $this->errorStatus(["Cart is empty"]);
                }
            } else {
                $itemsCount = $cartQuote->getItemsCount();
                if ($itemsCount) {
                    $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                    $cartQuote->setData('amrewards_point', 0);
                    $cartQuote->setDataChanges(true);
                    $cartQuote->collectTotals();
                }
                $this->quoteRepository->save($cartQuote);

                $this->_rewardsQuote->addReward(
                    $cartQuote->getId(),
                    0
                );
                $msg = 'You Canceled Reward';
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->errorStatus([$e->getMessage()]);
        } catch (\Exception $e) {
            return $this->errorStatus(['We cannot Reward.']);
            $this->logger->critical($e);
        }

        $info = $this->successStatus($msg);
        $info['data'] = $this->cart->getCartDetails();
        return $info;
    }

    /**
     * Send points reminder to customers with high points
     *
     * @return bool
     */
    public function sendPointsReminder(){
        $db = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $notificationController = $this->objectManager->get('Josequal\APIMobile\Controller\Adminhtml\Notification\Save');
        $customerFactory = $this->objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();

        $select = $db->getConnection()->select()
            ->from(
                'amasty_rewards_rewards',
                ['SUM(amount) as points','customer_id']
            )->where('customer_id!=0')->group('customer_id');
        $results = $db->getConnection()->fetchAll($select);

        $tokens = [];
        if ($results) {
            foreach($results as $customerPoints){
				$customer = $customerFactory->load($customerPoints['customer_id']);
				if($customer && $customerPoints['points'] >= 400){
					$tokens[] = $customer->getData('firebase');
				}
            }
        }

        if(!empty($tokens)){
            $title = "نقاطك";
            $message = "لديك العديد من النقاط التي تستطيع استعمالها، سارع باستعمالها للحصول على خصومات مميزة";
            $notificationController->sendNotification($title,$message,$tokens);
            echo "all notifications send for " . count($tokens) . ' customers';
            return false;
        }

        echo "There are no valid customers to send notification for them";
        return false;


    }





}
