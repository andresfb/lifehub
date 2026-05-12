# LifeHub\ApiClient\AuthApi



All URIs are relative to http://localhost:8000/api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**v1Login()**](AuthApi.md#v1Login) | **POST** /v1/login |  |
| [**v1LoginValidate()**](AuthApi.md#v1LoginValidate) | **POST** /v1/login/validate |  |
| [**v1Logout()**](AuthApi.md#v1Logout) | **POST** /v1/logout |  |
| [**v1Me()**](AuthApi.md#v1Me) | **GET** /v1/me |  |
| [**v1PasswordEmail()**](AuthApi.md#v1PasswordEmail) | **POST** /v1/forgot-password |  |
| [**v1PasswordReset()**](AuthApi.md#v1PasswordReset) | **POST** /v1/reset-password |  |
| [**v1Register()**](AuthApi.md#v1Register) | **POST** /v1/register |  |


## `v1Login()`

```php
v1Login($login_request, $fields_users): \LifeHub\ApiClient\Model\V1Register200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$login_request = new \LifeHub\ApiClient\Model\LoginRequest(); // \LifeHub\ApiClient\Model\LoginRequest
$fields_users = array('fields_users_example'); // string[]

try {
    $result = $apiInstance->v1Login($login_request, $fields_users);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1Login: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **login_request** | [**\LifeHub\ApiClient\Model\LoginRequest**](../Model/LoginRequest.md)|  | |
| **fields_users** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1Register200Response**](../Model/V1Register200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1LoginValidate()`

```php
v1LoginValidate($two_factor_code_request, $fields_users): \LifeHub\ApiClient\Model\V1Register200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$two_factor_code_request = new \LifeHub\ApiClient\Model\TwoFactorCodeRequest(); // \LifeHub\ApiClient\Model\TwoFactorCodeRequest
$fields_users = array('fields_users_example'); // string[]

try {
    $result = $apiInstance->v1LoginValidate($two_factor_code_request, $fields_users);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1LoginValidate: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **two_factor_code_request** | [**\LifeHub\ApiClient\Model\TwoFactorCodeRequest**](../Model/TwoFactorCodeRequest.md)|  | |
| **fields_users** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1Register200Response**](../Model/V1Register200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/vnd.api+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1Logout()`

```php
v1Logout(): \LifeHub\ApiClient\Model\V1Logout200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->v1Logout();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1Logout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\LifeHub\ApiClient\Model\V1Logout200Response**](../Model/V1Logout200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1Me()`

```php
v1Me($fields_users): \LifeHub\ApiClient\Model\V1Register200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$fields_users = array('fields_users_example'); // string[]

try {
    $result = $apiInstance->v1Me($fields_users);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1Me: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **fields_users** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1Register200Response**](../Model/V1Register200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1PasswordEmail()`

```php
v1PasswordEmail($forgot_password_request): \LifeHub\ApiClient\Model\V1PasswordEmail200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$forgot_password_request = new \LifeHub\ApiClient\Model\ForgotPasswordRequest(); // \LifeHub\ApiClient\Model\ForgotPasswordRequest

try {
    $result = $apiInstance->v1PasswordEmail($forgot_password_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1PasswordEmail: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **forgot_password_request** | [**\LifeHub\ApiClient\Model\ForgotPasswordRequest**](../Model/ForgotPasswordRequest.md)|  | |

### Return type

[**\LifeHub\ApiClient\Model\V1PasswordEmail200Response**](../Model/V1PasswordEmail200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1PasswordReset()`

```php
v1PasswordReset($reset_password_request): \LifeHub\ApiClient\Model\V1PasswordReset200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$reset_password_request = new \LifeHub\ApiClient\Model\ResetPasswordRequest(); // \LifeHub\ApiClient\Model\ResetPasswordRequest

try {
    $result = $apiInstance->v1PasswordReset($reset_password_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1PasswordReset: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **reset_password_request** | [**\LifeHub\ApiClient\Model\ResetPasswordRequest**](../Model/ResetPasswordRequest.md)|  | |

### Return type

[**\LifeHub\ApiClient\Model\V1PasswordReset200Response**](../Model/V1PasswordReset200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `v1Register()`

```php
v1Register($register_request, $fields_users): \LifeHub\ApiClient\Model\V1Register200Response
```



### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new LifeHub\ApiClient\Api\AuthApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$register_request = new \LifeHub\ApiClient\Model\RegisterRequest(); // \LifeHub\ApiClient\Model\RegisterRequest
$fields_users = array('fields_users_example'); // string[]

try {
    $result = $apiInstance->v1Register($register_request, $fields_users);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AuthApi->v1Register: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **register_request** | [**\LifeHub\ApiClient\Model\RegisterRequest**](../Model/RegisterRequest.md)|  | |
| **fields_users** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\LifeHub\ApiClient\Model\V1Register200Response**](../Model/V1Register200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/vnd.api+json`, `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
