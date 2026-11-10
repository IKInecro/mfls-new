@extends('layouts.admin')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- Header Banner -->
    <div class="relative overflow-hidden bg-[#0B1120] text-white p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl shadow-slate-950/20">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-blue-400 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    Kontrol Hak Akses &bull; Kelola Pengguna Sistem
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">
                    Kelola User & Hak Akses
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Kelola seluruh akun sistem: Admin, Palugada, Akademik, Mentor, Panitia, Dosen, serta Akun Pendaftar Beasiswa. Anda dapat menambah, mengedit kredensial, mereset password, atau menghapus akun beserta seluruh data relasinya sampai ke akar-akarnya.
                </p>
            </div>
            
            <button onclick="openAddModal()" 
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#0870B8] hover:bg-[#065A96] text-white rounded-xl text-xs font-bold transition-all shadow-md shrink-0 cursor-pointer">
                <span class="iconify text-base" data-icon="solar:user-plus-bold"></span>
                <span>Tambah User Baru</span>
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 text-[#0870B8] flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="solar:users-group-two-rounded-bold"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Pengguna</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalUsers ?? 0) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="solar:diploma-bold"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pendaftar Beasiswa</p>
                <h3 class="text-xl font-black text-emerald-600 mt-0.5">{{ number_format($totalPendaftar ?? 0) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="solar:shield-user-bold"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tim Staff & Internal</p>
                <h3 class="text-xl font-black text-purple-600 mt-0.5">{{ number_format($totalStaff ?? 0) }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <span class="iconify text-2xl" data-icon="solar:verified-check-bold"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email Terverifikasi</p>
                <h3 class="text-xl font-black text-amber-600 mt-0.5">{{ number_format($totalVerified ?? 0) }}</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filter Panel -->
    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.user.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-center">
            
            <!-- Search Text -->
            <div class="sm:col-span-6 relative">
                <span class="iconify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg" data-icon="solar:magnifer-linear"></span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama lengkap, alamat email, NISN, atau sekolah..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all">
            </div>

            <!-- Role Filter -->
            <div class="sm:col-span-4">
                <select name="role" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all">
                    <option value="">Semua Role (Seluruh Pengguna)</option>
                    <option value="pendaftar" {{ request('role') === 'pendaftar' ? 'selected' : '' }}>Pendaftar (Calon Mahasiswa)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin (Super Administrator)</option>
                    <option value="palugada" {{ request('role') === 'palugada' ? 'selected' : '' }}>Palugada (Verifikator Final)</option>
                    <option value="akademik" {{ request('role') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                    <option value="mentor" {{ request('role') === 'mentor' ? 'selected' : '' }}>Mentor</option>
                    <option value="panitia" {{ request('role') === 'panitia' ? 'selected' : '' }}>Panitia / Administrasi</option>
                    <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen (Interviewer)</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <span class="iconify text-sm" data-icon="solar:filter-bold"></span>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('admin.user.index') }}" 
                       class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all text-center" 
                       title="Reset Filter">
                        <span class="iconify text-sm" data-icon="solar:restart-bold"></span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-400">
                        <th class="py-4 px-6">User Pengguna</th>
                        <th class="py-4 px-6 text-center">Hak Akses / Role</th>
                        <th class="py-4 px-6 text-center">Status Verifikasi</th>
                        <th class="py-4 px-6">Terdaftar Sejak</th>
                        <th class="py-4 px-6 text-right">Aksi Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($users as $user)
                    @php
                        $roleConfig = [
                            'admin'     => ['bg-rose-50 text-rose-700 border-rose-200', 'ADMIN'],
                            'palugada'  => ['bg-purple-50 text-purple-700 border-purple-200', 'PALUGADA'],
                            'akademik'  => ['bg-blue-50 text-blue-700 border-blue-200', 'AKADEMIK'],
                            'mentor'    => ['bg-indigo-50 text-indigo-700 border-indigo-200', 'MENTOR'],
                            'panitia'   => ['bg-amber-50 text-amber-700 border-amber-200', 'ADMINISTRASI'],
                            'dosen'     => ['bg-sky-50 text-sky-700 border-sky-200', 'DOSEN / INTERVIEWER'],
                            'pendaftar' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'PENDAFTAR'],
                        ][$user->role] ?? ['bg-slate-100 text-slate-700 border-slate-200', strtoupper($user->role)];
                        
                        $isSelf = (auth()->id() === $user->id);
                        $hasPeserta = !empty($user->peserta);
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- User Column -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-black text-sm flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-slate-900 text-sm truncate">{{ $user->nama }}</h4>
                                        @if($isSelf)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[9px] font-black uppercase rounded-md">Akun Anda</span>
                                        @endif
                                    </div>
                                    <p class="text-slate-500 text-xs truncate mt-0.5">{{ $user->email }}</p>
                                    @if($hasPeserta)
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-slate-400">
                                            @if($user->peserta->nisn)
                                                <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-600">NISN: {{ $user->peserta->nisn }}</span>
                                            @endif
                                            @if($user->peserta->nama_sekolah && $user->peserta->nama_sekolah !== '-')
                                                <span class="truncate">&bull; {{ $user->peserta->nama_sekolah }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Role Column -->
                        <td class="py-4 px-6 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold border {{ $roleConfig[0] }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $roleConfig[1] }}
                            </span>
                        </td>

                        <!-- Status Column -->
                        <td class="py-4 px-6 text-center">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-[10px] font-bold">
                                    <span class="iconify text-xs" data-icon="solar:check-circle-bold"></span>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 border border-slate-200 text-slate-500 rounded-lg text-[10px] font-bold">
                                    <span class="iconify text-xs" data-icon="solar:clock-circle-bold"></span>
                                    Belum Verifikasi
                                </span>
                            @endif
                        </td>

                        <!-- Created At -->
                        <td class="py-4 px-6 text-slate-500">
                            <div class="font-medium">{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</div>
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <!-- Edit Button -->
                                <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'id' => $user->id,
                                            'nama' => $user->nama,
                                            'email' => $user->email,
                                            'role' => $user->role,
                                            'nisn' => $user->peserta->nisn ?? '',
                                            'nama_sekolah' => $user->peserta->nama_sekolah ?? '',
                                        ]) }})"
                                        class="p-2 bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded-xl transition-colors cursor-pointer"
                                        title="Edit Akun">
                                    <span class="iconify text-base" data-icon="solar:pen-bold-duotone"></span>
                                </button>

                                <!-- Reset Password Button -->
                                <button type="button" 
                                        onclick="confirmQuickReset({{ $user->id }}, '{{ addslashes($user->nama) }}')" 
                                        class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl border border-amber-200 transition-colors cursor-pointer" 
                                        title="Reset Password">
                                    <span class="iconify text-base" data-icon="solar:key-minimalistic-bold-duotone"></span>
                                </button>

                                <!-- Deep Delete (Akar-akarnya) -->
                                @if(!$isSelf)
                                    <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDeepDelete(this, '{{ addslashes($user->nama) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}', {{ $hasPeserta ? 'true' : 'false' }})" 
                                                class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl border border-rose-200 transition-colors cursor-pointer" 
                                                title="Hapus Akun Sampai ke Akar-akarnya">
                                            <span class="iconify text-base" data-icon="solar:trash-bin-trash-bold"></span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400">
                            <span class="iconify text-4xl mx-auto mb-2 text-slate-300" data-icon="solar:users-group-rounded-linear"></span>
                            <p class="font-bold text-sm">Tidak ada data pengguna yang cocok.</p>
                            <p class="text-xs text-slate-400 mt-1">Coba gunakan kata kunci pencarian atau filter role lain.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-5 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Add / Edit User -->
