<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitHighScoreRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'player_name' => ['required', 'string', 'min:1', 'max:24'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Collapse runs of whitespace so "   " fails validation and names stay tidy.
        $this->merge(['player_name' => $this->string('player_name')->squish()->toString()]);
    }
}
