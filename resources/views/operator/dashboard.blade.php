@extends('layouts.app')

@section('content')
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary mb-4 text-white">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-pills fa-2x me-3"></i>
                    <span>Jumlah Obat</span>
                </div>
                <div class="h5 font-weight-bold mb-0">{{ $totalObat }}</div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small stretched-link text-white" href="{{ route('operator.dataobat') }}">Lihat Detail</a>
                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
@endsection
