# LifeHub\ApiClient\SearchProviderApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1DashboardSearchProvidersDestroy()**](SearchProviderApi.md#v1DashboardSearchProvidersDestroy) | **DELETE** /v1/dashboard/search/providers/{provider} |  |
| [**v1DashboardSearchProvidersIndex()**](SearchProviderApi.md#v1DashboardSearchProvidersIndex) | **GET** /v1/dashboard/search/providers |  |
| [**v1DashboardSearchProvidersShow()**](SearchProviderApi.md#v1DashboardSearchProvidersShow) | **GET** /v1/dashboard/search/providers/{provider} |  |
| [**v1DashboardSearchProvidersStore()**](SearchProviderApi.md#v1DashboardSearchProvidersStore) | **POST** /v1/dashboard/search/providers |  |
| [**v1DashboardSearchProvidersUpdate()**](SearchProviderApi.md#v1DashboardSearchProvidersUpdate) | **PUT** /v1/dashboard/search/providers/{provider} |  |


## `v1DashboardSearchProvidersDestroy()`

```php
v1DashboardSearchProvidersDestroy($provider): object
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID

try {
    $result = $apiInstance->v1DashboardSearchProvidersDestroy($provider);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersDestroy: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |

### Return type

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardSearchProvidersIndex()`

```php
v1DashboardSearchProvidersIndex($fields_search_providers): \LifeHub\ApiClient\Model\V1DashboardSearchProvidersIndex200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$fields_search_providers = array('fields_search_providers_example'); // string[]

try {
    $result = $apiInstance->v1DashboardSearchProvidersIndex($fields_search_providers);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersIndex: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **fields_search_providers** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardSearchProvidersIndex200Response**](../Model/V1DashboardSearchProvidersIndex200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardSearchProvidersShow()`

```php
v1DashboardSearchProvidersShow($provider): object
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID

try {
    $result = $apiInstance->v1DashboardSearchProvidersShow($provider);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersShow: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |

### Return type

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardSearchProvidersStore()`

```php
v1DashboardSearchProvidersStore(): object
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->v1DashboardSearchProvidersStore();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersStore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardSearchProvidersUpdate()`

```php
v1DashboardSearchProvidersUpdate($provider): object
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID

try {
    $result = $apiInstance->v1DashboardSearchProvidersUpdate($provider);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |

### Return type

**object**

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
