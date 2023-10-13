<?php>
    Route::breadcrumbs(function (BreadcrumbsGenerator $trail) {
    $trail->push('Home', route('home'));

    $trail->push('Categoria', route('category'));
    });

