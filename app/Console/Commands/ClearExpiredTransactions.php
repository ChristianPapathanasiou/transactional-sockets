<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Transaction;
use App\Project;
use DateTime;
use App\Events\ProjectTransaction;
use Log;

class ClearExpiredTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expired:transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears transactions that have expired from the database and recalculates the projects amount_reserved';

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
        // Store the projects that have transactions that have expired
        $project_ids = [];
        $expired_transactions = Transaction::where('expires_at', '<', new DateTime())->whereNull('transacted_at')->get();
        foreach($expired_transactions as $expired_transaction){
            // Calculate the new amount_reserved
            $project = Project::find($expired_transaction->project_id);
            $project->amount_reserved = $project->amount_reserved - $expired_transaction->amount;
            $project->save();
            if(!in_array($expired_transaction->project_id, $project_ids)){
                // Create an array of project_ids that have been effected
                $project_ids[] = $expired_transaction->project_id;
            }
            Log::info('destroyed transaction '. $expired_transaction->id);
            Transaction::destroy($expired_transaction->id);
        }
        foreach($project_ids as $project_id){
            // Dispatch an event to the Front-End which will update the amount left
            event(new ProjectTransaction(Project::find($project_id)));
        }
    }
}
