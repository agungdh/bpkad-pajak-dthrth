# AdminLTE 4 Layout - Dokumentasi

## Instalasi Dependencies

Dependencies sudah terinstall:
- `admin-lte` (^4.0.0-rc6)
- `bootstrap-icons`
- `overlayscrollbars`
- `@popperjs/core`
- `bootstrap`
- `@fontsource/source-sans-3`

## File yang Dibuat

### 1. Layout Files

- **Layout Utama**: `resources/views/layouts/adminlte.blade.php`
- **Header**: `resources/views/partials/adminlte/header.blade.php`
- **Sidebar**: `resources/views/partials/adminlte/sidebar.blade.php`
- **Footer**: `resources/views/partials/adminlte/footer.blade.php`

### 2. Asset Files

- **CSS**: `resources/css/adminlte.css`
- **JS**: `resources/js/adminlte.js`

### 3. Contoh Halaman

- `resources/views/pages/examples/simple-tables.blade.php`

## Cara Menggunakan

### 1. Membuat Halaman Baru

Buat file blade baru yang extends layout AdminLTE:

```blade
@extends('layouts.adminlte')

@section('title', 'Judul Halaman')

@section('page-title', 'Judul Halaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Judul Halaman</li>
@endsection

@section('content')
    {{-- Konten halaman di sini --}}
@endsection

@push('styles')
    {{-- Custom CSS di sini (opsional) --}}
@endpush

@push('scripts')
    {{-- Custom JavaScript di sini (opsional) --}}
@endpush
```

### 2. Menambahkan Menu di Sidebar

Edit file `resources/views/partials/adminlte/sidebar.blade.php` dan tambahkan item menu baru:

```blade
<li class="nav-item">
    <a href="{{ route('nama.route') }}" class="nav-link {{ request()->routeIs('nama.route') ? 'active' : '' }}">
        <i class="nav-icon bi bi-icon-name"></i>
        <p>Nama Menu</p>
    </a>
</li>
```

Untuk menu dengan submenu:

```blade
<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon bi bi-icon-name"></i>
        <p>
            Parent Menu
            <i class="nav-arrow bi bi-chevron-right"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('submenu.route') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Submenu 1</p>
            </a>
        </li>
    </ul>
</li>
```

### 3. Build Assets

Setelah membuat perubahan, jalankan:

```bash
# Development
npm run dev

# Production
npm run build
```

## Fitur yang Tersedia

- ✅ Responsive layout
- ✅ Dark/Light mode support
- ✅ Custom scrollbar dengan OverlayScrollbars
- ✅ Bootstrap 5 components
- ✅ Bootstrap Icons
- ✅ User menu dengan avatar
- ✅ Notifications dropdown
- ✅ Search widget
- ✅ Fullscreen toggle
- ✅ Breadcrumb navigation
- ✅ Sidebar navigation dengan treeview
- ✅ No CDN - semua dependencies local

## Bootstrap Icons

Untuk menggunakan icon, lihat dokumentasi di: https://icons.getbootstrap.com/

Contoh:
```html
<i class="bi bi-house"></i>
<i class="bi bi-gear"></i>
<i class="bi bi-person"></i>
```

## Komponen AdminLTE

Dokumentasi lengkap AdminLTE 4: https://adminlte.io/docs/4.0/

### Cards

```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Card Title</h3>
    </div>
    <div class="card-body">
        Card content
    </div>
    <div class="card-footer">
        Card footer
    </div>
</div>
```

### Tables

```html
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>John Doe</td>
        </tr>
    </tbody>
</table>
```

## Notes

- Assets di-bundle menggunakan Vite
- Layout menggunakan Laravel auth helper untuk user info
- Sidebar menu mendukung authorization dengan `@can` directive
- User avatar menggunakan UI Avatars service (https://ui-avatars.com)
