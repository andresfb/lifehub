# LifeHub\ApiClient\UserAiModelApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1AiModelsDestroy()**](UserAiModelApi.md#v1AiModelsDestroy) | **DELETE** /v1/ai/models/{model} |  |
| [**v1AiModelsShow()**](UserAiModelApi.md#v1AiModelsShow) | **GET** /v1/ai/models/{model} |  |
| [**v1AiModelsUpdate()**](UserAiModelApi.md#v1AiModelsUpdate) | **PATCH** /v1/ai/models/{model} |  |


## `v1AiModelsDestroy()`

```php
v1AiModelsDestroy($model)
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiModelApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$model = 56; // int | The model ID

try {
    $apiInstance->v1AiModelsDestroy($model);
} catch (Exception $e) {
    echo 'Exception when calling UserAiModelApi->v1AiModelsDestroy: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **model** | **int**| The model ID | |

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

## `v1AiModelsShow()`

```php
v1AiModelsShow($model, $fields_user_ai_models): \LifeHub\ApiClient\Model\V1AiModelsShow200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiModelApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$model = 56; // int | The model ID
$fields_user_ai_models = array('fields_user_ai_models_example'); // string[]

try {
    $result = $apiInstance->v1AiModelsShow($model, $fields_user_ai_models);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiModelApi->v1AiModelsShow: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **model** | **int**| The model ID | |
| **fields_user_ai_models** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1AiModelsShow200Response**](../Model/V1AiModelsShow200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1AiModelsUpdate()`

```php
v1AiModelsUpdate($model, $fields_user_ai_models, $user_ai_model_update_request): \LifeHub\ApiClient\Model\V1AiModelsShow200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiModelApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$model = 56; // int | The model ID
$fields_user_ai_models = array('fields_user_ai_models_example'); // string[]
$user_ai_model_update_request = new \LifeHub\ApiClient\Model\UserAiModelUpdateRequest(); // \LifeHub\ApiClient\Model\UserAiModelUpdateRequest

try {
    $result = $apiInstance->v1AiModelsUpdate($model, $fields_user_ai_models, $user_ai_model_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiModelApi->v1AiModelsUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **model** | **int**| The model ID | |
| **fields_user_ai_models** | [**string[]**](../Model/string.md)|  | [optional] |
| **user_ai_model_update_request** | [**\LifeHub\ApiClient\Model\UserAiModelUpdateRequest**](../Model/UserAiModelUpdateRequest.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1AiModelsShow200Response**](../Model/V1AiModelsShow200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
