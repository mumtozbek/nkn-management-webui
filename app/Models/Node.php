<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        $mined = false;
        $result = $json->result;
        $speed = ($result->relayMessageCount / $result->uptime) * 3600;
        $blocks = (int)$result->height - (int)$this->height;

        if ($result->proposalSubmitted > $this->proposals()->sum('count')) {
            $mined = true;
        }

        $this->update([
            'status' => $result->syncState,
            'version' => $result->version,
            'height' => $result->height,
            'relays' => $result->relayMessageCount,
            'uptime' => $result->uptime,
        ]);

        $this->uptimes()->create([
            'speed' => $speed,
            'response' => json_encode($json),
        ]);

        if ($result->syncState == 'PERSIST_FINISHED') {
            $this->proposals()->create([
                'count' => (int)$mined,
            ]);

            if ($mined) {
                mail(env('MAIL_ADMIN'), "Node {$this->host} has just mined!", "Node {$this->host} has just mined!", '', '-f' . env('MAIL_FROM_ADDRESS'));
            }
        } else {
            if ($blocks > 0) {
                $this->blocks()->create([
                    'count' => $blocks,
                ]);
            }
        }
    }

    public function reindex($json, $date)
    {
        if (empty($json->result)) {
            return false;
        }

        $mined = false;
        $result = $json->result;
        $speed = ($result->relayMessageCount / $result->uptime) * 3600;
        $blocks = (int)$result->height - (int)$this->height;

        if ($result->proposalSubmitted > $this->proposals()->sum('count')) {
            $mined = true;
        }

        $this->update([
            'status' => $result->syncState,
            'version' => $result->version,
            'height' => $result->height,
            'relays' => $result->relayMessageCount,
            'uptime' => $result->uptime,
        ]);

        if ($result->syncState == 'PERSIST_FINISHED') {
            $this->proposals()->create([
                'count' => (int)$mined,
                'created_at' => $date,
            ]);
        } else {
            if ($blocks > 0) {
                $this->blocks()->create([
                    'count' => $blocks,
                    'created_at' => $date,
                ]);
            }
        }
    }
}
