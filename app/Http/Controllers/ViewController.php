<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Transaction;

class ViewController extends Controller
{
    /**
    * Reserves a specified amount to a project
    * /
    * @return View
    */
    public function project(Request $request){
        // If a transaction is in progress - return to transaction page to allow user to complete or cancel transaction
        if ($request->session()->has('transaction_id')){
            return view('transaction')->with('transaction', Transaction::with('project')->find($request->session()->get('transaction_id')));
        }
        // This would ideally be passed in via route parameters but for a proof of concept we'll just use 1
        $project = Project::find(1);
        return view('index')->with('project', $project);
    }

    /**
    * Reserves a specified amount to a project
    * /transaction
    * @return View
    */
    public function transaction(Request $request){
        // If no transaction is in progress - return to project page
        if (!$request->session()->has('transaction_id')){
            return view('index')->with('project', Project::find(1));
        }
        return view('transaction')->with('transaction', Transaction::with('project')->find($request->session()->get('transaction_id')));
    }


}
