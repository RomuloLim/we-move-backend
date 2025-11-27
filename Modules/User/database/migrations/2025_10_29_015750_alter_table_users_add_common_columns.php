<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->comment('Cadastro de Pessoa Física (CPF)');
            $table->string('rg')->nullable()->comment('Registro Geral (RG)');
            $table->string('phone_contact')->nullable()->comment('Telefone de contato');
            $table->string('gender')->nullable()->comment('Gênero do usuário');
            $table->string('profile_picture_url')->nullable()->comment('URL da foto de perfil');
            $table->unique(['cpf', 'rg']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
