<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateShopSubscriptionsTableAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if(!Schema::hasColumn('shops_subscriptions', 'status')){
            Schema::table('shops_subscriptions', function(Blueprint $table){
                $table->boolean('status')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('shop_subscriptions', 'status')) {
            Schema::table('shop_subscriptions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
}