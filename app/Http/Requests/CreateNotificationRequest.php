<?php

namespace App\Http\Requests;

use App\Notifications\BaseNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'min:4', 'max:255', function ($attribute, $value, $fail) {
                is_subclass_of('App\Notifications\\' . ucfirst($value), BaseNotification::class) || $fail('Wrong notification type');
            }],
            'locale' => ['nullable', 'size:2'],
            'channelsChainStrategy' => [Rule::in(['failover', 'multi'])],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*.name' => ['required'],
            'channels.*.route' => ['required'],
            'variables' => ['array'],
            'variables.name' => ['nullable'],
            'variables.email' => ['nullable', 'email'],
        ];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->json()->get($key, $default);
    }

    public function keys(): array
    {
        return $this->json()->keys();
    }

    public function all($keys = null): array
    {
        return $this->json()->all($keys);
    }

}
