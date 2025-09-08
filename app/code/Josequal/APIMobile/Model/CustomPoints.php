<?php
namespace Josequal\APIMobile\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Custom Points Model
 */
class CustomPoints extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'custom_points';

    /**
     * @var string
     */
    protected $_eventObject = 'custom_points';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param AbstractDb|null $resource
     * @param AbstractCollection|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        AbstractDb $resource = null,
        AbstractCollection $resourceCollection = null,
        array $data = []
    ) {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Josequal\APIMobile\Model\ResourceModel\CustomPoints');
    }

    /**
     * Add points to customer
     *
     * @param int $customerId
     * @param int $points
     * @param string $action
     * @param string $description
     * @param int|null $orderId
     * @param int|null $productId
     * @return bool
     */
    public function addPoints($customerId, $points, $action, $description = '', $orderId = null, $productId = null)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName('custom_points');

            $data = [
                'customer_id' => $customerId,
                'points' => $points,
                'action' => $action,
                'description' => $description,
                'order_id' => $orderId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $connection->insert($table, $data);

            // Update customer balance
            $this->updateCustomerBalance($customerId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get customer total points
     *
     * @param int $customerId
     * @return int
     */
    public function getCustomerPoints($customerId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName('custom_points_balance');

            $select = $connection->select()
                ->from($table, ['available_points'])
                ->where('customer_id = ?', $customerId);

            $result = $connection->fetchOne($select);
            return (int) $result;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get customer points history
     *
     * @param int $customerId
     * @param int $limit
     * @return array
     */
    public function getCustomerPointsHistory($customerId, $limit = 10)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName('custom_points');

            $select = $connection->select()
                ->from($table, ['points', 'action', 'description', 'created_at'])
                ->where('customer_id = ?', $customerId)
                ->where('points > 0')
                ->order('created_at DESC')
                ->limit($limit);

            $results = $connection->fetchAll($select);

            $history = [];
            foreach ($results as $row) {
                $history[] = [
                    'points' => '+' . $row['points'],
                    'action' => $this->getActionDescription($row['action']),
                    'description' => $row['description'] ?: 'Points earned',
                    'date' => date('d/m/Y • H:i', strtotime($row['created_at'])),
                    'icon' => $this->getActionIcon($row['action'])
                ];
            }

            return $history;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Use points in cart
     *
     * @param int $customerId
     * @param int $quoteId
     * @param int $points
     * @return bool
     */
    public function usePointsInCart($customerId, $quoteId, $points)
    {
        try {
            $availablePoints = $this->getCustomerPoints($customerId);

            if ($availablePoints < $points) {
                return false;
            }

            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName('custom_points_cart');

            $data = [
                'quote_id' => $quoteId,
                'customer_id' => $customerId,
                'points_used' => $points,
                'discount_amount' => $this->calculateDiscount($points),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $connection->insertOnDuplicate($table, $data, ['points_used', 'discount_amount', 'updated_at']);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove points from cart
     *
     * @param int $quoteId
     * @return bool
     */
    public function removePointsFromCart($quoteId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $table = $this->resourceConnection->getTableName('custom_points_cart');

            $connection->delete($table, ['quote_id = ?' => $quoteId]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update customer balance
     *
     * @param int $customerId
     * @return void
     */
    private function updateCustomerBalance($customerId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $pointsTable = $this->resourceConnection->getTableName('custom_points');
            $balanceTable = $this->resourceConnection->getTableName('custom_points_balance');

            // Calculate total points
            $select = $connection->select()
                ->from($pointsTable, ['SUM(points) as total_points'])
                ->where('customer_id = ?', $customerId);

            $totalPoints = (int) $connection->fetchOne($select);

            // Calculate used points
            $cartTable = $this->resourceConnection->getTableName('custom_points_cart');
            $select = $connection->select()
                ->from($cartTable, ['SUM(points_used) as used_points'])
                ->where('customer_id = ?', $customerId);

            $usedPoints = (int) $connection->fetchOne($select);

            $availablePoints = $totalPoints - $usedPoints;

            $data = [
                'customer_id' => $customerId,
                'total_points' => $totalPoints,
                'used_points' => $usedPoints,
                'available_points' => $availablePoints,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $connection->insertOnDuplicate($balanceTable, $data, ['total_points', 'used_points', 'available_points', 'updated_at']);
        } catch (\Exception $e) {
            // Log error if needed
        }
    }

    /**
     * Calculate discount amount from points
     *
     * @param int $points
     * @return float
     */
    private function calculateDiscount($points)
    {
        // 1 point = 0.01 currency unit (customize as needed)
        return $points * 0.01;
    }

    /**
     * Get action description
     *
     * @param string $action
     * @return string
     */
    private function getActionDescription($action)
    {
        switch ($action) {
            case 'order':
                return 'Online Order';
            case 'review':
                return 'Product Review';
            case 'signup':
                return 'Account Registration';
            case 'birthday':
                return 'Birthday Bonus';
            case 'referral':
                return 'Referral Bonus';
            default:
                return 'Points Earned';
        }
    }

    /**
     * Get action icon
     *
     * @param string $action
     * @return string
     */
    private function getActionIcon($action)
    {
        switch ($action) {
            case 'order':
                return 'shopping_cart';
            case 'review':
                return 'star';
            case 'signup':
                return 'person_add';
            case 'birthday':
                return 'cake';
            case 'referral':
                return 'share';
            default:
                return 'percent';
        }
    }
}
