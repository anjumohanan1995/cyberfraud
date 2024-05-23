
@extends('layouts.app')
@php use Illuminate\Support\Facades\Crypt;
           $id = request()->segment(count(request()->segments()));
        $new_id = Crypt::decrypt($id); @endphp
@section('content')
<div class="container-fluid">

    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div>
            <h4 class="content-title mb-2">
                Hi, welcome back!
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('case-data.view', ['id' => @$id ]) }}">Case Data</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Uploaded Evidences
                    </li>
                </ol>
            </nav>
        </div>

    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class=" m-4 d-flex justify-content-between">
                        <h4 class="card-title mg-b-10">
                            Evidence Details
                        </h4>
                        <div class="task-box primary col-1">
                            <a class="text-white" data-toggle="tooltip" data-placement="top"
                                title="Back" href="{{ route('case-data.view', ['id' => @$id ]) }}">
                                <h3 class="mb-0"><i class="ti ti-arrow-left"></i></h3>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="alert alert-success col-6" id="successAlert">
                                {{ session('success') }}
                                <script>
                                    setTimeout(function() {
                                           document.getElementById('successAlert').style.opacity = '0';
                                           setTimeout(function() {
                                               document.getElementById('successAlert').style.display = 'none';
                                           }, 500);
                                       }, 1000);
                               </script>
                            </div>
                        </div>
                        @endif
                        @if ($evidences->isEmpty())
                        <div class="d-flex justify-content-center align-items-center"><div class="alert alert-info col-6 text-center mt-3 mb-3">
                            No evidence available yet!
                        </div>

                    </div>


                    @else
                        {{-- Group evidences by type --}}

                        @foreach ($evidences->groupBy('evidence_type') as $type => $groupedEvidences)
                            <div class="mb-4">
                                <h5 class="mb-3 title">{{ $type }}</h5> <!-- Show evidence type name once -->
                                <div class="row">
                                    @foreach ($groupedEvidences as $evidence)
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">


                                                    <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer"><h6 class="card-title">URL : {{ $evidence->url }}</h6></a>

<div class="text-right">
    <form action="{{ route('evidence.destroy', $evidence->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this evidence?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-link text-danger p-0 m-0">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
</div>


                                                    @if($evidence->domain)<p class="card-text">Domain: {{ $evidence->domain }}</p>@endif
                                                    @if($evidence->registry_details)<p class="card-text">Registry Details: {{ $evidence->registry_details }}</p>@endif
                                                    @if($evidence->ip)<p class="card-text">IP Address: {{ $evidence->ip }}</p>@endif
                                                    @if($evidence->registrar)<p class="card-text">Registrar: {{ $evidence->registrar }}</p>@endif

                                                    {{-- Explode PDFs --}}
                                                    @if($evidence->pdf)
                                                    <p class="card-text">Document:
                                                        @foreach(explode(',', $evidence->pdf) as $pdfKey => $pdf)
                                                            <a target="_blank" href="{{ Storage::url(trim($pdf)) }}" class="">View Document{{ $pdfKey + 1 }}</a>
                                                        @endforeach
                                                    </p>
                                                    @endif

                                                    {{-- Explode Screenshots --}}
                                                    @if($evidence->screenshots)
                                                    <p class="card-text">Screenshot:
                                                        @foreach(explode(',', $evidence->screenshots) as $screenshotKey =>  $screenshot)
                                                            <a target="_blank" href="{{ Storage::url(trim($screenshot)) }}" class="">View Screenshot{{ $screenshotKey + 1 }}</a>
                                                        @endforeach
                                                        </p>
                                                    @endif

                                                    <p class="card-text mt-2">Remarks: {{ $evidence->remarks }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


