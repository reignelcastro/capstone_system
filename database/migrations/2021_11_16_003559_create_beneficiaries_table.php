<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->increments('beneficiary_id')->unsigned(false);
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename');
            $table->string('suffix')->nullable();
            $table->enum('gender',['MALE','FEMALE']);
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city_municipality')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->string('address_line')->nullable();
            $table->string('type_of_id')->nullable();
            $table->string('id_number')->nullable();
            $table->date('birthdate');
            $table->string('occupation')->nullable();
            $table->string('monthly_income')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('workplace_and_address')->nullable();
            $table->string('sector')->nullable();
            $table->string('health_condition')->nullable();
            $table->string('beneficiary_type')->nullable();
            $table->string('ip_group')->nullable();
            $table->string('beneficiary_type_others')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_submitted')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->integer('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiaries');
    }
}
