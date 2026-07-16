@extends('admin.layouts.app')
@section('title')
KABAN
@endsection
@section('content')
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

.kanban-board {
    display: flex;
    gap: 20px;
    padding: 20px;
    overflow-x: auto;
}

.kanban-column {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    flex: 0 0 300px;
    display: flex;
    flex-direction: column;
    height: auto;
}

.kanban-header {
    background: #007bff;
    color: white;
    font-weight: bold;
    padding: 10px;
    border-radius: 8px 8px 0 0;
    cursor: text;
    text-align: center;
}

.kanban-cards {
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 100px;
}

.kanban-card {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    cursor: grab;
    transition: transform 0.2s;
}

.kanban-card:hover {
    transform: scale(1.02);
}

</style>
<div class="app-content content">
    <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDE-LISTE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="content-body">
                    <div class="kanban-board">
                        <div class="kanban-column" id="column-1">
                            <div class="kanban-header" contenteditable="true">Offene Anfrage</div>
                            <div class="kanban-cards">
                                <div class="kanban-card">WP - Dippel</div>
                                <div class="kanban-card">WB - Keipert, HG</div>
                                <div class="kanban-card">WB - Odenweller-Klügl</div>
                            </div>
                        </div>

                        <div class="kanban-column" id="column-2">
                            <div class="kanban-header" contenteditable="true">Daten vorhanden/Sichten</div>
                            <div class="kanban-cards">
                                <div class="kanban-card">WP - Heil, Wehrheim</div>
                                <div class="kanban-card">WP - Bich Tatjana</div>
                                <div class="kanban-card">WP - Sachs Wehrheim</div>
                            </div>
                        </div>

                        <div class="kanban-column" id="column-3">
                            <div class="kanban-header" contenteditable="true">Wartestellung</div>
                            <div class="kanban-cards">
                                <div class="kanban-card">WP - Bangert, Neu-Anspach</div>
                                <div class="kanban-card">WP - Müller Sabine</div>
                                <div class="kanban-card">WP - Blank Kilian</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script>
        // Using Dragula for drag-and-drop functionality
        document.addEventListener("DOMContentLoaded", function () {
            const dragulaScript = document.createElement("script");
            dragulaScript.src = "https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js";
            document.head.appendChild(dragulaScript);

            dragulaScript.onload = function () {
                const columns = document.querySelectorAll('.kanban-cards');
                dragula([...columns]);
            };
        });

    </script>
@endsection