@foreach ($report as $subject)
    <p>
        {!! $subject->greetings !!}
    </p>
@endforeach