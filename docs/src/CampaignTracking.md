# Campaign Tracking System

This document explains how to implement and use the campaign tracking system in the APDE backend.

## Overview

The campaign tracking system provides comprehensive UTM parameter tracking and analytics for marketing campaigns. It tracks visitor interactions, conversions, and provides detailed analytics for campaign performance.

## Architecture

### Components

1. **CampaignTracking Model** - Stores tracking data and visitor interactions
2. **CampaignTrackingService** - Business logic for tracking and analytics
3. **CampaignTrackingController** - API endpoints for tracking and analytics
4. **TrackCampaignVisits Middleware** - Automatic visit tracking
5. **CampaignTrackingResource** - API response formatting

### Database Schema

The `campaign_tracking` collection stores:

- **Basic Info**: campaign_id, landingpage_id, prospect_id, session_id
- **Visitor Info**: ip_address, user_agent, referrer
- **UTM Parameters**: utm_source, utm_medium, utm_campaign, utm_content, utm_term
- **Platform IDs**: gclid (Google), fbclid (Facebook)
- **Tracking Data**: device_type, browser, os, language, etc.
- **Analytics**: visit_count, converted, timestamps

## Usage

### 1. Automatic Visit Tracking

The system automatically tracks visits when users access landing pages with UTM parameters:

```
https://yoursite.com/landing/summer-sale?utm_source=facebook&utm_medium=cpc&utm_campaign=summer_sale
```

### 2. Manual Tracking via API

#### Track a Visit
```http
POST /api/tracking/visit/{landingpageSlug}
```

#### Track a Conversion
```http
POST /api/tracking/conversion
Content-Type: application/json

{
    "prospect_id": "optional-prospect-id"
}
```

### 3. Generate Tracking URLs

```http
POST /api/campaigns/{campaignId}/generate-tracking-url
Content-Type: application/json

{
    "landingpage_id": "landingpage-id",
    "utm_source": "facebook",
    "utm_medium": "cpc",
    "utm_campaign": "summer_sale",
    "utm_content": "banner",
    "utm_term": "discount"
}
```

Response:
```json
{
    "campaign_id": "campaign-id",
    "landingpage_id": "landingpage-id",
    "tracking_url": "https://yoursite.com/landing/summer-sale?utm_source=facebook&utm_medium=cpc&utm_campaign=summer_sale&utm_content=banner&utm_term=discount",
    "utm_parameters": {
        "utm_source": "facebook",
        "utm_medium": "cpc",
        "utm_campaign": "summer_sale",
        "utm_content": "banner",
        "utm_term": "discount"
    }
}
```

### 4. Analytics

#### Campaign Analytics
```http
GET /api/campaigns/{campaignId}/analytics?start_date=2024-01-01&end_date=2024-12-31
```

Response:
```json
{
    "campaign_id": "campaign-id",
    "campaign_title": "Summer Sale Campaign",
    "analytics": {
        "total_visits": 1250,
        "unique_visitors": 980,
        "conversions": 125,
        "conversion_rate": 10.0,
        "utm_source_breakdown": {
            "facebook": 450,
            "google": 300,
            "email": 200,
            "direct": 300
        },
        "daily_visits": {
            "2024-01-01": 45,
            "2024-01-02": 52,
            "2024-01-03": 38
        }
    }
}
```

#### Overall Statistics
```http
GET /api/tracking/stats/overall
```

Response:
```json
{
    "total_visits": 5000,
    "unique_visitors": 3200,
    "total_conversions": 450,
    "overall_conversion_rate": 9.0,
    "top_campaigns": [
        {
            "campaign_id": "campaign-1",
            "campaign_title": "Summer Sale",
            "visit_count": 1250
        }
    ],
    "top_utm_sources": {
        "facebook": 1800,
        "google": 1200,
        "email": 800
    }
}
```

#### Detailed Tracking Data
```http
GET /api/campaigns/{campaignId}/tracking-data
```

## UTM Parameters

### Standard UTM Parameters

- **utm_source** - Traffic source (facebook, google, email, etc.)
- **utm_medium** - Marketing medium (cpc, social, email, banner, etc.)
- **utm_campaign** - Campaign name (summer_sale, holiday_promo, etc.)
- **utm_content** - Content variation (banner, text_link, etc.)
- **utm_term** - Keywords (discount, sale, etc.)

### Platform-Specific Parameters

- **gclid** - Google Click Identifier
- **fbclid** - Facebook Click Identifier

## Implementation Examples

### Frontend Integration

```javascript
// Track conversion when form is submitted
async function trackConversion(prospectId = null) {
    try {
        const response = await fetch('/api/tracking/conversion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ prospect_id: prospectId })
        });
        
        const data = await response.json();
        console.log('Conversion tracked:', data);
    } catch (error) {
        console.error('Failed to track conversion:', error);
    }
}
```

### Email Campaign Integration

```php
// Generate tracking URL for email campaign
$trackingUrl = $trackingService->generateTrackingUrl(
    $campaign,
    $landingpage,
    [
        'utm_source' => 'email',
        'utm_medium' => 'email',
        'utm_campaign' => 'newsletter_january',
        'utm_content' => 'cta_button'
    ]
);

// Use in email template
$emailContent = "Click here: {$trackingUrl}";
```

### Social Media Integration

```php
// Generate tracking URL for Facebook ad
$trackingUrl = $trackingService->generateTrackingUrl(
    $campaign,
    $landingpage,
    [
        'utm_source' => 'facebook',
        'utm_medium' => 'cpc',
        'utm_campaign' => 'summer_sale',
        'utm_content' => 'video_ad'
    ]
);
```

## Best Practices

### 1. UTM Parameter Naming

- Use lowercase with underscores: `summer_sale_2024`
- Be consistent across campaigns
- Keep names descriptive but concise

### 2. Tracking Strategy

- Track all marketing channels consistently
- Use unique campaign names for different initiatives
- Monitor conversion rates by source/medium

### 3. Privacy Compliance

- Respect user privacy preferences
- Implement cookie consent if required
- Anonymize IP addresses if needed

### 4. Performance Optimization

- Use database indexes on frequently queried fields
- Implement caching for analytics data
- Clean up old tracking data periodically

## Troubleshooting

### Common Issues

1. **No tracking data appearing**
   - Check if middleware is properly registered
   - Verify landing page slugs match
   - Check session configuration

2. **Conversion tracking not working**
   - Ensure session ID is consistent
   - Verify prospect_id format
   - Check API authentication

3. **Analytics showing incorrect data**
   - Verify date range parameters
   - Check timezone settings
   - Ensure proper data aggregation

### Debug Mode

Enable debug logging in `config/logging.php`:

```php
'channels' => [
    'campaign_tracking' => [
        'driver' => 'single',
        'path' => storage_path('logs/campaign_tracking.log'),
        'level' => 'debug',
    ],
],
```

## Future Enhancements

1. **Real-time Analytics** - WebSocket-based live updates
2. **A/B Testing** - Built-in split testing capabilities
3. **Attribution Modeling** - Multi-touch attribution
4. **Export Features** - CSV/Excel export of tracking data
5. **Advanced Segmentation** - Custom audience segments
6. **Integration APIs** - Connect with external analytics tools 