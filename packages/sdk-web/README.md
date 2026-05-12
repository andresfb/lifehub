# OpenAPIClient-php

API for the Personal Everything App


## Installation & Usage

### Requirements

PHP 8.1 and later.

### Composer

To install the bindings via [Composer](https://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
<?php
require_once('/path/to/OpenAPIClient-php/vendor/autoload.php');
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

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

## API Endpoints

All URIs are relative to *http://localhost:8000/api*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*AuthApi* | [**v1Login**](docs/Api/AuthApi.md#v1login) | **POST** /v1/login | 
*AuthApi* | [**v1LoginValidate**](docs/Api/AuthApi.md#v1loginvalidate) | **POST** /v1/login/validate | 
*AuthApi* | [**v1Logout**](docs/Api/AuthApi.md#v1logout) | **POST** /v1/logout | 
*AuthApi* | [**v1Me**](docs/Api/AuthApi.md#v1me) | **GET** /v1/me | 
*AuthApi* | [**v1PasswordEmail**](docs/Api/AuthApi.md#v1passwordemail) | **POST** /v1/forgot-password | 
*AuthApi* | [**v1PasswordReset**](docs/Api/AuthApi.md#v1passwordreset) | **POST** /v1/reset-password | 
*AuthApi* | [**v1Register**](docs/Api/AuthApi.md#v1register) | **POST** /v1/register | 
*DashboardApi* | [**v1DashboardV1Dashboard**](docs/Api/DashboardApi.md#v1dashboardv1dashboard) | **GET** /v1/dashboard | 
*GlobalSearchApi* | [**v1Search**](docs/Api/GlobalSearchApi.md#v1search) | **GET** /v1/search | 
*ManifestApi* | [**v1Manifesto**](docs/Api/ManifestApi.md#v1manifesto) | **GET** /v1/manifesto | 
*ManifestApi* | [**v1ManifestoVersion**](docs/Api/ManifestApi.md#v1manifestoversion) | **GET** /v1/manifesto/version | 
*PinApi* | [**v1DashboardPinsDestroy**](docs/Api/PinApi.md#v1dashboardpinsdestroy) | **DELETE** /v1/dashboard/pins/{pin} | 
*PinApi* | [**v1DashboardPinsIndex**](docs/Api/PinApi.md#v1dashboardpinsindex) | **GET** /v1/dashboard/pins | 
*PinApi* | [**v1DashboardPinsStore**](docs/Api/PinApi.md#v1dashboardpinsstore) | **POST** /v1/dashboard/pins | 
*PinApi* | [**v1DashboardPinsUpdate**](docs/Api/PinApi.md#v1dashboardpinsupdate) | **PUT** /v1/dashboard/pins/{pin} | 
*ReminderApi* | [**v1ReminderShow**](docs/Api/ReminderApi.md#v1remindershow) | **GET** /v1/reminder/{reminder} | 
*SearchHistoryApi* | [**v1SearchHistoryDestroy**](docs/Api/SearchHistoryApi.md#v1searchhistorydestroy) | **DELETE** /v1/search/history/{searchHistory} | 
*SearchHistoryApi* | [**v1SearchHistoryStore**](docs/Api/SearchHistoryApi.md#v1searchhistorystore) | **POST** /v1/search/history | 
*SearchHistoryApi* | [**v1SearchTerms**](docs/Api/SearchHistoryApi.md#v1searchterms) | **GET** /v1/search/terms | 
*SearchPinApi* | [**v1DashboardPinsSearch**](docs/Api/SearchPinApi.md#v1dashboardpinssearch) | **GET** /v1/dashboard/pins/search | 
*SearchProviderApi* | [**v1DashboardSearchProvidersDestroy**](docs/Api/SearchProviderApi.md#v1dashboardsearchprovidersdestroy) | **DELETE** /v1/dashboard/search/providers/{provider} | 
*SearchProviderApi* | [**v1DashboardSearchProvidersIndex**](docs/Api/SearchProviderApi.md#v1dashboardsearchprovidersindex) | **GET** /v1/dashboard/search/providers | 
*SearchProviderApi* | [**v1DashboardSearchProvidersShow**](docs/Api/SearchProviderApi.md#v1dashboardsearchprovidersshow) | **GET** /v1/dashboard/search/providers/{provider} | 
*SearchProviderApi* | [**v1DashboardSearchProvidersStore**](docs/Api/SearchProviderApi.md#v1dashboardsearchprovidersstore) | **POST** /v1/dashboard/search/providers | 
*SearchProviderApi* | [**v1DashboardSearchProvidersUpdate**](docs/Api/SearchProviderApi.md#v1dashboardsearchprovidersupdate) | **PUT** /v1/dashboard/search/providers/{provider} | 
*SearchTagApi* | [**v1SearchTags**](docs/Api/SearchTagApi.md#v1searchtags) | **GET** /v1/search/tags | 
*UserAiModelApi* | [**v1AiModelsDestroy**](docs/Api/UserAiModelApi.md#v1aimodelsdestroy) | **DELETE** /v1/ai/models/{model} | 
*UserAiModelApi* | [**v1AiModelsShow**](docs/Api/UserAiModelApi.md#v1aimodelsshow) | **GET** /v1/ai/models/{model} | 
*UserAiModelApi* | [**v1AiModelsUpdate**](docs/Api/UserAiModelApi.md#v1aimodelsupdate) | **PATCH** /v1/ai/models/{model} | 
*UserAiProviderApi* | [**v1AiProvidersDestroy**](docs/Api/UserAiProviderApi.md#v1aiprovidersdestroy) | **DELETE** /v1/ai/providers/{provider} | 
*UserAiProviderApi* | [**v1AiProvidersIndex**](docs/Api/UserAiProviderApi.md#v1aiprovidersindex) | **GET** /v1/ai/providers | 
*UserAiProviderApi* | [**v1AiProvidersModelsStore**](docs/Api/UserAiProviderApi.md#v1aiprovidersmodelsstore) | **POST** /v1/ai/providers/{provider}/models | 
*UserAiProviderApi* | [**v1AiProvidersShow**](docs/Api/UserAiProviderApi.md#v1aiprovidersshow) | **GET** /v1/ai/providers/{provider} | 
*UserAiProviderApi* | [**v1AiProvidersStore**](docs/Api/UserAiProviderApi.md#v1aiprovidersstore) | **POST** /v1/ai/providers | 
*UserAiProviderApi* | [**v1AiProvidersUpdate**](docs/Api/UserAiProviderApi.md#v1aiprovidersupdate) | **PATCH** /v1/ai/providers/{provider} | 

## Models

- [ForgotPasswordRequest](docs/Model/ForgotPasswordRequest.md)
- [HomepageItemResource](docs/Model/HomepageItemResource.md)
- [HomepageItemResourceAttributes](docs/Model/HomepageItemResourceAttributes.md)
- [HomepageSectionResource](docs/Model/HomepageSectionResource.md)
- [HomepageSectionResourceAttributes](docs/Model/HomepageSectionResourceAttributes.md)
- [HomepageSectionResourceRelationships](docs/Model/HomepageSectionResourceRelationships.md)
- [HomepageSectionResourceRelationshipsItems](docs/Model/HomepageSectionResourceRelationshipsItems.md)
- [HomepageSectionResourceRelationshipsItemsDataInner](docs/Model/HomepageSectionResourceRelationshipsItemsDataInner.md)
- [InlineObject](docs/Model/InlineObject.md)
- [InlineObject1](docs/Model/InlineObject1.md)
- [LoginRequest](docs/Model/LoginRequest.md)
- [PinCreateRequest](docs/Model/PinCreateRequest.md)
- [PinUpdateRequest](docs/Model/PinUpdateRequest.md)
- [RegisterRequest](docs/Model/RegisterRequest.md)
- [ResetPasswordRequest](docs/Model/ResetPasswordRequest.md)
- [SearchHistoryCreateRequest](docs/Model/SearchHistoryCreateRequest.md)
- [SearchHistoryResource](docs/Model/SearchHistoryResource.md)
- [SearchHistoryResourceAttributes](docs/Model/SearchHistoryResourceAttributes.md)
- [SearchProviderResource](docs/Model/SearchProviderResource.md)
- [SearchProviderResourceAttributes](docs/Model/SearchProviderResourceAttributes.md)
- [TagResource](docs/Model/TagResource.md)
- [TagResourceAttributes](docs/Model/TagResourceAttributes.md)
- [TwoFactorCodeRequest](docs/Model/TwoFactorCodeRequest.md)
- [UserAiModelResource](docs/Model/UserAiModelResource.md)
- [UserAiModelResourceAttributes](docs/Model/UserAiModelResourceAttributes.md)
- [UserAiModelStoreRequest](docs/Model/UserAiModelStoreRequest.md)
- [UserAiModelUpdateRequest](docs/Model/UserAiModelUpdateRequest.md)
- [UserAiProviderResource](docs/Model/UserAiProviderResource.md)
- [UserAiProviderResourceAttributes](docs/Model/UserAiProviderResourceAttributes.md)
- [UserAiProviderResourceAttributesModels](docs/Model/UserAiProviderResourceAttributesModels.md)
- [UserAiProviderStoreRequest](docs/Model/UserAiProviderStoreRequest.md)
- [UserAiProviderUpdateRequest](docs/Model/UserAiProviderUpdateRequest.md)
- [UserApiResource](docs/Model/UserApiResource.md)
- [UserApiResourceAttributes](docs/Model/UserApiResourceAttributes.md)
- [V1AiModelsShow200Response](docs/Model/V1AiModelsShow200Response.md)
- [V1AiProvidersIndex200Response](docs/Model/V1AiProvidersIndex200Response.md)
- [V1AiProvidersStore200Response](docs/Model/V1AiProvidersStore200Response.md)
- [V1DashboardPinsIndex200Response](docs/Model/V1DashboardPinsIndex200Response.md)
- [V1DashboardPinsSearch200Response](docs/Model/V1DashboardPinsSearch200Response.md)
- [V1DashboardPinsStore201Response](docs/Model/V1DashboardPinsStore201Response.md)
- [V1DashboardPinsStore201ResponseData](docs/Model/V1DashboardPinsStore201ResponseData.md)
- [V1DashboardPinsStore400Response](docs/Model/V1DashboardPinsStore400Response.md)
- [V1DashboardPinsUpdate200Response](docs/Model/V1DashboardPinsUpdate200Response.md)
- [V1DashboardSearchProvidersIndex200Response](docs/Model/V1DashboardSearchProvidersIndex200Response.md)
- [V1Login203Response](docs/Model/V1Login203Response.md)
- [V1Login401Response](docs/Model/V1Login401Response.md)
- [V1LoginValidate401Response](docs/Model/V1LoginValidate401Response.md)
- [V1LoginValidate401ResponseAnyOf](docs/Model/V1LoginValidate401ResponseAnyOf.md)
- [V1LoginValidate401ResponseAnyOf1](docs/Model/V1LoginValidate401ResponseAnyOf1.md)
- [V1Logout200Response](docs/Model/V1Logout200Response.md)
- [V1ManifestoVersion200Response](docs/Model/V1ManifestoVersion200Response.md)
- [V1ManifestoVersion200ResponseData](docs/Model/V1ManifestoVersion200ResponseData.md)
- [V1PasswordEmail200Response](docs/Model/V1PasswordEmail200Response.md)
- [V1PasswordEmail500Response](docs/Model/V1PasswordEmail500Response.md)
- [V1PasswordReset200Response](docs/Model/V1PasswordReset200Response.md)
- [V1PasswordReset400Response](docs/Model/V1PasswordReset400Response.md)
- [V1Register200Response](docs/Model/V1Register200Response.md)
- [V1Search200Response](docs/Model/V1Search200Response.md)
- [V1SearchHistoryStore201Response](docs/Model/V1SearchHistoryStore201Response.md)
- [V1SearchHistoryStore201ResponseData](docs/Model/V1SearchHistoryStore201ResponseData.md)
- [V1SearchTags200Response](docs/Model/V1SearchTags200Response.md)
- [V1SearchTerms200Response](docs/Model/V1SearchTerms200Response.md)

## Authorization
Endpoints do not require authorization.

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author



## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `0.1.0.1`
    - Generator version: `7.22.0`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
