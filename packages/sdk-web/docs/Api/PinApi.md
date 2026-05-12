# LifeHub\ApiClient\PinApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1DashboardPinsDestroy()**](PinApi.md#v1DashboardPinsDestroy) | **DELETE** /v1/dashboard/pins/{pin} |  |
| [**v1DashboardPinsIndex()**](PinApi.md#v1DashboardPinsIndex) | **GET** /v1/dashboard/pins |  |
| [**v1DashboardPinsStore()**](PinApi.md#v1DashboardPinsStore) | **POST** /v1/dashboard/pins |  |
| [**v1DashboardPinsUpdate()**](PinApi.md#v1DashboardPinsUpdate) | **PUT** /v1/dashboard/pins/{pin} |  |


## `v1DashboardPinsDestroy()`

```php
v1DashboardPinsDestroy($pin)
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\PinApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$pin = 'pin_example'; // string | The pin slug

try {
    $apiInstance->v1DashboardPinsDestroy($pin);
} catch (Exception $e) {
    echo 'Exception when calling PinApi->v1DashboardPinsDestroy: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pin** | **string**| The pin slug | |

### Return type

void (empty response body)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardPinsIndex()`

```php
v1DashboardPinsIndex($status, $include, $fields_homepage_sections, $fields_homepage_items): \LifeHub\ApiClient\Model\V1DashboardPinsIndex200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\PinApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$status = 56; // int
$include = 'include_example'; // string
$fields_homepage_sections = array('fields_homepage_sections_example'); // string[]
$fields_homepage_items = array('fields_homepage_items_example'); // string[]

try {
    $result = $apiInstance->v1DashboardPinsIndex($status, $include, $fields_homepage_sections, $fields_homepage_items);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PinApi->v1DashboardPinsIndex: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **status** | **int**|  | |
| **include** | **string**|  | [optional] |
| **fields_homepage_sections** | [**string[]**](../Model/string.md)|  | [optional] |
| **fields_homepage_items** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardPinsIndex200Response**](../Model/V1DashboardPinsIndex200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardPinsStore()`

```php
v1DashboardPinsStore($pin_create_request): \LifeHub\ApiClient\Model\V1DashboardPinsStore201Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\PinApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$pin_create_request = new \LifeHub\ApiClient\Model\PinCreateRequest(); // \LifeHub\ApiClient\Model\PinCreateRequest

try {
    $result = $apiInstance->v1DashboardPinsStore($pin_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PinApi->v1DashboardPinsStore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pin_create_request** | [**\LifeHub\ApiClient\Model\PinCreateRequest**](../Model/PinCreateRequest.md)|  | |

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardPinsStore201Response**](../Model/V1DashboardPinsStore201Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardPinsUpdate()`

```php
v1DashboardPinsUpdate($pin, $pin_update_request): \LifeHub\ApiClient\Model\V1DashboardPinsUpdate200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\PinApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$pin = 'pin_example'; // string | The pin slug
$pin_update_request = new \LifeHub\ApiClient\Model\PinUpdateRequest(); // \LifeHub\ApiClient\Model\PinUpdateRequest

try {
    $result = $apiInstance->v1DashboardPinsUpdate($pin, $pin_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling PinApi->v1DashboardPinsUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **pin** | **string**| The pin slug | |
| **pin_update_request** | [**\LifeHub\ApiClient\Model\PinUpdateRequest**](../Model/PinUpdateRequest.md)|  | |

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardPinsUpdate200Response**](../Model/V1DashboardPinsUpdate200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
