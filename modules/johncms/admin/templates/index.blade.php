@extends('system::layout/default')
@section('content')
    <h1><?= __('Dashboard') ?></h1>

    <div class="d-block d-md-none text-center">
        <button class="show_menu_btn btn btn-outline-primary w-100"><?= __('Show admin menu') ?></button>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3">
        <div class="col mt-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="h1 fw-bold"><?= $data['last_day_users'] ?></div>
                    <div class="mt-2 h4"><?= __('registered visitors today') ?></div>
                </div>
            </div>
        </div>
        <div class="col mt-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="h1 fw-bold"><?= $data['registered_users'] ?></div>
                    <div class="mt-2 h4"><?= __('users registered today') ?></div>
                </div>
            </div>
        </div>
        <div class="col mt-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <div class="h1 fw-bold"><?= $data['forum_messages'] ?></div>
                    <div class="mt-2 h4"><?= __('posts on the forum today') ?></div>
                </div>
            </div>
        </div>
    </div>
@endsection
