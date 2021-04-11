<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Block extends Model
{
    use HasFactory;

    /**
     * Mass assignment fields.
     */
    protected $fillable = [
        'count',
    ];

    public static function getChartData($id = null)
    {
        $data = [];

        $query = DB::table('blocks')->selectRaw('node_id, count, created_at AS time')->groupByRaw('created_at, node_id')->orderByRaw('created_at ASC, count DESC');

        if (!empty($id)) {
            $query->where('node_id', $id);
        }

        $nodes = $query->get();
        $nodes = $nodes->groupBy('node_id');

        foreach ($nodes as $node_id => $records) {
            $node = Node::find($node_id);
            $color = Cache::get('nodes.colors.' . $node_id);

            if (is_null($color)) {
                $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
                Cache::forever('nodes.colors.' . $node_id, $color);
            }

            $dataset = [
                'label' => $node->host,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'borderWidth' => 1,
            ];

            foreach ($records as $record) {
                $dataset['data'][$record->time] = $record->count;
            }

            $data['datasets'][] = $dataset;
        }

        return [
            'type' => 'line',
            'data' => $data,
            'options' => [
                'maintainAspectRatio' => false,
                'scales' => ['y' => ['beginAtZero' => true]],
            ],
        ];
    }
}
