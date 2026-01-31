# FacebookAudienceBundle

Symfony bundle for managing Facebook Custom Audiences using the Facebook Business SDK.

## Requirements

- PHP >= 7.2 (compatible with PHP 7.4 and PHP 8.x)
- Symfony ^4.4 or ^5.0
- Facebook Business SDK ^15.0

## Installation

```bash
composer require progrupa/facebook-audience-bundle
```

## Configuration

Configure the bundle in your `config/packages/progrupa_facebook_audience.yaml`:

```yaml
progrupa_facebook_audience:
    client_id: 'your-facebook-app-id'
    client_secret: 'your-facebook-app-secret'
    marketing_token: 'your-facebook-marketing-token'
    business_id: 'your-business-account-id'
```

## Usage

The bundle provides services for exporting user data to Facebook Custom Audiences.

### Using the AudienceExporter

```php
use Progrupa\FacebookAudienceBundle\Exporter\AudienceExporter;

// Inject the service
public function __construct(AudienceExporter $exporter) {
    $this->exporter = $exporter;
}

// Export emails to an audience
$emails = ['email1@example.com', 'email2@example.com'];
$this->exporter->exportAudience('My Audience Name', $emails, 'EMAIL');
```

## Version History

- **v2.0**: Updated to Facebook Business SDK v15.0.3, requires PHP >= 7.2 (compatible with PHP 7.4 and 8.x)
- **v1.x**: Used Facebook Business SDK v9+, required PHP >= 7.2