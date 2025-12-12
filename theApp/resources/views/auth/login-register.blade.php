<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register - telUArc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================== CUSTOM CSS ================== */
        .red-text { color: #DC2626; }
        .red-bg { background-color: #DC2626; }
        .red-border { border-color: #DC2626; }
        .red-hover:hover { background-color: #B91C1C; }
        .input-focus:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        /* ================== TAB ================== */
        .tab-content { display: none; }
        #login-tab:checked ~ .tab-contents #login-content,
        #register-tab:checked ~ .tab-contents #register-content {
            display: block;
        }
        #login-tab:checked ~ .tab-navs label[for="login-tab"],
        #register-tab:checked ~ .tab-navs label[for="register-tab"] {
            border-bottom: 2px solid #DC2626;
            color: #DC2626;
        }
        input[type="radio"][name="tabs"] {
            position: absolute;
            opacity: 0;
        }
        .tab-navs label {
            cursor: pointer;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            color: #6B7280;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        .tab-navs label:hover { color: #DC2626; }

        /* ================== AVATAR UPLOAD ================== */
        .avatar-upload { position: relative; display: inline-block; margin-bottom: 1rem; }
        .avatar-upload .avatar-edit { position: absolute; right: 8px; bottom: 8px; z-index: 1; }
        .avatar-upload .avatar-preview {
            width: 100px; height: 100px; position: relative; border-radius: 50%; overflow: hidden;
            border: 3px solid #F3F4F6; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .avatar-upload .avatar-preview > div {
            width: 100%; height: 100%; background-size: cover; background-repeat: no-repeat;
            background-position: center; background-color: #F3F4F6; display: flex;
            align-items: center; justify-content: center;
        }
        .avatar-upload input[type=file] {
            position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;
        }
        .avatar-placeholder { color: #9CA3AF; font-size: 2rem; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

<div class="max-w-md w-full">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold red-text mb-2">telUArc</h1>
        <p class="text-gray-600">Platform untuk berbagi karya seni digital</p>
    </div>

    <!-- Hidden radio buttons -->
    <input type="radio" name="tabs" id="login-tab" checked>
    <input type="radio" name="tabs" id="register-tab">

    <!-- Tab Navigation -->
    <div class="tab-navs flex border-b border-gray-200 mb-6">
        <label for="login-tab" class="flex-1 text-center">Masuk</label>
        <label for="register-tab" class="flex-1 text-center">Daftar</label>
    </div>

    <!-- Tab Contents -->
    <div class="tab-contents">

        <!-- ===== LOGIN FORM ===== -->
        <div id="login-content" class="tab-content">
            <form class="bg-white p-8 rounded-lg shadow-md" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="login-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="login-email" name="email" type="email" autocomplete="email" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="nama@email.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="login-password" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="#" class="text-sm red-text hover:text-red-700">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="login-password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-center mt-6">
                    <input id="remember-me" name="remember-me" type="checkbox"
                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white red-bg red-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt h-5 w-5 text-red-200 group-hover:text-red-100"></i>
                        </span>
                        Masuk
                    </button>
                </div>

                {{-- Error login server-side --}}
                @if($errors->any())
                    <div class="mt-4 text-red-600 text-sm">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>

        <!-- ===== REGISTER FORM ===== -->
        <div id="register-content" class="tab-content">
            <form class="bg-white p-8 rounded-lg shadow-md" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">

                    <!-- Avatar -->
                    <div class="flex justify-center">
                        <div class="avatar-upload">
                            <div class="avatar-edit">
                                <label for="avatar-upload" class="bg-white rounded-full p-2 shadow-md cursor-pointer hover:bg-gray-100">
                                    <i class="fas fa-camera text-gray-600"></i>
                                </label>
                                <input type="file" id="avatar-upload" name="avatar" accept="image/*" />
                            </div>
                            <div class="avatar-preview">
                                <div>
                                    <i class="fas fa-user avatar-placeholder"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="register-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="register-name" name="name" type="text" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="Masukkann Nama Lengkap Anda" value="{{ old('name') }}">
                        </div>
                    </div>

                    <div>
                        <label for="register-username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-at text-gray-400"></i>
                            </div>
                            <input id="register-username" name="username" type="text" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="Tulis username Anda" value="{{ old('username') }}">
                        </div>
                    </div>

                    <div>
                        <label for="register-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="register-email" name="email" type="email" autocomplete="email" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="Masukkan email Anda" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <label for="register-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="register-password" name="password" type="password" autocomplete="new-password" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="Masukkan password Anda">
                        </div>
                    </div>

                    <div>
                        <label for="register-confirm-password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="register-confirm-password" name="password_confirmation" type="password" autocomplete="new-password" required
                                class="appearance-none relative block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                                placeholder="Masukkan Ulang Password Anda">
                        </div>
                    </div>

                    <div>
                        <label for="register-bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea id="register-bio" name="bio" rows="3"
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-500 text-gray-900 input-focus sm:text-sm"
                            placeholder="Ceritakan tentang diri Anda...">{{ old('bio') }}</textarea>
                    </div>

                </div>

                <div class="flex items-start mt-6">
                    <div class="flex items-center h-5">
                        <input id="agree-terms" name="agree-terms" type="checkbox" required
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="agree-terms" class="text-gray-600">
                            Saya menyetujui <a href="#" class="red-text hover:text-red-700">Syarat & Ketentuan</a> dan <a href="#" class="red-text hover:text-red-700">Kebijakan Privasi</a>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white red-bg red-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus h-5 w-5 text-red-200 group-hover:text-red-100"></i>
                        </span>
                        Daftar
                    </button>
                </div>

                {{-- Error server-side --}}
                @if($errors->any())
                    <div class="mt-4 text-red-600 text-sm">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>

    </div>
</div>

<script>
// ================= AVATAR PREVIEW =================
document.getElementById('avatar-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        const previewDiv = document.querySelector('.avatar-preview > div');
        previewDiv.style.backgroundImage = `url(${event.target.result})`;
        previewDiv.innerHTML = ''; // hilangkan placeholder icon
    }
    reader.readAsDataURL(file);
});

// ================= REGISTER FORM VALIDATION =================
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('#register-content form');

    registerForm.addEventListener('submit', function(e) {
        const name = document.getElementById('register-name').value.trim();
        const username = document.getElementById('register-username').value.trim();
        const email = document.getElementById('register-email').value.trim();
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;
        const bio = document.getElementById('register-bio').value.trim();
        const agreeTerms = document.getElementById('agree-terms').checked;

        let errors = [];

        if (!name) errors.push("Nama lengkap wajib diisi.");
        if (!username) errors.push("Username wajib diisi.");
        if (!email) errors.push("Email wajib diisi.");
        if (!password) errors.push("Password wajib diisi.");
        if (!confirmPassword) errors.push("Konfirmasi password wajib diisi.");
        if (password && confirmPassword && password !== confirmPassword) errors.push("Password dan konfirmasi password harus sama.");
        if (!bio) errors.push("Bio wajib diisi.");
        if (!agreeTerms) errors.push("Anda harus menyetujui Syarat & Ketentuan.");

        if (errors.length > 0) {
            e.preventDefault(); // hentikan submit
            let errorDiv = registerForm.querySelector('.client-errors');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.classList.add('client-errors', 'mt-4', 'text-red-600', 'text-sm');
                registerForm.prepend(errorDiv);
            }
            errorDiv.innerHTML = errors.map(err => `<p>${err}</p>`).join('');
        }
    });
});
</script>

</body>
</html>
