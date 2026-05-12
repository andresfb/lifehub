# LifeHub\ApiClient\ManifestApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1Manifesto()**](ManifestApi.md#v1Manifesto) | **GET** /v1/manifesto |  |
| [**v1ManifestoVersion()**](ManifestApi.md#v1ManifestoVersion) | **GET** /v1/manifesto/version |  |


## `v1Manifesto()`

```php
v1Manifesto(): \LifeHub\ApiClient\Model\V1Search200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\ManifestApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->v1Manifesto();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ManifestApi->v1Manifesto: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\LifeHub\ApiClient\Model\V1Search200Response**](../Model/V1Search200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1ManifestoVersion()`

```php
v1ManifestoVersion(): \LifeHub\ApiClient\Model\V1ManifestoVersion200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\ManifestApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->v1ManifestoVersion();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ManifestApi->v1ManifestoVersion: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\LifeHub\ApiClient\Model\V1ManifestoVersion200Response**](../Model/V1ManifestoVersion200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
