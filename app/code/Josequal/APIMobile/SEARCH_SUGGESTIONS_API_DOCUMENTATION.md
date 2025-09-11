# Search Suggestions API Documentation

## Overview
تم إنشاء نظام اقتراحات البحث المتكامل للصفحة المعروضة، يتضمن اقتراحات المنتجات والفئات والبحث الحديث والشائع.

## APIs

### 1. Get Search Suggestions API
**Endpoint:** `GET /apimobile/product/search-suggestions`

**Parameters:**
- `query` (optional): كلمة البحث للاقتراحات (الحد الأدنى: حرفين)
- `limit` (optional): عدد الاقتراحات (افتراضي: 10، الحد الأقصى: 50)
- `customer_id` (optional): معرف العميل للحصول على البحث الحديث

**Example Request:**
```
GET /apimobile/product/search-suggestions?query=shirt&limit=15&customer_id=123
```

**Response:**
```json
{
    "status": true,
    "message": "Search Suggestions",
    "data": {
        "query": "shirt",
        "has_query": true,
        "suggestions": [
            {
                "type": "product",
                "text": "Blue Cotton Shirt",
                "sku": "SHIRT-001",
                "product_id": 123,
                "relevance": 90
            },
            {
                "type": "category",
                "text": "Men's Shirts",
                "category_id": 456,
                "relevance": 85
            }
        ],
        "recent_searches": [
            {
                "text": "Apple",
                "timestamp": 1691234567
            },
            {
                "text": "Banana",
                "timestamp": 1691230967
            }
        ],
        "popular_searches": [
            {
                "text": "iPhone",
                "count": 1250
            },
            {
                "text": "Samsung",
                "count": 980
            }
        ]
    }
}
```

### 2. Save Search Query API
**Endpoint:** `POST /apimobile/product/save-search`

**Parameters:**
- `query` (required): كلمة البحث المراد حفظها
- `customer_id` (required): معرف العميل

**Example Request:**
```json
{
    "query": "iPhone 15",
    "customer_id": 123
}
```

**Response:**
```json
{
    "status": true,
    "message": "Search query saved",
    "data": {
        "query": "iPhone 15",
        "customer_id": 123,
        "timestamp": 1691234567
    }
}
```

## Features

### 1. Product Suggestions
- البحث في أسماء المنتجات و SKU
- ترتيب حسب الصلة (Relevance Score)
- إرجاع معلومات المنتج الكاملة

### 2. Category Suggestions
- البحث في أسماء الفئات
- إرجاع معرف الفئة
- ترتيب حسب الصلة

### 3. Recent Searches
- عرض آخر عمليات البحث للعميل
- مرتبة حسب الوقت (الأحدث أولاً)
- محدودة بعدد معين (افتراضي: 5)

### 4. Popular Searches
- عرض أكثر عمليات البحث شيوعاً
- مرتبة حسب عدد المرات
- مفيدة للاكتشاف

## Relevance Scoring

نظام تسجيل الصلة يعطي نقاط مختلفة حسب نوع المطابقة:

- **Exact Match (100):** مطابقة تامة
- **Starts With (90):** يبدأ بالكلمة
- **Contains (70):** يحتوي على الكلمة
- **Word Boundary (50):** مطابقة حدود الكلمة

## Usage Examples

### Frontend Implementation
```javascript
// Get search suggestions
function getSearchSuggestions(query, customerId) {
    const url = `/apimobile/product/search-suggestions?query=${encodeURIComponent(query)}&customer_id=${customerId}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                displaySuggestions(data.data.suggestions);
                displayRecentSearches(data.data.recent_searches);
                displayPopularSearches(data.data.popular_searches);
            }
        });
}

// Save search query
function saveSearchQuery(query, customerId) {
    fetch('/apimobile/product/save-search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: query,
            customer_id: customerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            console.log('Search query saved');
        }
    });
}

// Auto-suggest functionality
let searchTimeout;
const searchInput = document.getElementById('search-input');

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length >= 2) {
        searchTimeout = setTimeout(() => {
            getSearchSuggestions(query, getCurrentCustomerId());
        }, 300); // Debounce 300ms
    }
});
```

### Mobile App Implementation

#### Android (Java/Kotlin)
```java
// Get search suggestions
public void getSearchSuggestions(String query, int customerId) {
    String url = "https://your-domain.com/apimobile/product/search-suggestions?query=" + 
                 URLEncoder.encode(query, "UTF-8") + "&customer_id=" + customerId;
    
    RequestQueue queue = Volley.newRequestQueue(this);
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.GET, url, null,
        response -> {
            try {
                JSONObject data = response.getJSONObject("data");
                JSONArray suggestions = data.getJSONArray("suggestions");
                JSONArray recentSearches = data.getJSONArray("recent_searches");
                JSONArray popularSearches = data.getJSONArray("popular_searches");
                
                updateSuggestionsList(suggestions);
                updateRecentSearchesList(recentSearches);
                updatePopularSearchesList(popularSearches);
                
            } catch (JSONException e) {
                Log.e("SearchSuggestions", "Error parsing response", e);
            }
        },
        error -> Log.e("SearchSuggestions", "Error: " + error.getMessage())
    );
    
    queue.add(request);
}

