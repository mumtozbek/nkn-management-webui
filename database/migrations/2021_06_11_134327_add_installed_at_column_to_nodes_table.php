<?php

use App\Models\Node;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class AddInstalledAtColumnToNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('down');

        Schema::table('nodes', function (Blueprint $table) {
            $table->timestamp('installed_at')->nullable(true)->after('city');
        });

        Node::withTrashed()->whereNull('installed_at')->get()->each(function ($node) {
            if ($firstConnection = $node->uptimes()->first()) {
                $installedAt = $firstConnection->created_at;
            } else {
                $installedAt = $node->created_at;
            }

            if (Carbon::now()->subSeconds($node->uptime)->lt($installedAt)) {
                $installedAt = Carbon::now()->subSeconds($node->uptime);
            }

            $node->update([
                'installed_at' => $installedAt,
            ]);
        });

        Artisan::call('up');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nodes', function (Blueprint $table) {
            $table->dropColumn('installed_at');
        });
    }
}
