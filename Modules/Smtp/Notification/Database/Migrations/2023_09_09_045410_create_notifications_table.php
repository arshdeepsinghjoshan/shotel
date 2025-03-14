<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sqlFilePath = __DIR__ . '/../db/install.sql';
        $sqlStatements = file_get_contents($sqlFilePath);
        $statements = explode(';', $sqlStatements);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                DB::statement($statement);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
