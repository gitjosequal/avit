# Search Suggestions API Testing Guide

## Test Cases for Search Suggestions APIs

### 1. Get Search Suggestions API Tests

#### Test Case 1: Basic Suggestions
```bash
# Test with query
curl "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&limit=10"

# Expected: Should return product and category suggestions
```

#### Test Case 2: Suggestions with Customer ID
```bash
# Test with customer ID for recent searches
curl "https://your-domain.com/apimobile/product/search-suggestions?query=phone&customer_id=123&limit=15"

# Expected: Should return suggestions + recent searches
```

#### Test Case 3: Empty Query (Recent & Popular Only)
```bash
# Test without query
curl "https://your-domain.com/apimobile/product/search-suggestions?customer_id=123"

# Expected: Should return only recent and popular searches
```

#### Test Case 4: Edge Cases
```bash
# Test short query (should not return suggestions)
curl "https://your-domain.com/apimobile/product/search-suggestions?query=a"

# Test excessive limit (should cap at 50)
curl "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&limit=100"

# Test negative limit (should default to 10)
curl "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&limit=-5"
```

### 2. Save Search Query API Tests

#### Test Case 1: Basic Save
```bash
# Test saving search query
curl -X POST "https://your-domain.com/apimobile/product/save-search" \
  -H "Content-Type: application/json" \
  -d '{"query": "iPhone 15", "customer_id": 123}'

# Expected: Should return success with timestamp
```

#### Test Case 2: Validation Tests
```bash
# Test without query (should fail)
curl -X POST "https://your-domain.com/apimobile/product/save-search" \
  -H "Content-Type: application/json" \
  -d '{"customer_id": 123}'

# Test without customer_id (should fail)
curl -X POST "https://your-domain.com/apimobile/product/save-search" \
  -H "Content-Type: application/json" \
  -d '{"query": "iPhone 15"}'

# Test with empty query (should fail)
curl -X POST "https://your-domain.com/apimobile/product/save-search" \
  -H "Content-Type: application/json" \
  -d '{"query": "", "customer_id": 123}'
```

## Expected Response Formats

### Success Response - Search Suggestions
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

### Success Response - Save Search
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

### Error Response
```json
{
    "status": false,
    "message": "Query and customer_id are required"
}
```

## Browser Testing

### JavaScript Console Tests
```javascript
// Test Search Suggestions
function testSearchSuggestions() {
    fetch('/apimobile/product/search-suggestions?query=shirt&customer_id=123&limit=10')
        .then(response => response.json())
        .then(data => {
            console.log('Search Suggestions:', data);
            
            if (data.status) {
                console.log('Suggestions:', data.data.suggestions);
                console.log('Recent Searches:', data.data.recent_searches);
                console.log('Popular Searches:', data.data.popular_searches);
            }
        });
}

// Test Save Search Query
function testSaveSearch() {
    fetch('/apimobile/product/save-search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: 'iPhone 15',
            customer_id: 123
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Save Search Result:', data);
    });
}

// Test Auto-suggest
function testAutoSuggest() {
    const queries = ['shirt', 'phone', 'laptop', 'headphones'];
    
    queries.forEach(query => {
        setTimeout(() => {
            fetch(`/apimobile/product/search-suggestions?query=${query}&limit=5`)
                .then(response => response.json())
                .then(data => {
                    console.log(`Suggestions for "${query}":`, data.data.suggestions);
                });
        }, 1000);
    });
}

// Run tests
testSearchSuggestions();
testSaveSearch();
testAutoSuggest();
```

## Mobile App Testing

