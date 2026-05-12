# LifeHub\ApiClient\SearchTagApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1SearchTags()**](SearchTagApi.md#v1SearchTags) | **GET** /v1/search/tags |  |


## `v1SearchTags()`

```php
v1SearchTags($q, $fields_tags): \LifeHub\ApiClient\Model\V1SearchTags200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\SearchTagApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$q = 'q_example'; // string
$fields_tags = array('fields_tags_example'); // string[]

try {
    $result = $apiInstance->v1SearchTags($q, $fields_tags);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling SearchTagApi->v1SearchTags: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **q** | **string**|  | |
| **fields_tags** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1SearchTags200Response**](../Model/V1SearchTags200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
