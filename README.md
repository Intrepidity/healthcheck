[![Build Status](https://travis-ci.org/Intrepidity/healthcheck.svg?branch=master)](https://travis-ci.org/Intrepidity/healthcheck)

Warning: This is a library in development, and probably not production ready. Here still be some tiny dragons.

# Health check
In order to quickly diagnose problems in your applications it's important to be able to quickly distinguish between problems in the application itself, and it's dependencies.

Modern applications often have multiple layers of dependencies. For example, your application uses an API, and that API consumes a MySQL database. Downtime on that database will almost certainly affect your application 2 levels up in the chain.

In order to deal with this we need effective health checks. This allows us to quickly spot that our dependency `authentication_api` (for example) has become unhealthy, and the health status of that API then indicates it's MySQL dependency has left the building.

This library aims to make dealing with health checks a little bit easier.

## Usage
A health check consists of an instance of `HealthService`, containing one or more instances of `HealthCheckInterface`. A health check is checking a single dependency, and the health service instance then reports on the collection as a whole.

For example, we can initialize and execute a set of checks like so:

```$php
$healthCheck = HealthService([
   new PdoCheck("dsn", "username", "password"),
   new HttpStatusCheck($httpClient, $requestFactory, new Uri("http://my-endpoint"), [200])
]);

$report = $healthCheck->performAll();
```

The report variable then contains an object with all the check results, plus some higher level summary information.

This library comes out of the box with a simple HTTP Status check and PDO connection check, other checks have to be implemented manually.

This library heavily relies on PSR-7, PSR-17 and PSR-18 standards, so it is often assumed that you supply your own implementations of PSR interfaces, such as request- and response factories.  

## Recommendations

### Labels
The label property for each check should be reflective of the name of the application being checked.

For example, my main application could have a health check labelled `authentication_api`, which can then lead to some montitoring system firing a query at that particular application to determine which of it's dependencies has become unhealthy, and so forth.
This way an almost automatic map of service health can be generated within your organization.

### Exposing health checks
Health checks should be exposed on a URL so that external services and monitoring systems can access them. It is recommended however to keep the health endpoints exposed only inside of your network.

The `ResponseFactory` class can be used to easily create a PSR-7 response for use in your health endpoints, like so:

```$php
$factory = new ResponseFactory(
    $psrResponseFacory,
    $streamFactory,
    $healthService
);

$response = $factory->create();
```

## Todo's
The following features are still in progress 

 - PSR middleware to expose health information
 - More standard health checks
