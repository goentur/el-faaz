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
    <li class="sidebar-item{{ request()->is('zona-waktu*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('zona-waktu.index') }}">
            <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Zona Waktu</span>
        </a>
    </li>
    @endrole
    @canany(['gudang','penjualan','pembelian','angsuran','riwayat','retur','jurnal'])
    <li class="sidebar-header">
        Transaksi
    </li>
    @can('gudang')
    <li class="sidebar-item{{ request()->is('gudang*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('gudang.index') }}">
            <i class="align-middle" data-feather="package"></i> <span class="align-middle">Gudang</span>
        </a>
    </li>
    @endcan
    @can('pembelian')
    <li class="sidebar-item{{ request()->is('pembelian*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('pembelian.index') }}">
            <i class="align-middle" data-feather="shopping-cart"></i> <span class="align-middle">Pembelian</span>
        </a>
    </li>
    @endcan
    @can('penjualan')
    <li class="sidebar-item{{ request()->is('penjualan*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('penjualan.index') }}">
            <i class="align-middle" data-feather="shopping-bag"></i> <span class="align-middle">Penjualan</span>
        </a>
    </li>
    @endcan
    @can('angsuran')
    <li class="sidebar-item{{ request()->is('angsuran*') ? ' active' : '' }}">
        <a data-bs-target="#angsuran" data-bs-toggle="collapse" class="sidebar-link{{ request()->is('angsuran*') ? '' : ' collapsed' }}">
            <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Angsuran</span>
        </a>
        <ul id="angsuran" class="sidebar-dropdown list-unstyled collapse{{ request()->is('angsuran*') ? ' show' : '' }}" data-bs-parent="#sidebar">
            <li class="sidebar-item{{ request()->is('angsuran/hutang-dagang*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('angsuran.hutang-dagang.index') }}">Hutang dagang</a></li>
            <li class="sidebar-item{{ request()->is('angsuran/piutang-dagang*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('angsuran.piutang-dagang.index') }}">Piutang dagang</a></li>
        </ul>
    </li>
    @endcan
    @can('riwayat')
    <li class="sidebar-item{{ request()->is('riwayat*') ? ' active' : '' }}">
        <a data-bs-target="#riwayat" data-bs-toggle="collapse" class="sidebar-link{{ request()->is('riwayat*') ? '' : ' collapsed' }}">
            <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Riwayat</span>
        </a>
        <ul id="riwayat" class="sidebar-dropdown list-unstyled collapse{{ request()->is('riwayat*') ? ' show' : '' }}" data-bs-parent="#sidebar">
            <li class="sidebar-item{{ request()->is('riwayat/pembelian*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('riwayat.pembelian.index') }}">Pembelian</a></li>
            <li class="sidebar-item{{ request()->is('riwayat/penjualan*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('riwayat.penjualan.index') }}">Penjualan</a></li>
        </ul>
    </li>
    @endcan
    @can('retur')
    <li class="sidebar-item{{ request()->is('retur*') ? ' active' : '' }}">
        <a data-bs-target="#retur" data-bs-toggle="collapse" class="sidebar-link{{ request()->is('retur*') ? '' : ' collapsed' }}">
            <i class="align-middle" data-feather="corner-down-left"></i> <span class="align-middle">Retur</span>
        </a>
        <ul id="retur" class="sidebar-dropdown list-unstyled collapse{{ request()->is('retur*') ? ' show' : '' }}" data-bs-parent="#sidebar">
            <li class="sidebar-item{{ request()->is('retur/penjualan*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('retur.penjualan.index') }}">Penjualan</a></li>
        </ul>
    </li>
    @endcan
    @can('jurnal')
    <li class="sidebar-item{{ request()->is('jurnal*') ? ' active' : '' }}">
        <a data-bs-target="#jurnal" data-bs-toggle="collapse" class="sidebar-link{{ request()->is('jurnal*') ? '' : ' collapsed' }}">
            <i class="align-middle" data-feather="book"></i> <span class="align-middle">Jurnal</span>
        </a>
        <ul id="jurnal" class="sidebar-dropdown list-unstyled collapse{{ request()->is('jurnal*') ? ' show' : '' }}" data-bs-parent="#sidebar">
            <li class="sidebar-item{{ request()->is('jurnal/penjualan/lunas*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('jurnal.penjualan.lunas.index') }}">Penjualan Lunas</a></li>
            <li class="sidebar-item{{ request()->is('jurnal/pembelian/lunas*') ? ' active' : '' }}"><a class="sidebar-link" href="{{ route('jurnal.pembelian.lunas.index') }}">Pembelian Lunas</a></li>
        </ul>
    </li>
    @endcan
    @endcanany
    @canany(['akun', 'anggota', 'pemasok', 'warna', 'satuan barang', 'ukuran', 'barang','dagangan'])
    <li class="sidebar-header">
        Master Data
    </li>
    @can('akun')
    <li class="sidebar-item{{ request()->is('akun*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('akun.index') }}">
            <i class="align-middle" data-feather="bookmark"></i> <span class="align-middle">Akun</span>
        </a>
    </li>
    @endcan
    @can('metode pembayaran')
    <li class="sidebar-item{{ request()->is('metode-pembayaran*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('metode-pembayaran.index') }}">
            <i class="align-middle" data-feather="credit-card"></i> <span class="align-middle">Metode Pembayaran</span>
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
    @can('warna')
    <li class="sidebar-item{{ request()->is('warna*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('warna.index') }}">
            <i class="align-middle" data-feather="aperture"></i> <span class="align-middle">Warna</span>
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
    @can('dagangan')
    <li class="sidebar-item{{ request()->is('dagangan*') ? ' active' : '' }}">
        <a class="sidebar-link" href="{{ route('dagangan.index') }}">
            <i class="align-middle" data-feather="package"></i> <span class="align-middle">Dagangan</span>
        </a>
    </li>
    @endcan
    @endcanany
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
</ul>
