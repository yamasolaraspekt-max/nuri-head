@extends('admin.layouts.app')

@section('title', 'Produkt Wizard')
@section('style')
<style>
    .wizard-step {
        text-align: center;
        position: relative;
    }

    .wizard-step .circle {
        width: 40px;
        height: 40px;
        line-height: 38px;
        border-radius: 50%;
        background-color: #e0e0e0;
        margin: 0 auto;
        font-weight: bold;
        border: 2px solid #007bff;
        color: #007bff;
    }

    .wizard-step.active .circle {
        background-color: #007bff;
        color: #fff;
    }

    .wizard-step .label {
        margin-top: 5px;
        font-size: 12px;
    }

    .stage-content {
        display: none;
    }

    .stage-content.active {
        display: block;
    }

    .progress-bar {
        height: 10px;
    }

    .form-check-percent {
        font-size: 12px;
        color: green;
    }
</style>
@endsection

@section('content')

<div class="container mt-3">
    <div class="progress mb-4">
        <div id="overallProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%">0%</div>
    </div>

    <div class="row text-center mb-3">
        @php
            $steps = ['Product Info', 'Technical', 'Distributor', 'Warehouse', 'Photo', 'Document', 'Final'];
        @endphp

        @foreach($steps as $index => $step)
        <div class="col wizard-step" data-index="{{ $index }}">
            <div class="circle">{{ $index + 1 }}</div>
            <div class="label">{{ $step }}</div>
        </div>
        @endforeach
    </div>

    <form id="wizardForm">
        <div class="stage-content active" data-index="0">@include('admin.product.stages.product_info')</div>
        <div class="stage-content" data-index="1">@include('admin.product.stages.product_technical')</div>
        <div class="stage-content" data-index="2">@include('admin.product.stages.distributor')</div>
        <div class="stage-content" data-index="3">@include('admin.product.stages.warehouse')</div>
        <div class="stage-content" data-index="4">@include('admin.product.stages.photo')</div>
        <div class="stage-content" data-index="5">@include('admin.product.stages.document')</div>
        <div class="stage-content" data-index="6">@include('admin.product.stages.final')</div>

        <div class="mt-3 text-right">
            <button type="button" id="prevBtn" class="btn btn-secondary" disabled>Zurück</button>
            <button type="button" id="nextBtn" class="btn btn-primary">Weiter</button>
        </div>
    </form>
</div>

@endsection

@section('script')
<script>
    let currentIndex = 0;
    const steps = document.querySelectorAll('.wizard-step');
    const stages = document.querySelectorAll('.stage-content');
    const overallProgress = document.getElementById('overallProgress');

    function showStage(index) {
        stages.forEach((stage, i) => {
            stage.classList.toggle('active', i === index);
            steps[i].classList.toggle('active', i === index);
        });

        document.getElementById('prevBtn').disabled = index === 0;
        document.getElementById('nextBtn').innerText = index === stages.length - 1 ? 'Fertigstellen' : 'Weiter';

        updateOverallProgress();
    }

    function updateOverallProgress() {
        const completed = Array.from(stages).filter(s => s.classList.contains('saved')).length;
        const percent = Math.round((completed / stages.length) * 100);
        overallProgress.style.width = percent + '%';
        overallProgress.innerText = percent + '%';
    }

    function saveCurrentStage(callback) {
        const formData = new FormData(document.getElementById('wizardForm'));
        formData.append('stage', currentIndex);

        Swal.fire({
            title: 'Speichern?',
            text: "Möchten Sie diesen Abschnitt speichern, bevor Sie fortfahren?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja, speichern',
            cancelButtonText: 'Nein'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("product.store") }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function () {
                        stages[currentIndex].classList.add('saved');
                        updateOverallProgress();
                        callback();
                    },
                    error: function (xhr) {
                        Swal.fire('Fehler', 'Beim Speichern ist ein Fehler aufgetreten.', 'error');
                    }
                });
            }
        });
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentIndex < stages.length - 1) {
            saveCurrentStage(() => {
                currentIndex++;
                showStage(currentIndex);
            });
        } else {
            Swal.fire('Fertig', 'Alle Abschnitte wurden abgeschlossen.', 'success');
        }
    });

    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            showStage(currentIndex);
        }
    });

    showStage(currentIndex);
</script>
@endsection
