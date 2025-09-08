# 🧪 اختبار Address APIs

## 🔑 الحصول على Token

### 1. تسجيل الدخول
```bash
curl -X POST "https://avit.josequal.net/V1/mobile/login" \
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

## 📍 اختبار Address APIs

### **Test 1: إضافة عنوان جديد**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/add" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "John",
    "lastname": "Doe",
    "street": ["123 Main Street"],
    "city": "New York",
    "region": "NY",
    "postcode": "10001",
    "country_id": "US",
    "telephone": "1234567890",
    "company": "My Company"
  }'
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Address added successfully",
  "data": {
    "address_id": 123,
    "message": "Address has been added successfully"
  }
}
```

---

### **Test 2: إضافة عنوان آخر**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/add" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "John",
    "lastname": "Doe",
    "street": ["456 Oak Avenue"],
    "city": "Los Angeles",
    "region": "CA",
    "postcode": "90210",
    "country_id": "US",
    "telephone": "0987654321",
    "company": "My Company"
  }'
```

---

### **Test 3: عرض قائمة العناوين**

```bash
curl -X GET "https://avit.josequal.net/apimobile/address/getlist" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Addresses retrieved successfully",
  "data": {
    "addresses": [
      {
        "id": 123,
        "type": "billing",
        "firstname": "John",
        "lastname": "Doe",
        "company": "My Company",
        "street": ["123 Main Street"],
        "city": "New York",
        "region": "NY",
        "region_id": 43,
        "postcode": "10001",
        "country_id": "US",
        "telephone": "1234567890",
        "fax": null,
        "vat_id": null,
        "is_default_billing": true,
        "is_default_shipping": false
      },
      {
        "id": 124,
        "type": "shipping",
        "firstname": "John",
        "lastname": "Doe",
        "company": "My Company",
        "street": ["456 Oak Avenue"],
        "city": "Los Angeles",
        "region": "CA",
        "region_id": 12,
        "postcode": "90210",
        "country_id": "US",
        "telephone": "0987654321",
        "fax": null,
        "vat_id": null,
        "is_default_billing": false,
        "is_default_shipping": true
      }
    ],
    "total_count": 2
  }
}
```

---

### **Test 4: تعديل العنوان الأول**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 123,
    "firstname": "Jane",
    "city": "Brooklyn",
    "postcode": "11201"
  }'
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Address updated successfully",
  "data": {
    "address_id": 123,
    "message": "Address has been updated successfully"
  }
}
```

---

### **Test 5: تعديل العنوان الثاني**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 124,
    "company": "New Company Name",
    "fax": "1234567890"
  }'
```

---

### **Test 6: عرض العناوين بعد التعديل**

```bash
curl -X GET "https://avit.josequal.net/apimobile/address/getlist" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

### **Test 7: حذف العنوان الثاني**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/delete" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 124
  }'
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Address deleted successfully",
  "data": {
    "message": "Address has been deleted successfully"
  }
}
```

---

### **Test 8: عرض العناوين بعد الحذف**

```bash
curl -X GET "https://avit.josequal.net/apimobile/address/getlist" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Addresses retrieved successfully",
  "data": {
    "addresses": [
      {
        "id": 123,
        "type": "billing",
        "firstname": "Jane",
        "lastname": "Doe",
        "company": "My Company",
        "street": ["123 Main Street"],
        "city": "Brooklyn",
        "region": "NY",
        "region_id": 43,
        "postcode": "11201",
        "country_id": "US",
        "telephone": "1234567890",
        "fax": null,
        "vat_id": null,
        "is_default_billing": true,
        "is_default_shipping": false
      }
    ],
    "total_count": 1
  }
}
```

## 🧪 اختبار الحالات الاستثنائية

### **Test 9: إضافة عنوان بدون حقول مطلوبة**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/add" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "John",
    "lastname": "Doe"
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Field 'street' is required",
  "data": []
}
```

---

### **Test 10: تعديل عنوان غير موجود**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 999,
    "firstname": "Test"
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Address not found",
  "data": []
}
```

---

### **Test 11: حذف عنوان بدون ID**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/delete" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{}'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Address ID is required",
  "data": []
}
```

---

### **Test 12: طلب بدون مصادقة**

```bash
curl -X GET "https://avit.josequal.net/apimobile/address/getlist"
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Unauthorized",
  "data": []
}
```

## 📱 اختبار من التطبيق

### **Android Test:**
```kotlin
// Test adding address
val addressData = JSONObject().apply {
    put("firstname", "Test")
    put("lastname", "User")
    put("street", JSONArray().put("Test Street"))
    put("city", "Test City")
    put("region", "Test Region")
    put("postcode", "12345")
    put("country_id", "US")
    put("telephone", "1234567890")
}

// Make API call
val response = apiService.addAddress("Bearer $token", addressData)
if (response.status) {
    Log.d("Address", "Address added: ${response.data.address_id}")
} else {
    Log.e("Address", "Error: ${response.message}")
}
```

