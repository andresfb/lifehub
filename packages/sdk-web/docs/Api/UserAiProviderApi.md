# LifeHub\ApiClient\UserAiProviderApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1AiProvidersDestroy()**](UserAiProviderApi.md#v1AiProvidersDestroy) | **DELETE** /v1/ai/providers/{provider} |  |
| [**v1AiProvidersIndex()**](UserAiProviderApi.md#v1AiProvidersIndex) | **GET** /v1/ai/providers |  |
| [**v1AiProvidersModelsStore()**](UserAiProviderApi.md#v1AiProvidersModelsStore) | **POST** /v1/ai/providers/{provider}/models |  |
| [**v1AiProvidersShow()**](UserAiProviderApi.md#v1AiProvidersShow) | **GET** /v1/ai/providers/{provider} |  |
| [**v1AiProvidersStore()**](UserAiProviderApi.md#v1AiProvidersStore) | **POST** /v1/ai/providers |  |
| [**v1AiProvidersUpdate()**](UserAiProviderApi.md#v1AiProvidersUpdate) | **PATCH** /v1/ai/providers/{provider} |  |


## `v1AiProvidersDestroy()`

```php
v1AiProvidersDestroy($provider)
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID

try {
    $apiInstance->v1AiProvidersDestroy($provider);
} catch (Exception $e) {
    echo 'Exception when calling UserAiProviderApi->v1AiProvidersDestroy: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |

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

## `v1AiProvidersIndex()`

```php
v1AiProvidersIndex($fields_user_ai_providers): \LifeHub\ApiClient\Model\V1AiProvidersIndex200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$fields_user_ai_providers = array('fields_user_ai_providers_example'); // string[]

try {
    $result = $apiInstance->v1AiProvidersIndex($fields_user_ai_providers);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiProviderApi->v1AiProvidersIndex: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **fields_user_ai_providers** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1AiProvidersIndex200Response**](../Model/V1AiProvidersIndex200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1AiProvidersModelsStore()`

```php
v1AiProvidersModelsStore($provider, $user_ai_model_store_request, $fields_user_ai_models): \LifeHub\ApiClient\Model\V1AiModelsShow200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID
$user_ai_model_store_request = new \LifeHub\ApiClient\Model\UserAiModelStoreRequest(); // \LifeHub\ApiClient\Model\UserAiModelStoreRequest
$fields_user_ai_models = array('fields_user_ai_models_example'); // string[]

try {
    $result = $apiInstance->v1AiProvidersModelsStore($provider, $user_ai_model_store_request, $fields_user_ai_models);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiProviderApi->v1AiProvidersModelsStore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |
| **user_ai_model_store_request** | [**\LifeHub\ApiClient\Model\UserAiModelStoreRequest**](../Model/UserAiModelStoreRequest.md)|  | |
| **fields_user_ai_models** | [**string[]**](../Model/string.md)|  | [optional] |

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

## `v1AiProvidersShow()`

```php
v1AiProvidersShow($provider, $fields_user_ai_providers): \LifeHub\ApiClient\Model\V1AiProvidersStore200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID
$fields_user_ai_providers = array('fields_user_ai_providers_example'); // string[]

try {
    $result = $apiInstance->v1AiProvidersShow($provider, $fields_user_ai_providers);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiProviderApi->v1AiProvidersShow: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |
| **fields_user_ai_providers** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1AiProvidersStore200Response**](../Model/V1AiProvidersStore200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1AiProvidersStore()`

```php
v1AiProvidersStore($user_ai_provider_store_request, $fields_user_ai_providers): \LifeHub\ApiClient\Model\V1AiProvidersStore200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$user_ai_provider_store_request = new \LifeHub\ApiClient\Model\UserAiProviderStoreRequest(); // \LifeHub\ApiClient\Model\UserAiProviderStoreRequest
$fields_user_ai_providers = array('fields_user_ai_providers_example'); // string[]

try {
    $result = $apiInstance->v1AiProvidersStore($user_ai_provider_store_request, $fields_user_ai_providers);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiProviderApi->v1AiProvidersStore: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **user_ai_provider_store_request** | [**\LifeHub\ApiClient\Model\UserAiProviderStoreRequest**](../Model/UserAiProviderStoreRequest.md)|  | |
| **fields_user_ai_providers** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1AiProvidersStore200Response**](../Model/V1AiProvidersStore200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1AiProvidersUpdate()`

```php
v1AiProvidersUpdate($provider, $fields_user_ai_providers, $user_ai_provider_update_request): \LifeHub\ApiClient\Model\V1AiProvidersStore200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\UserAiProviderApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$provider = 56; // int | The provider ID
$fields_user_ai_providers = array('fields_user_ai_providers_example'); // string[]
$user_ai_provider_update_request = new \LifeHub\ApiClient\Model\UserAiProviderUpdateRequest(); // \LifeHub\ApiClient\Model\UserAiProviderUpdateRequest

try {
    $result = $apiInstance->v1AiProvidersUpdate($provider, $fields_user_ai_providers, $user_ai_provider_update_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling UserAiProviderApi->v1AiProvidersUpdate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **provider** | **int**| The provider ID | |
| **fields_user_ai_providers** | [**string[]**](../Model/string.md)|  | [optional] |
| **user_ai_provider_update_request** | [**\LifeHub\ApiClient\Model\UserAiProviderUpdateRequest**](../Model/UserAiProviderUpdateRequest.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1AiProvidersStore200Response**](../Model/V1AiProvidersStore200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
