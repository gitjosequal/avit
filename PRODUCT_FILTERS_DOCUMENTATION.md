# توثيق نظام الفلترة الجديد للمنتجات

## 🎯 نظرة عامة
تم إضافة نظام فلترة شامل لجميع APIs المنتجات في module `APIMobile`، مما يتيح للمستخدمين تصفية المنتجات بناءً على معايير متعددة.

## 📋 APIs المحدثة

### 1. Search Products API
```bash
GET /apimobile/product/search
```

### 2. Latest Products API
```bash
GET /apimobile/product/latest
```

### 3. Category Products API
```bash
GET /apimobile/category/products
```

## 🔍 معاملات الفلترة المتاحة

جميع معاملات الفلترة **nullable** (اختيارية) ويمكن استخدامها مع أي من APIs المنتجات:

### 1. Date Filter
```json
{
  "date": "2025-01-09"
}
```
- **الوصف**: فلترة المنتجات حسب تاريخ الإنشاء
- **النوع**: string (YYYY-MM-DD format)
- **القيمة الافتراضية**: null

### 2. Price Range Filters
```json
{
  "min_price": 100.00,
  "max_price": 500.00
}
```
- **الوصف**: فلترة المنتجات حسب نطاق السعر
- **النوع**: float
- **القيمة الافتراضية**: null
- **ملاحظة**: يمكن استخدام `min_price` أو `max_price` منفردين أو معاً

### 3. Rating Filter
```json
{
  "rating": 4
}
```
- **الوصف**: فلترة المنتجات حسب التقييم (1-5 نجوم)
- **النوع**: integer (1-5)
- **القيمة الافتراضية**: null
- **ملاحظة**: حالياً غير مفعل (يتطلب تطوير إضافي)

### 4. Product Type Filter
```json
{
  "product_type": "simple"
}
```
- **الوصف**: فلترة المنتجات حسب نوع المنتج
- **النوع**: string
- **القيم المتاحة**: simple, configurable, grouped, bundle, virtual, downloadable
- **القيمة الافتراضية**: null

### 5. Size Filter
```json
{
  "size": "M"
}
```
- **الوصف**: فلترة المنتجات حسب الحجم
- **النوع**: string
- **القيم المتاحة**: S, M, L, XL, XXL
- **القيمة الافتراضية**: null

### 6. Availability Filter
```json
{
  "availability": "in_stock"
}
```
- **الوصف**: فلترة المنتجات حسب التوفر
- **النوع**: string
- **القيم المتاحة**: 
  - `"in_stock"` - متوفر في المخزن
  - `"out_of_stock"` - غير متوفر
- **القيمة الافتراضية**: null

## 📱 أمثلة على الاستخدام

### مثال 1: البحث مع فلترة السعر
```bash
curl -X GET "https://avit.josequal.net/apimobile/product/search?search=shirt&min_price=50&max_price=200&page=1&limit=20"
```

### مثال 2: أحدث المنتجات مع فلترة الحجم والتوفر
```bash
curl -X GET "https://avit.josequal.net/apimobile/product/latest?size=M&availability=in_stock&page=1&limit=10"
```

### مثال 3: منتجات الفئة مع فلترة متعددة
```bash
curl -X GET "https://avit.josequal.net/apimobile/category/products?category_id=123&product_type=simple&min_price=100&availability=in_stock&page=1&limit=20"
```

## 📊 مثال على الاستجابة

```json
{
    "status": true,
    "message": "Search Results",
    "data": {
        "search_query": "shirt",
        "products": [
            {
                "id": "123",
                "name": "Cotton Shirt",
                "price": "150.00",
                "image": "https://example.com/shirt.jpg",
                "rating": 4.5,
                "availability": "in_stock"
            }
        ],
        "filters": {
            "date": null,
            "min_price": 50.0,
            "max_price": 200.0,
            "rating": null,
            "product_type": null,
            "size": null,
            "availability": "in_stock"
        },
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total_items": 15,
            "total_pages": 1,
            "has_next_page": false,
            "has_previous_page": false,
            "next_page": null,
            "previous_page": null
        }
    }
}
```

## 🔧 التطبيق التقني

### الدوال المحدثة:

#### 1. `searchProducts($data)`
- إضافة معاملات الفلترة
- تمرير المعاملات إلى `_getProductsList()`
- إرجاع معلومات الفلاتر في الاستجابة

