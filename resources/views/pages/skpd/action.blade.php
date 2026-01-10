<div class="d-flex gap-2">
    <a href="/skpd/{{ $row->uuid }}/edit" class="btn btn-sm btn-info">
        <i class="bx bx-edit me-1"></i>
        Ubah
    </a>
    <button
        type="button"
        class="btn btn-sm btn-danger"
        onclick="hapusData('{{ $row->uuid }}')"
    >
        <i class="bx bx-trash me-1"></i>
        Hapus
    </button>
</div>
