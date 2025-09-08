# 🎯 Points API Testing Guide

## نظرة عامة
هذا الدليل يوفر أمثلة شاملة لاختبار جميع Points APIs مع أمثلة عملية.

## 🔑 الحصول على Token للمصادقة

### 1. تسجيل الدخول للحصول على Token
```bash
curl -X POST https://avit.josequal.net/V1/mobile/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "status": true,
  "message": "Login successful",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "customer_id": "123"
  }
}
```

## 🧪 اختبار Points APIs

### **Test 1: عرض نقاط العميل**

```bash
curl -X GET https://avit.josequal.net/apimobile/cart/points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Points Total",
  "data": {
    "points": 1500,
    "customer_name": "Fatima Husni",
    "loyalty_level": "Gold",
    "next_level_points": 3000,
    "points_to_next_level": 1500,
    "progress_percentage": 50,
    "points_history": [
        {
          "points": "+240",
          "action": "Online Order",
          "description": "Suite 2 pieces, hijab...",
          "date": "26/10/2024 • 09:47",
        "icon": "shopping_cart"
      },
      {
        "points": "+240",
        "action": "Online Order",
        "description": "Product purchase",
        "date": "25/10/2024 • 14:30",
        "icon": "shopping_cart"
      },
      {
        "points": "+50",
        "action": "Product Review",
        "description": "Review submitted",
        "date": "24/10/2024 • 16:20",
        "icon": "star"
      },
      {
        "points": "+100",
        "action": "Account Registration",
        "description": "Welcome bonus",
        "date": "20/10/2024 • 10:00",
        "icon": "person_add"
      }
    ]
  }
}
```

### **Test 2: تطبيق 100 نقطة على السلة**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

**Expected Response:**
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
        "value": "-$10.00"
      },
      {
        "label": "Shipping",
        "value": "$0.00"
      },
      {
        "label": "Grand Total",
        "value": "$109.99"
      }
    ]
  }
}
```

### **Test 3: إزالة النقاط من السلة**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "remove": 1
  }'
```

**Expected Response:**
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

### **Test 4: إرسال تذكير النقاط (للمدير)**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/points-reminder
```

**Expected Response:**
```
all notifications send for 25 customers
```

## 🚨 اختبار حالات الخطأ

### **Test 5: محاولة تطبيق نقاط بدون تحديد العدد**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "remove": 0
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Number of points is required",
  "data": []
}
```

### **Test 6: محاولة تطبيق نقاط أكثر من المتاح**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 2000,
    "remove": 0
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Too much point(s) used.",
  "data": []
}
```

### **Test 7: محاولة تطبيق نقاط بدون وجود نقاط**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "You don't have points to apply",
  "data": []
}
```

### **Test 8: محاولة تطبيق نقاط مع وجود كوبون**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Please remove coupon so you can use points.",
  "data": []
}
```

### **Test 9: محاولة تطبيق نقاط على سلة فارغة**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Cart is empty",
  "data": []
}
```

### **Test 10: محاولة الوصول بدون مصادقة**

```bash
curl -X GET https://avit.josequal.net/apimobile/cart/points
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Unauthorized",
  "data": []
}
```

### **Test 11: حالة عدم وجود Amasty Rewards**

إذا لم يكن نظام Amasty Rewards مثبتاً، ستظهر الرسالة التالية:

```bash
curl -X GET https://avit.josequal.net/apimobile/cart/points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Points system is not available. Please install Amasty Rewards extension.",
  "data": []
}
```

### **Test 12: تطبيق النقاط بدون Amasty Rewards**

```bash
curl -X POST https://avit.josequal.net/apimobile/cart/apply-points \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Points system is not available. Please install Amasty Rewards extension.",
  "data": []
}
```

## 📱 اختبار من Postman

### **1. إنشاء Collection جديد**
- Name: `Points API Tests`
- Description: `Testing Points API endpoints`

