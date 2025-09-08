# 🎯 نظام النقاط الكامل - Complete Points System

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [قاعدة البيانات](#قاعدة-البيانات)
3. [النماذج](#النماذج)
4. [APIs](#apis)
5. [الوظائف](#الوظائف)
6. [الاختبار](#الاختبار)
7. [التثبيت](#التثبيت)
8. [التخصيص](#التخصيص)

---

## 🎯 نظرة عامة

نظام نقاط متكامل يدعم:
- ✅ **النظام المخصص** - يعمل بدون Amasty Rewards
- ✅ **Amasty Rewards** - إذا كان متاحاً
- ✅ **انتقال تلقائي** بين النظامين
- ✅ **APIs متكاملة** لجميع الوظائف
- ✅ **نظام مستويات الولاء** (Silver, Gold, Platinum)
- ✅ **تاريخ النقاط والمعاملات**
- ✅ **تطبيق النقاط على السلة**
- ✅ **إرسال تذكيرات النقاط**

---

## 🗄️ قاعدة البيانات

### **الجداول المطلوبة:**

#### **1. `custom_points` - سجل النقاط والمعاملات**
```sql
CREATE TABLE `custom_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL,
  `description` text,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **2. `custom_points_balance` - إجمالي النقاط للعملاء**
```sql
CREATE TABLE `custom_points_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `total_points` int(11) NOT NULL DEFAULT 0,
  `used_points` int(11) NOT NULL DEFAULT 0,
  `available_points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### **3. `custom_points_cart` - استخدام النقاط في السلة**
```sql
CREATE TABLE `custom_points_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `points_used` int(11) NOT NULL DEFAULT 0,
  `discount_amount` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quote_id` (`quote_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **البيانات التجريبية:**
```sql
-- إدراج بيانات تجريبية
INSERT INTO `custom_points` (`customer_id`, `points`, `action`, `description`, `created_at`) VALUES
(1, 100, 'signup', 'Welcome bonus for new customer', NOW()),
(1, 50, 'review', 'Product review bonus', NOW()),
(1, 200, 'order', 'Order completion bonus', NOW()),
(2, 150, 'signup', 'Welcome bonus for new customer', NOW()),
(2, 75, 'order', 'Order completion bonus', NOW());

-- تحديث إجمالي النقاط للعملاء
INSERT INTO `custom_points_balance` (`customer_id`, `total_points`, `used_points`, `available_points`) VALUES
(1, 350, 0, 350),
(2, 225, 0, 225)
ON DUPLICATE KEY UPDATE
`total_points` = VALUES(`total_points`),
`available_points` = VALUES(`available_points`);
```

---

## 📁 النماذج

### **1. CustomPoints.php**
```php
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
    protected $_eventPrefix = 'custom_points';
    protected $_eventObject = 'custom_points';
    protected $resourceConnection;

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

    protected function _construct()
    {
        $this->_init('Josequal\APIMobile\Model\ResourceModel\CustomPoints');
    }

    /**
     * Add points to customer
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
            $this->updateCustomerBalance($customerId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get customer total points
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
     */
    private function updateCustomerBalance($customerId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $pointsTable = $this->resourceConnection->getTableName('custom_points');
            $balanceTable = $this->resourceConnection->getTableName('custom_points_balance');

            $select = $connection->select()
                ->from($pointsTable, ['SUM(points) as total_points'])
                ->where('customer_id = ?', $customerId);

            $totalPoints = (int) $connection->fetchOne($select);

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
     */
    private function calculateDiscount($points)
    {
        return $points * 0.01; // 1 point = 0.01 currency unit
    }

    /**
     * Get action description
     */
    private function getActionDescription($action)
    {
        switch ($action) {
            case 'order': return 'Online Order';
            case 'review': return 'Product Review';
            case 'signup': return 'Account Registration';
            case 'birthday': return 'Birthday Bonus';
            case 'referral': return 'Referral Bonus';
            default: return 'Points Earned';
        }
    }

    /**
     * Get action icon
     */
    private function getActionIcon($action)
    {
        switch ($action) {
            case 'order': return 'shopping_cart';
            case 'review': return 'star';
            case 'signup': return 'person_add';
            case 'birthday': return 'cake';
            case 'referral': return 'share';
            default: return 'percent';
        }
    }
}
```

### **2. ResourceModel/CustomPoints.php**
```php
<?php
namespace Josequal\APIMobile\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomPoints extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('custom_points', 'id');
    }
}
```

### **3. ResourceModel/CustomPoints/Collection.php**
```php
<?php
namespace Josequal\APIMobile\Model\ResourceModel\CustomPoints;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Josequal\APIMobile\Model\CustomPoints;
use Josequal\APIMobile\Model\ResourceModel\CustomPoints as CustomPointsResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(CustomPoints::class, CustomPointsResource::class);
    }
}
```

---

## 🚀 APIs

### **1. عرض النقاط - Get Points**

#### **Endpoint:**
```
GET /apimobile/cart/points
```

#### **المصادقة:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### **الاستجابة:**
```json
{
  "status": true,
  "message": "Points Total",
  "data": {
    "points": 350,
    "customer_name": "John Doe",
    "loyalty_level": "Silver",
    "next_level_points": 1000,
    "points_to_next_level": 650,
    "progress_percentage": 35,
    "points_history": [
      {
        "points": "+200",
        "action": "Online Order",
        "description": "Order completion bonus",
        "date": "26/10/2024 • 09:47",
        "icon": "shopping_cart"
      },
      {
        "points": "+50",
        "action": "Product Review",
        "description": "Product review bonus",
        "date": "25/10/2024 • 14:30",
        "icon": "star"
      },
      {
        "points": "+100",
        "action": "Account Registration",
        "description": "Welcome bonus for new customer",
        "date": "20/10/2024 • 10:00",
        "icon": "person_add"
      }
    ],
    "system_type": "custom"
  }
}
```

#### **اختبار:**
```bash
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

### **2. تطبيق النقاط - Apply Points**

#### **Endpoint:**
```
POST /apimobile/cart/apply-points
```

#### **المصادقة:**
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
```

#### **المعاملات:**
```json
{
  "points": 100,
  "remove": 0
}
```

#### **الاستجابة:**
```json
{
  "status": true,
  "message": "You used 100 point(s)",
  "data": {
    "items": [
      {
        "id": "1915",
        "product_id": "261",
        "name": "3-in-1 Wireless Charger for Apple Devices",
        "sku": "FS-1046",
        "qty": 1,
        "price": "$119.99",
        "row_total": "$119.99",
        "image": "https://avit.josequal.net/media/catalog/product/xyz.jpg",
        "options": {
          "color": "#FF0000",
          "size": "M"
        }
      }
    ],
    "cart_qty": 1,
    "has_coupon": false,
    "coupon": "",
    "totals": [
      {
        "label": "Subtotal",
        "value": "$119.99"
      },
      {
        "label": "Points Discount",
        "value": "-$1.00"
      },
      {
        "label": "Shipping",
        "value": "$0.00"
      },
      {
        "label": "Grand Total",
        "value": "$118.99"
      }
    ]
  }
}
```

#### **اختبار:**
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

---

### **3. إزالة النقاط - Remove Points**

#### **Endpoint:**
```
POST /apimobile/cart/apply-points
```

#### **المصادقة:**
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
```

#### **المعاملات:**
```json
{
  "remove": 1
}
```

#### **الاستجابة:**
```json
{
  "status": true,
  "message": "You Canceled Reward",
  "data": {
    "items": [
      {
        "id": "1915",
        "product_id": "261",
        "name": "3-in-1 Wireless Charger for Apple Devices",
        "sku": "FS-1046",
        "qty": 1,
        "price": "$119.99",
        "row_total": "$119.99",
        "image": "https://avit.josequal.net/media/catalog/product/xyz.jpg",
        "options": {
          "color": "#FF0000",
          "size": "M"
        }
      }
    ],
    "cart_qty": 1,
    "has_coupon": false,
    "coupon": "",
    "totals": [
      {
        "label": "Subtotal",
        "value": "$119.99"
      },
      {
        "label": "Shipping",
        "value": "$0.00"
      },
      {
        "label": "Grand Total",
        "value": "$119.99"
      }
    ]
  }
}
```

#### **اختبار:**
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "remove": 1
  }'
```

---

### **4. إرسال تذكير النقاط - Points Reminder**

#### **Endpoint:**
```
POST /apimobile/cart/points-reminder
```

#### **المصادقة:**
```
غير مطلوبة (للمدير)
```

#### **الاستجابة:**
```
all notifications send for 2 customers
```

#### **اختبار:**
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/points-reminder"
```

---

## 🎯 الوظائف

### **1. نظام مستويات الولاء**

#### **المستويات:**
- **Silver:** 0-999 نقطة
- **Gold:** 1000-2999 نقطة  
- **Platinum:** 3000+ نقطة

#### **حساب التقدم:**
```php
private function getLoyaltyLevel($points) {
    if ($points >= 3000) {
        return 'Platinum';
    } elseif ($points >= 1000) {
        return 'Gold';
    } else {
        return 'Silver';
    }
}

private function getNextLevelPoints($loyaltyLevel) {
    switch ($loyaltyLevel) {
        case 'Silver': return 1000;
        case 'Gold': return 3000;
        case 'Platinum': return 5000;
        default: return 1000;
    }
}

private function calculateProgressPercentage($points, $nextLevelPoints) {
    if ($nextLevelPoints <= 0) return 100;
    return min(100, round(($points / $nextLevelPoints) * 100));
}
```

### **2. أنواع العمليات**

#### **أنواع النقاط:**
- **`order`** - طلب عبر الإنترنت
- **`review`** - مراجعة المنتج
- **`signup`** - تسجيل الحساب
- **`birthday`** - مكافأة عيد الميلاد
- **`referral`** - مكافأة الإحالة

#### **الأيقونات:**
- **`shopping_cart`** - للطلبات
- **`star`** - للمراجعات
- **`person_add`** - للتسجيل
- **`cake`** - لعيد الميلاد
- **`share`** - للإحالات

### **3. إدارة النقاط**

#### **إضافة نقاط:**
```php
$customPoints = $objectManager->get('\Josequal\APIMobile\Model\CustomPoints');
$customPoints->addPoints(
    $customerId,    // ID العميل
    $points,        // عدد النقاط
    $action,        // نوع العملية
    $description,   // الوصف
    $orderId,       // ID الطلب (اختياري)
    $productId      // ID المنتج (اختياري)
);
```

#### **عرض النقاط:**
```php
$customPoints = $objectManager->get('\Josequal\APIMobile\Model\CustomPoints');
$points = $customPoints->getCustomerPoints($customerId);
```

#### **عرض التاريخ:**
```php
$customPoints = $objectManager->get('\Josequal\APIMobile\Model\CustomPoints');
$history = $customPoints->getCustomerPointsHistory($customerId, 10);
```

---

## 🧪 الاختبار

### **اختبار شامل:**

#### **1. اختبار عرض النقاط:**
```bash
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### **2. اختبار تطبيق النقاط:**
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

#### **3. اختبار إزالة النقاط:**
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "remove": 1
  }'
```

#### **4. اختبار تذكير النقاط:**
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/points-reminder"
```

### **اختبار قاعدة البيانات:**
```sql
-- التحقق من الجداول
SHOW TABLES LIKE 'custom_points%';

-- التحقق من البيانات
SELECT * FROM custom_points WHERE customer_id = 1;
SELECT * FROM custom_points_balance WHERE customer_id = 1;

-- إضافة نقاط تجريبية
INSERT INTO custom_points (customer_id, points, action, description) VALUES
(1, 100, 'signup', 'Welcome bonus for new customer');
```

---

## 🔧 التثبيت

### **الخطوة 1: تشغيل Setup**
```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### **الخطوة 2: التحقق من التثبيت**
```sql
-- التحقق من إنشاء الجداول
SHOW TABLES LIKE 'custom_points%';

-- التحقق من البيانات التجريبية
SELECT * FROM custom_points WHERE customer_id = 1;
SELECT * FROM custom_points_balance WHERE customer_id = 1;
```

### **الخطوة 3: اختبار النظام**
```bash
# اختبار عرض النقاط
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# اختبار تطبيق النقاط
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"points": 50, "remove": 0}'
```

---

## ⚙️ التخصيص

### **1. تغيير قيمة النقاط:**
```php
// في CustomPoints.php
private function calculateDiscount($points)
{
    // 1 point = 0.01 currency unit (يمكن تغييرها)
    return $points * 0.01;
}
```

### **2. إضافة أنواع عمليات جديدة:**
```php
// في CustomPoints.php
private function getActionDescription($action)
{
    switch ($action) {
        case 'order': return 'Online Order';
        case 'review': return 'Product Review';
        case 'signup': return 'Account Registration';
        case 'birthday': return 'Birthday Bonus';
        case 'referral': return 'Referral Bonus';
        case 'social_share': return 'Social Share Bonus'; // جديد
        case 'newsletter': return 'Newsletter Subscription'; // جديد
        default: return 'Points Earned';
    }
}
```

### **3. تغيير مستويات الولاء:**
```php
// في Points.php
private function getLoyaltyLevel($points) {
    if ($points >= 5000) {
        return 'Diamond'; // مستوى جديد
    } elseif ($points >= 3000) {
        return 'Platinum';
    } elseif ($points >= 1000) {
        return 'Gold';
    } else {
        return 'Silver';
    }
}
```

### **4. تخصيص الرسائل:**
```php
// في Points.php
private function getLoyaltyMessage($loyaltyLevel, $pointsToNextLevel) {
    switch ($loyaltyLevel) {
        case 'Silver':
            return "You need {$pointsToNextLevel} more points to reach Gold level!";
        case 'Gold':
            return "You need {$pointsToNextLevel} more points to reach Platinum level!";
        case 'Platinum':
            return "You are at the highest level! Keep earning points for exclusive benefits!";
        default:
            return "Keep earning points to unlock new levels!";
    }
}
```

---

## 📊 إحصائيات النظام

### **الميزات المتاحة:**
- ✅ **4 APIs** متكاملة
- ✅ **3 جداول** قاعدة بيانات
- ✅ **3 مستويات** ولاء
- ✅ **5 أنواع** عمليات
- ✅ **5 أيقونات** مختلفة
- ✅ **معالجة شاملة** للأخطاء
- ✅ **تكامل تلقائي** مع Amasty Rewards

### **الوظائف المدعومة:**
- ✅ عرض النقاط مع معلومات العميل
- ✅ تطبيق النقاط على السلة
- ✅ إزالة النقاط من السلة
- ✅ تاريخ النقاط والمعاملات
- ✅ نظام مستويات الولاء
- ✅ حساب التقدم للوصول للمستوى التالي
- ✅ إرسال تذكيرات النقاط
- ✅ تكامل مع Firebase للإشعارات

---

## 🎉 الخلاصة

تم إنشاء نظام نقاط متكامل يدعم:

### **✅ النظام المخصص:**
- يعمل بدون Amasty Rewards
- قاعدة بيانات محلية
- APIs متكاملة
- قابل للتخصيص بالكامل

### **✅ Amasty Rewards:**
- يدعم Amasty Rewards إذا كان متاحاً
- انتقال تلقائي بين النظامين
- يحافظ على جميع الوظائف

### **✅ الميزات المتقدمة:**
- نظام مستويات الولاء
- تاريخ النقاط والمعاملات
- تطبيق النقاط على السلة
- إرسال تذكيرات النقاط
- تكامل مع Firebase

**النظام جاهز للاستخدام!** 🚀

قم بتشغيل `setup:upgrade` واختبر النظام باستخدام APIs المرفقة.
