<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvidenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evidence', function (Blueprint $table) {
            $table->id();
            $table->string('evidence_type');
            $table->string('ack_no')->nullable();
            $table->string('url')->nullable();
            $table->string('domain')->nullable();
            $table->string('registry_details')->nullable();
            $table->string('ip')->nullable();
            $table->string('registrar')->nullable();
            $table->string('pdf')->nullable();
            $table->string('screenshots')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('evidence');
    }
}
