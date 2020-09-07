<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\S\Store\MicroPageService;
use App\Module\DiyComponentValidatorModule;

class ValidateMicroPageComponentInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:micropage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $microPageService = new MicroPageService();
		foreach ($microPageService->cursor([], function ($record) {
			$componentValidator = new DiyComponentValidatorModule();
			$validateResult = $componentValidator->validateComponents($record->page_template_info);
			if ($componentValidator->getErrno() > 0) {
				return $record->id . '.' . $componentValidator->getErrmsg();
			}
		}) as $result) {
			if (!is_null($result)) {
				$this->warn($result);
			}
		}
    }
}
