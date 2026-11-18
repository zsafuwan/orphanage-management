<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnableChildPhotos extends Command
{
    protected $signature = 'care:enable-child-photos';

    protected $description = 'Add an optional private photo path to the existing Child table';

    public function handle(): int
    {
        if (! Schema::hasTable('Child')) {
            $this->error('The existing Child table was not found. No table was created.');

            return self::FAILURE;
        }
        if (! Schema::hasColumn('Child', 'Child_Photo_Path')) {
            Schema::table('Child', function (Blueprint $table) {
                $table->string('Child_Photo_Path', 255)->nullable();
            });
        }
        $this->info('Child_Photo_Path is available on the existing Child table.');

        return self::SUCCESS;
    }
}
