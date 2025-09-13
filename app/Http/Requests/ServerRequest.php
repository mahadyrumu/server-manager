<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('servers')->where(fn($query) => $query->where('provider', $this->provider))
            ],
            'ip_address' => 'required|ipv4|unique:servers,ip_address',
            'provider' => 'required|in:aws,digitalocean,vultr,other',
            'status' => 'required|in:active,inactive,maintenance',
            'cpu_cores' => 'required|integer|between:1,128',
            'ram_mb' => 'required|integer|between:512,1048576',
            'storage_gb' => 'required|integer|between:10,1048576',
        ];
    }
}
