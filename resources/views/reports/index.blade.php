@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Queue Reports</div>

            <div class="card-body">
                <h2 class="mb-4">Generate Reports</h2>
                
                <div class="mb-4">
                    <a href="{{ route('reports.generate') }}" class="btn btn-primary">
                        Generate Weekly CSV Report
                    </a>
                </div>
                
                @livewire('report-generator')
            </div>
        </div>
    </div>
</div>
@endsection

