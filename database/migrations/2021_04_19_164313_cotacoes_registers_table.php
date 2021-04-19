<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CotacoesRegistersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotacoes_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('code');
            $table->string('codein');
            $table->string('name');
            $table->float('hight', 8, 4);
            $table->float('low', 8, 4);
            $table->float('varBid', 8, 4);
            $table->float('pctChange', 8, 2);
            $table->float('bid', 8, 2);
            $table->float('ask', 8, 2);
            $table->bigInteger('timestamp');
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
        Schema::dropIfExists('cotacoes_registers');
    }
}
