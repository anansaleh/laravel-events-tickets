<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
         * Notice:
         * The important line is this one : $table->uuid('ticket_id')->primary();
         * Notice I'm using the uuid() method instead of the common increments() method.
         * As you can see, the uuid() method specifies the column should be a UUID equivalent column and I also specify that it should be the primary key on the table.
         */
        Schema::create('events_tickets', function (Blueprint $table) {
            // NOTICE
            $table->uuid('ticket_id')->primary();
            //////

            $table->unsignedInteger('event_id');
            $table->boolean('status')->default(1); //1: OK , 0: redeemed
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->foreign('event_id', 'fk_events_tickets_event_id')->references('event_id')->on('events')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events_tickets');
    }
}
