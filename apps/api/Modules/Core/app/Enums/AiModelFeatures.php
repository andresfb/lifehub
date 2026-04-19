<?php

declare(strict_types=1);

namespace Modules\Core\Enums;

enum AiModelFeatures: string
{
    case text = 'supports_text';
    case images = 'supports_images';
    case tts = 'supports_tts';
    case stt = 'supports_stt';
    case embeddings = 'supports_embeddings';
    case reranking = 'supports_reranking';
    case files = 'supports_files';
}
