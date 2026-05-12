# LifeHub\ApiClient\GlobalSearchApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1Search()**](GlobalSearchApi.md#v1Search) | **GET** /v1/search |  |


## `v1Search()`

```php
v1Search($q, $limit, $module, $entity_type, $is_private, $is_archived): \LifeHub\ApiClient\Model\V1Search200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\GlobalSearchApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$q = 'q_example'; // string
$limit = 56; // int
$module = 'module_example'; // string
$entity_type = 'entity_type_example'; // string
$is_private = True; // bool
$is_archived = True; // bool

try {
    $result = $apiInstance->v1Search($q, $limit, $module, $entity_type, $is_private, $is_archived);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GlobalSearchApi->v1Search: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **q** | **string**|  | |
| **limit** | **int**|  | [optional] |
| **module** | **string**|  | [optional] |
| **entity_type** | **string**|  | [optional] |
| **is_private** | **bool**|  | [optional] |
| **is_archived** | **bool**|  | [optional] |

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
