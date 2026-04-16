> Ok, so can you explain how do I use this new implementation? For example this service @Domain/Bookmarks/ Services/MarkerAiSummaryService.php uses the @Domain/Bookmarks/Tasks/MarkerAiSummaryTask.php task to load a provider at random (@app/Factories/ProviderFactory.php) then uses a Laravel AI agent on @Domain/Bookmarks/Ai/Agents/MarkerAiSummaryAgent.php to ask the AI to generate a summary and a set of tags for the page on the given URL. The entire Domain folder is on a state of flux at the moment as I need to migrate conver it into a Module, so don't change any code, just give me an example on how to use this implementation in that case.


• Use it in two steps.

  First, the user has to configure at least one AI provider and one AI model through the new endpoints. For
  your bookmark summary case, the model needs supports_text = true.

```
  POST /api/v1/me/ai/providers
  Idempotency-Key: create-openai
  Authorization: Bearer <token>
  Content-Type: application/json

  {
    "code": "openai",
    "api_key": "sk-...",
    "url": "https://api.openai.com/v1"
  }

  POST /api/v1/me/ai/providers/{provider_id}/models
  Idempotency-Key: create-openai-model
  Authorization: Bearer <token>
  Content-Type: application/json

  {
    "name": "gpt-4.1-mini",
    "supports_text": true,
    "can_web": false
  }
```

  Then, in a service/task flow like your bookmark summary flow, stop asking ProviderFactory for a random
  config-backed provider. Resolve a user-scoped runtime provider instead:

```
  use App\Services\AI\UserAiResolver;

  /** @var \App\Dtos\AI\ResolvedUserAiProvider $resolved */
  $resolved = resolve(UserAiResolver::class)->resolve(
      user: $user,
      feature: 'text',
  );
```

  That gives you:

  - providerName: the transient provider name Laravel AI can use at runtime
  - model: the saved user model name
  - lab: the provider enum
  - canWeb: the per-model web flag
  - featureCapabilities: the stored capability map

  For a MarkerAiSummaryTask shape, the usage is basically:

  $resolved = resolve(UserAiResolver::class)->resolve($user, 'text');

  $response = (new MarkerAiSummaryAgent())->prompt(
      prompt: $prompt,
      provider: $resolved->providerName,
      model: $resolved->model,
  );

  So the flow for your bookmark example becomes:

  1. MarkerAiSummaryService knows which authenticated user owns the bookmark.
  2. It passes that User into MarkerAiSummaryTask.
  3. MarkerAiSummaryTask calls UserAiResolver::resolve($user, 'text').
  4. The resolver injects the user’s saved credentials into config('ai.providers.<runtime-name>') and purges
     the cached AI instance.
  5. The task calls the Laravel AI agent with provider: $resolved->providerName and model: $resolved->model.
  6. The agent runs exactly like before, except the credentials and model now come from the user’s saved
     settings instead of config/ai.php and config/constants.php.

  If you want provider selection to stay “random”, do it at the database level now, not with
  ProviderFactory. In other words, either: