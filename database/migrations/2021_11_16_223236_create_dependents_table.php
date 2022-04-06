<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDependentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependents', function (Blueprint $table) {
            $table->increments('dependent_id')->unsigned(false);
            $table->string('member_name');
            $table->string('member_relation');
            $table->string('date_of_birth');
            $table->string('sex');
            $table->string('member_occupation');
            $table->string('member_sector');
            $table->string('member_health_condition');
            $table->integer('beneficiary_id');
            $table->integer('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependents');
    }
}
