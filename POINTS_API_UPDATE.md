# تحديث API النقاط - إضافة صورة العميل

## 🎯 التحديث المطلوب
تم إضافة صورة العميل إلى API النقاط ليعطي معلومات أكثر تفصيلاً عن العميل.

## 📋 التغييرات المطبقة

### 1. تحديث دالة `getCustomerPoints()`
- تم إضافة استدعاء `getCustomerProfileImage()` للحصول على صورة العميل
- تم إضافة حقل `customer_image` في الـ response

### 2. إضافة دالة جديدة `getCustomerProfileImage()`
```php
private function getCustomerProfileImage($customer) {
    // البحث عن صورة العميل في عدة أماكن
    // 1. profile_image attribute
    // 2. avatar attribute  
    // 3. custom attributes
    // 4. default image إذا لم توجد صورة
}
```

## 🔍 منطق البحث عن الصورة

### المرحلة الأولى: البحث في Customer Model
- `profile_image` - صورة الملف الشخصي المخصصة
- `avatar` - صورة الصورة الرمزية

### المرحلة الثانية: البحث في Custom Attributes
- البحث في `profile_image` custom attribute
- البحث في `avatar` custom attribute

### المرحلة الثالثة: الصورة الافتراضية
- إذا لم توجد صورة، يتم إرجاع الصورة الافتراضية: `{base_url}/media/default_profile.png`

## 📱 مثال على الاستجابة الجديدة

### API Call:
```bash
GET /apimobile/cart/points
Authorization: Bearer YOUR_TOKEN
```

### Response:
```json
{
    "status": true,
    "message": "Points Total",
    "data": {
        "points": 1250,
        "customer_name": "أحمد محمد",
        "customer_image": "https://avit.josequal.net/media/customer/profile/ahmed_profile.jpg",
        "loyalty_level": "Gold",
        "next_level_points": 3000,
        "points_to_next_level": 1750,
        "progress_percentage": 42,
        "points_history": [
            {
                "points": "+100",
                "action": "Online Order",
                "description": "Order #12345",
                "date": "08/09/2025 • 14:30",
                "icon": "shopping_cart"
            }
        ],
        "system_type": "custom"
    }
}
```

## 🎨 أنواع الصور المدعومة

### 1. صور مخصصة
- يمكن للعملاء رفع صور شخصية مخصصة
- يتم حفظها في `/media/customer/profile/`

### 2. صور من مصادر خارجية
- URLs كاملة للصور من خدمات أخرى
- يتم التحقق من بداية الرابط بـ `http`

### 3. الصورة الافتراضية
- صورة افتراضية لجميع العملاء
- مسار: `/media/default_profile.png`

## 🔧 إعدادات إضافية مطلوبة

### 1. إنشاء مجلد الصور
```bash
mkdir -p pub/media/customer/profile
mkdir -p pub/media/customer/avatar
```

### 2. رفع الصورة الافتراضية
- رفع صورة `default_profile.png` إلى `/pub/media/`

### 3. إعدادات الصلاحيات
```bash
chmod 755 pub/media/customer/
chmod 644 pub/media/default_profile.png
```

## 🚀 كيفية الاختبار

### 1. اختبار العميل بدون صورة
```bash
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. اختبار العميل مع صورة مخصصة
- رفع صورة للعميل
- اختبار API مرة أخرى

## 📊 الفوائد

### 1. تجربة مستخدم محسنة
- عرض صورة العميل في صفحة النقاط
- مظهر أكثر شخصية

### 2. معلومات شاملة
- اسم العميل + صورته
- تجربة متكاملة

### 3. مرونة في المصادر
- دعم صور متعددة المصادر
- fallback للصورة الافتراضية

## ⚠️ ملاحظات مهمة

### 1. الأمان
- التحقق من صحة مسارات الصور
- منع الوصول لملفات حساسة

### 2. الأداء
- تحسين تحميل الصور
- ضغط الصور عند الحاجة

### 3. التوافق
- يعمل مع النظام المخصص و Amasty Rewards
- لا يؤثر على APIs أخرى

---

**تاريخ التحديث:** 9 يناير 2025  
**المطور:** AI Assistant  
**الحالة:** ✅ مكتمل
