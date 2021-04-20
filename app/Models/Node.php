<?php

namespace App\Models;

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
        $count = (int)$json->result->height -(int)$this->height;

        if (Cache::has('nodes.mined.' . $this->id) && $json->result->proposalSubmitted > Cache::get('nodes.mined.' . $this->id, 0)) {
            $mined = 1;
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
            'count' => $count,
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

        $count = (int)$json->result->height -(int)$this->height;

        if (Cache::has('nodes.mined.' . $this->id) && $json->result->proposalSubmitted > Cache::get('nodes.mined.' . $this->id, 0)) {
            $mined = 1;
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
            'count' => $count,
            'created_at' => $date,
        ]);

        $this->proposals()->create([
            'count' => $mined,
            'created_at' => $date,
        ]);

        if ($mined) {
            Log::channel('debug.reindex')->info("Node {$this->host} has just mined!");
        }

        Cache::set('nodes.mined.' . $this->id, $json->result->proposalSubmitted);
    }
}
