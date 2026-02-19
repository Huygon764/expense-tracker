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
            $table->string('google_id')->nullable()->unique()->after('password');
            $table->boolean('email_notification')->default(true)->after('remember_token');
            $table->decimal('monthly_income', 12, 2)->nullable()->after('email_notification');
            $table->timestamp('onboarding_completed_at')->nullable()->after('monthly_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'email_notification', 'monthly_income', 'onboarding_completed_at']);
        });
    }
};
