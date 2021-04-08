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
        'status',
        'version',
        'height',
        'proposals',
        'relays',
        'speed',
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
}
