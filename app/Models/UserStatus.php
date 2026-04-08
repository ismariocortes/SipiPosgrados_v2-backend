<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserStatus extends Model
{
    public const CODE_QUICK_REGISTRATION = 'quick_registration';

    public const CODE_WITH_FOLIO = 'with_folio';

    public const CODE_PROFILE_COMPLETE = 'profile_complete';

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
