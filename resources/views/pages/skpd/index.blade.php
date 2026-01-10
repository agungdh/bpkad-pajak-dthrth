@extends('layouts.adminlte')

@section('title')
    SKPD
@endsection

@section('page-title')
    SKPD
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">SKPD</li>
@endsection

@section('content')
    <!--begin::Row-->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">SKPD</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="tabel" class="table-bordered table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
    <!--end::Row-->
@endsection

@push('scripts')
    <script type="module">
        $('#tabel').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: `/skpd/datatable`,
                type: 'POST',
                data: function (d) {},
            },
            columns: [
                { data: 'nama', name: 'nama' },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                    orderable: false,
                },
            ],
        });

        window.hapusData = async function (id) {
            let result = await Swal.fire({
                title: 'Apakah Anda yakin menghapus isian ini?',
                text: 'Data yang telah terhapus tidak dapat dikembalikan lagi',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Ya, Hapus',
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/skpd/${id}`);

                    $('#tabel').DataTable().ajax.reload();

                    toastr.success('SKPD berhasil dihapus');
                } catch (e) {
                    toastr.error(
                        'Terjadi kesalahan sistem. Silahkan refresh halaman ini. Jika error masih terjadi, silahkan hubungi Tim IT.',
                    );
                }
            }
        };
    </script>
@endpush
