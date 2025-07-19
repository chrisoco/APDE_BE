# Campaign Analytics API

## Endpoint

`GET /api/campaigns/{campaign}/analytics`

- **Example:** `/api/campaigns/686a3affca7748f6b807cbee/analytics`

## Description

Returns analytics data for a specific campaign. This endpoint provides aggregated statistics and insights about the campaign's performance, such as visits, conversions, and other tracked metrics.

## Authorization

- Requires authentication.
- User must have permission to view analytics for the specified campaign (policy: `viewAnalytics`).

## Request

- **Method:** `GET`
- **URL Params:**
  - `campaign` (string, required): The campaign's unique identifier (UUID).

## Response

- **Status:** `200 OK`
- **Content:** JSON object containing analytics data for the campaign.

## Errors

- `403 Forbidden`: If the user is not authorized to view analytics for the campaign.
- `404 Not Found`: If the campaign does not exist.

## Example Request

```
curl -X GET \
  -H "Authorization: Bearer <token>" \
  {{URL}}/api/campaigns/686a3affca7748f6b807cbee/analytics
```

## Example Response

```
{
    "campaign_overview": {
        "campaign_id": "686a3affca7748f6b807cbee",
        "campaign_title": "Ut reiciendis consequatur dolore.",
        "status": "completed",
        "start_date": "2025-08-04T13:02:20.000000Z",
        "end_date": "2025-08-18T00:20:20.000000Z"
    },
    "visits": {
        "total": 10,
        "unique_ip": 10,
        "total_unique": 10
    },
    "statistics": {
        "total_prospects_notified": 11,
        "unique_prospect_visits": 4,
        "email_cta_click_rate": 36.36
    },
    "device_browser_breakdown": {
        "device_types": {
            "desktop": 5,
            "tablet": 2,
            "mobile": 3
        },
        "browsers": {
            "Edge": 2,
            "Safari": 2,
            "Chrome": 4,
            "Firefox": 2
        },
        "operating_systems": {
            "Windows": 1,
            "Android": 3,
            "macOS": 2,
            "Linux": 1,
            "iOS": 3
        },
        "languages": {
            "it": 3,
            "en": 1,
            "fr": 2,
            "de": 4
        }
    },
    "utm_sources": {
        "source": {
            "facebook": 2,
            "google": 1,
            "direct": 3,
            "linkedin": 3,
            "twitter": 1
        },
        "medium": {
            "email": 2,
            "affiliate": 4,
            "banner": 3,
            "organic": 1
        }
    }
}
```

## Response Structure

### Campaign Overview
- `campaign_id`: The unique identifier of the campaign
- `campaign_title`: The title of the campaign
- `status`: Current status of the campaign (draft, active, paused, completed)
- `start_date`: Campaign start date in ISO format
- `end_date`: Campaign end date in ISO format

### Visits
- `total`: Total number of page visits
- `unique_ip`: Number of unique IP addresses that visited
- `total_unique`: Number of unique visitors (deduplicated by IP + user agent)

### Statistics
- `total_prospects_notified`: Total number of prospects that have been sent emails for this campaign
- `unique_prospect_visits`: Number of unique prospects who clicked through to the landing page
- `email_cta_click_rate`: Click-through rate percentage (calculated as: unique_prospect_visits / total_prospects_notified × 100)

### Device & Browser Breakdown
- `device_types`: Distribution of device types (desktop, mobile, tablet)
- `browsers`: Browser usage statistics
- `operating_systems`: Operating system distribution
- `languages`: Language preferences of visitors

### UTM Sources
- `source`: Traffic sources breakdown
- `medium`: Marketing medium breakdown