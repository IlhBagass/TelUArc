<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $user->username }} - telUArc</title>
    <script>
        window.currentUserId = "{{ Auth::id() }}";
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('logo-telkom.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/profilePage.css') }}">
</head>
<body class="bg-white">
    <!-- Header - Same as Landing Page -->
    <!-- Header -->
    <header class="header text-gray-800 sticky top-0 z-40" id="mainHeader">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('artworks.index') }}">
                        <img src="{{ asset('logo-telu-arc.png') }}" alt="TelUArc Logo" class="h-12 w-auto object-contain rounded-xl">
                    </a>
                </div>
                
                <!-- Search Bar -->
                <div class="search-bar">
                    <form action="{{ route('artworks.index') }}" method="GET" class="w-full">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Cari judul karya..." value="{{ request('search') }}">
                    </form>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button id="themeToggle" onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                        <i class="fas fa-moon text-gray-600"></i>
                    </button>

                    <button onclick="openUploadModal()" class="red-bg text-white px-4 py-2 rounded-full text-sm font-semibold transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i> Upload Karya
                    </button>
                    
                    <div class="relative">
                        <button onclick="toggleProfileMenu()" class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-gray-200 hover:ring-red-400 transition-all">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile">
                            @else
                                <img src="https://i.pravatar.cc/100" alt="Profile">
                            @endif
                        </button>
                        
                        <div id="profileMenu" class="hidden absolute right-0 mt-4 w-56 bg-white rounded-xl shadow-2xl py-2 border border-gray-100 transform transition-all duration-200 origin-top-right z-50">
                            @auth
                            <div class="px-4 py-3 border-b border-gray-100 mb-2">
                                <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('users.show', auth()->user()->id) }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-500 transition-colors flex items-center">
                                <i class="fas fa-user w-6 text-center mr-2"></i> Profil Saya
                            </a>

                            <div class="my-1 border-t border-gray-100"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors flex items-center">
                                    <i class="fas fa-sign-out-alt w-6 text-center mr-2"></i> Keluar
                                </button>
                            </form>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Profile Hero - Custom Design -->
    <section class="profile-hero">
        <div class="profile-hero-content">
            <div class="profile-info">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="profile-avatar-large">
                @else
                    <img src="https://i.pravatar.cc/150" alt="{{ $user->name }}" class="profile-avatar-large">
                @endif
                
                <!-- Name and Username moved to Info Card -->
                
                <!-- Profile Info Cards -->
                <div class="profile-info-cards">
                    <!-- Contact Info Card -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-card-icon">
                                <i class="fas fa-address-card"></i>
                            </div>
                            <h3 class="info-card-title">Informasi Akun</h3>
                        </div>
                        <div class="info-card-content">
                            <div class="info-item">
                                <i class="fas fa-user"></i>
                                <span>{{ $user->name }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-at"></i>
                                <span>{{ $user->username }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media Card -->
                    <!-- Description Card -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="info-card-icon">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <h3 class="info-card-title">Deskripsi Account</h3>
                        </div>
                        <div class="info-card-content">
                            <p class="text-gray-700 leading-relaxed">{{ $user->bio ?: 'Tidak ada deskripsi' }}</p>
                        </div>
                    </div>
                    

                </div>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $artworks->count() }}</div>
                        <div class="stat-label">Karya</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $user->bookmarks->count() }}</div>
                        <div class="stat-label">Bookmark</div>
                    </div>

                </div>
                
                <div class="profile-actions">
                    <button class="edit-profile-btn" onclick="openEditProfileModal()">
                        <i class="fas fa-edit"></i>
                        <span>Edit Profil</span>
                    </button>
                    <button class="share-profile-btn">
                        <i class="fas fa-share-alt"></i>
                        <span>Bagikan</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation Tabs - HILANGKAN TAB "Disukai" -->
    <nav class="profile-nav">
        <div class="nav-content">
            <div class="nav-tabs">
                <div class="nav-tab active" onclick="showTab('artworks')">
                    <i class="fas fa-images tab-icon"></i>
                    <span>Karya Saya</span>
                </div>
                <div class="nav-tab" onclick="showTab('bookmarks')">
                    <i class="fas fa-bookmark tab-icon"></i>
                    <span>Bookmark</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Area -->
    <main class="content">
        <!-- Artworks Tab -->
        <div id="artworks-tab" class="tab-content active">
            @if($artworks->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-32 h-32 bg-red-50 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-palette text-5xl text-red-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Karya</h3>
                    <p class="text-gray-500 max-w-sm mx-auto mb-6">Anda belum mengunggah karya apapun. Bagikan kreativitas Anda kepada dunia sekarang!</p>
                    <button onclick="openUploadModal()" class="px-6 py-3 bg-red-600 text-white rounded-full font-medium hover:bg-red-700 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-plus mr-2"></i> Upload Karya Pertama
                    </button>
                </div>
            @else
                <div class="masonry-grid flex-grow">
                    @foreach($artworks as $artwork)
                        <div class="masonry-item fade-in" data-artwork-id="{{ $artwork->id }}">
                            <div class="artwork-card"
                                data-artwork-id="{{ $artwork->id }}"
                                onclick="openArtworkPopup('{{ $artwork->id }}')">
                                <div class="relative">
                                    @if($artwork->main_image)
                                        <img src="{{ asset('storage/' . $artwork->main_image) }}" alt="{{ $artwork->title }}" class="w-full">
                                    @else
                                        <img src="https://via.placeholder.com/400x400" alt="{{ $artwork->title }}" class="w-full">
                                    @endif
                                    <div class="artwork-overlay">
                                        <div class="artwork-actions relative">
                                            <button class="action-button" onclick="toggleBookmark(event, '{{ $artwork->id }}')">
                                                <i class="{{ (Auth::check() && Auth::user()->bookmarks->contains($artwork->id)) ? 'fas' : 'far' }} fa-bookmark"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-1">{{ $artwork->title }}</h3>
                                    <div class="flex items-center justify-between text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <img src="{{ $artwork->user->avatar ? asset('storage/' . $artwork->user->avatar) : 'https://i.pravatar.cc/100' }}" alt="{{ $artwork->user->name }}" class="w-6 h-6 rounded-full mr-2">
                                            <span>{{ $artwork->user->name }}</span>
                                        </div>
                                        <span>{{ $artwork->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Bookmarks Tab -->
        <div id="bookmarks-tab" class="tab-content">
            <!-- Empty State -->
            <div id="no-bookmarks" class="{{ $user->bookmarks->isEmpty() ? '' : 'hidden' }} flex flex-col items-center justify-center py-16 text-center">
                <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="far fa-bookmark text-5xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Bookmark</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">Simpan karya yang Anda sukai untuk melihatnya kembali di sini.</p>
                <button onclick="document.querySelector('.nav-tab[onclick*=\'artworks\']').click()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-full font-medium hover:bg-gray-50 transition-colors">
                    Jelajahi Karya Anda
                </button>
            </div>

            <!-- Grid -->
            <div class="masonry-grid flex-grow {{ $user->bookmarks->isEmpty() ? 'hidden' : '' }}" id="bookmarks-grid">
                @foreach($user->bookmarks as $artwork)
                    <div class="masonry-item fade-in" data-artwork-id="{{ $artwork->id }}">
                        <div class="artwork-card"
                            data-artwork-id="{{ $artwork->id }}"
                            onclick="openArtworkPopup('{{ $artwork->id }}')">
                            <div class="relative">
                                @if($artwork->main_image)
                                    <img src="{{ asset('storage/' . $artwork->main_image) }}" alt="{{ $artwork->title }}" class="w-full">
                                @else
                                    <img src="https://via.placeholder.com/400x400" alt="{{ $artwork->title }}" class="w-full">
                                @endif
                                <div class="artwork-overlay">
                                    <div class="artwork-actions relative">
                                        <button class="action-button bookmarked" onclick="toggleBookmark(event, '{{ $artwork->id }}')">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg mb-1">{{ $artwork->title }}</h3>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <img src="{{ $artwork->user->avatar ? asset('storage/' . $artwork->user->avatar) : 'https://i.pravatar.cc/100' }}" alt="{{ $artwork->user->name }}" class="w-6 h-6 rounded-full mr-2">
                                        <span>{{ $artwork->user->name }}</span>
                                    </div>
                                    <span>{{ $artwork->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Artwork Popup - Same as Landing Page -->
    <div id="artworkPopup" class="artwork-popup">
        <div class="popup-content relative min-h-[400px]">
            <button onclick="closeArtworkPopup()" class="absolute top-6 right-6 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-100 z-30 transition-all">
                <i class="fas fa-times text-gray-600 text-xl"></i>
            </button>
            
            <div class="p-8">
                <!-- Artwork Header -->
                <div class="mb-6">
                    <h2 id="popupTitle" class="text-3xl font-bold mb-2"></h2>
                    <div class="flex items-center space-x-4 text-gray-600">
                        <div class="flex items-center space-x-3">
                            <img id="popupArtistAvatar" src="" alt="Artist" class="w-10 h-10 rounded-full">
                            <div>
                                <div id="popupArtist" class="font-medium"></div>
                                <div class="text-sm text-gray-500">Seniman</div>
                            </div>
                        </div>
                        <span id="popupTime" class="text-sm"></span>
                    </div>
                </div>
                
                <!-- Image Gallery -->
                <div class="image-gallery mb-8">
                    <!-- Main Image Container -->
                    <div class="main-image-container">
                        <img id="popupMainImage" src="" alt="Artwork" class="main-image">
                    </div>
                    
                    <!-- Thumbnail Grid -->
                    <div class="thumbnail-grid" id="thumbnailGrid">
                        <!-- Thumbnails will be dynamically inserted here -->
                    </div>
                </div>
                
                <!-- Description Section -->
                <div class="mb-8">
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <h3 class="text-xl font-semibold mb-4 flex items-center">
                            <i class="fas fa-align-left mr-3 text-red-500"></i> Deskripsi Karya
                        </h3>
                        <p id="popupDescription" class="text-gray-700 leading-relaxed text-lg"></p>
                    </div>
                </div>
                
                <!-- Tags Section -->
                <div class="mb-8" id="popupTagsContainer">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-tags mr-3 text-red-500"></i> Tags
                    </h3>
                    <div id="popupTags" class="flex flex-wrap gap-2"></div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <div class="flex space-x-4">
                        <button id="popupBookmarkBtn" class="action-button-large group" onclick="toggleBookmark(event, currentArtworkId)">
                            <i class="far fa-bookmark text-xl group-hover:scale-110 transition-transform"></i>
                        </button>
                        
                        <!-- Edit Button -->
                        <button id="popupEditBtn" class="hidden action-button-large group text-blue-500 hover:bg-blue-50" onclick="openEditArtworkModal(currentArtworkId)">
                            <i class="fas fa-edit text-xl group-hover:scale-110 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal - SAMA DENGAN LANDING PAGE -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border-0 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col modal-content" id="uploadModalContent">
            <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-cloud-upload-alt mr-3"></i> Upload Karya
                    </h2>
                    <button onclick="closeUploadModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto flex-grow">
                @csrf
                
                <!-- Main Image Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Gambar Utama</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer" id="mainImageContainer">
                        <div id="mainImagePreview" class="hidden mb-4">
                            <img src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow-md">
                        </div>
                        <div id="mainImagePlaceholder">
                            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-2">Klik atau seret gambar ke sini</p>
                            <p class="text-gray-500 text-sm">PNG, JPG, JPEG hingga 2MB</p>
                        </div>
                        <input type="file" name="main_image" accept="image/*" required class="hidden" id="mainImageInput">
                    </div>
                </div>

                <!-- Title Input -->
                <div class="mb-5">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Karya</label>
                    <div class="relative">
                        <input type="text" name="title" id="title" class="w-full form-input" placeholder="Berikan judul untuk karya Anda" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-heading text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Description Textarea -->
                <div class="mb-5">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <div class="relative">
                        <textarea name="description" id="description" class="w-full form-input" rows="4" placeholder="Ceritakan tentang karya Anda..."></textarea>
                        <div class="absolute top-3 right-3 pointer-events-none">
                            <i class="fas fa-align-left text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Additional Images -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Gambar Tambahan (Opsional)</label>
                    <div class="border border-gray-300 rounded-xl p-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-gray-600">Tambahkan gambar opsional</span>
                            <label for="additionalImagesInput" class="cursor-pointer bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </label>
                            <input type="file" name="additional_images[]" multiple accept="image/*" id="additionalImagesInput" class="hidden">
                        </div>
                        <div id="additionalImagesPreview" class="grid grid-cols-3 gap-2">
                            <!-- Preview images will be added here dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeUploadModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary flex items-center">
                        <i class="fas fa-upload mr-2"></i> Upload Karya
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Artwork Modal -->
    <div id="editArtworkModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border-0 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col modal-content" id="editArtworkModalContent">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-edit mr-3"></i> Edit Karya
                    </h2>
                    <button onclick="closeEditArtworkModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="editArtworkForm" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto flex-grow">
                @csrf
                @method('PUT')
                
                <!-- Main Image Update -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Gambar Utama (Opsional)</label>
                    <div class="relative group cursor-pointer" onclick="document.getElementById('editMainImageInput').click()">
                        <img id="editMainImagePreview" src="" class="w-full h-48 object-cover rounded-xl shadow-md">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                            <span class="text-white font-medium"><i class="fas fa-camera mr-2"></i> Ganti Gambar</span>
                        </div>
                    </div>
                    <input type="file" name="main_image" accept="image/*" class="hidden" id="editMainImageInput">
                </div>

                <!-- Title Input -->
                <div class="mb-5">
                    <label for="editTitle" class="block text-sm font-medium text-gray-700 mb-2">Judul Karya</label>
                    <input type="text" name="title" id="editTitle" class="w-full form-input" required>
                </div>

                <!-- Description Textarea -->
                <div class="mb-5">
                    <label for="editDescription" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="editDescription" class="w-full form-input" rows="4"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <button type="button" onclick="deleteArtwork(currentArtworkId)" class="px-5 py-2.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus
                    </button>
                    
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeEditArtworkModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg flex items-center transition-colors shadow-sm">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border-0 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col modal-content" id="editProfileModalContent">
            <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 text-white flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-user-edit mr-3"></i> Edit Profil
                    </h2>
                    <button onclick="closeEditProfileModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto flex-grow">
                @csrf
                @method('PUT')
                
                <!-- Avatar Upload -->
                <div class="mb-6 text-center">
                    <div class="relative inline-block">
                        <img id="avatarPreview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://i.pravatar.cc/150' }}" alt="Profile" class="w-32 h-32 rounded-full mx-auto border-4 border-white shadow-lg">
                        <label for="avatarInput" class="absolute bottom-0 right-0 bg-red-500 text-white rounded-full w-10 h-10 flex items-center justify-center cursor-pointer hover:bg-red-600 transition-colors">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="avatar" accept="image/*" id="avatarInput" class="hidden">
                    </div>
                </div>

                <!-- Name Input -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <input type="text" name="name" id="name" value="{{ $user->name }}" class="w-full form-input" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Username Input -->
                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <input type="text" name="username" id="username" value="{{ $user->username }}" class="w-full form-input" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-at text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Bio Textarea -->
                <div class="mb-5">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <div class="relative">
                        <textarea name="bio" id="bio" class="w-full form-input" rows="3" placeholder="Ceritakan tentang diri Anda...">{{ $user->bio }}</textarea>
                        <div class="absolute top-3 right-3 pointer-events-none">
                            <i class="fas fa-align-left text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeEditProfileModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary flex items-center">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" onclick="openUploadModal()">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <!-- Auth Check Modal (Copied from Landing Page) -->
    <div id="authCheckModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm border-0 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col modal-content" id="authCheckModalContent">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-lock text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Akses Terbatas</h3>
                <p class="text-gray-600 mb-6">Fitur ini hanya tersedia untuk pengguna yang sudah login.</p>
                
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <p class="text-sm font-medium text-gray-800 mb-3">Apakah Anda sudah memiliki akun?</p>
                    <div class="flex gap-3">
                        <a href="{{ route('auth.form') }}" class="flex-1 bg-red-600 text-white py-2 rounded-lg font-medium hover:bg-red-700 transition-colors shadow-sm">
                            Sudah
                        </a>
                        <a href="{{ route('auth.form', ['tab' => 'register']) }}" class="flex-1 bg-white text-gray-700 border border-gray-300 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Belum
                        </a>
                    </div>
                </div>
                
                <button onclick="closeAuthCheckModal()" class="text-gray-400 hover:text-gray-600 text-sm">
                    Batal, kembali melihat karya
                </button>
            </div>
        </div>
    </div>

    <script>
        // =======================
        // Global Variables
        // =======================
        let bookmarks = [];
        let currentArtworkId = null; 
        let currentTab = 'artworks';
        const currentUserId = window.currentUserId || null;

        // =======================
        // Initialization
        // =======================
        document.addEventListener("DOMContentLoaded", async () => {
            // Load Bookmarks
             try {
                const res = await fetch("/bookmarks", {
                    headers: { "Accept": "application/json" }
                });
                if (res.ok) {
                    const bookmarkedIds = await res.json();
                    bookmarks = bookmarkedIds; 

                    // Update UI based on fetched bookmarks
                    bookmarkedIds.forEach(id => {
                        const cards = document.querySelectorAll(`[data-artwork-id="${id}"]`);
                        cards.forEach(card => {
                            const icon = card.querySelector(".action-button i");
                            const button = card.querySelector(".action-button");
                            if(icon && button) {
                                icon.classList.remove("far");
                                icon.classList.add("fas");
                                button.classList.add("bookmarked");
                            }
                        });
                    });
                }
            } catch (err) {
                console.error("Gagal memuat data bookmark:", err);
            }

            // Image Upload Previews
            setupImageUploads();

            // Avatar Preview
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');
            if(avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => avatarPreview.src = e.target.result;
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Global Event Listeners
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-backdrop')) {
                    e.target.classList.add('hidden');
                }
                if (e.target.id === 'artworkPopup') {
                    closeArtworkPopup();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeArtworkPopup();
                }
            });
        });

        function setupImageUploads() {
            const mainCtx = document.getElementById('mainImageContainer');
            if(!mainCtx) return;

            const mainInp = document.getElementById('mainImageInput');
            const mainPrev = document.getElementById('mainImagePreview');
            const mainPlace = document.getElementById('mainImagePlaceholder');
            const addInp = document.getElementById('additionalImagesInput');
            const addPrev = document.getElementById('additionalImagesPreview');
            
            mainCtx.addEventListener('click', () => mainInp.click());
            
            mainInp.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        mainPrev.querySelector('img').src = e.target.result;
                        mainPrev.classList.remove('hidden');
                        mainPlace.classList.add('hidden');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            addInp.addEventListener('change', function() {
                addPrev.innerHTML = '';
                if (this.files) {
                    Array.from(this.files).slice(0, 5).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const div = document.createElement('div');
                            div.className = 'relative group w-20 h-20';
                            div.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">
                                <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" onclick="this.parentElement.remove()">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            `;
                            addPrev.appendChild(div);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            });

            // Drag & Drop
            ['dragover', 'dragleave', 'drop'].forEach(eventName => {
                mainCtx.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    if(eventName === 'dragover') mainCtx.classList.add('border-red-500', 'bg-red-50');
                    else mainCtx.classList.remove('border-red-500', 'bg-red-50');
                });
            });
            
            mainCtx.addEventListener('drop', (e) => {
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    mainInp.files = e.dataTransfer.files;
                    mainInp.dispatchEvent(new Event('change'));
                }
            });
        }

        // =======================
        // Tab Navigation
        // =======================
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(navTab => navTab.classList.remove('active'));
            
            document.getElementById(`${tabName}-tab`).classList.add('active');
            if(event && event.target) {
                 const navTab = event.target.closest('.nav-tab');
                 if(navTab) navTab.classList.add('active');
            }
            currentTab = tabName;
        }

        // =======================
        // Auth Helpers
        // =======================
        function checkAuth() {
            return !!window.currentUserId;
        }

        function openAuthCheckModal() {
            const modal = document.getElementById('authCheckModal');
            const content = document.getElementById('authCheckModalContent');
            if(modal && content) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        }

        function closeAuthCheckModal() {
            const modal = document.getElementById('authCheckModal');
            const content = document.getElementById('authCheckModalContent');
            if(modal && content) {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => modal.classList.add('hidden'), 300);
            }
        }

        // =======================
        // Bookmark Functions
        // =======================
        async function toggleBookmark(event, artworkId) {
            if (event) event.stopPropagation();

            let icon, button;
            const popupBtn = document.getElementById('popupBookmarkBtn');

            // 1. Determine Context
            if (event && event.currentTarget && event.currentTarget.id === 'popupBookmarkBtn') {
                button = popupBtn;
                icon = button.querySelector('i');
            } else {
                const targetBtn = event.target.closest('.action-button');
                if(targetBtn) {
                     button = targetBtn;
                     icon = button.querySelector('i');
                } else {
                     const card = document.querySelector(`[data-artwork-id="${artworkId}"]`);
                     if (card) {
                        button = card.querySelector(".action-button"); 
                        icon = button.querySelector("i");
                    }
                }
            }

            if (!icon || !button) return;

            // 2. Auth Check
            if (!checkAuth()) {
                openAuthCheckModal();
                return;
            }

            const isBookmarked = icon.classList.contains("fas");

            try {
                if (isBookmarked) {
                    // DELETE
                    const res = await fetch(`/bookmarks/${artworkId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (res.ok) {
                        updateBookmarkUI(artworkId, 'remove');
                        bookmarks = bookmarks.filter(id => id != artworkId);
                        
                        // Dynamic Update: Remove from bookmarks tab
                        const itemToRemove = document.querySelector(`#bookmarks-tab .masonry-item[data-artwork-id="${artworkId}"]`);
                        if (itemToRemove) itemToRemove.remove();

                        // Check if empty
                        const remainingItems = document.querySelectorAll('#bookmarks-tab .masonry-item');
                        if (remainingItems.length === 0) {
                            const grid = document.getElementById('bookmarks-grid');
                            const emptyState = document.getElementById('no-bookmarks');
                            if(grid) grid.classList.add('hidden');
                            if(emptyState) emptyState.classList.remove('hidden');
                        }
                    } 
                } else {
                    // CREATE
                    const res = await fetch('/bookmarks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ artwork_id: artworkId })
                    });
                    
                    if (res.ok) {
                        updateBookmarkUI(artworkId, 'add');
                        if (!bookmarks.includes(Number(artworkId))) {
                            bookmarks.push(Number(artworkId));
                        }
                        addToBookmarksTab(artworkId);
                    }
                }
            } catch (error) {
                console.error('Error toggling bookmark:', error);
                alert('Terjadi kesalahan saat menyimpan bookmark');
            }
        }

        function updateBookmarkUI(artworkId, action) {
             // Update Popup
            const popupBtn = document.getElementById('popupBookmarkBtn');
            if (popupBtn && currentArtworkId == artworkId) {
                const icon = popupBtn.querySelector('i');
                icon.className = action === 'add' ? 'fas fa-bookmark' : 'far fa-bookmark';
                if(action === 'add') popupBtn.classList.add('bookmarked');
                else popupBtn.classList.remove('bookmarked');
            }

            // Update All Cards
            document.querySelectorAll(`.artwork-card[data-artwork-id="${artworkId}"] .action-button`).forEach(btn => {
                 const icon = btn.querySelector('i');
                 if(icon && icon.classList.contains('fa-bookmark')) {
                    icon.className = action === 'add' ? 'fas fa-bookmark' : 'far fa-bookmark';
                    if(action === 'add') btn.classList.add('bookmarked');
                    else btn.classList.remove('bookmarked');
                 }
            });
        }

        function addToBookmarksTab(artworkId) {
             const bookmarksTabGrid = document.getElementById('bookmarks-grid');
             const emptyState = document.getElementById('no-bookmarks');

             if (bookmarksTabGrid && !document.querySelector(`#bookmarks-tab .masonry-item[data-artwork-id="${artworkId}"]`)) {
                // Clone from "Karya Saya"
                const sourceCard = document.querySelector(`#artworks-tab .masonry-item[data-artwork-id="${artworkId}"]`);
                if (sourceCard) {
                    const clone = sourceCard.cloneNode(true);
                    const cloneBtn = clone.querySelector('.fa-bookmark').closest('button');
                    if(cloneBtn) {
                        cloneBtn.classList.add('bookmarked');
                        cloneBtn.querySelector('i').className = 'fas fa-bookmark';
                    }
                    bookmarksTabGrid.appendChild(clone);

                    // Show grid if hidden
                    if(emptyState && !emptyState.classList.contains('hidden')) {
                        emptyState.classList.add('hidden');
                        bookmarksTabGrid.classList.remove('hidden');
                    }
                }
            }
        }

        // Edit Artwork Modal Functions
        function openEditArtworkModal(artworkId) {
            currentArtworkId = artworkId;
            const modal = document.getElementById('editArtworkModal');
            const content = document.getElementById('editArtworkModalContent');
            const form = document.getElementById('editArtworkForm');
            
            // Set form action
            form.action = `/artworks/${artworkId}`;

            // Populate current data from DOM
            document.getElementById('editTitle').value = document.getElementById('popupTitle').textContent;
            document.getElementById('editDescription').value = document.getElementById('popupDescription').textContent;
            document.getElementById('editMainImagePreview').src = document.getElementById('popupMainImage').src;

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            // Hide popup temporarily (optional, but cleaner)
             document.getElementById('artworkPopup').classList.add('hidden');
        }

        async function deleteArtwork(artworkId) {
            if (!confirm('Apakah Anda yakin ingin menghapus karya ini? Tindakan ini tidak dapat dibatalkan.')) return;

            try {
                const res = await fetch(`/artworks/${artworkId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    alert('Karya berhasil dihapus');
                    window.location.reload(); // Reload to reflect changes
                } else {
                    const data = await res.json();
                    alert(data.message || 'Gagal menghapus karya');
                }
            } catch (error) {
                console.error('Error deleting artwork:', error);
                alert('Terjadi kesalahan saat menghapus karya');
            }
        }

        function closeEditArtworkModal() {
            const modal = document.getElementById('editArtworkModal');
            const content = document.getElementById('editArtworkModalContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                // Reshow popup if needed, but usually we just stay closed or reload
                if(currentArtworkId) {
                     document.getElementById('artworkPopup').classList.remove('hidden');
                }
            }, 300);
        }

        // Image Preview for Edit
        // Image Preview for Edit
        const editImageInput = document.getElementById('editMainImageInput');
        if (editImageInput) {
            editImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => document.getElementById('editMainImagePreview').src = e.target.result;
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // =======================
        // Artwork Popup
        // =======================
        async function openArtworkPopup(artworkId) {
            console.log('[DEBUG] openArtworkPopup called for:', artworkId);
            currentArtworkId = artworkId;
            
            try {
                console.log('[DEBUG] Fetching artwork data...');
                const response = await fetch(`/artworks/${artworkId}/view`);
                if (!response.ok) throw new Error('Gagal memuat detail karya');

                const artwork = await response.json();
                console.log('[DEBUG] Data fetched:', artwork);
                
                // Populate Content
                document.getElementById('popupTitle').textContent = artwork.title;
                document.getElementById('popupDescription').textContent = artwork.description || 'Tidak ada deskripsi';
                document.getElementById('popupMainImage').src = artwork.main_image || 'https://via.placeholder.com/600x400';
                
                const commentsEl = document.getElementById('popupComments');
                if (commentsEl) commentsEl.textContent = artwork.comments_count || 0;
                
                const viewsEl = document.getElementById('popupViews');
                if (viewsEl) viewsEl.textContent = artwork.views || 0; 
                
                const artistEl = document.getElementById('popupArtist');
                if (artistEl) artistEl.textContent = artwork.user.name;

                const avatarEl = document.getElementById('popupArtistAvatar');
                if (avatarEl) avatarEl.src = artwork.user.avatar || 'https://i.pravatar.cc/100';

                const timeEl = document.getElementById('popupTime');
                if (timeEl) timeEl.textContent = new Date(artwork.created_at).toLocaleDateString('id-ID');

                // Bookmark
                console.log('[DEBUG] Setting bookmark state...');
                const bookmarkBtn = document.getElementById('popupBookmarkBtn');
                if(!bookmarkBtn) console.error('[DEBUG] Bookmark button not found!');

                const bookmarkIcon = bookmarkBtn.querySelector('i');
                const isBookmarked = bookmarks.includes(Number(artworkId)) || artwork.is_bookmarked;
                
                if (isBookmarked) {
                    bookmarkIcon.className = 'fas fa-bookmark';
                    bookmarkBtn.classList.add('bookmarked');
                } else {
                    bookmarkIcon.className = 'far fa-bookmark';
                    bookmarkBtn.classList.remove('bookmarked');
                }

                // Edit Button
                console.log('[DEBUG] Setting edit button...');
                const editBtn = document.getElementById('popupEditBtn');
                if(!editBtn) console.error('[DEBUG] Edit button not found!');

                if (window.currentUserId && String(window.currentUserId) === String(artwork.user.id)) {
                    console.log('[DEBUG] User is owner, showing edit button');
                    editBtn.classList.remove('hidden');
                } else {
                    editBtn.classList.add('hidden');
                }

                // Comment Form ID
                const commentIdInput = document.getElementById('commentArtworkId');
                if (commentIdInput) commentIdInput.value = artworkId;

                // Tags
                console.log('[DEBUG] Rendering tags...');
                const tagsContainer = document.getElementById('popupTags');
                if(tagsContainer) {
                    tagsContainer.innerHTML = '';
                    if (artwork.tags && artwork.tags.length > 0) {
                        document.getElementById('popupTagsContainer').style.display = 'block';
                        artwork.tags.forEach(tag => {
                            const span = document.createElement('span');
                            span.className = 'bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm mr-2 mb-2 inline-block';
                            span.textContent = `#${tag.name || tag}`;
                            tagsContainer.appendChild(span);
                        });
                    } else {
                        document.getElementById('popupTagsContainer').style.display = 'none';
                    }
                }

                // Thumbnails
                console.log('[DEBUG] Rendering thumbnails...');
                const thumbnailGrid = document.getElementById('thumbnailGrid');
                if(thumbnailGrid) {
                    thumbnailGrid.innerHTML = '';
                    createThumbnail(artwork.main_image, 0, true);
                    
                    if (artwork.additional_images) {
                         const images = Array.isArray(artwork.additional_images) ? artwork.additional_images : JSON.parse(artwork.additional_images);
                         images.forEach((img, idx) => {
                             createThumbnail(img.url || img, idx + 1, false);
                         });
                    }
                }

                // Show Popup
                console.log('[DEBUG] Showing popup element...');
                const popupEl = document.getElementById('artworkPopup');
                if(!popupEl) console.error('[DEBUG] Popup element not found!');
                
                popupEl.classList.remove('hidden');
                // Force check if class 'artwork-popup' has proper styling or if we need utility classes
                // We'll rely on the classList removal of 'hidden' for now.
                
                popupEl.classList.add('active');
                document.body.style.overflow = 'hidden';

                loadComments(artworkId);
                console.log('[DEBUG] Popup opened successfully');

            } catch (error) {
                console.error('[DEBUG] Error in openArtworkPopup:', error);
                alert('Error debugging: ' + error.message);
            }
        }



        function createThumbnail(src, index, isActive) {
            const grid = document.getElementById('thumbnailGrid');
            const img = document.createElement('img');
            img.src = src;
            img.className = `w-20 h-20 object-cover rounded-lg cursor-pointer border-2 ${isActive ? 'border-red-500' : 'border-transparent'}`;
            img.onclick = function() {
                changeMainImage(src);
                Array.from(grid.children).forEach(c => c.classList.replace('border-red-500', 'border-transparent'));
                img.classList.replace('border-transparent', 'border-red-500');
            };
            grid.appendChild(img);
        }

        function changeMainImage(src) {
            const img = document.getElementById('popupMainImage');
            img.style.opacity = '0.5';
            img.src = src;
            img.onload = () => img.style.opacity = '1';
        }

        function closeArtworkPopup() {
            document.getElementById('artworkPopup').classList.add('hidden');
            document.getElementById('artworkPopup').classList.remove('active');
            document.body.style.overflow = 'auto';
            currentArtworkId = null;
        }

        // =======================
        // Comment Functions
        // =======================
        async function loadComments(artworkId) {
            const container = document.getElementById('commentsContainer');
            container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i></div>';
            
            try {
                const res = await fetch(`/comments/${artworkId}`);
                if (res.ok) {
                    const comments = await res.json();
                    renderComments(comments);
                } else {
                    container.innerHTML = '<p class="text-center text-red-500">Gagal memuat komentar</p>';
                }
            } catch (e) {
                console.error(e);
            }
        }

        function renderComments(comments) {
            const container = document.getElementById('commentsContainer');
            if (!comments || comments.length === 0) {
                container.innerHTML = `<div class="text-center py-8 text-gray-500"><p>Belum ada komentar</p></div>`;
                return;
            }
            
            container.innerHTML = comments.map(c => {
                const isOwner = window.currentUserId == c.user_id;
                const avatar = c.user.avatar || 'https://i.pravatar.cc/100';
                
                return `
                <div class="border-b border-gray-100 py-3" id="comment-${c.id}">
                     <div class="flex gap-3">
                        <img src="${avatar}" class="w-8 h-8 rounded-full object-cover">
                        <div class="flex-grow">
                            <div class="flex justify-between">
                                <div>
                                    <span class="font-semibold text-sm">${c.user.name}</span>
                                    <span class="text-xs text-gray-500 ml-2">${new Date(c.created_at).toLocaleDateString()}</span>
                                </div>
                                ${isOwner ? `<button onclick="deleteComment(event, ${c.id})" class="text-red-400 hover:text-red-600 text-xs"><i class="fas fa-trash"></i></button>` : ''}
                            </div>
                            <p class="text-gray-700 text-sm mt-1">${escapeHtml(c.content)}</p>
                        </div>
                     </div>
                </div>`;
            }).join('');
        }

        async function addComment(event) {
            event.preventDefault();
            if(!checkAuth()) { openAuthCheckModal(); return; }
            
            const form = event.target;
            const input = document.getElementById('commentInput');
            const content = input.value.trim();
            const artworkId = document.getElementById('commentArtworkId').value;

            if(!content) return;

            try {
                const res = await fetch('/comments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ artwork_id: artworkId, content: content })
                });

                if(res.ok) {
                    input.value = '';
                    loadComments(artworkId);
                    const countEl = document.getElementById('popupComments');
                    if(countEl) countEl.textContent = parseInt(countEl.textContent || 0) + 1;
                } else {
                    alert('Gagal mengirim komentar');
                }
            } catch(e) {
                console.error(e);
                alert('Error');
            }
        }

        async function deleteComment(event, id) {
             if(!confirm('Hapus komentar?')) return;
             try {
                 const res = await fetch(`/comments/${id}`, {
                     method: 'DELETE',
                     headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                 });
                 if(res.ok) {
                     document.getElementById(`comment-${id}`).remove();
                     const countEl = document.getElementById('popupComments');
                     if(countEl) countEl.textContent = Math.max(0, parseInt(countEl.textContent || 0) - 1);
                 }
             } catch(e) { console.error(e); }
        }

        function escapeHtml(text) {
             const div = document.createElement('div');
             div.textContent = text;
             return div.innerHTML;
        }

        function openCommentsModal(event, artworkId) {
            if(event) event.stopPropagation();
            openArtworkPopup(artworkId);
        }

        // =======================
        // Other Modals
        // =======================
        function openEditProfileModal() {
            const m = document.getElementById('editProfileModal');
            const c = document.getElementById('editProfileModalContent');
            m.classList.remove('hidden');
            setTimeout(() => { c.classList.remove('scale-95', 'opacity-0'); c.classList.add('scale-100', 'opacity-100'); }, 10);
        }
        function closeEditProfileModal() {
            const m = document.getElementById('editProfileModal');
            const c = document.getElementById('editProfileModalContent');
            c.classList.remove('scale-100', 'opacity-100'); c.classList.add('scale-95', 'opacity-0');
            setTimeout(() => m.classList.add('hidden'), 300);
        }

        function openUploadModal() {
            const m = document.getElementById('uploadModal');
            const c = document.getElementById('uploadModalContent');
            m.classList.remove('hidden');
            setTimeout(() => { c.classList.remove('scale-95', 'opacity-0'); c.classList.add('scale-100', 'opacity-100'); }, 10);
        }
        function closeUploadModal() {
            const m = document.getElementById('uploadModal');
            const c = document.getElementById('uploadModalContent');
            c.classList.remove('scale-100', 'opacity-100'); c.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                m.classList.add('hidden');
                document.querySelector('#uploadModal form').reset();
                document.getElementById('mainImagePreview').classList.add('hidden');
                document.getElementById('mainImagePlaceholder').classList.remove('hidden');
                document.getElementById('additionalImagesPreview').innerHTML = '';
            }, 300);
        }

        function openReportModal(event, artworkId) {
             if(event) event.stopPropagation();
             if(!checkAuth()) { openAuthCheckModal(); return; }
             const m = document.getElementById('reportModal');
             m.classList.remove('hidden');
             // Set artwork ID if form exists
             const inp = document.getElementById('reportArtworkId');
             if(inp) inp.value = artworkId;
        }
        function closeReportModal() {
             document.getElementById('reportModal').classList.add('hidden');
        }

        function toggleProfileMenu() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        }
    </script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script>
        // Efek transparan buram pada navbar saat di-scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('mainHeader');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>