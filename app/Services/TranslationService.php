<?php

namespace App\Services;

use App\Models\Translation;

class TranslationService
{
    public function saveTranslation(string $modelType, int $modelId, string $language, string $field, string $value): void
    {
        Translation::updateOrCreate(
            [
                'trans_type' => $modelType,
                'trans_id' => $modelId,
                'language' => $language,
                'field' => $field,
            ],
            ['value' => $value]
        );
    }
}