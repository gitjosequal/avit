# 🛒 نظام السلة (Cart System) - APIMobile

## 📋 **نظرة عامة**

تم إعادة بناء نظام السلة في APIMobile بنفس طريقة APIExcample مع تحسينات إضافية وميزات متقدمة.

## 🚀 **الميزات الجديدة**

### **1. إدارة السلة الأساسية**
- ✅ إضافة منتج للسلة
- ✅ عرض معلومات السلة
- ✅ تحديث كمية العناصر
- ✅ حذف عناصر من السلة
- ✅ مسح السلة بالكامل

### **2. إدارة الكوبونات**
- ✅ تطبيق كوبون خصم
- ✅ إزالة كوبون الخصم
- ✅ عرض حالة الكوبون

### **3. إدارة الخيارات**
- ✅ دعم الخيارات المخصصة (لون، حجم، مادة)
- ✅ دمج العناصر المتشابهة تلقائياً
- ✅ عرض تفاصيل الخيارات

### **4. معلومات متقدمة**
- ✅ حالة المخزون
- ✅ توفر المنتج
- ✅ معلومات الضرائب والخصومات
- ✅ معرفات السلة والمتجر

## 🔧 **الوظائف المتاحة**

### **Cart Model (Model/V1/Cart.php)**

#### **1. addToCart($data)**
```php
// إضافة منتج للسلة
$data = [
    'product_id' => 123,
    'quantity' => 2,
    'color' => 'أحمر',
    'size' => 'L'
];
$result = $cartModel->addToCart($data);
```

#### **2. getCartInfo()**
```php
// عرض معلومات السلة
$cartInfo = $cartModel->getCartInfo();
```

#### **3. updateCartItem($data)**
```php
// تحديث كمية عنصر
$data = [
    'item_id' => 456,
    'qty' => 3
];
$result = $cartModel->updateCartItem($data);
```

#### **4. deleteCartItem($data)**
```php
// حذف عنصر من السلة
$data = ['item_id' => 456];
// أو
$data = ['product_id' => 123]; // حذف جميع عناصر المنتج
$result = $cartModel->deleteCartItem($data);
```

#### **5. clearCart()**
```php
// مسح السلة بالكامل
$result = $cartModel->clearCart();
```

#### **6. applyCoupon($data)**
```php
// تطبيق كوبون خصم
$data = ['coupon_code' => 'SAVE20'];
$result = $cartModel->applyCoupon($data);
```

#### **7. removeCoupon()**
```php
// إزالة كوبون الخصم
$result = $cartModel->removeCoupon();
```

## 📱 **Controllers المتاحة**

### **1. Cart/Add.php**
```
POST /cart/add
```
**المعاملات:**
- `product_id` (إجباري): معرف المنتج
- `quantity` (اختياري): الكمية (افتراضي: 1)
- `color` (اختياري): اللون
- `size` (اختياري): الحجم
- `custom_options` (اختياري): خيارات مخصصة

### **2. Cart/Info.php**
```
GET /cart/info
```
**الاستجابة:**
```json
{
  "status": true,
  "message": "Cart Details",
  "data": {
    "items": [...],
    "cart_qty": 2,
    "has_coupon": true,
    "coupon": "SAVE20",
    "totals": [...],
    "cart_id": "123",
    "store_id": 1
  }
}
```

### **3. Cart/Update.php**
```
POST /cart/update
```
**المعاملات:**
- `item_id` (إجباري): معرف العنصر في السلة
- `qty` (إجباري): الكمية الجديدة

### **4. Cart/Delete.php**
```
POST /cart/delete
```
**المعاملات:**
- `item_id` (اختياري): معرف العنصر
- `product_id` (اختياري): معرف المنتج

### **5. Cart/Clear.php**
```
POST /cart/clear
```
**مسح السلة بالكامل**

### **6. Cart/ApplyCoupon.php**
```
POST /cart/apply-coupon
```
**المعاملات:**
- `coupon_code` (إجباري): رمز الكوبون

### **7. Cart/RemoveCoupon.php**
```
POST /cart/remove-coupon
```
**إزالة الكوبون المطبق**

## 🎨 **إدارة الخيارات**