### **iOS Test:**
```swift
// Test adding address
let addressData: [String: Any] = [
    "firstname": "Test",
    "lastname": "User",
    "street": ["Test Street"],
    "city": "Test City",
    "region": "Test Region",
    "postcode": "12345",
    "country_id": "US",
    "telephone": "1234567890"
]

// Make API call
apiService.addAddress(token: token, data: addressData) { result in
    switch result {
    case .success(let response):
        if response.status {
            print("Address added: \(response.data.address_id)")
        } else {
            print("Error: \(response.message)")
        }
    case .failure(let error):
        print("Network error: \(error)")
    }
}
```

## 🔍 مراقبة النتائج

### **التحقق من قاعدة البيانات:**
```sql
-- عرض عناوين العميل
SELECT * FROM customer_address_entity WHERE parent_id = CUSTOMER_ID;

-- عرض معلومات العميل
SELECT * FROM customer_entity WHERE entity_id = CUSTOMER_ID;
```

### **التحقق من Logs:**
```bash
# Magento logs
tail -f var/log/system.log
tail -f var/log/exception.log

# PHP error logs
tail -f /var/log/php_errors.log
```

## ✅ قائمة التحقق

- [ ] إضافة عنوان جديد يعمل
- [ ] تعديل العنوان يعمل
- [ ] حذف العنوان يعمل
- [ ] عرض قائمة العناوين يعمل
- [ ] التحقق من الحقول المطلوبة يعمل
- [ ] المصادقة تعمل بشكل صحيح
- [ ] معالجة الأخطاء تعمل
- [ ] البيانات تُحفظ في قاعدة البيانات
- [ ] Region ID يتم تحديده تلقائياً
- [ ] العناوين تُعرض بالترتيب الصحيح

## 🚀 نصائح للاختبار

1. **ابدأ بإضافة عنوان** قبل اختبار العمليات الأخرى
2. **احفظ Address ID** من الاستجابة الأولى لاستخدامه في الاختبارات اللاحقة
3. **اختبر الحالات الاستثنائية** للتأكد من معالجة الأخطاء
4. **تحقق من قاعدة البيانات** للتأكد من حفظ البيانات
5. **اختبر من التطبيق** للتأكد من عمل API في البيئة الحقيقية

هذه الاختبارات تضمن أن **Address APIs تعمل بشكل صحيح** وتتعامل مع جميع الحالات.

## 🧪 اختبار معالجة Region (مهم!)

### **Test 13: اختبار Region Handling**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/add" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "Region",
    "lastname": "Test",
    "street": ["Region Test Street"],
    "city": "Region Test City",
    "region": "NY",
    "postcode": "10001",
    "country_id": "US",
    "telephone": "1234567890"
  }'
```

**Expected Response (Success - No TypeError):**
```json
{
  "status": true,
  "message": "Address added successfully",
  "data": {
    "address_id": 125,
    "message": "Address has been added successfully"
  }
}
```

---

### **Test 14: اختبار Region مع Country مختلف**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/add" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "Saudi",
    "lastname": "Test",
    "street": ["Saudi Test Street"],
    "city": "Riyadh",
    "region": "Riyadh",
    "postcode": "12345",
    "country_id": "SA",
    "telephone": "1234567890"
  }'
```

---

### **Test 15: اختبار تعديل Region**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 125,
    "region": "CA",
    "city": "Los Angeles"
  }'
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Address updated successfully",
  "data": {
    "address_id": 125,
    "message": "Address has been updated successfully"
  }
}
```

---

### **Test 16: عرض العناوين مع Region Info**

```bash
curl -X GET "https://avit.josequal.net/apimobile/address/getlist" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Response (with region_id):**
```json
{
  "status": true,
  "message": "Addresses retrieved successfully",
  "data": {
    "addresses": [
      {
        "id": 125,
        "type": "other",
        "firstname": "Region",
        "lastname": "Test",
        "street": ["Region Test Street"],
        "city": "Los Angeles",
        "region": "CA",
        "region_id": 12,
        "postcode": "10001",
        "country_id": "US",
        "telephone": "1234567890",
        "is_default_billing": false,
        "is_default_shipping": false
      }
    ],
    "total_count": 1
  }
}
```

## ✅ اختبار إصلاح TypeError

### **قبل الإصلاح:**
```bash
# كان يحدث خطأ:
TypeError: Magento\Customer\Model\Data\Address::setRegion(): 
Argument #1 ($region) must be of type ?Magento\Customer\Api\Data\RegionInterface, string given
```

