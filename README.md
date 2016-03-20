# URI-Template module for ZF2

## Installation

### Configure Composer
```
$ composer require "final-gene/uri-template-module"
```

### Load module
Add `FinalGene\UriTemplateModule` to your project's application-config `modules` key

## Usage

### Retrieve the service from the service manager

```php
<?php
$uriTemplateService = $serviceManager->get('FinalGene\UriTemplateModule\UriTemplateService')
```

### Use the UriTemplateService via it's public methods

#### getFromRoute($routeName)

This method takes a route-key (for example `api/rest/foo/bar`) and returns a string containing
a templated uri.

#### getFromResource($resourceName)

This method works exactly like `getFromRoute` but it takes a `zf-rest` config-key (resource name) instead of a route-key
(for example `Vendor\Module\RestController`).
