<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScorePaintingRequest extends FormRequest
{
    /** Roughly a 4 MB image once base64 encoded. */
    public const MAX_LENGTH = 6_000_000;

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'image' => ['required', 'string', 'max:'.self::MAX_LENGTH, 'regex:#^data:image/(png|jpeg);base64,#'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['image.regex' => 'The painting must be a PNG or JPEG data URL.'];
    }
}
