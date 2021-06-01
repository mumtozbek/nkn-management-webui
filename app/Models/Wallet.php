<?php

namespace App\Models;

use App\Jobs\Dispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;

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

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            if ($model->node) {
                Dispatcher::dispatch($model->node, [
                    "sudo mkdir -p /home/nkn/nkn-commercial/services/nkn-node",
                    "sudo echo '" . trim($model->keystore) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.json",
                    "sudo echo '" . trim($model->password) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd",
                    "sudo systemctl restart nkn-commercial",
                ]);
            }
        });

        self::updated(function ($model) {
            if ($model->node && ($model->isDirty('node_id') || $model->isDirty('address'))) {
                Dispatcher::dispatch($model->node, [
                    "sudo mkdir -p /home/nkn/nkn-commercial/services/nkn-node",
                    "sudo echo '" . trim($model->keystore) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.json",
                    "sudo echo '" . trim($model->password) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd",
                    "sudo systemctl restart nkn-commercial",
                ]);
            }
        });
    }

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
