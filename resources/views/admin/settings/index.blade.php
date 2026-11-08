@extends('admin.layout.app')
@section('title', 'Pengaturan')

@section('content')
<div class="space-y-6">
    <!-- Pengaturan Akun (Admin) -->
    @php($me = auth()->user())
    <form method="POST" action="{{ route('admin.settings.account') }}" enctype="multipart/form-data"
          x-data="{ photoPreview: null }" class="bg-white rounded-xl border border-line shadow-sm overflow-hidden">
        @csrf

        <div class="p-6">
            <h3 class="text-lg font-bold text-ink mb-4 pb-2 border-b border-line">Pengaturan Akun (Admin)</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <img :src="photoPreview || '{{ $me->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($me->name ?? 'Admin') }}'"
                         alt="Foto Profil" class="h-20 w-20 rounded-full object-cover border-4 border-page">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Foto Profil</label>
                        <input type="file" name="profile_photo" accept="image/*"
                               @change="const f = $event.target.files[0]; if (f) photoPreview = URL.createObjectURL(f)"
                               class="block text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-brand file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-green-700">
                        <p class="text-xs text-muted mt-1">Format JPG/PNG/WebP, maks 5 MB. Pratinjau muncul sebelum disimpan.</p>
                        @if($me->profile_photo)
                            <label class="mt-2 inline-flex items-center gap-2 text-xs text-muted">
                                <input type="checkbox" name="remove_photo" value="1" @change="if ($event.target.checked) photoPreview = 'https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $me->name ?? 'Admin' }}')" class="h-4 w-4 rounded border-line text-brand focus:ring-brand">
                                Hapus foto saat ini
                            </label>
                        @endif
                        @error('profile_photo')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $me->name) }}" required class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand">
                        <p class="text-xs text-muted mt-1">Nama tampilan yang muncul ke pengguna lain (boleh berspasi).</p>
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $me->email) }}" required class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand">
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">No. HP / WA</label>
                        <input type="text" name="phone" value="{{ old('phone', $me->phone) }}" class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand" placeholder="08xxxxxxxxxx">
                        @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password" class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand" placeholder="Kosongkan jika tidak diubah">
                        @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-ink mb-1">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-line flex justify-end">
            <button type="submit" class="bg-brand hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-colors">
                Simpan Akun
            </button>
        </div>
    </form>

    <!-- Moderasi Konten -->
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl border border-line shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_section" value="moderation">
        
        <div class="p-6">
            <h3 class="text-lg font-bold text-ink mb-4 pb-2 border-b border-line">Moderasi Konten</h3>
            <div class="space-y-4">
                <div class="flex items-start">
                    <input type="checkbox" name="banned_words_enabled" value="1" id="banned_words_enabled" {{ ($settings['banned_words_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="mt-1 h-4 w-4 text-brand border-line rounded focus:ring-brand">
                    <div class="ml-3">
                        <label for="banned_words_enabled" class="text-sm font-medium text-ink">Aktifkan Filter Kata Terlarang</label>
                        <p class="text-xs text-muted">Blokir postingan yang mengandung kata tidak pantas</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1">Daftar Kata Terlarang</label>
                    <p class="text-xs text-muted mb-2">Pisahkan dengan koma atau baris baru. Contoh: judi, narkoba, penipuan</p>
                    <textarea name="banned_words" rows="3" class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand" placeholder="kata1, kata2, kata3">{{ $settings['banned_words'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-line flex justify-end">
            <button type="submit" class="bg-brand hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-colors">
                Simpan Pengaturan
            </button>
        </div>
    </form>

    <!-- Session Timeout -->
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-xl border border-line shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="form_section" value="security">
        
        <div class="p-6">
            <h3 class="text-lg font-bold text-ink mb-4 pb-2 border-b border-line">Keamanan</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1">Waktu Sesi (Menit)</label>
                    <p class="text-xs text-muted mb-2">Pengguna akan logout otomatis setelah tidak aktif</p>
                    <input type="number" name="session_timeout_minutes" value="{{ $settings['session_timeout_minutes'] ?? 120 }}" min="1" class="w-full p-2 border border-line rounded-lg focus:ring-brand focus:border-brand">
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-line flex justify-end">
            <button type="submit" class="bg-brand hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg shadow-sm transition-colors">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
