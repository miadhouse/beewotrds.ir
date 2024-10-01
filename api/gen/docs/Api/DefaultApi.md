# OpenAPI\Client\DefaultApi

All URIs are relative to http://localhost/beewords-api, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**loginPost()**](DefaultApi.md#loginPost) | **POST** /login | ورود کاربر |
| [**logoutPost()**](DefaultApi.md#logoutPost) | **POST** /logout | خروج کاربر |
| [**recoverPasswordPost()**](DefaultApi.md#recoverPasswordPost) | **POST** /recover-password | درخواست بازیابی رمز عبور |
| [**registerPost()**](DefaultApi.md#registerPost) | **POST** /register | ثبت نام کاربر جدید |
| [**resendVerificationPost()**](DefaultApi.md#resendVerificationPost) | **POST** /resend-verification | ارسال مجدد ایمیل تأیید |
| [**resetPasswordPost()**](DefaultApi.md#resetPasswordPost) | **POST** /reset-password | تنظیم رمز عبور جدید |
| [**userGet()**](DefaultApi.md#userGet) | **GET** /user | دریافت اطلاعات کاربر |
| [**verifyGet()**](DefaultApi.md#verifyGet) | **GET** /verify | تأیید ایمیل کاربر |


## `loginPost()`

```php
loginPost($login_post_request): \OpenAPI\Client\Model\LoginPost200Response
```

ورود کاربر

کاربر را احراز هویت کرده و یک توکن JWT بازمی‌گرداند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$login_post_request = new \OpenAPI\Client\Model\LoginPostRequest(); // \OpenAPI\Client\Model\LoginPostRequest

try {
    $result = $apiInstance->loginPost($login_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->loginPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **login_post_request** | [**\OpenAPI\Client\Model\LoginPostRequest**](../Model/LoginPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\LoginPost200Response**](../Model/LoginPost200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `logoutPost()`

```php
logoutPost(): \OpenAPI\Client\Model\LogoutPost200Response
```

خروج کاربر

کاربر احراز هویت شده را خارج می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->logoutPost();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->logoutPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\OpenAPI\Client\Model\LogoutPost200Response**](../Model/LogoutPost200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `recoverPasswordPost()`

```php
recoverPasswordPost($resend_verification_post_request): \OpenAPI\Client\Model\RecoverPasswordPost200Response
```

درخواست بازیابی رمز عبور

کاربر با وارد کردن ایمیل خود درخواست بازیابی رمز عبور را ارسال می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resend_verification_post_request = new \OpenAPI\Client\Model\ResendVerificationPostRequest(); // \OpenAPI\Client\Model\ResendVerificationPostRequest

try {
    $result = $apiInstance->recoverPasswordPost($resend_verification_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->recoverPasswordPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **resend_verification_post_request** | [**\OpenAPI\Client\Model\ResendVerificationPostRequest**](../Model/ResendVerificationPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\RecoverPasswordPost200Response**](../Model/RecoverPasswordPost200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `registerPost()`

```php
registerPost($register_post_request): \OpenAPI\Client\Model\RegisterPost201Response
```

ثبت نام کاربر جدید

کاربر جدیدی را با اطلاعات داده شده ثبت می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$register_post_request = new \OpenAPI\Client\Model\RegisterPostRequest(); // \OpenAPI\Client\Model\RegisterPostRequest

try {
    $result = $apiInstance->registerPost($register_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->registerPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **register_post_request** | [**\OpenAPI\Client\Model\RegisterPostRequest**](../Model/RegisterPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\RegisterPost201Response**](../Model/RegisterPost201Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `resendVerificationPost()`

```php
resendVerificationPost($resend_verification_post_request): \OpenAPI\Client\Model\ResendVerificationPost200Response
```

ارسال مجدد ایمیل تأیید

ایمیل تأیید جدیدی به کاربر ارسال می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$resend_verification_post_request = new \OpenAPI\Client\Model\ResendVerificationPostRequest(); // \OpenAPI\Client\Model\ResendVerificationPostRequest

try {
    $result = $apiInstance->resendVerificationPost($resend_verification_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->resendVerificationPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **resend_verification_post_request** | [**\OpenAPI\Client\Model\ResendVerificationPostRequest**](../Model/ResendVerificationPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ResendVerificationPost200Response**](../Model/ResendVerificationPost200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `resetPasswordPost()`

```php
resetPasswordPost($reset_password_post_request): \OpenAPI\Client\Model\ResetPasswordPost200Response
```

تنظیم رمز عبور جدید

کاربر با استفاده از توکن بازیابی رمز عبور جدید خود را تنظیم می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$reset_password_post_request = new \OpenAPI\Client\Model\ResetPasswordPostRequest(); // \OpenAPI\Client\Model\ResetPasswordPostRequest

try {
    $result = $apiInstance->resetPasswordPost($reset_password_post_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->resetPasswordPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **reset_password_post_request** | [**\OpenAPI\Client\Model\ResetPasswordPostRequest**](../Model/ResetPasswordPostRequest.md)|  | |

### Return type

[**\OpenAPI\Client\Model\ResetPasswordPost200Response**](../Model/ResetPasswordPost200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `userGet()`

```php
userGet(): \OpenAPI\Client\Model\UserGet200Response
```

دریافت اطلاعات کاربر

اطلاعات کاربر احراز هویت شده را بازیابی می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearerAuth
$config = OpenAPI\Client\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);

try {
    $result = $apiInstance->userGet();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->userGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\OpenAPI\Client\Model\UserGet200Response**](../Model/UserGet200Response.md)

### Authorization

[bearerAuth](../../README.md#bearerAuth)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `verifyGet()`

```php
verifyGet($code): \OpenAPI\Client\Model\VerifyGet200Response
```

تأیید ایمیل کاربر

کد تأیید را دریافت کرده و حساب کاربر را فعال می‌کند.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new OpenAPI\Client\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$code = 'code_example'; // string | کد تأیید ارسال شده به ایمیل کاربر

try {
    $result = $apiInstance->verifyGet($code);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->verifyGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **code** | **string**| کد تأیید ارسال شده به ایمیل کاربر | |

### Return type

[**\OpenAPI\Client\Model\VerifyGet200Response**](../Model/VerifyGet200Response.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
