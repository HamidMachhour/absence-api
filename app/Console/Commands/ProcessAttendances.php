<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Web\AttendanceController;
use App\Models\SchoolConfiguration;

class ProcessAttendances extends Command
{
    protected $signature = 'attendance:process';
    protected $description = 'Process attendance lists for starting and ending seances';

    public function handle()
    {
        $this->info('Checking process_attendances flag...');

        $config = SchoolConfiguration::getSettings();
        if ($config && isset($config->process_attendances_enabled) && !$config->process_attendances_enabled) {
            $this->info('Processing of attendances is disabled by admin. Skipping.');
            return 0;
        }

        $this->info('Processing attendances...');
        AttendanceController::handlePeriodicTasks();
        $this->info('Done processing attendances.');
        return 0;
    }
}
