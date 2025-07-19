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
        "emails_sent": 11,
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