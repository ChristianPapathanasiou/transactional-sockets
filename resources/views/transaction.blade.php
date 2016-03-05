@extends('templates.app')

@section('title', ' - Transaction')

@section('body-class', 'transaction project-transaction')

@section('content')
    <div class="col-sm-4 col-sm-offset-4">
        <div class="project-transaction" v-cloak>
            <h1 class="text-center">{{ $transaction->project->name }}</h1>
            <form id="form" action="/transaction/process" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input id="expires_at" type="hidden" name="expires_at" value="{{ $transaction->expires_at }}">
                 <div class="form-group">
                    <label for="amount">Amount to Invest</label>
                    <input type="number" name="amount" class="form-control" disabled value="{{ $transaction->amount }}" />
                </div>
                <div class="alert" v-bind:class="{ 'alert-success': time_left >= 120000, 'alert-warning': time_left < 120000 && time_left >= 30000, 'alert-danger': time_left < 30000 }" v-if="time_left">
                    Time Left: @{{ time_left_display }}
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                </div>
                <div class="form-group">
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-danger" formaction="/transaction/cancel">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection