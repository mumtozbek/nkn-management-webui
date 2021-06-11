<?php

namespace App\Models;

use App\Jobs\Dispatcher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Node extends Model
{
    use HasFactory, SoftDeletes;

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
        'ping',
        'country',
        'region',
        'city',
        'installed_at',
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($model) {
            $wallet = Wallet::whereNull('node_id')->orderBy('generated_at', 'DESC')->first();

            if (!$wallet) {
                $wallet = Wallet::generate();
            }

            $wallet->update([
                'node_id' => $model->id,
            ]);

            Dispatcher::dispatch($model, [
                "sudo sed -i 's/#PasswordAuthentication/PasswordAuthentication/' /etc/ssh/sshd_config",
                "sudo sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config",
                "sudo systemctl restart ssh",

                "sudo mkdir -p /home/nkn/nkn-commercial/services/nkn-node",
                "sudo echo '" . trim($wallet->keystore) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.json",
                "sudo echo '" . trim($wallet->password) . "' | sudo tee /home/nkn/nkn-commercial/services/nkn-node/wallet.pswd",

                "sudo wget -O install.sh 'http://" . env('INSTALLER_SERVER') . "/install.txt'",
                "sudo bash install.sh > /dev/null 2>&1 &",
            ]);
        });

        self::updating(function ($model) {
            if ($model->isDirty('host')) {
                $model->attributes['host'] = $model->getOriginal('host');
            }
        });

        self::deleted(function ($model) {
            if ($model->wallet) {
                $model->wallet->update(['node_id' => null]);
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
            'host' => "required|unique:nodes,host,$this->id,id,deleted_at,NULL",
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
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
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

    public function index($json, $info)
    {
        $count = (int)$json->result->height - (int)$this->height;

        $speed = $json->result->uptime > 0 ? round(($json->result->relayMessageCount / $json->result->uptime) * 3600, 2) : 0;

        if (Cache::has('nodes.mined.' . $this->id) && $json->result->proposalSubmitted > Cache::get('nodes.mined.' . $this->id, 0)) {
            $mined = $json->result->proposalSubmitted - Cache::get('nodes.mined.' . $this->id, 0);
        } else {
            $mined = 0;
        }

        if ($this->status == 'PERSIST_FINISHED' && $json->result->uptime <= $this->uptime) {
            $restartedAt = Carbon::now()->subSeconds($json->result->uptime);
        } else {
            $restartedAt = null;
        }

        $this->update([
            'status' => $json->result->syncState,
            'version' => $json->result->version,
            'height' => $json->result->height,
            'relays' => $json->result->relayMessageCount,
            'uptime' => $json->result->uptime,
            'ping' => $info['total_time'],
        ]);

        if (is_null($this->installed_at)) {
            $this->update([
                'installed_at' => Carbon::now()->subSeconds($json->result->uptime),
            ]);
        }

        $this->uptimes()->create([
            'speed' => $speed,
            'time_total' => $info['total_time'],
            'time_connect' => $info['connect_time'],
            'time_pretransfer' => $info['pretransfer_time'],
            'speed_upload' => $info['speed_upload'],
            'speed_download' => $info['speed_download'],
        ]);

        $this->blocks()->create([
            'count' => ($this->blocks()->count() > 0 ? $count : 0),
        ]);

        if ($mined > 0) {
            $this->proposals()->create([
                'count' => $mined,
                'speed' => $speed,
            ]);
        }

        if ($mined) {
            mail(env('MAIL_ADMIN'), "Node {$this->host} has just mined!", "Node {$this->host} has mined at speed {$speed} r/h!", '', '-f' . env('MAIL_FROM_ADDRESS'));
        }

        if ($restartedAt) {
            mail(env('MAIL_ADMIN'), "Node {$this->host} restarted!", "Node {$this->host} restarted at {$restartedAt}!", '', '-f' . env('MAIL_FROM_ADDRESS'));
        }

        Cache::forever('nodes.mined.' . $this->id, $json->result->proposalSubmitted);
    }
}
