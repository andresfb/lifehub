# LifeHub\ApiClient\SearchHistoryApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1SearchHistoryDestroy()**](SearchHistoryApi.md#v1SearchHistoryDestroy) | **DELETE** /v1/search/history/{searchHistory} |  |
| [**v1SearchHistoryStore()**](SearchHistoryApi.md#v1SearchHistoryStore) | **POST** /v1/search/history |  |
| [**v1SearchTerms()**](SearchHistoryApi.md#v1SearchTerms) | **GET** /v1/search/terms |  |


## `v1SearchHistoryDestroy()`

```php
v1SearchHistoryDestroy($search_history)
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchHistoryApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$search_history = 56; // int | The search history ID

try {
    $apiInstance->v1SearchHistoryDestroy($search_history);
} catch (Exception $e) {
    echo 'Exception when calling SearchHistoryApi->v1SearchHistoryDestroy: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **search_history** | **int**| The search history ID | |

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

## `v1SearchHistoryStore()`

```php
v1SearchHistoryStore($search_history_create_request): \LifeHub\ApiClient\Model\V1SearchHistoryStore201Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchHistoryApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$search_history_create_request = new \LifeHub\ApiClient\Model\SearchHistoryCreateRequest(); // \LifeHub\ApiClient\Model\SearchHistoryCreateRequest

try {
    $result = $apiInstance->v1SearchHistoryStore($search_history_create_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchHistoryApi->v1SearchHistoryStore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **search_history_create_request** | [**\LifeHub\ApiClient\Model\SearchHistoryCreateRequest**](../Model/SearchHistoryCreateRequest.md)|  | |

### Return type

[**\LifeHub\ApiClient\Model\V1SearchHistoryStore201Response**](../Model/V1SearchHistoryStore201Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1SearchTerms()`

```php
v1SearchTerms($module, $type, $term, $fields_search_histories): \LifeHub\ApiClient\Model\V1SearchTerms200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchHistoryApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$module = 'module_example'; // string
$type = 'type_example'; // string
$term = 'term_example'; // string
$fields_search_histories = array('fields_search_histories_example'); // string[]

try {
    $result = $apiInstance->v1SearchTerms($module, $type, $term, $fields_search_histories);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchHistoryApi->v1SearchTerms: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **module** | **string**|  | |
| **type** | **string**|  | |
| **term** | **string**|  | |
| **fields_search_histories** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1SearchTerms200Response**](../Model/V1SearchTerms200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