// Save search query
public void saveSearchQuery(String query, int customerId) {
    String url = "https://your-domain.com/apimobile/product/save-search";
    
    JSONObject params = new JSONObject();
    try {
        params.put("query", query);
        params.put("customer_id", customerId);
    } catch (JSONException e) {
        Log.e("SaveSearch", "Error creating params", e);
        return;
    }
    
    RequestQueue queue = Volley.newRequestQueue(this);
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.POST, url, params,
        response -> Log.d("SaveSearch", "Search query saved"),
        error -> Log.e("SaveSearch", "Error: " + error.getMessage())
    );
    
    queue.add(request);
}
```

#### iOS (Swift)
```swift
// Get search suggestions
func getSearchSuggestions(query: String, customerId: Int) {
    let encodedQuery = query.addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed) ?? ""
    let url = URL(string: "https://your-domain.com/apimobile/product/search-suggestions?query=\(encodedQuery)&customer_id=\(customerId)")!
    
    URLSession.shared.dataTask(with: url) { data, response, error in
        if let data = data {
            do {
                let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
                if let data = json?["data"] as? [String: Any] {
                    let suggestions = data["suggestions"] as? [[String: Any]] ?? []
                    let recentSearches = data["recent_searches"] as? [[String: Any]] ?? []
                    let popularSearches = data["popular_searches"] as? [[String: Any]] ?? []
                    
                    DispatchQueue.main.async {
                        self.updateSuggestionsList(suggestions)
                        self.updateRecentSearchesList(recentSearches)
                        self.updatePopularSearchesList(popularSearches)
                    }
                }
            } catch {
                print("JSON Error: \(error)")
            }
        }
    }.resume()
}

// Save search query
func saveSearchQuery(query: String, customerId: Int) {
    let url = URL(string: "https://your-domain.com/apimobile/product/save-search")!
    var request = URLRequest(url: url)
    request.httpMethod = "POST"
    request.setValue("application/json", forHTTPHeaderField: "Content-Type")
    
    let params = [
        "query": query,
        "customer_id": customerId
    ]
    
    do {
        request.httpBody = try JSONSerialization.data(withJSONObject: params)
    } catch {
        print("Error creating request body: \(error)")
        return
    }
    
    URLSession.shared.dataTask(with: request) { data, response, error in
        if let error = error {
            print("Error: \(error)")
        } else {
            print("Search query saved successfully")
        }
    }.resume()
}
```

## Testing

### Test Cases

#### 1. Basic Suggestions
```bash
# Test with query
curl "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&limit=10"

# Test without query (should return recent and popular)
curl "https://your-domain.com/apimobile/product/search-suggestions?customer_id=123"
```

#### 2. Save Search Query
```bash
curl -X POST "https://your-domain.com/apimobile/product/save-search" \
  -H "Content-Type: application/json" \
  -d '{"query": "iPhone 15", "customer_id": 123}'
```

#### 3. Edge Cases
```bash
# Test short query (should not return suggestions)
curl "https://your-domain.com/apimobile/product/search-suggestions?query=a"

# Test excessive limit (should cap at 50)
curl "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&limit=100"
```

## Error Handling

### Common Errors
```json
{
    "status": false,
    "message": "Query and customer_id are required"
}
```

```json
{
    "status": false,
    "message": "Failed to save search query"
}
```

## Performance Considerations

1. **Debouncing:** استخدم debouncing لتجنب طلبات كثيرة أثناء الكتابة
2. **Caching:** يمكن إضافة cache للاقتراحات الشائعة
3. **Limit Cap:** الحد الأقصى 50 للاقتراحات لمنع الحمل الزائد
4. **Database Optimization:** استخدام indexes على أسماء المنتجات والفئات

## Future Enhancements

1. **Analytics Integration:** تتبع عمليات البحث لتحسين الاقتراحات
2. **Machine Learning:** استخدام ML لتحسين الاقتراحات
3. **Personalization:** اقتراحات مخصصة حسب تاريخ العميل
4. **Auto-complete:** إكمال تلقائي للكلمات
5. **Search History Management:** إدارة تاريخ البحث (حذف، تصدير)
6. **Voice Search:** دعم البحث الصوتي
7. **Image Search:** البحث بالصور
8. **Trending Searches:** البحثات الرائجة حالياً