### **الخيارات المدعومة:**
- **لون (Color)**
- **حجم (Size)**
- **مادة (Material)**
- **نمط (Style)**
- **نمط (Pattern)**
- **علامة تجارية (Brand)**
- **موديل (Model)**
- **وزن (Weight)**
- **أبعاد (Dimensions)**

### **مثال: إضافة منتج مع خيارات**
```json
POST /cart/add
{
  "product_id": 123,
  "quantity": 1,
  "color": "أزرق",
  "size": "XL",
  "material": "قطن",
  "custom_options": {
    "pattern": "مخطط",
    "brand": "ماركة معروفة"
  }
}
```

## 🔄 **دمج العناصر المتشابهة**

### **كيف يعمل:**
1. **فحص المنتج** - نفس معرف المنتج
2. **مقارنة الخيارات** - نفس القيم تماماً
3. **دمج الكميات** - إضافة الكمية الجديدة للكمية الموجودة
4. **رسالة واضحة** - "Quantity updated for existing item"

### **مثال:**
- **المرة الأولى:** قميص أحمر، حجم M
- **المرة الثانية:** قميص أحمر، حجم M
- **النتيجة:** عنصر واحد مع كمية 2

## 📊 **معلومات المخزون**

### **البيانات المُرجعة:**
```json
{
  "is_available": true,
  "stock_status": {
    "is_in_stock": true,
    "qty": 50,
    "min_qty": 1,
    "max_qty": 100
  }
}
```

## 🎫 **إدارة الكوبونات**

### **تطبيق كوبون:**
```json
POST /cart/apply-coupon
{
  "coupon_code": "SUMMER2024"
}
```

### **الاستجابة:**
```json
{
  "status": true,
  "message": "Coupon applied successfully",
  "data": {
    "totals": [
      {
        "label": "Subtotal",
        "value": "$100.00"
      },
      {
        "label": "Discount",
        "value": "-$20.00"
      },
      {
        "label": "Grand Total",
        "value": "$80.00"
      }
    ]
  }
}
```

## 🚨 **معالجة الأخطاء**

### **أمثلة على الأخطاء:**
```json
{
  "status": false,
  "message": "Product ID is required",
  "data": []
}
```

```json
{
  "status": false,
  "message": "Product not found",
  "data": []
}
```

```json
{
  "status": false,
  "message": "Quantity must be greater than 0",
  "data": []
}
```

## 🔧 **الأحداث (Events)**

### **الأحداث المُرسلة:**
- `josequal_cart_item_added` - عند إضافة منتج
- `josequal_cart_item_updated` - عند تحديث كمية
- `josequal_cart_item_deleted` - عند حذف منتج
- `josequal_cart_cleared` - عند مسح السلة
- `josequal_coupon_applied` - عند تطبيق كوبون
- `josequal_coupon_removed` - عند إزالة كوبون

## 📝 **أمثلة الاستخدام**

### **1. إضافة منتج بسيط:**
```bash
curl -X POST http://your-store.com/cart/add \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 123, "quantity": 2}'
```

### **2. إضافة منتج مع خيارات:**
```bash
curl -X POST http://your-store.com/cart/add \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 123,
    "quantity": 1,
    "color": "أحمر",
    "size": "L"
  }'
```

### **3. عرض معلومات السلة:**
```bash
curl -X GET http://your-store.com/cart/info \
  -H "Authorization: Bearer {token}"
```

### **4. تطبيق كوبون:**
```bash
curl -X POST http://your-store.com/cart/apply-coupon \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"coupon_code": "SAVE20"}'
```

## 🎯 **التحسينات المستقبلية**

### **المقترحات:**
1. **إضافة دعم للخصومات** - خصومات تلقائية
2. **إدارة الشحن** - خيارات الشحن
3. **حفظ السلة** - حفظ السلة للمستخدم
4. **مقارنة الأسعار** - مقارنة مع أسعار أخرى
5. **إشعارات المخزون** - تنبيهات عند نفاد المخزون

## 📚 **المراجع**

- **APIExcample:** النموذج الأساسي
- **Magento 2 Cart API:** الوثائق الرسمية
- **Cart Events:** أحداث السلة
- **Product Options:** خيارات المنتجات

---

**تم تطوير هذا النظام بواسطة Josequal Team** 🚀
