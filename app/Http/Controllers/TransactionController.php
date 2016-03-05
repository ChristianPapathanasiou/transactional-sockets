<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Transaction;
use App\Project;
use App\Events\ProjectTransaction;
use Input;
use DateTime;
use DateInterval;
use Session;

class TransactionController extends Controller
{
    /**
    * Reserves a specified amount to a project
    * /v1/transaction/reserve
    * @return View
    */
    public function reserve(Request $request){
        // If user already is in temporary session, send to payment page
        if ($request->session()->has('transaction_id')){
            return redirect('transaction');
        }
        $project = Project::find($request->input('project_id'));
        // If amount > amount allowed return back
        if ($project->amount_goal - ($project->amount_reserved + intval($request->input('amount'))) < 0){
            return back()->withInput();
        }
        $date = new DateTime();
        $transaction = Transaction::create(array(
            'user_id' => 1,
            'project_id' => $request->input('project_id'),
            'amount' => $request->input('amount'),
            'expires_at' => $date->add(new DateInterval('PT5M'))
        ));
        // Save the transaction in a User Session
        $request->session()->put('transaction_id', $transaction->id);

        // Update the project amount reserved
        $project->amount_reserved = $project->amount_reserved + intval($request->input('amount'));
        $project->save();

        // Dispatch an event to the Front-End which will update the amount left
        event(new ProjectTransaction($project));

        return redirect('transaction');
    }

    /**
    * Completes a transaction
    * /v1/transaction/process
    * @return View
    */
    public function process(Request $request){
        // if there is no session there is nothing to process
        if (!$request->session()->has('transaction_id')){
            return redirect('/');
        }
        // The pull method will retrieve and delete an item from the session:
        $transaction = Transaction::find($request->session()->pull('transaction_id'));
        $transaction->transacted_at = new DateTime();
        $transaction->expires_at = null;
        $transaction->save();
        
        $project = Project::find($transaction->project_id);
        $project->amount_raised = $project->amount_raised + intval($request->input('amount'));
        $project->save();

        return view('thanks')->with('transaction', $transaction);
    }

    /**
    * Cancel a transaction
    * /v1/transaction/cancel
    * @return View
    */
    public function cancel(Request $request){
        // if there is no session there is nothing to cancel
        if (!$request->session()->has('transaction_id')){
            return redirect('/');
        }
        // The pull method will retrieve and delete an item from the session:
        $transaction = Transaction::find($request->session()->pull('transaction_id'));

        $project = Project::find($transaction->project_id);
        $project->amount_reserved = $project->amount_reserved - intval($transaction->amount);
        $project->save();

        Transaction::destroy($transaction->id);

        // Dispatch an event to the Front-End which will update the amount left
        event(new ProjectTransaction($project));

        return redirect('/');
    }
}
