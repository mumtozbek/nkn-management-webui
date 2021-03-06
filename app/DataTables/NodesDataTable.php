<?php

namespace App\DataTables;

use App\Models\Node;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class NodesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('status', function ($item) {
                if ($item->status == 'OFFLINE') {
                    $class = 'danger';
                } elseif ($item->status == 'GENERATE_ID') {
                    $class = 'info';
                } elseif ($item->status == 'PRUNING_DB') {
                    $class = 'secondary';
                } elseif ($item->status == 'WAIT_FOR_SYNCING') {
                    $class = 'warning';
                } elseif ($item->status == 'SYNC_STARTED') {
                    $class = 'primary';
                } elseif ($item->status == 'PERSIST_FINISHED') {
                    $class = 'success';
                } else {
                    $class = 'light';
                }

                return '<span class="badge badge-' . $class . '">' . $item->status . '</span>';
            })->addColumn('action', function ($item) {
                return implode('', [
                    '<form id="delete-' . $item->id . '" action="' . route('nodes.destroy', $item->id) . '" method="POST">',
                    '<input type="hidden" name="_method" value="DELETE">',
                    '<input type="hidden" name="_token" value="' . csrf_token() . '">',
                    '</form>',
                    '<div class="dt-buttons btn-group flex-wrap">',
                    '<a href="' . route('nodes.show', $item->id) . '" class="btn btn-primary" title="' . __('Show') . '"><i class="fa fa-eye"></i></a>',
                    '<a href="' . route('nodes.edit', $item->id) . '" class="btn btn-success" title="' . __('Edit') . '"><i class="fa fa-pencil"></i></a>',
                    '<a class="btn btn-danger" href="#" onclick="if(confirm(\'' . __('Do You really want to delete this node?') . '\')) document.getElementById(\'delete-' . $item->id . '\').submit()" title="' . __('Delete') . '"><i class="fa fa-trash"></i></a>',
                    '</div>',
                ]);
            })->filterColumn('account', function ($query, $keyword) {
                $query->whereRaw('CONCAT(providers.name, " (", accounts.name, ")") LIKE "%' . trim($keyword) . '%"')
                    ->orWhere('providers.name', 'LIKE', "%" . $keyword . "%")
                    ->orWhere('accounts.name', 'LIKE', "%" . $keyword . "%");
            })->filterColumn('speed', function ($query, $keyword) {
                return false;
            })->filterColumn('uptime', function ($query, $keyword) {
                return false;
            })->filterColumn('lifetime', function ($query, $keyword) {
                return false;
            })->filterColumn('proposals', function ($query, $keyword) {
                return false;
            })->filterColumn('status', function ($query, $keyword) {
                $query->where('nodes.status', 'LIKE', "%" . $keyword . "%");
            })->rawColumns(['status', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Node $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Node $model)
    {
        return $model
            ->leftJoin('accounts', 'account_id', '=', 'accounts.id')
            ->leftJoin('providers', 'accounts.provider_id', '=', 'providers.id')
            ->select(['nodes.id', 'nodes.host', 'nodes.country', 'nodes.region', 'nodes.city', 'nodes.status', 'nodes.version', 'nodes.height', 'nodes.ping'])
            ->selectRaw('ROUND(nodes.uptime / 3600, 2) AS uptime')
            ->selectRaw('CONCAT(providers.name, " (", accounts.name, ")") AS account')
            ->selectRaw('(SELECT SUM(proposals.count) FROM proposals WHERE proposals.node_id = nodes.id) AS proposals')
            ->selectRaw('(SELECT ROUND(speed, 2) FROM uptimes WHERE node_id = nodes.id ORDER BY created_at DESC LIMIT 1) AS speed')
            ->selectRaw('ROUND(TIMESTAMPDIFF(SECOND, nodes.installed_at, NOW()) / 3600, 2) AS lifetime');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('nodes-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy([12, 'desc'])
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('account'),
            Column::make('host'),
            Column::make('country'),
            Column::make('region'),
            Column::make('city'),
            Column::make('status'),
            Column::make('version'),
            Column::make('height'),
            Column::make('uptime'),
            Column::make('lifetime'),
            Column::make('proposals'),
            Column::make('speed'),
            Column::make('ping'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->editField(true)
                ->width(120)
                ->addClass('text-right'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Nodes_' . date('YmdHis');
    }
}
