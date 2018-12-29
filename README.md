Warning: This is a library in development, and probably not production ready. Here still be some tiny dragons.

# Health check
In order to quickly diagnose problems in your applications it's important to be able to quickly distinguish between problems in the application itself, and it's dependencies.

Modern applications often have multiple layers of dependencies. For example, your application uses an API, and that API consumes a MySQL database. Downtime on that database will almost certainly affect your application 2 levels up in the chain.

In order to deal with this we need effective health checks. This allows us to quickly spot that our dependency `authentication_api` (for example) has become unhealthy, and the health status of that API then indicates it's MySQL dependency has left the building.

This library aims to make dealing with health checks a little bit easier.

## Usage
A health check consists of an instance of `HealthCheck`, containing one or more instances of `HealthTestInterface`. A health test is testing a single dependency, and the health check instance then reports on the collection as a whole.

For example, we can initialize and execute a set of tests like so:

```php
<?php
$healthCheck = HealthService([
   new PdoCheck("dsn", "username", "password"),
   new HttpStatusCheck($httpClient, $requestFactory, new Uri("http://my-endpoint"), [200])
]);

$report = $healthCheck->performAll();
```

The report variable then contains an object with all the test results, plus some higher level summary information.

This library comes out of the box with a simple URI HTTP status test, other tests have to be implemented manually.

## Recommendations

### Labels
The label property for each test should be reflective of the name of the application being tested.

For example, my main application could have a health test labelled `authentication_api`, which can then lead to some montitoring system firing a query at that particular application to determine which of it's dependencies has become unhealthy, and so forth.
This way an almost automatic map of service health can be generated within your organization.

### Exposing health checks
Health checks should be exposed on a URL so that external services and monitoring systems can access them. It is recommended however to keep the health endpoints exposed only inside of your network

## Todo's
The following features are still in progress 

 - PSR middleware to expose health information
 - More standard health tests
