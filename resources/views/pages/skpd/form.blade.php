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

@push('pre-scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('form', () => ({
                formData: {
                    nama: '',
                },
                validationErrors: {},

                async initData(id) {
                    let res = await axios.get(`/skpd/${id}`);
                    let data = res.data;

                    for (let key in this.formData) {
                        if (data.hasOwnProperty(key)) {
                            this.formData[key] = data[key];
                        }
                    }
                },

                async submit() {
                    try {
                        if (id) {
                            await axios.put(`/skpd/${id}`, this.formData);
                        } else {
                            await axios.post('/skpd', this.formData);
                        }

                        window.location.href = '/skpd';
                    } catch (err) {
                        if (err.response?.status === 422) {
                            this.validationErrors = err.response.data.errors ?? {};
                        } else {
                            toastr.error(
                                'Terjadi kesalahan sistem. Silahkan refresh halaman ini. Jika error masih terjadi, silahkan hubungi Tim IT.',
                            );
                        }
                    }
                },
            }));
        });
    </script>
@endpush

@push('scripts')
    <script type="module">
        id = @json($skpd?->uuid ?? null);

        $(document).ready(function () {
            formComponent = document.getElementById('formComponent');

            formAlpine = Alpine.$data(formComponent);

            id && formAlpine.initData(id);
        });
    </script>
@endpush