<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    /**
     * Mass assignment fields.
     */
    protected $fillable = [
        'name',
        'provider_id',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:50',
            'provider_id' => 'required|exists:providers,id',
        ];
    }

    /**
     * Node relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
