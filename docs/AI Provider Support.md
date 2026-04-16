### [Provider Support](https://laravel.com/docs/13.x/ai-sdk#provider-support)

The AI SDK supports a variety of providers across its features. The following table summarizes which providers are available for each feature:

|Feature|Providers|
|---|---|
|Text|OpenAI, Anthropic, Gemini, Azure, Groq, xAI, DeepSeek, Mistral, Ollama|
|Images|OpenAI, Gemini, xAI|
|TTS|OpenAI, ElevenLabs|
|STT|OpenAI, ElevenLabs, Mistral|
|Embeddings|OpenAI, Gemini, Azure, Cohere, Mistral, Jina, VoyageAI|
|Reranking|Cohere, Jina|
|Files|OpenAI, Anthropic, Gemini|

The `Laravel\Ai\Enums\Lab` enum may be used to reference providers throughout your code instead of using plain strings: