<?php

namespace App\Http\Requests;

use App\Helpers\Utils;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DownloadVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => 'nullable',
            'content_url' => 'nullable',
            'comment' => 'nullable',
            'description' => 'nullable',
            'is_prod' => 'nullable|boolean',
            'video' => 'nullable',
            'type' => 'required',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->url) {
            $type = Utils::getSocialTypeByLink($this->url);
        } else if ($this->content_url) {
            $type = 'simple';
        } else if ($this->video) {
            $type = 'video';
        }

        $this->merge([
            'type' => $type ?? null,
            'is_prod' => $this->is_prod === 'on',
        ]);
    }
}
