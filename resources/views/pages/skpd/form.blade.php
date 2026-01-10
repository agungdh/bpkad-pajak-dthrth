@extends('layouts.adminlte')

@section('title')
    SKPD
@endsection

@section('page-title')
    SKPD
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page"><a href="/skpd">SKPD</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($skpd) ? 'Ubah' : 'Tambah' }}</li>
@endsection

@section('content')
    <!--begin::Row-->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4" x-data="form" id="formComponent">
                <form class="form" @submit.prevent="submit">

                    <div class="card-header">
                        <h3 class="card-title">SKPD</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                @php($formName = 'nama')
                                @php($formLabel = 'Nama SKPD')
                                <label class="form-label" for="{{ $formName }}">{{ $formLabel }}</label>
                                <input
                                    class="form-control"
                                    id="{{ $formName }}"
                                    type="text"
                                    placeholder="{{ $formLabel }}"
                                    x-model.lazy="formData.{{ $formName }}"
                                    :class="{'is-invalid': validationErrors.{{ $formName }}}"
                                />
                                <template x-if="validationErrors.{{ $formName }}">
                                    <div class="invalid-feedback" x-text="validationErrors.{{ $formName }}"></div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex gap-2">
                        <button class="btn btn-primary btn-sm" type="submit">
                            Simpan
                        </button>
                        <a href="/skpd" class="btn btn-light btn-sm">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!--end::Row-->
@endsection

@push('scripts')
    <script type="module">

    </script>
@endpush