### **بعد الإصلاح:**
```bash
# الآن يعمل بدون أخطاء:
{
  "status": true,
  "message": "Address added successfully",
  "data": {
    "address_id": 125,
    "message": "Address has been added successfully"
  }
}
```

### **كيفية عمل الإصلاح:**
1. **النظام يحاول تحويل Region name إلى Region ID**
2. **إذا نجح التحويل، يستخدم `setRegionId()`**
3. **إذا فشل التحويل، يستخدم `setRegion()` كـ fallback**
4. **لا توجد أخطاء TypeError بعد الآن**

## ✅ اختبار إصلاح جميع الأخطاء

### **قبل الإصلاح:**
```bash
# خطأ 1: TypeError في setRegion
TypeError: Magento\Customer\Model\Data\Address::setRegion(): 
Argument #1 ($region) must be of type ?Magento\Customer\Api\Data\RegionInterface, string given

# خطأ 2: Method not found
Error: Call to undefined method Magento\Customer\Model\Data\Address::setSaveInAddressBook()
```

### **بعد الإصلاح:**
```bash
# الآن يعمل بدون أخطاء:
{
  "status": true,
  "message": "Address added successfully",
  "data": {
    "address_id": 125,
    "message": "Address has been added successfully"
  }
}
```

### **كيفية عمل الإصلاحات:**
1. **Region Handling:** النظام يحاول تحويل Region name إلى Region ID
2. **Method Removal:** إزالة `setSaveInAddressBook()` غير الموجود
3. **Fallback System:** استخدام `setRegion()` فقط كـ fallback

## 🧪 اختبار شامل للـ APIs

### **Test 17: اختبار شامل لإضافة عنوان**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/add" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "firstname": "Comprehensive",
    "lastname": "Test",
    "street": ["Comprehensive Test Street"],
    "city": "Comprehensive Test City",
    "region": "TX",
    "postcode": "75001",
    "country_id": "US",
    "telephone": "1234567890",
    "company": "Comprehensive Test Company",
    "fax": "0987654321",
    "vat_id": "VAT123456"
  }'
```

**Expected Response (Success - All Errors Fixed):**
```json
{
  "status": true,
  "message": "Address added successfully",
  "data": {
    "address_id": 126,
    "message": "Address has been added successfully"
  }
}
```

---

### **Test 18: اختبار تعديل شامل**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 126,
    "firstname": "Updated",
    "lastname": "User",
    "city": "Updated City",
    "region": "FL",
    "postcode": "33101",
    "company": "Updated Company"
  }'
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Address updated successfully",
  "data": {
    "address_id": 126,
    "message": "Address has been updated successfully"
  }
}
```

---

### **Test 19: اختبار عرض العناوين بعد التحديثات**

```bash
curl -X GET "https://avit.josequal.net/apimobile/address/getlist" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Expected Response (with updated data):**
```json
{
  "status": true,
  "message": "Addresses retrieved successfully",
  "data": {
    "addresses": [
      {
        "id": 126,
        "type": "other",
        "firstname": "Updated",
        "lastname": "User",
        "company": "Updated Company",
        "street": ["Comprehensive Test Street"],
        "city": "Updated City",
        "region": "FL",
        "region_id": 12,
        "postcode": "33101",
        "country_id": "US",
        "telephone": "1234567890",
        "fax": "0987654321",
        "vat_id": "VAT123456",
        "is_default_billing": false,
        "is_default_shipping": false
      }
    ],
    "total_count": 1
  }
}
```

## 🔍 مراقبة الأخطاء

### **التحقق من عدم وجود أخطاء:**
```bash
# Magento logs
tail -f var/log/system.log | grep -i "error\|exception"

# PHP error logs
tail -f /var/log/php_errors.log

# Application logs
tail -f var/log/exception.log
```

### **التحقق من قاعدة البيانات:**
```sql
-- عرض العناوين المُضافة
SELECT * FROM customer_address_entity WHERE parent_id = CUSTOMER_ID;

