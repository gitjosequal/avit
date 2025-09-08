# Points API 404 Error Troubleshooting Guide

## Problem
You're getting a 404 Not Found error when trying to access the points API endpoints:
- `https://avit.josequal.net/apimobile/cart/apply-points`
- `https://avit.josequal.net/apimobile/cart/points`
- `https://avit.josequal.net/apimobile/cart/points-reminder`

## Root Cause
The 404 error indicates that Magento hasn't recognized the new API routes. This typically happens when:
1. Magento setup commands haven't been run after adding new API routes
2. Required PHP extensions are missing
3. Cache hasn't been cleared

## Solution Steps

### Step 1: Enable Required PHP Extensions
Your system is missing these PHP extensions:
- `ext-intl` (Internationalization)
- `ext-soap` (SOAP) 
- `ext-sockets` (Sockets)

**To enable them:**
1. Edit your `php.ini` file
2. Uncomment or add these lines:
   ```ini
   extension=intl
   extension=soap
   extension=sockets
   ```
3. Restart your web server (Apache/Nginx)

### Step 2: Run Magento Setup Commands
After enabling the extensions, run these commands in your Magento root directory:

```bash
# Install dependencies
composer install

# Update database schema
php bin/magento setup:upgrade

# Compile dependency injection
php bin/magento setup:di:compile

# Clear all caches
php bin/magento cache:flush

# Reindex if needed
php bin/magento indexer:reindex
```

### Step 3: Test the API
I've created a simple test endpoint to verify the API routing is working:

**Test URL:** `https://avit.josequal.net/apimobile/cart/test-points`

This endpoint should return:
```json
{
    "status": true,
    "message": "Points API is working!",
    "data": {
        "test": true,
        "timestamp": "2024-01-XX XX:XX:XX",
        "endpoint": "/apimobile/cart/test-points"
    }
}
```

### Step 4: Verify Web Server Configuration
Make sure your web server (Apache/Nginx) is properly configured to handle Magento's rewrite rules:

**For Apache (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* index.php [L]
```

**For Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$args;
}
```

## Alternative Solution (If Extensions Can't Be Enabled)

If you can't enable the PHP extensions immediately, you can try this workaround:

1. **Manual Cache Clear:**
   - Delete the `var/cache` directory
   - Delete the `var/page_cache` directory
   - Delete the `var/view_preprocessed` directory

2. **Check Module Status:**
   - Verify the module is enabled in `app/etc/config.php`
   - Look for: `'Josequal_APIMobile' => 1`

3. **Verify File Permissions:**
   - Ensure all files have correct permissions (644 for files, 755 for directories)

## Testing the Points API

Once the setup is complete, you can test the points API endpoints:

### 1. Get Customer Points
```bash
curl -X GET "https://avit.josequal.net/apimobile/cart/points" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Apply Points to Cart
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/apply-points" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"points": 100, "apply": true}'
```

### 3. Send Points Reminder
```bash
curl -X POST "https://avit.josequal.net/apimobile/cart/points-reminder" \
  -H "Content-Type: application/json" \
  -d '{"customer_id": 123}'
```

## Expected Responses

### Get Customer Points Response:
```json
{
    "status": true,
    "message": "Points Total",
    "data": {
        "points": 1500,
        "customer_name": "John Doe",
        "loyalty_level": "Gold",
        "next_level_points": 2000,
        "points_to_next_level": 500,
        "progress_percentage": 75,
        "points_history": [
            {
                "id": 1,
                "points_amount": 100,
                "action_type": "purchase",
                "action_description": "Earned points from purchase",
                "created_at": "2024-01-01 10:00:00",
                "icon": "🛒"
            }
        ],
        "system_type": "custom"
    }
}
```

### Apply Points Response:
```json
{
    "status": true,
    "message": "Points applied successfully",
    "data": {
        "points_used": 100,
        "points_remaining": 1400,
        "cart_total": 50.00,
        "discount_applied": 10.00
    }
}
```

## Common Issues and Solutions

1. **404 Error Persists:**
   - Check if the module is properly enabled
   - Verify webapi.xml syntax
   - Ensure all controller files exist

2. **500 Internal Server Error:**
   - Check Magento logs in `var/log/`
   - Verify PHP error logs
   - Ensure all dependencies are installed

3. **Authentication Issues:**
   - Verify token format (Bearer token)
   - Check if customer exists and is active
   - Ensure proper authorization headers

## Next Steps

1. Enable the required PHP extensions
2. Run the Magento setup commands
3. Test the simple test endpoint first
4. Then test the actual points API endpoints
5. Check Magento logs for any errors

If you continue to have issues after following these steps, please share:
- The exact error message from Magento logs
- Your web server configuration
- PHP version and enabled extensions
