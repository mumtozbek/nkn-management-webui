<?php

namespace App\DataTables;

use App\Models\Account;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AccountsDataTable extends DataTable
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
            ->addColumn('provider', function ($item) {
                return $item->provider->name ?? '';
            })->addColumn('action', function ($item) {
                return implode('', [
                    '<form id="delete-' . $item->id . '" action="' . route('accounts.destroy', $item->id) . '" method="POST">',
                    '<input type="hidden" name="_method" value="DELETE">',
                    '<input type="hidden" name="_token" value="' . csrf_token() . '">',
                    '</form>',
                    '<div class="dt-buttons btn-group flex-wrap">',
                    '<a href="' . route('accounts.show', $item->id) . '" class="btn btn-primary">' . __('Show') . '</a>',
                    '<a href="' . route('accounts.edit', $item->id) . '" class="btn btn-success">' . __('Edit') . '</a>',
                    '<a class="btn btn-danger" href="#" onclick="if(confirm(\'' . __('Do You really want to delete this account?') . '\')) document.getElementById(\'delete-' . $item->id . '\').submit()">' . __('Delete') . '</a>',
                    '</div>',
                ]);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Account $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Account $model)
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
            ->setTableId('accounts-table')
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
            Column::make('provider'),
            Column::make('name'),
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
        return 'Accounts_' . date('YmdHis');
    }
}
