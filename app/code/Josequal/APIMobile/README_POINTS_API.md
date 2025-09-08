# 🎯 Points API Documentation

## نظرة عامة

هذا الوحدة توفر APIs شاملة لإدارة النقاط في تطبيق الهاتف المحمول.

## نقاط النهاية (Endpoints)

### النقاط (Points)

#### 1. عرض نقاط العميل
- **النقطة النهائية:** `apimobile/cart/points`
- **الطريقة:** GET
- **المصادقة:** مطلوبة
- **الوصف:** الحصول على إجمالي نقاط العميل

**الاستجابة (Response):**
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
            }
        ]
    }
}
```

#### 2. تطبيق النقاط على السلة
- **النقطة النهائية:** `apimobile/cart/apply-points`
- **الطريقة:** POST
- **المصادقة:** مطلوبة
- **الوصف:** تطبيق نقاط على السلة أو إزالتها

**المعاملات (Parameters):**
```json
{
    "points": 100,     // عدد النقاط المراد تطبيقها
    "remove": 0        // 0 لتطبيق النقاط، 1 لإزالتها
}
```

**الاستجابة (Response):**
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

#### 3. إرسال تذكير النقاط
- **النقطة النهائية:** `apimobile/cart/points-reminder`
- **الطريقة:** POST
- **المصادقة:** غير مطلوبة (للمدير)
- **الوصف:** إرسال إشعارات للعملاء الذين لديهم نقاط أكثر من 400

**الاستجابة (Response):**
```
all notifications send for 25 customers
```

## طرق الاستخدام

### 1. عرض نقاط العميل
```bash
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 2. تطبيق نقاط على السلة
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

### 3. إزالة النقاط من السلة
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "remove": 1
  }'
```

### 4. إرسال تذكير النقاط
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/points-reminder"
```

## أنواع الأخطاء

### أخطاء التحقق من البيانات
- `Number of points is required` - عدد النقاط مطلوب
- `Too much point(s) used.` - عدد النقاط المستخدمة أكثر من المتاح
- `You don't have points to apply` - لا توجد نقاط متاحة للتطبيق
- `Please remove coupon so you can use points.` - يجب إزالة الكوبون أولاً
- `Cart is empty` - السلة فارغة
- `Enter correct points number.` - أدخل عدد نقاط صحيح

### أخطاء المصادقة
- `Unauthorized` - غير مصرح بالوصول

## رموز الحالة

- `200` - نجح الطلب
- `400` - خطأ في البيانات
- `401` - غير مصرح
- `404` - غير موجود
- `500` - خطأ في الخادم

## أمثلة الاستخدام

### مثال 1: عرض النقاط
```bash
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### مثال 2: تطبيق النقاط
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "points": 100,
    "remove": 0
  }'
```

### مثال 3: إزالة النقاط
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "remove": 1
  }'
```

### مثال 4: إرسال تذكير
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/points-reminder"
```

## الميزات المتقدمة

1. **تكامل مع نظام Amasty Rewards**
2. **إدارة الخصومات بناءً على النقاط**
3. **دعم متعدد المتاجر والمواقع**
4. **تحقق من صحة البيانات**
5. **معالجة الأخطاء الشاملة**
6. **تكامل مع نظام الإشعارات**
7. **دعم Firebase للإشعارات**
8. **إشعارات باللغة العربية**
9. **تحقق من وجود الكوبونات**
10. **حساب الخصم التلقائي**

## الأمان والتحقق

- ✅ التحقق من وجود نقاط كافية
- ✅ التحقق من صحة البيانات
- ✅ دعم CSRF
- ✅ معالجة الأخطاء
- ✅ استجابة JSON منظمة
- ✅ دعم المصادقة للميزات الحساسة
- ✅ التحقق من وجود منتجات في السلة
- ✅ التحقق من وجود الكوبونات

## التكامل مع النظام

- ✅ تكامل مع نظام Amasty Rewards
- ✅ تكامل مع نظام السلة
- ✅ تكامل مع نظام الإشعارات
- ✅ تكامل مع Firebase
- ✅ تكامل مع نظام المكافآت

## الدعم

للحصول على الدعم أو الإبلاغ عن مشاكل، يرجى التواصل مع فريق التطوير.

## ملفات الاختبار

- `TEST_POINTS_API.md` - دليل اختبار شامل لجميع APIs
- أمثلة عملية لجميع الحالات
- اختبارات الأخطاء
- سيناريوهات اختبار كاملة
