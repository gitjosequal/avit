# تعليمات التحديث التلقائي لـ API حذف الحساب

## ✅ تم إعداد التحديث التلقائي

الآن يمكنك تحديث الوحدة تلقائياً وسيتم إنشاء جدول قاعدة البيانات تلقائياً!

## 🚀 خطوات التحديث:

### 1. تحديث الوحدة
```bash
php bin/magento setup:upgrade
```

### 2. مسح Cache
```bash
php bin/magento cache:clean
php bin/magento cache:flush
```

### 3. اختبار API
```bash
curl -X POST "https://avit.josequal.net/rest/V1/user/request-account-deletion" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"reason": "test"}'
```

## 📋 ما تم إعداده:

### ملفات Schema:
- ✅ `etc/db_schema.xml` - تعريف الجدول
- ✅ `etc/db_schema_whitelist.json` - قائمة البيضاء للجدول
- ✅ `etc/crontab.xml` - Cron Job للحذف التلقائي

### ملفات API:
- ✅ `Api/V1/AccountDeletionInterface.php` - واجهة API
- ✅ `Model/AccountDeletion.php` - Model للـ API
- ✅ `Service/AccountDeletionService.php` - منطق الأعمال

## 🔄 كيفية عمل التحديث:

1. **`setup:upgrade`** يقرأ `db_schema.xml`
2. **يقارن** مع قاعدة البيانات الحالية
3. **ينشئ** الجداول المفقودة تلقائياً
4. **يحدث** الجداول الموجودة إذا لزم الأمر

## 📊 هيكل الجدول الذي سيتم إنشاؤه:

```sql
customer_account_deletion
├── entity_id (Primary Key)
├── customer_id (Foreign Key)
├── deletion_requested_at (Timestamp)
├── scheduled_deletion_at (Timestamp)
├── status (1=Pending, 2=Cancelled, 3=Completed)
├── reason (Text)
├── cancelled_at (Timestamp)
└── deleted_at (Timestamp)
```

## 🎯 الميزات:

- **إنشاء تلقائي** للجدول
- **فترة انتظار 90 يوم** قبل الحذف
- **تعطيل الحساب** خلال فترة الانتظار
- **إمكانية الاسترجاع** في أي وقت
- **حذف تلقائي** عبر Cron Job
- **أمان كامل** مع Bearer Token

## ⚠️ ملاحظات مهمة:

- تأكد من وجود **Token صحيح** من تسجيل الدخول
- تأكد من أن جدول `customer_entity` موجود
- إذا استمرت المشكلة، راجع سجلات الخطأ في `var/log/`

## 🎉 النتيجة:

بعد تشغيل `setup:upgrade`، سيعمل API حذف الحساب تلقائياً بدون أي تدخل يدوي!
