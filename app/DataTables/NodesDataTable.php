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
            ->addColumn('action', function ($item) {
                return implode('', [
                    '<div class="dt-buttons btn-group flex-wrap">',
                    '<a href="' . route('nodes.show', $item->id) . '" class="btn btn-primary">' . __('Show') . '</a>',
                    '<a href="' . route('nodes.edit', $item->id) . '" class="btn btn-success">' . __('Edit') . '</a>',
                    '</div>',
                ]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Node $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Node $model)
    {
        return $model->newQuery();
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
            ->orderBy(1)
            ->parameters([
                'relays' => [
                    0, 'desc'
                ]
            ])
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
            Column::make('host'),
            Column::make('status'),
            Column::make('version'),
            Column::make('height'),
            Column::make('proposals'),
            Column::make('relays'),
            Column::make('speed'),
            Column::make('uptime'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->editField(true)
                ->width(200)
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
