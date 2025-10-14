# API Documentation

Call-Survey ULTRALIGHT REST API Reference

## Base URL

```
http://localhost:8000/api
https://yourdomain.com/api
```

## Authentication

Currently, the API does not require authentication. For production use, consider implementing:
- API key authentication
- JWT tokens
- IP whitelist
- Rate limiting

## Endpoints

### 1. Send Survey

Send a survey to a customer via phone call, SMS, or WhatsApp.

**Endpoint:** `POST /api/send-survey`

**Request Body:**
```json
{
  "survey_id": 1,
  "phone_number": "+358401234567",
  "channel": "call"
}
```

**Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| survey_id | integer | Yes | ID of the survey to send |
| phone_number | string | Yes | Phone number in E.164 format (+358401234567) |
| channel | string | Yes | Delivery channel: `call`, `sms`, or `whatsapp` |

**Response Success (200):**
```json
{
  "success": true,
  "sid": "CAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Response Error (400/404/500):**
```json
{
  "success": false,
  "error": "Error message"
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/send-survey \
  -H "Content-Type: application/json" \
  -d '{
    "survey_id": 1,
    "phone_number": "+358401234567",
    "channel": "call"
  }'
```

**Channel Details:**

- **call**: Initiates an automated voice call using Twilio IVR
- **sms**: Sends an SMS with a link to the survey
- **whatsapp**: Sends a WhatsApp message with survey link

---

### 2. Get Survey Statistics

Retrieve statistics for a specific survey.

**Endpoint:** `GET /api/survey-stats`

**Query Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Survey ID |

**Response (200):**
```json
{
  "survey_id": 1,
  "total_responses": 45,
  "completed_responses": 38,
  "questions": 3
}
```

**Example:**
```bash
curl http://localhost:8000/api/survey-stats?id=1
```

---

## Webhooks

Twilio uses webhooks to communicate with your application during calls and messages.

### Voice Webhook

**Endpoint:** `POST /webhook/voice.php?survey_id={id}`

**Called by:** Twilio Voice API

**Purpose:** Handle interactive voice response (IVR) for phone surveys

**Flow:**
1. Twilio calls the webhook when call starts
2. Webhook returns TwiML with first question
3. User presses keys (DTMF) to answer
4. Webhook saves answer and returns next question
5. Repeats until all questions answered
6. Call ends with thank you message

**TwiML Response Example:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <Gather numDigits="1" action="/webhook/voice.php?survey_id=1" method="POST">
    <Say language="fi-FI">Miten tyytyväinen olit palveluumme? Paina numero yhdestä viiteen.</Say>
  </Gather>
  <Say language="fi-FI">Emme saaneet vastaustasi. Hyvästä.</Say>
</Response>
```

**Configure in Twilio Console:**
```
Voice & Fax > A Call Comes In
Webhook URL: https://yourdomain.com/webhook/voice.php?survey_id=1
HTTP Method: POST
```

---

## Error Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 400 | Bad Request - Invalid parameters |
| 404 | Not Found - Resource doesn't exist |
| 405 | Method Not Allowed - Wrong HTTP method |
| 500 | Internal Server Error |

## Rate Limits

Currently no rate limits are enforced. Consider implementing:
- Per-IP rate limiting
- Per-API-key quotas
- Twilio's rate limits apply

## Data Formats

### Phone Numbers

Always use E.164 format:
- ✅ `+358401234567` (Correct)
- ❌ `0401234567` (Wrong)
- ❌ `+358 40 123 4567` (Wrong - no spaces)

### Dates

ISO 8601 format: `2025-10-14T13:06:19Z`

### Response Formats

All API responses are JSON with UTF-8 encoding.

## PHP SDK Usage

### Send Survey via Call

```php
require_once 'vendor/autoload.php';

$config = require 'config.php';
$twilio = new \CallSurvey\TwilioService($config['twilio']);

$result = $twilio->makeCall(
    '+358401234567',
    'https://yourdomain.com/webhook/voice.php?survey_id=1'
);

if ($result['success']) {
    echo "Call initiated: " . $result['sid'];
} else {
    echo "Error: " . $result['error'];
}
```

### Send Survey via SMS

```php
$result = $twilio->sendSMS(
    '+358401234567',
    'Please take our survey: https://yourdomain.com/survey/respond.php?id=1'
);
```

### Create Survey

```php
$db = \CallSurvey\Database::getInstance($config['database']);
$survey = new \CallSurvey\Survey($db);

// Create survey
$surveyId = $survey->create('Customer Satisfaction', 'Tell us about your experience');

// Add questions
$survey->addQuestion($surveyId, 'How satisfied are you? (1-5)', 'rating', 1);
$survey->addQuestion($surveyId, 'Would you recommend us?', 'yesno', 2);
```

### Get Results

```php
$results = $survey->getResults($surveyId);
foreach ($results as $response) {
    echo "Response from {$response['phone_number']}: {$response['status']}\n";
}
```

## JavaScript/AJAX Examples

### Send Survey

```javascript
fetch('/api/send-survey', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    survey_id: 1,
    phone_number: '+358401234567',
    channel: 'call'
  })
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Survey sent:', data.sid);
  } else {
    console.error('Error:', data.error);
  }
});
```

### Get Statistics

```javascript
fetch('/api/survey-stats?id=1')
  .then(response => response.json())
  .then(data => {
    console.log('Total responses:', data.total_responses);
    console.log('Completion rate:', 
      (data.completed_responses / data.total_responses * 100) + '%'
    );
  });
```

## Postman Collection

Import this collection to test the API:

```json
{
  "info": {
    "name": "Call-Survey ULTRALIGHT",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Send Survey",
      "request": {
        "method": "POST",
        "header": [{"key": "Content-Type", "value": "application/json"}],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"survey_id\": 1,\n  \"phone_number\": \"+358401234567\",\n  \"channel\": \"call\"\n}"
        },
        "url": "{{base_url}}/api/send-survey"
      }
    },
    {
      "name": "Get Survey Stats",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/api/survey-stats?id=1"
      }
    }
  ]
}
```

## Best Practices

1. **Always validate phone numbers** before sending
2. **Use HTTPS** in production
3. **Handle errors gracefully** in your application
4. **Log API calls** for debugging
5. **Test with Twilio test credentials** first
6. **Respect rate limits** and implement retries
7. **Monitor webhook responses** in Twilio console

## Support

- Check Twilio logs for webhook debugging
- Enable error logging in PHP
- Use `test.php` to validate setup
- Review CONTRIBUTING.md for development guidelines

## Future API Endpoints

Planned for future releases:
- `GET /api/surveys` - List all surveys
- `POST /api/surveys` - Create survey via API
- `GET /api/survey/{id}/responses` - Get detailed responses
- `DELETE /api/response/{id}` - Delete response (GDPR)
- `POST /api/webhook/sms` - SMS response handler
- `POST /api/webhook/whatsapp` - WhatsApp handler
