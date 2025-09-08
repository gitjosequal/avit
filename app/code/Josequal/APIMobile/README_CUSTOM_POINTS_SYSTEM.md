# 🎯 نظام النقاط المخصص - Custom Points System

## نظرة عامة

تم إنشاء نظام نقاط مخصص متكامل يعمل بدون الحاجة لـ Amasty Rewards. النظام يدعم جميع الوظائف الأساسية المطلوبة لإدارة النقاط في تطبيق الهاتف المحمول.

## ✨ الميزات المتاحة

### **🔹 الوظائف الأساسية:**
- ✅ عرض النقاط مع معلومات العميل
- ✅ تطبيق النقاط على السلة
- ✅ إزالة النقاط من السلة
- ✅ تاريخ النقاط والمعاملات
- ✅ نظام مستويات الولاء (Silver, Gold, Platinum)
- ✅ حساب التقدم للوصول للمستوى التالي
- ✅ إرسال تذكيرات النقاط
- ✅ تكامل مع Firebase للإشعارات

### **🔹 الميزات التقنية:**
- ✅ نظام قاعدة بيانات محلي
- ✅ APIs RESTful متكاملة
- ✅ معالجة شاملة للأخطاء
- ✅ مصادقة آمنة
- ✅ قابل للتخصيص بالكامل
- ✅ متوافق مع معايير Magento

## 🗄️ قاعدة البيانات

### **الجداول المنشأة:**

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
);
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
);
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
);
```

## 🚀 التثبيت والإعداد

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
```

## 📱 API Endpoints

### **1. عرض النقاط**
```
GET /apimobile/cart/points
```
**المصادقة:** مطلوبة (Bearer Token)

**الاستجابة:**
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
    "points_history": [...],
    "system_type": "custom"
  }
}
```

### **2. تطبيق النقاط**
```
POST /apimobile/cart/apply-points
```
**المصادقة:** مطلوبة (Bearer Token)

**المعاملات:**
```json
{
  "points": 100,
  "remove": 0
}
```

### **3. إرسال تذكير النقاط**
```
POST /apimobile/cart/points-reminder
```
**المصادقة:** غير مطلوبة (للمدير)

## 🎯 نظام مستويات الولاء

### **المستويات:**
- **Silver:** 0-999 نقطة
- **Gold:** 1000-2999 نقطة  
- **Platinum:** 3000+ نقطة

### **حساب التقدم:**
- النسبة المئوية للتقدم للوصول للمستوى التالي
- النقاط المطلوبة للوصول للمستوى التالي
- رسائل تحفيزية للوصول للمستوى التالي

## 🔧 التخصيص

### **تغيير قيمة النقاط:**
```php
// في CustomPoints.php
private function calculateDiscount($points)
{
    // 1 point = 0.01 currency unit (يمكن تغييرها)
    return $points * 0.01;
}
```

### **إضافة أنواع عمليات جديدة:**
```php
// في CustomPoints.php
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
        // إضافة أنواع جديدة هنا
        default:
            return 'Points Earned';
    }
}
```

### **تغيير مستويات الولاء:**
```php
// في Points.php
private function getLoyaltyLevel($points) {
    if ($points >= 3000) {
        return 'Platinum';
    } elseif ($points >= 1000) {
        return 'Gold';
    } else {
        return 'Silver';
    }
}
```

## 📊 إدارة النقاط

### **إضافة نقاط للعميل:**
```php
$customPoints = $objectManager->get('\Josequal\APIMobile\Model\CustomPoints');
$customPoints->addPoints(
    $customerId,
    $points,
    $action,
    $description,
    $orderId,
    $productId
);
```

### **عرض نقاط العميل:**
```php
$customPoints = $objectManager->get('\Josequal\APIMobile\Model\CustomPoints');
$points = $customPoints->getCustomerPoints($customerId);
```

### **عرض تاريخ النقاط:**
```php
$customPoints = $objectManager->get('\Josequal\APIMobile\Model\CustomPoints');
$history = $customPoints->getCustomerPointsHistory($customerId, 10);
```

## 🧪 الاختبار

### **اختبار شامل:**
```bash
# اختبار عرض النقاط
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# اختبار تطبيق النقاط
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"points": 100, "remove": 0}'

# اختبار إزالة النقاط
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"remove": 1}'
```

## 📁 الملفات المنشأة

### **النماذج:**
- `app/code/Josequal/APIMobile/Model/CustomPoints.php`
- `app/code/Josequal/APIMobile/Model/ResourceModel/CustomPoints.php`
- `app/code/Josequal/APIMobile/Model/ResourceModel/CustomPoints/Collection.php`

### **Setup:**
- `app/code/Josequal/APIMobile/Setup/InstallSchema.php`
- `app/code/Josequal/APIMobile/Setup/UpgradeSchema.php`
- `app/code/Josequal/APIMobile/Setup/UpgradeData.php`

### **قاعدة البيانات:**
- `app/code/Josequal/APIMobile/sql/custom_points_system.sql`

### **الوثائق:**
- `app/code/Josequal/APIMobile/CUSTOM_POINTS_SYSTEM_TEST.md`

## 🔄 التكامل مع النظام الحالي

### **التوافق:**
- ✅ يعمل مع النظام الحالي بدون تعارض
- ✅ يدعم Amasty Rewards إذا كان متاحاً
- ✅ ينتقل تلقائياً للنظام المخصص إذا لم يكن Amasty Rewards متاحاً
- ✅ يحافظ على جميع الوظائف الموجودة

### **الانتقال:**
```php
// النظام يتحقق تلقائياً من وجود Amasty Rewards
if ($this->pointsModel === null) {
    // استخدام النظام المخصص
    $points = $this->customPointsModel->getCustomerPoints($customerId);
} else {
    // استخدام Amasty Rewards
    $points = $this->pointsModel->getPoints($customerId);
}
```

## 🎉 الخلاصة

تم إنشاء نظام نقاط مخصص متكامل يعمل بدون الحاجة لـ Amasty Rewards. النظام يدعم جميع الوظائف الأساسية المطلوبة ويمكن تطويره لاحقاً لإضافة المزيد من الميزات.

### **المزايا:**
- ✅ مجاني ومفتوح المصدر
- ✅ قابل للتخصيص بالكامل
- ✅ متوافق مع Magento
- ✅ آمن ومحمي
- ✅ سهل التثبيت والصيانة

### **الاستخدام:**
1. قم بتشغيل `setup:upgrade`
2. اختبر النظام باستخدام APIs
3. قم بتخصيص النظام حسب الحاجة
4. استمتع بنظام نقاط متكامل!
