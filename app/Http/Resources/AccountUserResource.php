<?php

namespace App\Http\Resources;

use App\Models\AccountUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AccountUser */
class AccountUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'account_id' => $this->account_id,
            'user_id' => $this->user_id,

            'account' => new AccountResource($this->whenLoaded('account')),
        ];
    }
}
