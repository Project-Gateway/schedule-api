<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create1494477814AppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('client_id')->unsigned()->nullable();
                $table->foreign('client_id', '35989_5913ebf64ed0b')->references('id')->on('clients')->onDelete('cascade');
                $table->integer('provider_id')->unsigned()->nullable();
                $table->integer('service_id')->unsigned()->nullable();
                $table->foreign(['provider_id', 'service_id'])->references(['provider_id', 'service_id'])->on('provider_service')->onDelete('cascade');
                $table->datetime('start_time')->nullable();
                $table->datetime('finish_time')->nullable();
                $table->text('comments')->nullable();
                
                $table->timestamps();
                $table->softDeletes();

                $table->index(['deleted_at']);
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
        Schema::dropIfExists('appointments');
    }
}