<div id="userModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity" onclick="closeUserModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-100 transform transition-all">
            
            <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                <div>
                    <h3 id="modalTitle" class="text-lg font-black text-slate-900 tracking-tight">Tambah User Baru</h3>
                    <p id="modalSub" class="text-xs text-slate-500 mt-0.5">Lengkapi formulir akun di bawah ini.</p>
                </div>
                <button type="button" onclick="closeUserModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors cursor-pointer">
                    <span class="iconify text-base" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>

            <form id="userForm" method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
                @csrf
                <div id="methodField"></div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" 
                           name="nama" 
                           id="formNama" 
                           required 
                           placeholder="Contoh: Budi Santoso"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" 
                           name="email" 
                           id="formEmail" 
                           required 
                           placeholder="nama@email.com"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all">
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hak Akses / Role</label>
                    <select name="role" 
                            id="formRole" 
                            required 
                            onchange="handleRoleChange(this.value)"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all">
                        <option value="pendaftar">Pendaftar (Calon Mahasiswa)</option>
                        <option value="admin">Admin (Super Administrator)</option>
                        <option value="palugada">Palugada (Verifikator Final)</option>
                        <option value="akademik">Akademik</option>
                        <option value="mentor">Mentor</option>
                        <option value="panitia">Panitia / Administrasi</option>
                        <option value="dosen">Dosen (Interviewer)</option>
                    </select>
                </div>

                <!-- Pendaftar Specific Fields (Dynamic) -->
                <div id="pendaftarExtraFields" class="space-y-4 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100">
                    <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="iconify text-sm" data-icon="solar:info-circle-bold"></span>
                        Data Profil Peserta
                    </p>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">NISN (Nomor Induk Siswa Nasional)</label>
                        <input type="text" 
                               name="nisn" 
                               id="formNisn" 
                               maxlength="20" 
                               placeholder="Contoh: 0051234567 (Opsional, otomatis digenerate jika kosong)"
                               class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:border-emerald-600 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Asal Sekolah</label>
                        <input type="text" 
                               name="nama_sekolah" 
                               id="formSekolah" 
                               placeholder="Contoh: SMAN 1 Jakarta"
                               class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:border-emerald-600 outline-none transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span id="passOptional" class="text-slate-400 font-normal lowercase">(kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="formPassword" 
                           placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition-all">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeUserModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="submitBtnText" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-md cursor-pointer">
                        Simpan Akun
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function handleRoleChange(role) {
        const extraFields = document.getElementById('pendaftarExtraFields');
        if (role === 'pendaftar') {
            extraFields.classList.remove('hidden');
        } else {
            extraFields.classList.add('hidden');
        }
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerText = "Tambah User Baru";
        document.getElementById('modalSub').innerText = "Daftarkan akun baru ke dalam sistem MFLS.";
        document.getElementById('userForm').action = "{{ route('admin.user.store') }}";
        document.getElementById('methodField').innerHTML = "";
        
        document.getElementById('formNama').value = "";
        document.getElementById('formEmail').value = "";
        document.getElementById('formRole').value = "pendaftar";
        document.getElementById('formPassword').value = "";
        document.getElementById('formPassword').required = true;
        document.getElementById('passOptional').classList.add('hidden');
        document.getElementById('formNisn').value = "";
        document.getElementById('formSekolah').value = "";
        
        handleRoleChange('pendaftar');
        document.getElementById('submitBtnText').innerText = "Simpan User Baru";
        document.getElementById('userModal').classList.remove('hidden');
    }

    function openEditModal(user) {
        document.getElementById('modalTitle').innerText = "Edit Akun Pengguna";
        document.getElementById('modalSub').innerText = "Perbarui profil, hak akses, atau kata sandi.";
        document.getElementById('userForm').action = "/admin/user/" + user.id;
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        
        document.getElementById('formNama').value = user.nama || '';
        document.getElementById('formEmail').value = user.email || '';
        document.getElementById('formRole').value = user.role || 'pendaftar';
        document.getElementById('formPassword').value = "";
        document.getElementById('formPassword').required = false;
        document.getElementById('passOptional').classList.remove('hidden');
        document.getElementById('formNisn').value = user.nisn || '';
        document.getElementById('formSekolah').value = user.nama_sekolah || '';
        
        handleRoleChange(user.role);
        document.getElementById('submitBtnText').innerText = "Simpan Perubahan";
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    // SweetAlert2 Deep Delete Confirm
    function confirmDeepDelete(button, nama, email, role, hasPeserta) {
        let warningText = `Akun "${nama}" (${email}) dengan role ${role.toUpperCase()} akan dihapus secara permanen.`;
        if (hasPeserta) {
            warningText += `\n\nPERINGATAN: Seluruh berkas fisik (foto, ijazah, rapor, video) di storage, data formulir pendaftaran, nilai raport, jawaban CBT, dan seluruh relasi ujian peserta ini akan DIHAPUS SAMPAI KE AKAR-AKARNYA tanpa sisa!`;
        }

        Swal.fire({
            title: 'Hapus User Sampai ke Akar?',
            text: warningText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Sampai ke Akar!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }

    // SweetAlert2 Quick Reset Password
    function confirmQuickReset(userId, userName) {
        Swal.fire({
            title: `Reset Password ${userName}`,
            text: 'Masukkan kata sandi baru untuk pengguna ini:',
            input: 'password',
            inputPlaceholder: 'Kata sandi baru (minimal 6 karakter)',
            inputAttributes: {
                minlength: 6,
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Reset Password',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            preConfirm: (password) => {
                if (!password || password.length < 6) {
                    Swal.showValidationMessage('Password harus diisi minimal 6 karakter!');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/user/${userId}/reset-password`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PUT';
                form.appendChild(method);

                const passInput = document.createElement('input');
                passInput.type = 'hidden';
                passInput.name = 'password';
                passInput.value = result.value;
                form.appendChild(passInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
