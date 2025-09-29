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
            $table->string('phone', 20)->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('occupation', 50)->nullable()->after('birth_date');
            $table->string('workplace')->nullable()->after('occupation');
            $table->string('experience_years', 20)->nullable()->after('workplace');
            $table->text('bio')->nullable()->after('experience_years');
            $table->string('profile_image')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'birth_date',
                'occupation',
                'workplace',
                'experience_years',
                'bio',
                'profile_image'
            ]);
        });
    }
};
