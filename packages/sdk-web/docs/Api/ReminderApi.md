# LifeHub\ApiClient\ReminderApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1ReminderShow()**](ReminderApi.md#v1ReminderShow) | **GET** /v1/reminder/{reminder} |  |


## `v1ReminderShow()`

```php
v1ReminderShow($reminder)
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\ReminderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$reminder = 56; // int | The reminder ID

try {
    $apiInstance->v1ReminderShow($reminder);
} catch (Exception $e) {
    echo 'Exception when calling ReminderApi->v1ReminderShow: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **reminder** | **int**| The reminder ID | |

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
