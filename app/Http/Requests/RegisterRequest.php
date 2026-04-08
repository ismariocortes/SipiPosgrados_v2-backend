<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/\D/', '', (string) $this->input('phone', ''));
        $identityRaw = $this->input('identity_value');
        $identity = $identityRaw !== null && $identityRaw !== ''
            ? Str::upper(preg_replace('/\s+/', '', (string) $identityRaw))
            : null;

        $this->merge([
            'phone' => $phone,
            'identity_value' => $identity,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $identityRules = [
            'required',
            'string',
            Rule::unique('users', 'identity_value'),
        ];

        if ($this->input('identity_type') === 'curp') {
            $identityRules[] = 'size:18';
            $identityRules[] = 'regex:/^[A-Z]{4}\d{6}[HM][A-Z]{5}[0-9A-Z]{2}$/';
        } else {
            $identityRules[] = 'regex:/^[A-Z0-9]{1,50}$/';
        }

        return [
            'identity_type' => ['required', Rule::in(['curp', 'passport'])],
            'identity_value' => $identityRules,
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'size:10', 'regex:/^\d{10}$/', Rule::unique('users', 'phone')],
        ];
    }
}
