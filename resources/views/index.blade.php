@extends('templates.app')

@section('title', ' - Project Name')

@section('body-class', 'project project-transaction')

@section('content')
    <div class="project-amount" v-cloak>
        <h1 class="text-center">@{{ project.name }}</h1>
        <h2 class="text-center">@{{ project.amount_reserved }} / @{{ project.amount_goal }}</h2>
        <form action="/transaction/reserve" method="POST">
            <div class="col-sm-4 col-sm-offset-4">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <div class="form-group">
                    <label for="amount">Amount to Invest</label>
                    <input type="number" name="amount" class="form-control" placeholder="$$.$$" v-model="amount" />
                    <p class="helper-text">(Maximum amount: $ @{{ amount_left }})</p>
                    <div class="alert alert-danger" v-if="invalid">You've exceed the Maximum amount! Please enter a lower amount.</div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
        