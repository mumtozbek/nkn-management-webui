<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'node_id',
        'address',
        'keystore',
        'password',
        'generated_at',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'node_id' => 'nullable|exists:nodes,id|unique:wallets,node_id,' . $this->id,
            'address' => 'required|string|min:36|max:36|unique:wallets,address,' . $this->id,
            'keystore' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * Node relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function node()
    {
        return $this->belongsTo(Node::class);
    }
}
