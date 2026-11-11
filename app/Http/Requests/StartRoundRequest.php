<?php

namespace App\Http\Requests;

use App\Levels\LevelRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StartRoundRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'level_id' => ['required', 'string', Rule::in(app(LevelRepository::class)->all()->keys()->all())],
        ];
    }
}