### Android (Java/Kotlin)
```java
public class SearchSuggestionsTest {
    
    public void testSearchSuggestions() {
        String url = "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&customer_id=123&limit=10";
        
        RequestQueue queue = Volley.newRequestQueue(this);
        JsonObjectRequest request = new JsonObjectRequest(
            Request.Method.GET, url, null,
            response -> {
                try {
                    JSONObject data = response.getJSONObject("data");
                    JSONArray suggestions = data.getJSONArray("suggestions");
                    JSONArray recentSearches = data.getJSONArray("recent_searches");
                    JSONArray popularSearches = data.getJSONArray("popular_searches");
                    
                    Log.d("SearchSuggestions", "Suggestions count: " + suggestions.length());
                    Log.d("SearchSuggestions", "Recent searches count: " + recentSearches.length());
                    Log.d("SearchSuggestions", "Popular searches count: " + popularSearches.length());
                    
                    // Test relevance scoring
                    for (int i = 0; i < suggestions.length(); i++) {
                        JSONObject suggestion = suggestions.getJSONObject(i);
                        int relevance = suggestion.getInt("relevance");
                        String text = suggestion.getString("text");
                        Log.d("SearchSuggestions", "Suggestion: " + text + " (Relevance: " + relevance + ")");
                    }
                    
                } catch (JSONException e) {
                    Log.e("SearchSuggestions", "Error parsing response", e);
                }
            },
            error -> Log.e("SearchSuggestions", "Error: " + error.getMessage())
        );
        
        queue.add(request);
    }
    
    public void testSaveSearch() {
        String url = "https://your-domain.com/apimobile/product/save-search";
        
        JSONObject params = new JSONObject();
        try {
            params.put("query", "iPhone 15");
            params.put("customer_id", 123);
        } catch (JSONException e) {
            Log.e("SaveSearch", "Error creating params", e);
            return;
        }
        
        RequestQueue queue = Volley.newRequestQueue(this);
        JsonObjectRequest request = new JsonObjectRequest(
            Request.Method.POST, url, params,
            response -> {
                try {
                    JSONObject data = response.getJSONObject("data");
                    String query = data.getString("query");
                    int customerId = data.getInt("customer_id");
                    long timestamp = data.getLong("timestamp");
                    
                    Log.d("SaveSearch", "Saved query: " + query + " for customer: " + customerId);
                    Log.d("SaveSearch", "Timestamp: " + new Date(timestamp * 1000));
                    
                } catch (JSONException e) {
                    Log.e("SaveSearch", "Error parsing response", e);
                }
            },
            error -> Log.e("SaveSearch", "Error: " + error.getMessage())
        );
        
        queue.add(request);
    }
}
```

### iOS (Swift)
```swift
class SearchSuggestionsTest {
    
    func testSearchSuggestions() {
        let url = URL(string: "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&customer_id=123&limit=10")!
        
        URLSession.shared.dataTask(with: url) { data, response, error in
            if let data = data {
                do {
                    let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
                    if let data = json?["data"] as? [String: Any] {
                        let suggestions = data["suggestions"] as? [[String: Any]] ?? []
                        let recentSearches = data["recent_searches"] as? [[String: Any]] ?? []
                        let popularSearches = data["popular_searches"] as? [[String: Any]] ?? []
                        
                        print("Suggestions count: \(suggestions.count)")
                        print("Recent searches count: \(recentSearches.count)")
                        print("Popular searches count: \(popularSearches.count)")
                        
                        // Test relevance scoring
                        for suggestion in suggestions {
                            let text = suggestion["text"] as? String ?? ""
                            let relevance = suggestion["relevance"] as? Int ?? 0
                            print("Suggestion: \(text) (Relevance: \(relevance))")
                        }
                    }
                } catch {
                    print("JSON Error: \(error)")
                }
            }
        }.resume()
    }
    
    func testSaveSearch() {
        let url = URL(string: "https://your-domain.com/apimobile/product/save-search")!
        var request = URLRequest(url: url)
        request.httpMethod = "POST"
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        
        let params = [
            "query": "iPhone 15",
            "customer_id": 123
        ]
        
        do {
            request.httpBody = try JSONSerialization.data(withJSONObject: params)
        } catch {
            print("Error creating request body: \(error)")
            return
        }
        
        URLSession.shared.dataTask(with: request) { data, response, error in
            if let data = data {
                do {
                    let json = try JSONSerialization.jsonObject(with: data) as? [String: Any]
                    if let data = json?["data"] as? [String: Any] {
                        let query = data["query"] as? String ?? ""
                        let customerId = data["customer_id"] as? Int ?? 0
                        let timestamp = data["timestamp"] as? TimeInterval ?? 0
                        
                        print("Saved query: \(query) for customer: \(customerId)")
                        print("Timestamp: \(Date(timeIntervalSince1970: timestamp))")
                    }
                } catch {
                    print("JSON Error: \(error)")
                }
            }
        }.resume()
    }
}
```