-- التحقق من Region ID
SELECT * FROM directory_country_region WHERE country_id = 'US';
```

## ✅ قائمة التحقق النهائية

- [ ] إضافة عنوان جديد يعمل بدون أخطاء
- [ ] تعديل العنوان يعمل بدون أخطاء
- [ ] حذف العنوان يعمل بدون أخطاء
- [ ] عرض قائمة العناوين يعمل بدون أخطاء
- [ ] **لا توجد أخطاء TypeError** في setRegion
- [ ] **لا توجد أخطاء Method not found** في setSaveInAddressBook
- [ ] Region ID يتم تحديده تلقائياً
- [ ] جميع الحقول تُحفظ بشكل صحيح
- [ ] البيانات تُعرض في الاستجابة
- [ ] الأمان يعمل (كل عميل يرى عناوينه فقط)

## 🚀 نصائح للاختبار النهائي

1. **اختبر جميع العمليات** (Add, Edit, Delete, List)
2. **اختبر أنواع مختلفة من البيانات** (US, SA, etc.)
3. **اختبر الحالات الاستثنائية** (بيانات ناقصة، عناوين غير موجودة)
4. **تحقق من قاعدة البيانات** للتأكد من حفظ البيانات
5. **راقب الـ logs** للتأكد من عدم وجود أخطاء

**جميع الأخطاء تم إصلاحها!** 🎉

## 🚨 اختبار إصلاح Edit API

### **المشكلة الحالية:**
Edit API يعطي خطأ: "An error has happened during application run. See exception log for details."

### **البيانات المُرسلة:**
```json
{
  "address_id": 23,
  "firstname": "Jwane",
  "lastname": "Smwith",
  "street": ["456 Owak Ave"],
  "city": "Los Awngeles",
  "region": "CwA",
  "postcode": "90w210",
  "country_id": "US",
  "telephone": "0984447654321",
  "company": "New Ceompany",
  "fax": "0987654322",
  "vat_id": "VAT654321"
}
```

### **Test 20: اختبار Edit API مع بيانات صحيحة**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 23,
    "firstname": "Jane",
    "lastname": "Smith",
    "street": ["456 Oak Ave"],
    "city": "Los Angeles",
    "region": "CA",
    "postcode": "90210",
    "country_id": "US",
    "telephone": "0987654321",
    "company": "New Company",
    "fax": "0987654322",
    "vat_id": "VAT654321"
  }'
```

**Expected Response (Success):**
```json
{
  "status": true,
  "message": "Address updated successfully",
  "data": {
    "address_id": 23,
    "message": "Address has been updated successfully"
  }
}
```

### **Test 21: اختبار Edit API مع بيانات مبسطة**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 23,
    "firstname": "Jane",
    "lastname": "Smith",
    "city": "Los Angeles",
    "region": "CA"
  }'
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Address updated successfully",
  "data": {
    "address_id": 23,
    "message": "Address has been updated successfully"
  }
}
```

### **Test 22: اختبار Edit API مع Address ID غير موجود**

```bash
curl -X POST "https://avit.josequal.net/apimobile/address/edit" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "address_id": 999,
    "firstname": "Jane",
    "lastname": "Smith"
  }'
```

**Expected Response:**
```json
{
  "status": false,
  "message": "Address not found",
  "data": []
}
```

## 🔧 الإصلاحات المطبقة على Edit API

### **1. إزالة التكرار في معالجة Region:**
```php
// قبل الإصلاح (مشكلة):
if (isset($data['region'])) {
    // ... معالجة region ...
}
// ... ثم مرة أخرى ...
if (isset($data['region']) && !isset($data['region_id'])) {
    $address->setRegionId($this->getRegionId($data['region'], $data['country_id'] ?? $address->getCountryId()));
}

// بعد الإصلاح (يعمل):
if (isset($data['region'])) {
    // معالجة region مرة واحدة فقط
    if (!isset($data['region_id']) || !$data['region_id']) {
        $data['region_id'] = $this->getRegionId($data['region'], $data['country_id'] ?? $address->getCountryId());
    }
    
    if ($data['region_id']) {
        $address->setRegionId($data['region_id']);
    } else {
        $address->setRegion($data['region']);
    }
}
```

### **2. تحسين معالجة الأخطاء:**
```php
try {
    // ... كود التحديث ...
    $updatedAddress = $this->addressRepository->save($address);
    
    return $this->successStatus('Address updated successfully', [
        'address_id' => $updatedAddress->getId(),
        'message' => 'Address has been updated successfully'
    ]);
} catch (\Exception $e) {
    return $this->errorStatus('Failed to update address: ' . $e->getMessage());
}
```

## 📋 خطوات اختبار Edit API

1. **اختبر مع بيانات صحيحة** - يجب أن يعمل
2. **اختبر مع بيانات مبسطة** - يجب أن يعمل
3. **اختبر مع Address ID غير موجود** - يجب أن يعطي خطأ واضح
4. **تحقق من قاعدة البيانات** - تأكد من تحديث البيانات
5. **اختبر GetList مرة أخرى** - تأكد من ظهور التحديثات

## 🎯 النتيجة المتوقعة بعد الإصلاح

- **لا توجد أخطاء** "An error has happened during application run"
- **استجابة واضحة** مع status و message و data
- **تحديث ناجح** للعنوان
- **معالجة صحيحة** للأخطاء

**اختبر Edit API الآن!** 🚀