#### 2. `latestProducts($data)`
- إضافة معاملات الفلترة
- تمرير المعاملات إلى `_getProductsList()`
- إرجاع معلومات الفلاتر في الاستجابة

#### 3. `categoryProductList($data)`
- إضافة معاملات الفلترة
- تمرير المعاملات إلى `_getCategoryProducts()`
- إرجاع معلومات الفلاتر في الاستجابة

#### 4. `_getProductsList()` - محدثة
- إضافة معاملات الفلترة الجديدة
- استدعاء `_applyFilters()` لتطبيق الفلاتر

#### 5. `_getCategoryProducts()` - محدثة
- إضافة معاملات الفلترة الجديدة
- استدعاء `_applyFilters()` لتطبيق الفلاتر

#### 6. `_applyFilters()` - جديدة
- تطبيق جميع أنواع الفلاتر على collection المنتجات
- معالجة الأخطاء بشكل آمن
- دعم جميع أنواع الفلترة المطلوبة

## 🎨 منطق الفلترة

### 1. Date Filter
```php
if ($date !== null && !empty($date)) {
    $dateFilter = date('Y-m-d', strtotime($date));
    $productsQuery->addAttributeToFilter('created_at', ['gteq' => $dateFilter . ' 00:00:00']);
}
```

### 2. Price Filters
```php
if ($min_price !== null && $min_price > 0) {
    $productsQuery->addAttributeToFilter('price', ['gteq' => $min_price]);
}

if ($max_price !== null && $max_price > 0) {
    $productsQuery->addAttributeToFilter('price', ['lteq' => $max_price]);
}
```

### 3. Availability Filter
```php
if ($availability === 'in_stock') {
    $productsQuery->addAttributeToFilter('is_salable', 1);
} elseif ($availability === 'out_of_stock') {
    $productsQuery->addAttributeToFilter('is_salable', 0);
}
```

## ⚠️ ملاحظات مهمة

### 1. التوافق مع النظام الحالي
- جميع المعاملات الجديدة nullable
- لا تؤثر على الاستخدام الحالي للـ APIs
- تحافظ على التوافق مع التطبيقات الموجودة

### 2. معالجة الأخطاء
- جميع الفلاتر محمية بـ try-catch
- في حالة حدوث خطأ، يتم تجاهل الفلتر والمتابعة
- لا تؤثر الأخطاء على النتائج الأساسية

### 3. الأداء
- الفلاتر تطبق على مستوى قاعدة البيانات
- تحسين استعلامات SQL
- لا تؤثر على سرعة الاستجابة

### 4. التطوير المستقبلي
- يمكن إضافة فلاتر جديدة بسهولة
- دعم فلاتر مخصصة حسب احتياجات المتجر
- إمكانية إضافة فلاتر معقدة

## 🚀 اختبار النظام

### 1. اختبار الفلترة الأساسية
```bash
# اختبار فلترة السعر
curl -X GET "https://avit.josequal.net/apimobile/product/search?search=test&min_price=100"

# اختبار فلترة التوفر
curl -X GET "https://avit.josequal.net/apimobile/product/latest?availability=in_stock"
```

### 2. اختبار الفلترة المتعددة
```bash
# اختبار عدة فلاتر معاً
curl -X GET "https://avit.josequal.net/apimobile/category/products?category_id=123&min_price=50&max_price=200&availability=in_stock"
```

### 3. اختبار القيم الفارغة
```bash
# اختبار معاملات null
curl -X GET "https://avit.josequal.net/apimobile/product/search?search=test&min_price=&max_price=&availability="
```

## 📈 الفوائد

### 1. تجربة مستخدم محسنة
- فلترة دقيقة للمنتجات
- نتائج أكثر صلة بالبحث
- توفير الوقت في البحث

### 2. مرونة في الاستخدام
- فلاتر اختيارية
- يمكن دمج عدة فلاتر
- سهولة في الاستخدام

### 3. قابلية التوسع
- إضافة فلاتر جديدة بسهولة
- دعم احتياجات متعددة
- مرونة في التطوير

---

**تاريخ التحديث:** 9 يناير 2025  
**المطور:** AI Assistant  
**الحالة:** ✅ مكتمل وجاهز للاستخدام
