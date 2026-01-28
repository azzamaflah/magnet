<x-main-layout>
    <div class="claude-container">

        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <h2 class="claude-title text-2xl text-white">
                            Data Magang
                        </h2>
                        <div class="hidden sm:flex items-center bg-black/20 rounded-lg p-1 border border-[#3a3a3a]">
                            <button id="view-grid-btn" class="view-toggle-btn active" title="Tampilan Grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button id="view-row-btn" class="view-toggle-btn" title="Tampilan Scroll">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('magang.create') }}"
                            class="claude-button px-5 py-2.5 inline-flex items-center gap-2 w-full sm:w-auto justify-center">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Data</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6">
            @if (session('success'))
                <div class="success-alert mb-6">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- FILTER SECTION --}}
            <div class="filter-container">
                <form method="GET" action="{{ route('magang.index') }}" class="filter-form" id="filterForm">
                    <div class="filter-grid">
                        {{-- Filter Search --}}
                        <div class="filter-item search-item">
                            <div class="filter-label">
                                <i class="fas fa-search"></i>
                                <span>Cari Peserta</span>
                            </div>
                            <div style="position: relative;">
                                <input type="text" name="search" id="searchInput" class="filter-input"
                                    placeholder="Cari berdasarkan nama..." value="{{ request('search') }}"
                                    autocomplete="off">
                                <div id="searchLoader" class="search-loader" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Filter Kampus --}}
                        <div class="filter-item">
                            <div class="filter-label">
                                <i class="fas fa-university"></i>
                                <span>Universitas</span>
                            </div>
                            <div style="position: relative;">
                                <select name="kampus" id="kampusSelect" class="filter-select">
                                    <option value="">Semua Kampus</option>
                                    @foreach($kampusList as $kampus)
                                        <option value="{{ $kampus }}" {{ request('kampus') == $kampus ? 'selected' : '' }}>
                                            {{ $kampus }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="kampusLoader" class="select-loader" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Filter Tahun --}}
                        <div class="filter-item">
                            <div class="filter-label">
                                <i class="fas fa-calendar"></i>
                                <span>Tahun</span>
                            </div>
                            <div style="position: relative;">
                                <select name="year" id="yearSelect" class="filter-select">
                                    <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun
                                    </option>
                                    @foreach($availableYears as $year)
                                        @if($year)
                                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div id="yearLoader" class="select-loader" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Reset --}}
                        <div class="filter-item filter-actions">
                            @if(request('search') || request('kampus') || $selectedYear != 'all')
                                <a href="{{ route('magang.index') }}" class="filter-btn filter-btn-secondary">
                                    <i class="fas fa-times"></i>
                                    <span>Reset</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            
            <div id="magang-results">
            @if (count($magangs) > 0)
                <div id="magang-container" class="flex overflow-x-auto gap-6 py-4 hide-scrollbar">
                    @foreach ($magangs as $magang)
                    <div class="card-wrapper flex-shrink-0 w-80 sm:w-96">
                        <div class="magang-card relative" @if ($magang->foto)
                            style="background-image: url('{{ asset('storage/' . $magang->foto) }}');" @else
                            style="background: linear-gradient(135deg, #{{ substr(md5($magang->nama), 0, 6) }} 0%, #{{ substr(md5($magang->nama), 6, 6) }} 100%);"
                            @endif>

                            @if (!$magang->foto)
                                <div class="initial-avatar">
                                    {{ $magang->initials }}
                                </div>
                            @endif

                            <div class="card-overlay"></div>
                            <div class="card-blur-bottom"></div>
                            <!--modifikasi start-->
                            {{-- NOMOR URUT --}}
                            <div class="absolute bottom-3 left-3 z-20">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                                             bg-black/50 border border-white/10 text-white backdrop-blur">
                                    #{{ ($magangs->firstItem() ?? 0) + $loop->index }}
                                </span>
                            </div>
                            <!--modifikasi end-->
                            
                            {{-- CONTENT --}}
                            <div class="card-content relative z-10">
                                <div class="card-header">
                                    <div class="card-name">{{ $magang->nama }}</div>
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $mulai = \Carbon\Carbon::parse($magang->tanggal_mulai);
                                        $selesai = \Carbon\Carbon::parse($magang->tanggal_selesai);
                                        if ($now->lt($mulai)) {
                                            $statusClass = 'belum';
                                            $statusText = 'Belum Mulai';
                                        } elseif ($now->between($mulai, $selesai)) {
                                            $statusClass = 'aktif';
                                            $statusText = 'Aktif';
                                        } else {
                                            $statusClass = 'selesai';
                                            $statusText = 'Selesai';
                                        }
                                    @endphp
                                    <div class="status-badge {{ $statusClass }}">
                                        <span class="status-dot"></span>
                                        {{ $statusText }}
                                    </div>
                                </div>

                                <div class="card-spacer"></div>

                                <div class="card-footer">
                                    <div class="card-info">
                                        <div class="info-item">
                                            <div class="info-label">Asal Kampus</div>
                                            <div class="info-value">{{ $magang->asal_kampus }}</div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">Periode</div>
                                            <div class="info-value">
                                                {{ $magang->periode_bulan ?? 'N/A' }} bulan
                                                <span class="text-xs opacity-75">
                                                    ({{ $mulai->format('d/m/y') }} - {{ $selesai->format('d/m/y') }})
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TOMBOL AKSI & SOCIALS --}}
                                    <div class="card-actions-row">

                                        {{-- =============================================== --}}
                                        {{-- PINDAHKAN BLOK SOCIALS KE SINI --}}
                                        {{-- =============================================== --}}
                                        @if ($magang->whatsapp || $magang->instagram || $magang->tiktok)
                                            <div class="card-socials">
                                                @if ($magang->whatsapp && auth()->check() && auth()->user()->role == 'admin')
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $magang->whatsapp) }}"
                                                        target="_blank" class="social-icon-link" title="WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                @endif
                                                @if ($magang->instagram)
                                                    <a href="https://instagram.com/{{ ltrim($magang->instagram, '@') }}" target="_blank"
                                                        class="social-icon-link" title="Instagram">
                                                        <i class="fab fa-instagram"></i>
                                                    </a>
                                                @endif
                                                @if ($magang->tiktok)
                                                    <a href="https://tiktok.com/{{ ltrim($magang->tiktok, '@') }}" target="_blank"
                                                        class="social-icon-link" title="TikTok">
                                                        <i class="fab fa-tiktok"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            {{-- Tambahkan div kosong agar alignment 'space-between' tetap rapi --}}
                                            <div></div>
                                        @endif
                                        {{-- =============================================== --}}
                                        {{-- AKHIR DARI BLOK SOCIALS --}}
                                        {{-- =============================================== --}}


                                        <div class="card-actions">
                                            <a href="{{ route('magang.show', $magang) }}" class="action-btn detail"
                                                title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(auth()->user()->isAdmin() || (isset($magang->user_id) && $magang->user_id == auth()->id()))
                                                <a href="{{ route('magang.edit', $magang) }}" class="action-btn edit"
                                                    title="Edit">
                                                    <i class="fas fa-pencil"></i>
                                                </a>
                                            @endif

                                            @if(auth()->user()->isAdmin())
                                                <form id="delete-form-{{ $magang->id }}" action="{{ route('magang.destroy', $magang) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="action-btn delete" title="Hapus"
                                                        onclick="openDeleteModal('{{ $magang->id }}', '{{ $magang->nama }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- BLOK SOCIALS DIPINDAH DARI SINI --}}

                        </div>
                    </div>
                @endforeach
                
                </div>
            @else
                <div class="empty-state mt-6">
                    {{-- Empty state content (sama seperti sebelumnya) --}}
                    <i class="fas fa-search" style="font-size: 4rem; color: #4a4a4a; margin-bottom: 1rem;"></i>
                    <h3 style="color: white; font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">
                        Tidak Ada Hasil
                    </h3>
                    <p style="color: #9ca3af; margin-bottom: 2rem;">
                        Belum ada data magang
                    </p>
                </div>
            @endif
            </div>

            <!--modifikasi start-->
            @if ($magangs->hasPages())
                <div class="mt-8 border-t border-[#3a3a3a] pt-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs text-gray-400">
                            Showing {{ $magangs->firstItem() ?? 0 }} to {{ $magangs->lastItem() ?? 0 }}
                            of {{ $magangs->total() }} results
                        </div>
                        <div class="overflow-x-auto">
                            {{ $magangs->onEachSide(1)->links('vendor.pagination.magnet') }}
                        </div>
                    </div>
                </div>
            @endif
            <!--modifikasi end-->
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- CUSTOM DELETE MODAL (ALERT MANUAL RAPI) --}}
    {{-- ========================================== --}}
    <div id="customDeleteModal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center bg-black/70 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-[#2a2a2a] border border-[#4a4a4a] rounded-xl shadow-2xl p-6 w-[90%] max-w-sm transform scale-95 transition-transform duration-200" id="modalContent">
            
            {{-- Icon Peringatan --}}
            <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-900/20 mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>

            {{-- Teks --}}
            <h3 class="text-lg font-semibold text-white text-center mb-2">Hapus Data Magang?</h3>
            <p class="text-gray-400 text-sm text-center mb-6 leading-relaxed">
                Anda akan menghapus data <span id="modalMagangName" class="text-white font-medium"></span>. 
                <br><span class="text-red-400 text-xs">Status data pendaftaran terkait juga akan ditolak/diarsipkan.</span>
            </p>

            {{-- Tombol --}}
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="closeDeleteModal()" 
                    class="px-4 py-2 bg-[#3a3a3a] hover:bg-[#4a4a4a] text-gray-300 text-sm font-medium rounded-lg transition-colors border border-[#4a4a4a]">
                    Batal
                </button>
                <button type="button" id="confirmDeleteBtn"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg shadow-lg shadow-red-900/20 transition-all transform hover:scale-105">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // --- LOGIKA CUSTOM MODAL ---
            let currentFormId = null;
            const modal = document.getElementById('customDeleteModal');
            const modalContent = document.getElementById('modalContent');
            const nameSpan = document.getElementById('modalMagangName');
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            function openDeleteModal(id, name) {
                currentFormId = 'delete-form-' + id;
                nameSpan.textContent = name;
                
                // Tampilkan modal dengan animasi
                modal.classList.remove('hidden');
                // Sedikit delay agar transisi CSS opacity berjalan
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }, 10);
            }

            function closeDeleteModal() {
                // Animasi tutup
                modal.classList.add('opacity-0');
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
                
                setTimeout(() => {
                    modal.classList.add('hidden');
                    currentFormId = null;
                }, 300); // Sesuaikan dengan duration-300 di CSS
            }

            // // Aksi tombol Konfirmasi Hapus
            // confirmBtn.addEventListener('click', function() {
            //     if (currentFormId) {
            //         document.getElementById(currentFormId).submit();
            //     }
            // });
            
            // modifikais start
            // Aksi tombol Konfirmasi Hapus
            confirmBtn.addEventListener('click', function() {
                if (currentFormId) {
                    const form = document.getElementById(currentFormId);
                    const actionUrl = form.action;
                    const formData = new FormData(form);
            
                    // 1. Ubah tampilan tombol jadi Loading
                    const originalText = confirmBtn.innerText;
                    confirmBtn.innerText = 'Menghapus...';
                    confirmBtn.disabled = true;
                    confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
            
                    // 2. Kirim Request Hapus via Fetch (AJAX)
                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest' // Memberitahu Laravel ini AJAX
                        }
                    })
                    .then(response => {
                        // 3. Apapun hasilnya, Refresh halaman agar bersih
                        // Kita pakai reload() agar Flash Message dari session 'success' tetap muncul
                        // karena Laravel menyimpan session di request berikutnya.
                        window.location.reload(); 
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus data.');
                        window.location.reload(); // Tetap reload untuk keamanan
                    });
                }
            });
            // modifikasi end
            
            // Tutup jika klik di luar area modal (backdrop)
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeDeleteModal();
                }
            });
            // ---------------------------

            function initializeAppLogic() {
                // ... (Logika Filter & View Toggle tetap sama seperti sebelumnya) ...
                const searchInput = document.getElementById('searchInput');
                const kampusSelect = document.getElementById('kampusSelect');
                const yearSelect = document.getElementById('yearSelect');
                const searchLoader = document.getElementById('searchLoader');
                const kampusLoader = document.getElementById('kampusLoader');
                const yearLoader = document.getElementById('yearLoader');
                const resultsContainer = document.getElementById('magang-container');
                const gridBtn = document.getElementById('view-grid-btn');
                const rowBtn = document.getElementById('view-row-btn');
                const body = document.body;

                if (resultsContainer) {
                    resultsContainer.addEventListener('wheel', function (e) {
                        if (body.classList.contains('view-row') && e.deltaY != 0) {
                            e.preventDefault();
                            resultsContainer.scrollLeft += e.deltaY * 1;
                        }
                    });
                }

                if (gridBtn && rowBtn) {
                    gridBtn.addEventListener('click', function () {
                        body.classList.add('view-grid');
                        body.classList.remove('view-row');
                        gridBtn.classList.add('active');
                        rowBtn.classList.remove('active');
                    });

                    rowBtn.addEventListener('click', function () {
                        body.classList.add('view-row');
                        body.classList.remove('view-grid');
                        rowBtn.classList.add('active');
                        gridBtn.classList.remove('active');
                    });
                }

                let searchTimeout;

                function performFilter() {
                    const currentSearchInput = document.getElementById('searchInput');
                    const currentKampusSelect = document.getElementById('kampusSelect');
                    const currentYearSelect = document.getElementById('yearSelect');
                    if (!currentSearchInput || !currentKampusSelect || !currentYearSelect) return;
                    const searchValue = currentSearchInput.value;
                    const kampusValue = currentKampusSelect.value;
                    const yearValue = currentYearSelect.value;
                    
                    let loaderTimeout = setTimeout(() => {
                        if (searchLoader) searchLoader.style.display = 'flex';
                        if (kampusLoader) kampusLoader.style.display = 'flex';
                        if (yearLoader) yearLoader.style.display = 'flex';
                    }, 300);

                    const params = new URLSearchParams();
                    if (searchValue) params.append('search', searchValue);
                    if (kampusValue) params.append('kampus', kampusValue);
                    if (yearValue && yearValue !== 'all') params.append('year', yearValue);

                    fetch(`{{ route('magang.index') }}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
                    })
                    .then(response => response.text())
                    .then(html => {
                        clearTimeout(loaderTimeout);
                        if (searchLoader) searchLoader.style.display = 'none';
                        if (kampusLoader) kampusLoader.style.display = 'none';
                        if (yearLoader) yearLoader.style.display = 'none';
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        // modifikasi start
                        // const newContainer = doc.getElementById('magang-container');
                        // const oldContainer = document.getElementById('magang-container');

                        // if (newContainer && oldContainer) {
                        //     oldContainer.innerHTML = newContainer.innerHTML;
                        // }
                        const newResults = doc.getElementById('magang-results');
                        const oldResults = document.getElementById('magang-results');
                        
                        if (newResults && oldResults) {
                            oldResults.innerHTML = newResults.innerHTML;
                        }
                        // modifikasi end
                        
                        // Re-init logic & Update URL
                        const newUrl = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
                        window.history.pushState({}, '', newUrl);
                        initializeAppLogic();
                    });
                }

                // Attach Listeners
                if (searchInput && !searchInput.dataset.listenerAttached) {
                    searchInput.addEventListener('input', function () {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(performFilter, 800);
                    });
                    searchInput.dataset.listenerAttached = 'true';
                }
                if (kampusSelect && !kampusSelect.dataset.listenerAttached) {
                    kampusSelect.addEventListener('change', performFilter);
                    kampusSelect.dataset.listenerAttached = 'true';
                }
                if (yearSelect && !yearSelect.dataset.listenerAttached) {
                    yearSelect.addEventListener('change', performFilter);
                    yearSelect.dataset.listenerAttached = 'true';
                }
            }

            // Auto-hide success alert
            const successAlert = document.querySelector('.success-alert');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.classList.add('fade-out');
                    setTimeout(() => {
                        if (successAlert.parentElement) successAlert.parentElement.remove();
                        else successAlert.remove();
                    }, 500);
                }, 3000);
            }

            document.addEventListener('DOMContentLoaded', initializeAppLogic);
        </script>
    @endpush

</x-main-layout>