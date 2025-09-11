# Pagination APIs Documentation

## Overview
تم إضافة نظام pagination متكامل لجميع APIs المنتجات في APIMobile module.

## APIs with Pagination

### 1. Latest Products API
**Endpoint:** `GET /apimobile/product/latest`

**Parameters:**
- `page` (optional): رقم الصفحة (افتراضي: 1)
- `limit` (optional): عدد المنتجات في الصفحة (افتراضي: 10، الحد الأقصى: 100)
- `category_id` (optional): معرف الفئة للفلترة

**Example Request:**
```
GET /apimobile/product/latest?page=1&limit=20&category_id=123
```

**Response:**
```json
{
    "status": true,
    "message": "Latest Products List",
    "data": {
        "products": [...],
        "latest": true,
        "pagination": {
            "current_page": 1,
            "per_page": 20,
            "total_items": 150,
            "total_pages": 8,
            "has_next_page": true,
            "has_previous_page": false,
            "next_page": 2,
            "previous_page": null
        }
    }
}
```

### 2. Search Products API
**Endpoint:** `GET /apimobile/product/search`

**Parameters:**
- `search` (required): كلمة البحث (الحد الأدنى: حرفين)
- `page` (optional): رقم الصفحة (افتراضي: 1)
- `limit` (optional): عدد المنتجات في الصفحة (افتراضي: 20، الحد الأقصى: 100)
- `sort` (optional): ترتيب النتائج (افتراضي: name-a-z)
- `category_id` (optional): معرف الفئة للفلترة

**Sort Options:**
- `name-a-z`: ترتيب أبجدي تصاعدي
- `name-z-a`: ترتيب أبجدي تنازلي
- `price-l-h`: ترتيب حسب السعر تصاعدي
- `price-h-l`: ترتيب حسب السعر تنازلي
- `newest`: الأحدث أولاً
- `oldest`: الأقدم أولاً
- `rating-h-l`: ترتيب حسب التقييم تنازلي
- `rating-l-h`: ترتيب حسب التقييم تصاعدي

**Example Request:**
```
GET /apimobile/product/search?search=shirt&page=1&limit=15&sort=price-l-h
```

**Response:**
```json
{
    "status": true,
    "message": "Search Results",
    "data": {
        "search_query": "shirt",
        "products": [...],
        "pagination": {
            "current_page": 1,
            "per_page": 15,
            "total_items": 45,
            "total_pages": 3,
            "has_next_page": true,
            "has_previous_page": false,
            "next_page": 2,
            "previous_page": null
        }
    }
}
```

### 3. Category Products API
**Endpoint:** `GET /apimobile/category/products`

**Parameters:**
- `category_id` (required): معرف الفئة
- `page` (optional): رقم الصفحة (افتراضي: 1)
- `limit` (optional): عدد المنتجات في الصفحة (افتراضي: 20، الحد الأقصى: 100)
- `sort` (optional): ترتيب النتائج (افتراضي: name-a-z)
- `search` (optional): كلمة البحث للفلترة

**Example Request:**
```
GET /apimobile/category/products?category_id=123&page=2&limit=25&sort=newest
```

**Response:**
```json
{
    "status": true,
    "message": "Category Products",
    "data": {
        "category_id": "123",
        "category_name": "Men's Clothing",
        "products": [...],
        "pagination": {
            "current_page": 2,
            "per_page": 25,
            "total_items": 200,
            "total_pages": 8,
            "has_next_page": true,
            "has_previous_page": true,
            "next_page": 3,
            "previous_page": 1
        }
    }
}
```

## Pagination Object Structure

كل API يحتوي على كائن `pagination` في الاستجابة:

```json
{
    "pagination": {
        "current_page": 1,        // الصفحة الحالية
        "per_page": 20,           // عدد العناصر في الصفحة
        "total_items": 150,       // إجمالي العناصر
        "total_pages": 8,         // إجمالي الصفحات
        "has_next_page": true,    // هل يوجد صفحة تالية
        "has_previous_page": false, // هل يوجد صفحة سابقة
        "next_page": 2,          // رقم الصفحة التالية (null إذا لم توجد)
        "previous_page": null     // رقم الصفحة السابقة (null إذا لم توجد)
    }
}
```

## Error Handling

### Search API Errors
```json
{
    "status": false,
    "message": "Search query is required"
}
```

```json
{
    "status": false,
    "message": "Search query must be at least 2 characters long"
}
```

### Category API Errors
```json
{
    "status": false,
    "message": "Category is required"
}
```

## Validation Rules

1. **Page Parameter:**
   - يجب أن يكون رقم صحيح أكبر من 0
   - إذا لم يتم تمريره، سيتم استخدام 1 كافتراضي

2. **Limit Parameter:**
   - يجب أن يكون رقم صحيح بين 1 و 100
   - إذا لم يتم تمريره، سيتم استخدام القيمة الافتراضية (10 للمنتجات الحديثة، 20 للبحث والفئات)

3. **Search Parameter:**
   - مطلوب في Search API
   - يجب أن يكون على الأقل حرفين
   - يتم تجاهل المسافات في البداية والنهاية

## Performance Considerations

1. **Limit Cap:** الحد الأقصى للـ limit هو 100 لمنع الحمل الزائد على الخادم
2. **Database Optimization:** يتم استخدام LIMIT و OFFSET في استعلامات قاعدة البيانات
3. **Caching:** يمكن إضافة caching للنتائج الشائعة في المستقبل

## Usage Examples

### Frontend Implementation
```javascript
// Load more products
function loadMoreProducts(page) {
    fetch(`/apimobile/product/latest?page=${page}&limit=20`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                displayProducts(data.data.products);
                updatePagination(data.data.pagination);
            }
        });
}

// Search products
function searchProducts(query, page = 1) {
    fetch(`/apimobile/product/search?search=${encodeURIComponent(query)}&page=${page}&limit=20`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                displaySearchResults(data.data.products);
                updatePagination(data.data.pagination);
            }
        });
}
```

### Mobile App Implementation
```swift
// Swift example
func loadProducts(page: Int, limit: Int = 20) {
    let url = "\(baseURL)/apimobile/product/latest?page=\(page)&limit=\(limit)"
    
    URLSession.shared.dataTask(with: URL(string: url)!) { data, response, error in
        // Handle response
    }.resume()
}
```

## Testing

يمكن اختبار APIs باستخدام:

1. **Postman/Insomnia:**
   ```
   GET https://your-domain.com/apimobile/product/latest?page=1&limit=10
   ```

2. **cURL:**
   ```bash
   curl "https://your-domain.com/apimobile/product/search?search=shirt&page=1&limit=20"
   ```

3. **Browser:**
   ```
   https://your-domain.com/apimobile/category/products?category_id=123&page=1&limit=15
   ```

## Future Enhancements

1. **Advanced Search Filters:** إضافة فلاتر متقدمة للبحث
2. **Sorting Options:** إضافة خيارات ترتيب أكثر
3. **Caching:** إضافة نظام cache للنتائج
4. **Analytics:** تتبع استخدام APIs
5. **Rate Limiting:** تحديد معدل الطلبات
