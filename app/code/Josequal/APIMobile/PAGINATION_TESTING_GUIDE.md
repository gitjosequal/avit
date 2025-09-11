# Pagination APIs Testing Guide

## Test Cases for Pagination APIs

### 1. Latest Products API Tests

#### Test Case 1: Basic Pagination
```bash
# Test page 1 with default limit
curl "https://your-domain.com/apimobile/product/latest?page=1"

# Expected: Should return first 10 products with pagination info
```

#### Test Case 2: Custom Limit
```bash
# Test with custom limit
curl "https://your-domain.com/apimobile/product/latest?page=1&limit=5"

# Expected: Should return 5 products with correct pagination
```

#### Test Case 3: Page Navigation
```bash
# Test page 2
curl "https://your-domain.com/apimobile/product/latest?page=2&limit=10"

# Expected: Should return products 11-20 with has_previous_page=true
```

#### Test Case 4: Edge Cases
```bash
# Test invalid page (should default to 1)
curl "https://your-domain.com/apimobile/product/latest?page=0"

# Test excessive limit (should cap at 100)
curl "https://your-domain.com/apimobile/product/latest?page=1&limit=200"

# Test negative page (should default to 1)
curl "https://your-domain.com/apimobile/product/latest?page=-1"
```

### 2. Search Products API Tests

#### Test Case 1: Basic Search
```bash
# Test basic search
curl "https://your-domain.com/apimobile/product/search?search=shirt"

# Expected: Should return products matching "shirt" with pagination
```

#### Test Case 2: Search with Pagination
```bash
# Test search with pagination
curl "https://your-domain.com/apimobile/product/search?search=shirt&page=2&limit=15"

# Expected: Should return page 2 of search results
```

#### Test Case 3: Search Validation
```bash
# Test empty search (should return error)
curl "https://your-domain.com/apimobile/product/search?search="

# Test short search (should return error)
curl "https://your-domain.com/apimobile/product/search?search=a"

# Expected: Should return validation errors
```

#### Test Case 4: Search with Sorting
```bash
# Test search with different sorting options
curl "https://your-domain.com/apimobile/product/search?search=shirt&sort=price-l-h"
curl "https://your-domain.com/apimobile/product/search?search=shirt&sort=price-h-l"
curl "https://your-domain.com/apimobile/product/search?search=shirt&sort=newest"
curl "https://your-domain.com/apimobile/product/search?search=shirt&sort=rating-h-l"

# Expected: Should return sorted results
```

### 3. Category Products API Tests

#### Test Case 1: Basic Category Products
```bash
# Test category products
curl "https://your-domain.com/apimobile/category/products?category_id=123"

# Expected: Should return products from category 123
```

#### Test Case 2: Category with Pagination
```bash
# Test category products with pagination
curl "https://your-domain.com/apimobile/category/products?category_id=123&page=3&limit=25"

# Expected: Should return page 3 with 25 products per page
```

#### Test Case 3: Category Validation
```bash
# Test without category_id (should return error)
curl "https://your-domain.com/apimobile/category/products"

# Test invalid category_id
curl "https://your-domain.com/apimobile/category/products?category_id=99999"

# Expected: Should return validation errors
```

#### Test Case 4: Category with Search
```bash
# Test category products with search filter
curl "https://your-domain.com/apimobile/category/products?category_id=123&search=blue&page=1&limit=20"

# Expected: Should return blue products from category 123
```

## Expected Response Format

### Success Response
```json
{
    "status": true,
    "message": "Latest Products List",
    "data": {
        "products": [
            {
                "product_id": 1,
                "name": "Product Name",
                "price": "29.99",
                "image": "https://example.com/image.jpg",
                "stock_status": true,
                "is_favorite": false
            }
        ],
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

### Error Response
```json
{
    "status": false,
    "message": "Search query is required"
}
```

## Performance Tests

### Load Testing
```bash
# Test with multiple concurrent requests
for i in {1..10}; do
    curl "https://your-domain.com/apimobile/product/latest?page=$i&limit=20" &
done
wait

# Expected: All requests should complete successfully
```

### Large Dataset Test
```bash
# Test with large page numbers
curl "https://your-domain.com/apimobile/product/latest?page=100&limit=100"

# Expected: Should handle gracefully (return empty results or error)
```

## Browser Testing

### JavaScript Console Tests
```javascript
// Test Latest Products API
fetch('/apimobile/product/latest?page=1&limit=10')
    .then(response => response.json())
    .then(data => {
        console.log('Latest Products:', data);
        console.log('Pagination:', data.data.pagination);
    });