## Performance Testing

### Load Testing
```bash
# Test multiple concurrent requests
for i in {1..20}; do
    curl "https://your-domain.com/apimobile/product/search-suggestions?query=test$i&limit=10" &
done
wait

# Expected: All requests should complete successfully
```

### Response Time Testing
```bash
# Test response time
time curl "https://your-domain.com/apimobile/product/search-suggestions?query=shirt&limit=20"

# Expected: Response time should be under 500ms
```

## Automated Testing Script

### PHP Test Script
```php
<?php
// search_suggestions_test.php

function testAPI($url, $expectedStatus = true, $description = '') {
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data['status'] === $expectedStatus) {
        echo "✅ PASS: $description\n";
        if (isset($data['data'])) {
            echo "   Data: " . json_encode($data['data'], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ FAIL: $description\n";
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

function testPostAPI($url, $data, $expectedStatus = true, $description = '') {
    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response, true);
    
    if ($result['status'] === $expectedStatus) {
        echo "✅ PASS: $description\n";
        if (isset($result['data'])) {
            echo "   Data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "❌ FAIL: $description\n";
        echo "   Error: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

$baseUrl = "https://your-domain.com";

echo "Testing Search Suggestions APIs...\n\n";

// Search Suggestions Tests
testAPI("$baseUrl/apimobile/product/search-suggestions?query=shirt&limit=10", true, "Basic suggestions");
testAPI("$baseUrl/apimobile/product/search-suggestions?query=shirt&customer_id=123&limit=15", true, "Suggestions with customer ID");
testAPI("$baseUrl/apimobile/product/search-suggestions?customer_id=123", true, "Recent and popular only");
testAPI("$baseUrl/apimobile/product/search-suggestions?query=a", true, "Short query (should return empty suggestions)");
testAPI("$baseUrl/apimobile/product/search-suggestions?query=shirt&limit=100", true, "Excessive limit (should cap at 50)");

// Save Search Tests
testPostAPI("$baseUrl/apimobile/product/save-search", 
    ["query" => "iPhone 15", "customer_id" => 123], 
    true, "Save search query");

testPostAPI("$baseUrl/apimobile/product/save-search", 
    ["customer_id" => 123], 
    false, "Save without query (should fail)");

testPostAPI("$baseUrl/apimobile/product/save-search", 
    ["query" => "iPhone 15"], 
    false, "Save without customer_id (should fail)");

echo "Testing completed!\n";
?>
```

## Test Results Validation

### Checklist
- [ ] Search suggestions return correct format
- [ ] Relevance scoring works correctly
- [ ] Recent searches are returned for logged-in customers
- [ ] Popular searches are returned
- [ ] Save search query works correctly
- [ ] Validation works for required fields
- [ ] Limit capping works (max 50)
- [ ] Short queries don't return suggestions
- [ ] Error handling works correctly
- [ ] Performance is acceptable (< 500ms)

### Common Issues to Check
1. **Relevance scoring accuracy** - Make sure most relevant results appear first
2. **Empty suggestions handling** - Should return empty array, not error
3. **Customer ID validation** - Should work with valid customer IDs
4. **Timestamp format** - Should be Unix timestamp
5. **Response consistency** - Same query should return consistent results
6. **Memory usage** - Should not consume excessive memory with large limits