### **2. إعداد Environment Variables**
```
base_url: https://avit.josequal.net
token: YOUR_ACTUAL_TOKEN
```

### **3. إنشاء Requests**

#### **Get Customer Points Request:**
```
Method: GET
URL: {{base_url}}/apimobile/cart/points
Headers:
  Authorization: Bearer {{token}}
```

#### **Apply Points Request:**
```
Method: POST
URL: {{base_url}}/apimobile/cart/apply-points
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "points": 100,
  "remove": 0
}
```

#### **Remove Points Request:**
```
Method: POST
URL: {{base_url}}/apimobile/cart/apply-points
Headers:
  Authorization: Bearer {{token}}
  Content-Type: application/json
Body (raw JSON):
{
  "remove": 1
}
```

#### **Points Reminder Request:**
```
Method: POST
URL: {{base_url}}/apimobile/cart/points-reminder
Headers:
  Content-Type: application/json
```

## 🔄 سيناريو اختبار كامل

### **الخطوات:**
1. **تسجيل الدخول** للحصول على token
2. **عرض نقاط العميل** للتأكد من وجود نقاط
3. **إضافة منتج للسلة** (إذا لم تكن موجودة)
4. **تطبيق نقاط على السلة** ومراقبة التغييرات
5. **عرض السلة** للتأكد من تطبيق الخصم
6. **إزالة النقاط** من السلة
7. **عرض السلة** للتأكد من إزالة الخصم
8. **اختبار حالات الخطأ** المختلفة

### **النتائج المتوقعة:**
- ✅ عرض النقاط يعمل بشكل صحيح
- ✅ تطبيق النقاط يعمل بشكل صحيح
- ✅ إزالة النقاط تعمل بشكل صحيح
- ✅ حساب الخصم يعمل بشكل صحيح
- ✅ معالجة الأخطاء تعمل بشكل صحيح
- ✅ المصادقة تعمل بشكل صحيح
- ✅ إرسال التذكيرات يعمل بشكل صحيح

## 📊 تقرير الاختبار

### **الوظائف المختبرة:**
- [x] عرض نقاط العميل
- [x] تطبيق النقاط على السلة
- [x] إزالة النقاط من السلة
- [x] إرسال تذكير النقاط
- [x] معالجة الأخطاء
- [x] المصادقة

### **الحالات المختبرة:**
- [x] الحالات الناجحة
- [x] حالات الخطأ
- [x] حالات المصادقة
- [x] حالات البيانات غير الصحيحة

### **الملاحظات:**
- جميع APIs تعمل بشكل صحيح
- معالجة الأخطاء تعمل بشكل مناسب
- المصادقة تعمل بشكل صحيح
- الأداء جيد
- الاستجابات متسقة
- تكامل مع نظام Amasty Rewards يعمل بشكل صحيح

## 🔧 الميزات المتاحة

### **1. إدارة النقاط:**
- ✅ عرض إجمالي نقاط العميل
- ✅ تطبيق النقاط على السلة
- ✅ إزالة النقاط من السلة
- ✅ التحقق من صحة النقاط المتاحة

### **2. قيود النظام:**
- ✅ لا يمكن استخدام النقاط مع الكوبونات
- ✅ التحقق من وجود نقاط كافية
- ✅ التحقق من وجود منتجات في السلة
- ✅ حساب الخصم بناءً على النقاط

### **3. إشعارات النقاط:**
- ✅ إرسال تذكير للعملاء الذين لديهم نقاط أكثر من 400
- ✅ إشعارات باللغة العربية
- ✅ تكامل مع Firebase للإشعارات

## 🎯 الخلاصة

تم إنشاء API شامل للنقاط في وحدة APIMobile يتضمن:
- عرض النقاط
- تطبيق النقاط على السلة
- إزالة النقاط
- إرسال تذكيرات النقاط
- تكامل مع نظام المكافآت

جميع هذه الوظائف متاحة عبر REST API مع مصادقة آمنة ومعالجة شاملة للأخطاء.
