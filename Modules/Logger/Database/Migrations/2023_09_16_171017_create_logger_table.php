<?php
/**
 *@copyright : ASk. < http://arshresume.epizy.com/ >
 *@author	 : Arshdeep Singh < arshdeepsinghjoshan84@gmail.com >
 *
 * All Rights Reserved.
 * Proprietary and confidential :  All information contained herein is, and remains
 * the property of ASK. and its partners.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 */
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
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
        Schema::dropIfExists('logs');
    }
};
