@extends('templates.app')

@section('title', ' - Thanks')

@section('body-class', '')

@section('content')
    <div class="col-sm-4 col-sm-offset-4">
        <div class="alert alert-success">Thank you for your payment!</div>
        <a href="<?= url('/') ?>" class="btn btn-primary">Back to the Project</a>
    </div>
@endsection
        