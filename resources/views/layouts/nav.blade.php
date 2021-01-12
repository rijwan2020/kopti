<!-- Layout sidenav -->
<div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-dark">
    <div class="app-brand demo">
        <img src="{{ config('koperasi.logo')==''?asset('storage/logo.png'):asset('storage/'.config('koperasi.logo')) }}" alt="" class="app-brand-logo demo">
        <a href="index.html" class="app-brand-text demo sidenav-text font-weight-normal ml-2">KOPTI</a>
        <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
            <i class="fa fa-bars align-middle"></i>
        </a>
    </div>

    <div class="sidenav-divider mt-0"></div>

    <!-- Links -->
    <ul class="sidenav-inner py-1">
        <!-- HOME -->
        <li class="sidenav-item {{ $data['active_menu'] == 'home' ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="sidenav-link" >
                <i class="sidenav-icon fa fa-home"></i>
                <div>Home</div>
            </a>
        </li>
        
        <!-- DATA MASTER -->
        @if (Auth::user()->hasRule('master'))
            <li class="sidenav-item">
                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                    <i class="sidenav-icon fa fa-database"></i>
                    <div>Data Master</div>
                </a>

                <ul class="sidenav-menu">
                    @if (Auth::user()->hasRule('koperasi'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'koperasi' ? 'active' : '' }}">
                            <a href="{{ route('koperasi') }}" class="sidenav-link">
                                <div>Profile Koperasi</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('memberList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'member' ? 'active' : '' }}">
                            <a href="{{ route('memberList') }}" class="sidenav-link">
                                <div>Data Anggota</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('managementList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'management' ? 'active' : '' }}">
                            <a href="{{ route('managementList') }}" class="sidenav-link">
                                <div>Data Pengurus</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('employeeList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'employee' ? 'active' : '' }}">
                            <a href="{{ route('employeeList') }}" class="sidenav-link">
                                <div>Data Karyawan</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('regionList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'region' ? 'active' : '' }}">
                            <a href="{{ route('regionList') }}" class="sidenav-link">
                                <div>Data Wilayah</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('assetList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'asset' ? 'active' : '' }}">
                            <a href="{{ route('assetList') }}" class="sidenav-link">
                                <div>Aset Barang</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('configApps'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'config-apps' ? 'active' : '' }}">
                            <a href="{{ route('configApps') }}" class="sidenav-link">
                                <div>Konfigurasi Aplikasi</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <!-- SIMPANAN -->
        @if (Auth::user()->hasRule('deposit'))
            <li class="sidenav-item">
                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                    <i class="sidenav-icon fa fa-money-bill"></i>
                    <div>Simpanan</div>
                </a>

                <ul class="sidenav-menu">
                    @if (Auth::user()->hasRule('depositList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'deposit' ? 'active' : '' }}">
                            <a href="{{ route('depositList') }}" class="sidenav-link">
                                <div>Data Simpanan</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('depositTypeList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'deposit-type' ? 'active' : '' }}">
                            <a href="{{ route('depositTypeList') }}" class="sidenav-link">
                                <div>Jenis Simpanan</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('depositTransactionList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'deposit-transaction' ? 'active' : '' }}">
                            <a href="{{ route('depositTransactionList') }}" class="sidenav-link">
                                <div>Data Transaksi</div>
                            </a>
                        </li>
                    @endif
                    {{-- @if (Auth::user()->hasRule('depositBillList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'deposit-bill' ? 'active' : '' }}">
                            <a href="{{ route('depositBillList') }}" class="sidenav-link">
                                <div>Tagihan</div>
                            </a>
                        </li>
                    @endif --}}
                    @if (Auth::user()->hasRule('laporanSimpanan'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'deposit-report' ? 'active' : '' }}">
                            <a href="{{ route('laporanSimpanan') }}" class="sidenav-link">
                                <div>Laporan</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <!-- TOKO -->
        @if (Auth::user()->hasRule('store'))
            <li class="sidenav-item">
                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                    <i class="sidenav-icon fa fa-truck"></i>
                    <div>Salur</div>
                </a>

                <ul class="sidenav-menu">
                    @if (Auth::user()->hasRule('itemList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'item' ? 'active' : '' }}">
                            <a href="{{ route('itemList') }}" class="sidenav-link">
                                <div>Data Barang</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('suplierList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'suplier' ? 'active' : '' }}">
                            <a href="{{ route('suplierList') }}" class="sidenav-link">
                                <div>Data Suplier</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('warehouseList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'warehouse' ? 'active' : '' }}">
                            <a href="{{ route('warehouseList') }}" class="sidenav-link">
                                <div>Data Gudang</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('purchaseList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'purchase' ? 'active' : '' }}">
                            <a href="{{ route('purchaseList') }}" class="sidenav-link">
                                <div>Data Pembelian</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('saleList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'sale' ? 'active' : '' }}">
                            <a href="{{ route('saleList') }}" class="sidenav-link">
                                <div>Data Penjualan</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('stockOpname'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'stockopname' ? 'active' : '' }}">
                            <a href="{{ route('stockOpname') }}" class="sidenav-link">
                                <div>Stock Opname</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('storeReport'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'store-report' ? 'active' : '' }}">
                            <a href="{{ route('storeReport') }}" class="sidenav-link">
                                <div>Laporan</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        <!-- PEMBUKUAN -->
        @if (Auth::user()->hasRule('accountancy'))
            <li class="sidenav-item">
                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                    <i class="sidenav-icon fa fa-book"></i>
                    <div>Pembukuan</div>
                </a>

                <ul class="sidenav-menu">
                    @if (Auth::user()->hasRule('accountList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'account' ? 'active' : '' }}">
                            <a href="{{ route('accountList') }}" class="sidenav-link">
                                <div>Data Akun</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('journalList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'journal' ? 'active' : '' }}">
                            <a href="{{ route('journalList') }}" class="sidenav-link">
                                <div>Jurnal Transaksi</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('ledger'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'ledger' ? 'active' : '' }}">
                            <a href="{{ route('ledger') }}" class="sidenav-link">
                                <div>Buku Besar</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('adjustingJournalList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'adjusting-journal' ? 'active' : '' }}">
                            <a href="{{ route('adjustingJournalList') }}" class="sidenav-link">
                                <div>Jurnal Penyesuaian</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('trialBalance'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'trialbalance' ? 'active' : '' }}">
                            <a href="{{ route('trialBalance') }}" class="sidenav-link">
                                <div>Neraca Saldo</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('closeBookList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'close-book' ? 'active' : '' }}">
                            <a href="{{ route('closeBookList') }}" class="sidenav-link">
                                <div>Tutup Buku</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
            
        <!-- LAPORAN -->
        @if (Auth::user()->hasRule('report'))
            <li class="sidenav-item">
                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                    <i class="sidenav-icon fa fa-balance-scale"></i>
                    <div>Laporan</div>
                </a>

                <ul class="sidenav-menu">
                    @if (Auth::user()->hasRule('balance'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'balance' ? 'active' : '' }}">
                            <a href="{{ route('balance') }}" class="sidenav-link">
                                <div>Neraca</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('phu'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'phu' ? 'active' : '' }}">
                            <a href="{{ route('phu') }}" class="sidenav-link">
                                <div>Penjelasan PHU</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('shu'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'shu' ? 'active' : '' }}">
                            <a href="{{ route('shu') }}" class="sidenav-link">
                                <div>Sisa Hasil Usaha</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('laporanHarian'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'laporanHarian' ? 'active' : '' }}">
                            <a href="{{ route('laporanHarian') }}" class="sidenav-link">
                                <div>Laporan Harian</div>
                            </a>
                        </li>
                    @endif
                    {{-- @if (Auth::user()->hasRule('cashflow'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'cashflow' ? 'active' : '' }}">
                            <a href="{{ route('cashflow') }}" class="sidenav-link">
                                <div>Arus Kas</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('ekuitas'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'ekuitas' ? 'active' : '' }}">
                            <a href="{{ route('ekuitas') }}" class="sidenav-link">
                                <div>Perubahan Modal</div>
                            </a>
                        </li>
                    @endif --}}
                </ul>
            </li>
        @endif

        <!-- MANAGE USER -->
        @if (Auth::user()->hasRule('user'))
            <li class="sidenav-item">
                <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                    <i class="sidenav-icon fa fa-users-cog"></i>
                    <div>Manage User</div>
                </a>

                <ul class="sidenav-menu">
                    @if (Auth::user()->hasRule('userList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'data-user' ? 'active' : '' }}">
                            <a href="{{ route('userList') }}" class="sidenav-link">
                                <div>Data User</div>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->hasRule('levelList'))
                        <li class="sidenav-item {{ $data['active_menu'] == 'level-user' ? 'active' : '' }}">
                            <a href="{{ route('levelList') }}" class="sidenav-link">
                                <div>Level User</div>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        {{-- RESET APLIKASI --}}
        @if (Auth::user()->hasRule('reset'))
            <li class="sidenav-item {{ $data['active_menu'] == 'reset' ? 'active' : '' }}">
                <a href="{{ route('reset') }}" class="sidenav-link" >
                    <i class="sidenav-icon fa fa-retweet"></i>
                    <div>Reset Aplikasi</div>
                </a>
            </li>
        @endif

        <li class="sidenav-divider mb-1"></li>

        <!-- PROFILE -->
        <li class="sidenav-item {{ $data['active_menu'] == 'profile' ? 'active' : '' }}">
            <a href="{{ route('profile') }}" class="sidenav-link" >
                <i class="sidenav-icon fa fa-user-alt"></i>
                <div>Profile</div>
            </a>
        </li>

        <!-- LOGOUT -->
        <li class="sidenav-item">
            <a href="{{ route('logout') }}" class="sidenav-link" >
                <i class="sidenav-icon fa fa-sign-out-alt"></i>
                <div>Logout</div>
            </a>
        </li>
    </ul>
</div>
<!-- / Layout sidenav -->