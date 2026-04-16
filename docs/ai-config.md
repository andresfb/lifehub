> when using Laravel's Ai SDK, is there a way to dynamically override the information it uses from the ai.php config? My system will allow users to configure their own Providers, keys and urls so I need a way for the SDK to not use the values in the config file, but the ones from the user."	

--

Yes, but not through providerOptions(). That only changes generation parameters. It doesn't change credentials or base URLs.

For laravel/ai 0.4.5, the SDK resolves provider credentials from config('ai.providers.<name>'), and the provider instance is cached by name. You can see that in the docs (https://github.com/laravel/docs/blob/13.x/ai-sdk.md#configuration), vendor/laravel/ai/src/AiManager.php: 455, vendor/laravel/ai/src/Providers/Provider.php:29, and vendor/laravel/ai/src/Gateway/OpenAi/Concerns/CreatesOpenAiClient.php:14.

  The practical pattern is:

```php
  use Laravel\Ai\Ai;

  $providerName = 'user-openai-'.$user->id;

  config([
      "ai.providers.{$providerName}" => [
          'driver' => 'openai',
          'key' => $user->openai_api_key,
          'url' => $user->openai_base_url, // optional
      ],
  ]);

  Ai::purge($providerName);

  $response = (new YourAgent)->prompt(
      'Hello',
      provider: $providerName,
      model: $user->openai_model,
  );
```  

  Important details:

 - provider: ... can be overridden per prompt. That's the supported runtime hook from the docs.
 - The credentials and url still come from ai.providers.<providerName>.
 - If you reuse the same provider name with different user values, call Ai::purge($providerName) after
    changing config. Otherwise the old instance may be reused.
 - If you're on Octane, Horizon, queues, or any long-lived worker, purging matters even more.
 - providerOptions() is for things like reasoning effort or penalties, not API keys or URLs.

  If you want this cleanly, build a small service like UserAiProviderResolver that:

  1. Maps a user's saved provider settings to an ai.providers.* config entry.
  2. Purges that provider name.
  3. Returns the provider name and model to use for the prompt.

  If you want, I can turn that into a concrete Laravel service and usage pattern for your app.