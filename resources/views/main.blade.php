@extends('welcome')

@section('content')

<main role="main">

    <section class="jumbotron text-center">
        <div class="container">
        <h1 class="jumbotron-heading"><strong>Приложение для расчета рабочего расписания сотрудников</h1>
        </div>
    </section>

    <h2>Результат запроса, JSON:</h2>
    <p><?php var_dump($resultShedule);?></p>

</main>

@endsection