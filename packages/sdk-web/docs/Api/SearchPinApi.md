# LifeHub\ApiClient\SearchPinApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1DashboardPinsSearch()**](SearchPinApi.md#v1DashboardPinsSearch) | **GET** /v1/dashboard/pins/search |  |


## `v1DashboardPinsSearch()`

```php
v1DashboardPinsSearch(): \LifeHub\ApiClient\Model\V1DashboardPinsSearch200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchPinApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->v1DashboardPinsSearch();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchPinApi->v1DashboardPinsSearch: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardPinsSearch200Response**](../Model/V1DashboardPinsSearch200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
