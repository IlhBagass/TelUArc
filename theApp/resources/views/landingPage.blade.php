<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>telUArc</title>
    @auth
        <meta name="user-name" content="{{ Auth::user()->name }}">
        <meta name="user-avatar" content="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://i.pravatar.cc/100' }}">
    @else
        <meta name="user-name" content="Anonymous">
        <meta name="user-avatar" content="https://i.pravatar.cc/100">
    @endauth
    <script>
        window.currentUserId = "{{ Auth::id() }}";
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/landingPage.css') }}">
    <style>
        :root {
            --primary-red: #e53e3e;
            --primary-red-dark: #c53030;
            --primary-red-light: #feb2b2;
        }

        body {
            background-color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Header Styles */
        .header {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .header.scrolled {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .red-text {
            color: var(--primary-red);
            font-weight: 800;
        }

        .red-bg {
            background-color: var(--primary-red);
            transition: all 0.3s ease;
        }

        .red-bg:hover {
            background-color: var(--primary-red-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(229, 62, 62, 0.3);
        }

        /* Gallery Styles */
        .masonry-grid {
            column-count: 4;
            column-gap: 1.5rem;
        }

        @media (max-width: 1024px) {
            .masonry-grid {
                column-count: 3;
            }
        }

        @media (max-width: 768px) {
            .masonry-grid {
                column-count: 2;
            }
        }

        @media (max-width: 480px) {
            .masonry-grid {
                column-count: 1;
            }
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .artwork-card {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background: white;
            border: 1px solid #f0f0f0;
        }

        .artwork-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .artwork-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 60%);
            display: flex;
            align-items: flex-end;
            padding: 15px;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .artwork-card:hover .artwork-overlay {
            opacity: 1;
        }

        .artwork-actions {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .action-button {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .action-button:hover {
            background: white;
            transform: scale(1.1);
            color: var(--primary-red);
        }

        .bookmarked i {
            color: var(--primary-red);
        }

        /* Artwork Popup Styles */
        .artwork-popup {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .artwork-popup.active {
            opacity: 1;
            visibility: visible;
        }

        .popup-content {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            max-width: 90vw;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: all 0.3s ease;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .artwork-popup.active .popup-content {
            transform: scale(1);
        }

        /* Image Container Styles */
        .image-gallery {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .main-image-container {
            position: relative;
            width: 100%;
            max-height: 500px;
            overflow: hidden;
            border-radius: 16px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-height: 500px;
            transition: all 0.3s ease;
        }

        /* Thumbnail Grid */
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
            max-height: 120px;
            overflow-y: auto;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .thumbnail {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .thumbnail.active {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.2);
        }

        /* Comments Section Styles */
        .comments-section {
            margin-top: 2rem;
            border-top: 1px solid #e5e7eb;
            padding-top: 1.5rem;
        }

        .comment-form {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }

        .comment-user {
            margin-bottom: 0.5rem;
        }

        .comment-input {
            border: 2px solid #e5e7eb;
            border-radius: 50px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .comment-input:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        .comment-submit {
            background-color: var(--primary-red);
            color: white;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-left: 10px;
        }

        .comment-submit:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        }

        .comments-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .comments-container::-webkit-scrollbar {
            width: 6px;
        }

        .comments-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .comments-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .comment-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .comment-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .comment-meta {
            flex-grow: 1;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .comment-time {
            font-size: 0.75rem;
            color: #888;
        }

        .comment-body {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 12px 16px;
            margin-left: 52px;
            position: relative;
        }

        .comment-body:before {
            content: '';
            position: absolute;
            top: 10px;
            left: -8px;
            width: 16px;
            height: 16px;
            background-color: #f8f9fa;
            transform: rotate(45deg);
            border-radius: 4px;
        }

        .comment-text {
            color: #444;
            line-height: 1.5;
            margin: 0;
        }

        .no-comments-message {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            background: #f9fafb;
            border-radius: 12px;
        }

        /* Responsive Image Container */
        @media (max-width: 768px) {
            .main-image-container {
                max-height: 350px;
            }

            .main-image {
                max-height: 350px;
            }

            .thumbnail-grid {
                grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            }

            .thumbnail {
                height: 60px;
            }
        }

        /* Modal Styles */
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: translateY(30px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal-active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--primary-red);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-red);
            border: 2px solid var(--primary-red);
            padding: 10px 22px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background-color: var(--primary-red);
            color: white;
        }

        .btn-outline-white {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 10px 22px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-white:hover {
            background-color: white;
            color: var(--primary-red);
        }

        /* Input Styles */
        .form-input {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .form-input:focus {
            border-color: var(--primary-red);
            background: white;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        /* Search Bar Styles */
        .search-bar {
            position: relative;
            flex-grow: 1;
            max-width: 600px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 20px 10px 45px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #333;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-bar input::placeholder {
            color: #a0aec0;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--primary-red);
            background: white;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        /* Tag Styles */
        .tag {
            background-color: #fef2f2;
            color: var(--primary-red);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 1px solid #fecaca;
        }

        .tag:hover {
            background-color: #fee2e2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(254, 202, 202, 0.4);
        }

        /* Profile Menu */
        .profile-menu {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform-origin: top right;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .profile-menu a {
            padding: 12px 16px;
            transition: all 0.2s ease;
        }

        .profile-menu a:hover {
            background: #f8fafc;
            color: var(--primary-red);
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-red);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(229, 62, 62, 0.4);
            z-index: 50;
            transition: all 0.3s ease;
        }

        .fab:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 15px 35px rgba(229, 62, 62, 0.5);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 20px;
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: var(--primary-red);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 1s ease forwards;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .popup-content::-webkit-scrollbar {
            width: 8px;
        }

        .popup-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .popup-content::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .popup-content::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .thumbnail-grid::-webkit-scrollbar {
            height: 6px;
        }

        .thumbnail-grid::-webkit-scrollbar-track {
            background: #e2e8f0;
            border-radius: 3px;
        }

        .thumbnail-grid::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        /* Video Background Styles */
        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        #bgVideo {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            z-index: 0;
        }

        /* Responsive adjustments untuk video */
        @media (max-width: 768px) {
            .video-container {
                height: 70vh;
            }

            #bgVideo {
                width: 100vw;
                height: 56.25vw;
                /* 16:9 Aspect Ratio */
                min-height: 100vh;
                min-width: 177.77vh;
                /* 16:9 Aspect Ratio */
            }
        }
    </style>
</head>
<body class="bg-white">
    <!-- Header -->

    <!-- Header -->
    <header class="header text-gray-800 sticky top-0 z-40" id="mainHeader">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('artworks.index') }}">
                        <img src="{{ asset('logo-telu-arc.png') }}" alt="TelUArc Logo" class="h-12 w-auto object-contain">
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
                    <!-- Theme Toggle -->
                    <button id="themeToggle" onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                        <i class="fas fa-moon text-gray-600"></i>
                    </button>

                    <button onclick="openUploadModal()" class="red-bg text-white px-4 py-2 rounded-full text-sm font-semibold transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i> Upload Karya
                    </button>
                    
                    <div class="relative">
                        @auth
                            <button onclick="toggleProfileMenu()" class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-gray-200 hover:ring-red-400 transition-all">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Profile">
                                @else
                                    <img src="https://i.pravatar.cc/100" alt="Profile">
                                @endif
                            </button>

                            <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 profile-menu py-2">
                                <a href="{{ route('users.show', auth()->user()->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 text-black">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>

                                <a href="#" onclick="openReportModal()" class="block px-4 py-2 text-sm hover:bg-gray-50 text-black">
                                    <i class="fas fa-flag mr-2"></i> Report
                                </a>

                                <hr class="my-1">

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block px-4 py-2 text-sm hover:bg-gray-50 text-black">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        @endauth

                        @guest
                            <form action="{{ route('auth.form') }}" method="GET">
                                @csrf
                                <button class="red-bg text-white px-4 py-2 rounded-full text-sm font-semibold transition-colors" type="submit">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                                </button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- Hero Section dengan Video Background -->
    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        <!-- Video Background -->
        <video 
            autoplay 
            muted 
            loop 
            playsinline 
            preload="metadata"
            class="absolute top-0 left-0 w-full h-full object-cover z-0"
            id="bgVideo">
            <source src="/videos/background.mp4" type="video/mp4">
            <!-- Fallback untuk browser yang tidak support video -->
            <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-purple-600"></div>
        </video>
        
        <!-- Overlay untuk memperjelas teks -->
        <div class="absolute inset-0 bg-black bg-opacity-40 z-10"></div>
        
        <!-- Konten Jumbotron -->
        <div class="relative z-20 text-center text-white px-4 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 fade-in">
                Jelajahi Karya Seni <span class="text-yellow-300">Digital</span>
            </h1>
            <p class="text-xl md:text-2xl mb-10 max-w-2xl mx-auto fade-in">
                Temukan dan bagikan karya seni digital dari seniman berbakat di seluruh dunia
            </p>
            
            <div class="flex flex-wrap justify-center gap-4 fade-in">
                <a href="{{ route('auth.form') }}" class="btn-outline-white px-6 py-3 rounded-full font-medium transition-all flex items-center">
                    <i class="fas fa-compass mr-2"></i> Gabung Sekarang
                </a>
                <button onclick="document.getElementById('gallerySection').scrollIntoView({behavior: 'smooth'})" class="btn-primary px-6 py-3 rounded-full font-medium shadow-lg transition-all flex items-center">
                    <i class="fas fa-compass mr-2"></i> Jelajahi Karya
                </button>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
            <button onclick="document.getElementById('gallerySection').scrollIntoView({behavior: 'smooth'})" class="text-white">
                <i class="fas fa-chevron-down text-2xl"></i>
            </button>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallerySection" class="section-content py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Karya Terbaru</h2>
                <div class="flex space-x-2">
                    <button class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center hover:bg-gray-50">
                        <i class="fas fa-filter text-gray-600"></i>
                    </button>
                    <button class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center hover:bg-gray-50">
                        <i class="fas fa-th text-gray-600"></i>
                    </button>
                </div>
            </div>
            
            <div class="masonry-grid">
                @foreach($artworks as $artwork)
                    <div class="masonry-item fade-in">
                        <div class="artwork-card"
                            data-artwork-id="{{ $artwork->id }}"
                            onclick="openArtworkPopup('{{ $artwork->id }}')">
                            <div class="relative">
                                <img src="{{ asset('storage/'.$artwork->main_image) }}" 
                                    alt="{{ $artwork->title }}" class="w-full">
                                <div class="artwork-overlay">
                                    <div class="artwork-actions relative">
                                        <button class="action-button" onclick="toggleBookmark(event, '{{ $artwork->id }}')" data-artwork-id="{{ $artwork->id }}">
                                            <i class="{{ in_array($artwork->id, $bookmarkedArtworks ?? []) ? 'fas' : 'far' }} fa-bookmark"></i>
                                        </button>
                                        <button class="action-button ml-2" onclick="openReportModal(event, '{{ $artwork->id }}')">
                                            <i class="fas fa-flag text-red-500"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg mb-1">{{ $artwork->title }}</h3>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <img src="{{ asset('storage/'.$artwork->user->avatar) }}" alt="Artist" class="w-6 h-6 rounded-full mr-2">
                                        <span>{{ $artwork->user->name ?? 'Anonim' }}</span>
                                    </div>
                                    <span>{{ $artwork->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <button class="bg-white px-6 py-3 rounded-full text-gray-700 font-medium shadow-lg hover:shadow-xl transition-all">
                    Muat Lebih Banyak <i class="fas fa-arrow-down ml-2"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Bookmarks Section -->
    <section id="bookmarksSection" class="section-content py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between mb-12">
                <h2 class="text-3xl font-bold text-gray-800">
                    <i class="far fa-bookmark mr-3 text-red-500"></i> Bookmark Saya
                </h2>
                <button onclick="clearAllBookmarks()" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center">
                    <i class="fas fa-trash mr-2"></i> Hapus Semua
                </button>
            </div>

            <div id="bookmarksContainer" class="masonry-grid">
                <!-- Bookmarked items will be dynamically inserted here -->
            </div>

            <!-- Empty State -->
            <div id="emptyBookmarks" class="empty-state">
                <i class="far fa-bookmark text-5xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-medium text-gray-600 mb-3">Belum ada bookmark</h3>
                <p class="text-gray-500 max-w-md mx-auto">Klik ikon bookmark pada karya untuk menyimpannya di sini</p>
                <button class="mt-6 btn-primary">
                    Jelajahi Karya
                </button>
            </div>
        </div>
    </section>

    <!-- Floating Action Button (FAB) - Tombol plus untuk menambah karya -->
    <button class="fab" onclick="openUploadModal()">
        <i class="fas fa-plus text-xl"></i>
    </button>

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
                
                <!-- Comments Section -->
                <div class="comments-section">
                    <h3 class="text-xl font-semibold mb-4 flex items-center">
                        <i class="fas fa-comments mr-3 text-red-500"></i> Komentar
                    </h3>
                    
                    <!-- Comment Form -->
                    <form class="comment-form" action="{{ route('comments.store') }}" method="POST" onsubmit="addComment(event)">
                        @csrf

                        <div class="comment-user flex items-center space-x-2">
                            @auth
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://i.pravatar.cc/100' }}" 
                                    alt="Profile" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <img src="https://i.pravatar.cc/100" 
                                    alt="Profile" class="w-10 h-10 rounded-full object-cover">
                            @endauth
                        </div>

                        @auth
                            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                        @endauth
                        <input type="hidden" name="artwork_id" id="commentArtworkId" value="">

                        <div class="flex items-center space-x-2 mt-2">
                            <input 
                                type="text" 
                                name="content" 
                                id="commentInput"
                                class="comment-input flex-grow border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200" 
                                placeholder="Tulis komentar..." 
                                required>
                            
                            <button type="submit" class="comment-submit bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>

                    
                    <!-- Comments Container -->
                    <div id="commentsContainer" class="comments-container">
                        @if(isset($artwork) && $artwork->comments->count() > 0)
                            @foreach($artwork->comments as $comment)
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://i.pravatar.cc/100' }}" 
                                             alt="{{ $comment->user->name }}" class="comment-avatar">
                                        <div class="comment-meta">
                                            <div class="comment-author">{{ $comment->user->name }}</div>
                                            <div class="comment-time">{{ $comment->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div class="comment-body">
                                        <p class="comment-text">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div id="noCommentsMessage" class="no-comments-message">
                                <i class="far fa-comment text-3xl text-gray-300 mb-3"></i>
                                <p>Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <div class="flex space-x-4">
                        <button id="popupBookmarkBtn" onclick="toggleBookmark(event, currentArtworkId)" class="flex items-center space-x-2 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                            <i class="far fa-bookmark text-gray-600"></i>
                            <span class="text-sm font-medium">Bookmark</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-6 text-gray-600">
                        <div class="flex items-center space-x-2">
                            <i class="far fa-comment text-xl"></i>
                            <span id="popupComments" class="font-medium">{{ isset($artwork) ? $artwork->comments->count() : '0' }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="far fa-eye text-xl"></i>
                            <span id="popupViews" class="font-medium">0</span>
                        </div>
                    </div>
                    </div>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border-0 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col modal-content" id="uploadModalContent">
            <div class="bg-red-600 p-6 text-white flex-shrink-0">
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
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer @error('main_image') border-red-500 bg-red-50 @enderror" id="mainImageContainer">
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
                    @error('main_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title Input -->
                <div class="mb-5">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Karya</label>
                    <div class="relative">
                        <input type="text" name="title" id="title" class="w-full form-input @error('title') border-red-500 @enderror" placeholder="Berikan judul untuk karya Anda" value="{{ old('title') }}" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-heading text-gray-400"></i>
                        </div>
                    </div>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description Textarea -->
                <div class="mb-5">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <div class="relative">
                        <textarea name="description" id="description" class="w-full form-input" rows="4" placeholder="Ceritakan tentang karya Anda...">{{ old('description') }}</textarea>
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
                    @error('additional_images.*')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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




    <!-- Report Modal -->
    <div id="reportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border-0 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col modal-content" id="reportModalContent">
            <div class="bg-red-600 p-6 text-white flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i> Laporkan Karya
                    </h2>
                    <button onclick="closeReportModal()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="reportForm" onsubmit="handleReport(event)" class="p-6">
                <input type="hidden" id="reportArtworkId" name="artwork_id">
                
                <p class="text-gray-600 mb-6 text-sm">
                    Apakah Anda yakin ingin melaporkan karya ini? Tim kami akan meninjau laporan Anda.
                </p>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Pelaporan</label>
                    <div class="relative">
                        <textarea id="reportReason" name="reason" class="w-full form-input" rows="4" placeholder="Jelaskan mengapa karya ini melanggar aturan..." required></textarea>
                        <div class="absolute top-3 right-3 pointer-events-none">
                            <i class="fas fa-pen text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeReportModal()" class="px-5 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl transition-colors font-medium">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-lg hover:shadow-red-500/30 transition-all duration-300 font-medium flex items-center">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Logo dan Deskripsi -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('logo-telu-arc.png') }}" alt="TelUArc Logo" class="h-12 w-auto object-contain">
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">
                        Platform untuk menjelajahi dan berbagi karya seni digital dari seniman berbakat di seluruh dunia.
                    </p>
                    <div class="flex space-x-4">

                    </div>
                </div>
                
                <!-- Link Cepat -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Link Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Jelajahi Karya</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Seniman Populer</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Kategori</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tentang Kami</a></li>
                    </ul>
                </div>
                
                <!-- Bantuan -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Ketentuan Layanan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Hubungi Kami</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Karir</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="pt-8 border-t border-gray-800 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} telUArc. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/landingPage.js') }}"></script>
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

        // Check if there are validation errors
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                openUploadModal();
                // Optional: show toast/alert
                // alert('Gagal upload. Mohon periksa kembali inputan Anda.');
            });
        @endif
    </script>
</body>
</html>