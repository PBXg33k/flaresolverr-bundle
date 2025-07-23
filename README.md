[![Coverage Status](https://coveralls.io/repos/github/PBXg33k/flaresolverr-bundle/badge.svg?branch=master)](https://coveralls.io/github/PBXg33k/flaresolverr-bundle?branch=master)
# FlareSolverrBundle

This Symfony bundle provides an easy way to integrate the [FlareSolverr](https://github.com/FlareSolverr/FlareSolverr) proxy service into your Symfony applications.
FlareSolverr is a proxy server to bypass Cloudflare's anti-bot page, allowing you to scrape websites protected by Cloudflare.

# Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
composer require pbxg33k/flaresolverr-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Create the bundle config file (for now)

Create a file named `flare_solverr.yaml` in the `config/packages/` directory of your Symfony project. 
This file will contain the configuration for the FlareSolverrBundle.

```yaml
# config/packages/flare_solverr.yaml
flare_solverr:
    # The URL of the FlareSolverr server
    url: 'http://localhost:8191' # note that this url lacks the '/v1' suffix, which is added automatically by the bundle
    ### Optional
    session:
        id: 'fixed-session-id' # Optional: If you want to use a fixed session ID, set it here
        ttl_timeout: 3600 # Optional: Set a custom TTL for the session in seconds (default is 3600 seconds)
```

### Step 2: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require pbxg33k/flaresolverr-bundle
```

### Step 3: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
        Pbxg33k\FlareSolverrBundle\FlareSolverrBundle::class => ['all' => true],
];
```


# Usage

To use the FlareSolverrBundle, you can inject the `FlareSolverrClient` service into your controllers or services. 
Here's an example of how to use it in a controller:

```php
// src/Controller/ExampleController.php
namespace App\Controller;

use Pbxg33k\FlareSolverrBundle\Client\FlareSolverrClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ExampleController extends AbstractController
{
    public function __construct(
        private(set) FlareSolverrClient $flareSolverrClient
    )
    {
    }

    public function index(): Response
    {
        $response = $this->flareSolverrClient->requestGet('https://example.com');
        
        $HTMLContentAsString  = $response->getResponseContent();
        $HTMLDocumentAsDOMDocument = $response->getResponseContentAsHTMLDocument();
        
        // Do your magic with the response here
    }
}
```
