<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $actor = $this->user();
        if (! $actor) {
            return false;
        }

        $requestedRole = (string) $this->input('role', User::ROLE_CASHIER);

        if ($this->isMethod('POST')) {
            return $actor->can('create', User::class)
                && $actor->can('assignRole', [User::class, $requestedRole]);
        }

        $target = $this->route('user');
        if ($target instanceof User) {
            if (! $actor->can('update', $target)) {
                return false;
            }

            $currentRole = $target->roleInBusiness($actor->current_business_id);
            if ($requestedRole !== $currentRole && $requestedRole !== $target->role) {
                return $actor->can('assignRole', [User::class, $requestedRole]);
            }

            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => ['required', 'in:super_admin,owner,admin,cajero'],
            'status' => ['required', 'in:active,inactive'],
        ];

        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', 'min:6'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:6'];
        }

        return $rules;
    }

    /**
     * Custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre completo',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'role' => 'rol',
            'status' => 'estado',
        ];
    }
}
