# LifeHub\ApiClient\SearchProviderApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1DashboardSearchProvidersDestroy()**](SearchProviderApi.md#v1DashboardSearchProvidersDestroy) | **DELETE** /v1/dashboard/search-providers/{searchProvider} |  |
| [**v1DashboardSearchProvidersIndex()**](SearchProviderApi.md#v1DashboardSearchProvidersIndex) | **GET** /v1/dashboard/search-providers |  |
| [**v1DashboardSearchProvidersStore()**](SearchProviderApi.md#v1DashboardSearchProvidersStore) | **POST** /v1/dashboard/search-providers |  |
| [**v1DashboardSearchProvidersUpdate()**](SearchProviderApi.md#v1DashboardSearchProvidersUpdate) | **PUT** /v1/dashboard/search-providers/{searchProvider} |  |


## `v1DashboardSearchProvidersDestroy()`

```php
v1DashboardSearchProvidersDestroy($search_provider): \LifeHub\ApiClient\Model\V1DashboardSearchProvidersDestroy200Response
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
$search_provider = 'search_provider_example'; // string | The search provider slug

try {
    $result = $apiInstance->v1DashboardSearchProvidersDestroy($search_provider);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersDestroy: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **search_provider** | **string**| The search provider slug | |

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardSearchProvidersDestroy200Response**](../Model/V1DashboardSearchProvidersDestroy200Response.md)

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

## `v1DashboardSearchProvidersStore()`

```php
v1DashboardSearchProvidersStore($search_provider_create_request): \LifeHub\ApiClient\Model\V1DashboardSearchProvidersStore201Response
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
$search_provider_create_request = new \LifeHub\ApiClient\Model\SearchProviderCreateRequest(); // \LifeHub\ApiClient\Model\SearchProviderCreateRequest

try {
    $result = $apiInstance->v1DashboardSearchProvidersStore($search_provider_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersStore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **search_provider_create_request** | [**\LifeHub\ApiClient\Model\SearchProviderCreateRequest**](../Model/SearchProviderCreateRequest.md)|  | |

### Return type

[**\LifeHub\ApiClient\Model\V1DashboardSearchProvidersStore201Response**](../Model/V1DashboardSearchProvidersStore201Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1DashboardSearchProvidersUpdate()`

```php
v1DashboardSearchProvidersUpdate($search_provider, $search_provider_update_request): \LifeHub\ApiClient\Model\V1DashboardPinsUpdate200Response
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
$search_provider = 'search_provider_example'; // string | The search provider slug
$search_provider_update_request = new \LifeHub\ApiClient\Model\SearchProviderUpdateRequest(); // \LifeHub\ApiClient\Model\SearchProviderUpdateRequest

try {
    $result = $apiInstance->v1DashboardSearchProvidersUpdate($search_provider, $search_provider_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchProviderApi->v1DashboardSearchProvidersUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **search_provider** | **string**| The search provider slug | |
| **search_provider_update_request** | [**\LifeHub\ApiClient\Model\SearchProviderUpdateRequest**](../Model/SearchProviderUpdateRequest.md)|  | |

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
