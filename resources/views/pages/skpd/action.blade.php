<div class="d-flex gap-2">
    <a href="/skpd/{{ $row->uuid }}/edit" class="btn btn-sm btn-info">
        Ubah
    </a>
    <button
        type="button"
        class="btn btn-sm btn-danger"
        onclick="hapusData('{{ $row->uuid }}')"
    >
        Hapus
    </button>
</div>
