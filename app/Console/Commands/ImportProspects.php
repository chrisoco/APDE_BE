<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Import\ImportErpProspects;
use App\Actions\Import\ImportKuebaProspects;
use App\Enums\ProspectDataSource;
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

            foreach (ProspectDataSource::cases() as $source) {

                $this->info("Starting {$source->label()} prospects import...");

                /** @var ImportErpProspects|ImportKuebaProspects $importer */
                $importer = new ($source->importAction());
                $importer->handle();

                $this->info("{$source->label()} prospects import completed successfully!");

            }

        } catch (Exception $exception) {
            $this->error('Failed to import ERP prospects: '.$exception->getMessage());

            return 1;
        }

        return 0;
    }
}
