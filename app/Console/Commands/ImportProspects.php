<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ImportErpProspects;
use Exception;
use Illuminate\Console\Command;

final class ImportProspects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-prospects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import prospects from the ERP system into the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('Starting ERP prospects import...');
            new ImportErpProspects()->handle();
            $this->info('ERP prospects import completed successfully!');
        } catch (Exception $e) {
            $this->error('Failed to import ERP prospects: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
