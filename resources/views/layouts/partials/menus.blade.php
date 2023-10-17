<ul class="sidebar-nav">
    @can('dashboard')
    <li class="sidebar-item{{ request()->is('dashboard*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('dashboard.index') }}">
            <i class="align-middle" data-feather="slack"></i> <span class="align-middle">Dashboard</span>
        </a>
    </li>
    @endcan
    @role(['developer'])
    <li class="sidebar-header">
        Developer
    </li>
    <li class="sidebar-item{{ request()->is('fitur-aplikasi*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('fitur-aplikasi.index') }}">
            <i class="align-middle" data-feather="package"></i> <span class="align-middle">Fitur Aplikasi</span>
        </a>
    </li>
    @endrole
    @can('pengguna')
    <li class="sidebar-header">
        Pengguna
    </li>
    @can('peran pengguna')
    <li class="sidebar-item{{ request()->is('peran-pengguna*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('peran-pengguna.index') }}">
            <i class="align-middle" data-feather="user-check"></i> <span class="align-middle">Peran Pengguna</span>
        </a>
    </li>
    @endcan
    <li class="sidebar-item{{ request()->is('pengguna*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('pengguna.index') }}">
            <i class="align-middle" data-feather="users"></i> <span class="align-middle">Pengguna</span>
        </a>
    </li>
    @endcan
    @canany(['akun', 'anggota', 'pemasok','satuan barang','ukuran','barang'])
    <li class="sidebar-header">
        Master Data
    </li>
    @can('akun')
    <li class="sidebar-item{{ request()->is('akun*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('akun.index') }}">
            <i class="align-middle" data-feather="credit-card"></i> <span class="align-middle">Akun</span>
        </a>
    </li>
    @endcan
    @can('anggota')
    <li class="sidebar-item{{ request()->is('anggota*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('anggota.index') }}">
            <i class="align-middle" data-feather="users"></i> <span class="align-middle">Anggota</span>
        </a>
    </li>
    @endcan
    @can('pemasok')
    <li class="sidebar-item{{ request()->is('pemasok*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('pemasok.index') }}">
            <i class="align-middle" data-feather="truck"></i> <span class="align-middle">Pemasok</span>
        </a>
    </li>
    @endcan
    @can('satuan barang')
    <li class="sidebar-item{{ request()->is('satuan-barang*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('satuan-barang.index') }}">
            <i class="align-middle" data-feather="box"></i> <span class="align-middle">Satuan Barang</span>
        </a>
    </li>
    @endcan
    @can('ukuran')
    <li class="sidebar-item{{ request()->is('ukuran*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('ukuran.index') }}">
            <i class="align-middle" data-feather="paperclip"></i> <span class="align-middle">Ukuran</span>
        </a>
    </li>
    @endcan
    @can('barang')
    <li class="sidebar-item{{ request()->is('barang*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('barang.index') }}">
            <i class="align-middle" data-feather="package"></i> <span class="align-middle">Barang</span>
        </a>
    </li>
    @endcan
    @endcanany
    @canany(['penjualan'])
    <li class="sidebar-header">
        Transaksi
    </li>
    @can('penjualan')
    <li class="sidebar-item{{ request()->is('penjualan*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('penjualan.index') }}">
            <i class="align-middle" data-feather="shopping-bag"></i> <span class="align-middle">Penjualan</span>
        </a>
    </li>
    @endcan
    @endcanany
</ul>
