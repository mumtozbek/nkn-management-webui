<?php

namespace App\Models;

use App\Jobs\ExecuteCommand;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Node extends Model
{
    use HasFactory;

    /**
     * Mass assignment fields.
     */
    protected $fillable = [
        'host',
        'account_id',
        'status',
        'version',
        'height',
        'relays',
        'uptime',
        'country',
        'region',
        'city',
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $wallet = Wallet::whereNull('node_id')->first();
            if ($wallet) {
                $wallet->update([
                    'node_id' => $model->id,
                ]);

                ExecuteCommand::dispatch($model, [
                    "sudo mkdir -p /home/nkn/nkn-commercial/services/nkn-node",
                    "sudo echo '" . trim($wallet->keystore) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.json",
                    "sudo echo '" . trim($wallet->password) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd",
                    "sudo wget -O install.sh 'http://" . env('INSTALLER_SERVER') . "/install.txt'",
                    "sudo bash install.sh > /dev/null 2>&1 &",
                ]);
            }
        });

        self::updated(function ($model) {
            if ($model->isDirty('host')) {
                if ($model->wallet) {
                    ExecuteCommand::dispatch($model, [
                        "sudo mkdir -p /home/nkn/nkn-commercial/services/nkn-node",
                        "sudo echo '" . trim($model->wallet->keystore) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.json",
                        "sudo echo '" . trim($model->wallet->password) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd",
                    ]);
                }

                $model->uptimes()->delete();
                $model->blocks()->delete();
                $model->proposals()->delete();
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
            'host' => 'required|unique:nodes,host,' . $this->id,
            'account_id' => 'required|exists:accounts,id',
        ];
    }

    /**
     * Get the path of current node.
     *
     * @return string
     */
    public function path()
    {
        return route('nodes.show', $this->id);
    }

    /**
     * Uptimes relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uptimes()
    {
        return $this->hasMany(Uptime::class);
    }

    /**
     * Blocks relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    /**
     * Proposals relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Wallet relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Account relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Provider relation.
     *
     * @return mixed
     */
    public function provider()
    {
        return $this->account->provider();
    }

    public function index($json)
    {
        $count = (int)$json->result->height - (int)$this->height;

        if (Cache::has('nodes.mined.' . $this->id) && $json->result->proposalSubmitted > Cache::get('nodes.mined.' . $this->id, 0)) {
            $mined = $json->result->proposalSubmitted - Cache::get('nodes.mined.' . $this->id, 0);
        } else {
            $mined = 0;
        }

        $this->update([
            'status' => $json->result->syncState,
            'version' => $json->result->version,
            'height' => $json->result->height,
            'relays' => $json->result->relayMessageCount,
            'uptime' => $json->result->uptime,
        ]);

        $this->uptimes()->create([
            'speed' => (($json->result->relayMessageCount / $json->result->uptime) * 3600),
            'response' => $json,
        ]);

        $this->blocks()->create([
            'count' => ($this->blocks()->count() > 0 ? $count : 0),
        ]);

        $this->proposals()->create([
            'count' => $mined,
        ]);

        if ($mined) {
            mail(env('MAIL_ADMIN'), "Node {$this->host} has just mined!", "Node {$this->host} has just mined!", '', '-f' . env('MAIL_FROM_ADDRESS'));
        }

        Cache::forever('nodes.mined.' . $this->id, $json->result->proposalSubmitted);
    }

    public function reindex($json, $date)
    {
        if (empty($json->result)) {
            return false;
        }

        $count = (int)$json->result->height - (int)$this->height;

        if (Cache::has('nodes.mined.' . $this->id) && $json->result->proposalSubmitted > Cache::get('nodes.mined.' . $this->id, 0)) {
            $mined = $json->result->proposalSubmitted - Cache::get('nodes.mined.' . $this->id, 0);
        } else {
            $mined = 0;
        }

        $this->update([
            'status' => $json->result->syncState,
            'version' => $json->result->version,
            'height' => $json->result->height,
            'relays' => $json->result->relayMessageCount,
            'uptime' => $json->result->uptime,
        ]);

        $this->blocks()->create([
            'count' => ($this->blocks()->count() > 0 ? $count : 0),
            'created_at' => $date,
        ]);

        $this->proposals()->create([
            'count' => $mined,
            'created_at' => $date,
        ]);

        if ($mined) {
            Log::debug("Node {$this->host} has just mined!");
        }

        Cache::set('nodes.mined.' . $this->id, $json->result->proposalSubmitted);
    }
}