// Test Search API
fetch('/apimobile/product/search?search=shirt&page=1&limit=20')
    .then(response => response.json())
    .then(data => {
        console.log('Search Results:', data);
        console.log('Total Items:', data.data.pagination.total_items);
    });

// Test Category Products API
fetch('/apimobile/category/products?category_id=123&page=1&limit=15')
    .then(response => response.json())
    .then(data => {
        console.log('Category Products:', data);
        console.log('Has Next Page:', data.data.pagination.has_next_page);
    });
```

## Mobile App Testing

### Android (Java/Kotlin)
```java
// Test pagination in Android
public void testPagination() {
    String url = "https://your-domain.com/apimobile/product/latest?page=1&limit=20";
    
    RequestQueue queue = Volley.newRequestQueue(this);
    JsonObjectRequest request = new JsonObjectRequest(
        Request.Method.GET, url, null,
        response -> {
            // Handle success
            JSONObject data = response.getJSONObject("data");
            JSONObject pagination = data.getJSONObject("pagination");
            
            int currentPage = pagination.getInt("current_page");
            int totalPages = pagination.getInt("total_pages");
            boolean hasNext = pagination.getBoolean("has_next_page");
            
            Log.d("Pagination", "Page: " + currentPage + "/" + totalPages);
            Log.d("Pagination", "Has Next: " + hasNext);
        },
        error -> {
            // Handle error
            Log.e("API Error", error.getMessage());
        }
    );
    
    queue.add(request);
}
```

### iOS (Swift)
```swift
// Test pagination in iOS
func testPagination() {
    let url = URL(string: "https://your-domain.com/apimobile/product/latest?page=1&limit=20")!
    
    URLSession.shared.dataTask(with: url) { data, response, error in
        if let data = data {
            do {
                let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
                if let data = json?["data"] as? [String: Any],
                   let pagination = data["pagination"] as? [String: Any] {
                    
                    let currentPage = pagination["current_page"] as? Int ?? 0
                    let totalPages = pagination["total_pages"] as? Int ?? 0
                    let hasNext = pagination["has_next_page"] as? Bool ?? false
                    
                    print("Page: \(currentPage)/\(totalPages)")
                    print("Has Next: \(hasNext)")
                }
            } catch {
                print("JSON Error: \(error)")
            }
        }
    }.resume()
}
```

## Automated Testing Script

### PHP Test Script
```php
<?php
// pagination_test.php

function testAPI($url, $expectedStatus = true) {
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data['status'] === $expectedStatus) {
        echo "✅ PASS: $url\n";
        if (isset($data['data']['pagination'])) {
            $pagination = $data['data']['pagination'];
            echo "   Pagination: Page {$pagination['current_page']}/{$pagination['total_pages']}\n";
            echo "   Items: {$pagination['total_items']}\n";
        }
    } else {
        echo "❌ FAIL: $url\n";
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

// Test cases
$baseUrl = "https://your-domain.com";

echo "Testing Pagination APIs...\n\n";

// Latest Products Tests
testAPI("$baseUrl/apimobile/product/latest?page=1&limit=10");
testAPI("$baseUrl/apimobile/product/latest?page=2&limit=5");

// Search Tests
testAPI("$baseUrl/apimobile/product/search?search=shirt&page=1&limit=20");
testAPI("$baseUrl/apimobile/product/search?search=", false); // Should fail
testAPI("$baseUrl/apimobile/product/search?search=a", false); // Should fail

// Category Tests
testAPI("$baseUrl/apimobile/category/products?category_id=123&page=1&limit=15");
testAPI("$baseUrl/apimobile/category/products", false); // Should fail

echo "Testing completed!\n";
?>
```

## Test Results Validation

### Checklist
- [ ] All APIs return proper pagination object
- [ ] Page numbers are validated (minimum 1)
- [ ] Limit is capped at 100
- [ ] Search requires minimum 2 characters
- [ ] Category ID is required for category API
- [ ] Pagination calculations are correct
- [ ] has_next_page and has_previous_page flags work
- [ ] next_page and previous_page values are correct
- [ ] Error handling works for invalid inputs
- [ ] Performance is acceptable with large datasets

### Common Issues to Check
1. **Off-by-one errors** in pagination calculations
2. **Memory issues** with large limit values
3. **SQL injection** in search parameters
4. **Performance degradation** with high page numbers
5. **Inconsistent sorting** across pages
6. **Missing validation** for edge cases
