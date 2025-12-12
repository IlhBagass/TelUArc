<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $user->username }} - telUArc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/landingPage.css') }}">
    <style>
        /* Custom CSS untuk desain yang lebih menarik */
        :root {
            --primary-red: #e53e3e;
            --primary-red-dark: #c53030;
            --primary-red-light: #feb2b2;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Header Styles */
        .header-gradient {
            background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
            box-shadow: 0 4px 20px rgba(229, 62, 62, 0.3);
        }
        
        .red-text {
            background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
        
        .red-bg {
            background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
            transition: all 0.3s ease;
        }
        
        .red-bg:hover {
            background: linear-gradient(90deg, var(--primary-red-dark) 0%, #9b1c1c 100%);
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
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 60%);
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
            background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
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
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .search-bar input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .search-bar input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }
        
        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Tag Styles */
        .tag {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #dc2626;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: 1px solid #fecaca;
        }
        
        .tag:hover {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
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
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease forwards;
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
            background: #a0aec0';
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
        
        /* Profile Header Styles */
        .profile-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);
            border-bottom: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }
        
        .profile-hero::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
            border-radius: 50%;
            opacity: 0.6;
        }
        
        .profile-hero-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
            position: relative;
            z-index: 1;
        }
        
        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.5rem;
        }
        
        .profile-avatar-large {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        
        .profile-avatar-large:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .profile-details h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .profile-username {
            font-size: 1.25rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .profile-bio {
            font-size: 1rem;
            color: #4b5563;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 2rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-red);
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .edit-profile-btn {
            background: white;
            border: 2px solid #e5e7eb;
            color: #374151;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .edit-profile-btn:hover {
            border-color: var(--primary-red);
            color: var(--primary-red);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .share-profile-btn {
            background: var(--primary-red);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }
        
        .share-profile-btn:hover {
            background: var(--primary-red-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(229, 62, 62, 0.4);
        }
        
        /* Navigation Tabs */
        .profile-nav {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 80px;
            z-index: 40;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .nav-tabs {
            display: flex;
            gap: 0;
        }
        
        .nav-tab {
            padding: 1.25rem 2rem;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .nav-tab:hover {
            color: var(--primary-red);
            background: rgba(229, 62, 62, 0.05);
        }
        
        .nav-tab.active {
            color: var(--primary-red);
        }
        
        .nav-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-red);
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        
        .tab-icon {
            font-size: 1.125rem;
        }
        
        /* Content Area */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        @media (max-width: 1024px) {
            .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); }
        }
        
        @media (max-width: 768px) {
            .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        }
        
        @media (max-width: 480px) {
            .gallery-grid { grid-template-columns: 1fr; }
        }
        
        /* Artwork Card */
        .artwork-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .artwork-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }
        
        .artwork-image-container {
            position: relative;
            padding-top: 100%;
            overflow: hidden;
            background: #f3f4f6;
        }
        
        .artwork-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .artwork-card:hover .artwork-image {
            transform: scale(1.05);
        }
        
        .artwork-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);
            display: flex;
            align-items: flex-end;
            padding: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .artwork-card:hover .artwork-overlay {
            opacity: 1;
        }
        
        .artwork-actions {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        
        .action-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .action-btn:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        
        .action-btn i {
            font-size: 1.125rem;
            color: #374151;
            transition: color 0.2s ease;
        }
        
        .action-btn:hover i {
            color: var(--primary-red);
        }
        
        .action-btn.bookmarked i {
            color: var(--primary-red);
        }
        
        .artwork-info {
            padding: 1rem;
        }
        
        .artwork-title {
            font-weight: 700;
            font-size: 1rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }
        
        .artwork-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .artist-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .artist-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid #e5e7eb;
        }
        
        .artwork-time {
            font-size: 0.75rem;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1.5rem;
        }
        
        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .empty-description {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        
        .empty-action {
            background: var(--primary-red);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }
        
        .empty-action:hover {
            background: var(--primary-red-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(229, 62, 62, 0.4);
        }
        
        /* Upload Area */
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            background: #f9fafb;
            transition: all 0.2s ease;
            cursor: pointer;
            margin-bottom: 1rem;
        }
        
        .upload-area:hover {
            border-color: var(--primary-red);
            background: #fef2f2;
        }
        
        .upload-area.dragover {
            border-color: var(--primary-red);
            background: #fef2f2;
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #9ca3af;
            margin-bottom: 1rem;
        }
        
        .upload-text {
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .upload-subtext {
            font-size: 0.875rem;
            color: #9ca3af;
        }
        
        /* Image Preview */
        .image-preview {
            margin-top: 1rem;
            display: none;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Error Message */
        .error-message {
            background: #fef2f2;
            color: #b91c1c;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                padding: 1rem;
            }
            
            .search-container {
                margin: 0 1rem;
            }
            
            .upload-btn {
                padding: 0.5rem 1rem;
                font-size: 0.75rem;
            }
            
            .profile-hero-content {
                padding: 2rem 1rem;
            }
            
            .profile-avatar-large {
                width: 120px;
                height: 120px;
            }
            
            .profile-details h1 {
                font-size: 2rem;
            }
            
            .profile-stats {
                gap: 2rem;
            }
            
            .nav-tabs {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .nav-tab {
                white-space: nowrap;
            }
            
            .content {
                padding: 1rem;
            }
            
            .gallery-grid {
                gap: 1rem;
            }
            
            .popup-container {
                margin: 1rem;
            }
            
            .popup-content {
                padding: 1.5rem;
            }
            
            .main-image-container {
                max-height: 350px;
            }
            
            .thumbnail {
                height: 60px;
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        .thumbnail-grid::-webkit-scrollbar {
            height: 6px;
        }
        
        .thumbnail-grid::-webkit-scrollbar-track {
            background: #e5e7eb;
            border-radius: 3px;
        }
        
        .thumbnail-grid::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header - Same as Landing Page -->
    <header class="header-gradient text-white sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-palette text-red-600 text-xl"></i>
                    </div>
                    <a href="{{ route('artworks.index') }}" class="text-2xl font-bold">telUArc</a>
                </div>
                
                <!-- Search Bar -->
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari karya, seniman, atau tag...">
                </div>
                
                <div class="flex items-center space-x-4">
                    <button onclick="openUploadModal()" class="bg-white text-red-600 px-4 py-2 rounded-full text-sm font-semibold hover:bg-red-50 transition-colors flex items-center">
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
                        
                        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-48 profile-menu py-2">
                            @auth
                                <a href="{{ route('users.show', auth()->user()->id) }}" class="block px-4 py-2 text-sm hover:bg-gray-50 text-black">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>
                            @endauth
                            <a href="#" onclick="openReportModal()" class="block px-4 py-2 text-sm hover:bg-gray-50 text-black">
                                <i class="fas fa-flag mr-2"></i> Report
                            </a>
                            <hr class="my-1">
                            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50 text-black">
                                <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                            </a>
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
                
                <h1>{{ $user->name }}</h1>
                <p class="profile-username">{{ $user->username }}</p>
                <p class="profile-bio">{{ $user->bio ?: 'Tidak ada bio' }}</p>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $artworks->count() }}</div>
                        <div class="stat-label">Karya</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $user->bookmarks->count() }}</div>
                        <div class="stat-label">Bookmark</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $user->followers_count ?? 0 }}</div>
                        <div class="stat-label">Pengikut</div>
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
            <div class="gallery-grid">
                @foreach($artworks as $artwork)
                    <div class="artwork-card" data-artwork-id="{{ $artwork->id }}" onclick="openArtworkPopup('{{ $artwork->id }}')">
                        <div class="artwork-image-container">
                            @if($artwork->main_image)
                                <img src="{{ asset('storage/' . $artwork->main_image) }}" alt="{{ $artwork->title }}" class="artwork-image">
                            @else
                                <img src="https://via.placeholder.com/400x400" alt="{{ $artwork->title }}" class="artwork-image">
                            @endif
                            <div class="artwork-overlay">
                                <div class="artwork-actions">
                                    <button class="action-btn {{ $user->bookmarks->contains($artwork->id) ? 'bookmarked' : '' }}" onclick="toggleBookmark(event, '{{ $artwork->id }}')">
                                        <i class="{{ $user->bookmarks->contains($artwork->id) ? 'fas' : 'far' }} fa-bookmark"></i>
                                    </button>
                                    <button class="action-btn" onclick="openCommentsModal(event, '{{ $artwork->id }}')">
                                        <i class="far fa-comment"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="artwork-info">
                            <h3 class="artwork-title">{{ $artwork->title }}</h3>
                            <div class="artwork-meta">
                                <div class="artist-info">
                                    <img src="https://i.pravatar.cc/100" alt="{{ $artwork->user->name }}" class="artist-avatar">
                                    <span>{{ $artwork->user->name }}</span>
                                </div>
                                <span class="artwork-time">{{ $artwork->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Bookmarks Tab -->
        <div id="bookmarks-tab" class="tab-content">
            <div class="gallery-grid">
                @foreach($user->bookmarks as $artwork)
                    <div class="artwork-card" data-artwork-id="{{ $artwork->id }}" onclick="openArtworkPopup('{{ $artwork->id }}')">
                        <div class="artwork-image-container">
                            @if($artwork->main_image)
                                <img src="{{ asset('storage/' . $artwork->main_image) }}" alt="{{ $artwork->title }}" class="artwork-image">
                            @else
                                <img src="https://via.placeholder.com/400x400" alt="{{ $artwork->title }}" class="artwork-image">
                            @endif
                            <div class="artwork-overlay">
                                <div class="artwork-actions">
                                    <button class="action-btn bookmarked" onclick="toggleBookmark(event, '{{ $artwork->id }}')">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                                    <button class="action-btn" onclick="openCommentsModal(event, '{{ $artwork->id }}')">
                                        <i class="far fa-comment"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="artwork-info">
                            <h3 class="artwork-title">{{ $artwork->title }}</h3>
                            <div class="artwork-meta">
                                <div class="artist-info">
                                    <img src="https://i.pravatar.cc/100" alt="{{ $artwork->user->name }}" class="artist-avatar">
                                    <span>{{ $artwork->user->name }}</span>
                                </div>
                                <span class="artwork-time">{{ $artwork->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Artwork Popup - Same as Landing Page -->
    <div id="artworkPopup" class="artwork-popup">
        <div class="popup-content">
            <button onclick="closeArtworkPopup()" class="absolute top-6 right-6 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg hover:bg-gray-100 z-10 transition-all">
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
                        <button id="popupBookmarkBtn" onclick="toggleBookmark(event, currentArtworkId)" class="flex items-center space-x-2 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                            <i class="far fa-bookmark text-gray-600"></i>
                            <span class="text-sm font-medium">Bookmark</span>
                        </button>
                        <button onclick="openCommentsModal(event)" class="flex items-center space-x-2 px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                            <i class="far fa-comment text-gray-600"></i>
                            <span class="text-sm font-medium">Komentar</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-6 text-gray-600">
                        <div class="flex items-center space-x-2">
                            <i class="far fa-comment text-xl"></i>
                            <span id="popupComments" class="font-medium">0</span>
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


        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="fab" onclick="openUploadModal()">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <script>
        // Global Variables
        let currentArtworkId = null;
        let currentTab = 'artworks';

        // Tab Navigation
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all nav tabs
            document.querySelectorAll('.nav-tab').forEach(navTab => {
                navTab.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(`${tabName}-tab`).classList.add('active');
            
            // Add active class to clicked nav tab
            event.target.closest('.nav-tab').classList.add('active');
            
            currentTab = tabName;
        }

        // Artwork Popup Functions
        async function openArtworkPopup(artworkId) {
            currentArtworkId = artworkId;
            
            try {
                const response = await fetch(`/artworks/${artworkId}`);
                const artwork = await response.json();
                
                // Set popup content
                document.getElementById('popupTitle').textContent = artwork.title;
                document.getElementById('popupDescription').textContent = artwork.description || 'Tidak ada deskripsi';
                document.getElementById('popupMainImage').src = artwork.main_image;
                document.getElementById('popupComments').textContent = artwork.comments_count || 0;
                document.getElementById('popupViews').textContent = artwork.views_count || 0;
                
                // Set artist info
                document.getElementById('popupArtist').textContent = artwork.user.name;
                document.getElementById('popupArtistAvatar').src = artwork.user.avatar || 'https://i.pravatar.cc/100';
                document.getElementById('popupTime').textContent = artwork.created_at;
                
                // Set bookmark button state
                const bookmarkBtn = document.getElementById('popupBookmarkBtn');
                const bookmarkIcon = bookmarkBtn.querySelector('i');
                if (artwork.is_bookmarked) {
                    bookmarkIcon.classList.remove('far');
                    bookmarkIcon.classList.add('fas');
                    bookmarkBtn.classList.add('bookmarked');
                } else {
                    bookmarkIcon.classList.remove('fas');
                    bookmarkIcon.classList.add('far');
                    bookmarkBtn.classList.remove('bookmarked');
                }
                
                // Load tags
                const tagsContainer = document.getElementById('popupTags');
                tagsContainer.innerHTML = '';
                if (artwork.tags && artwork.tags.length > 0) {
                    document.getElementById('popupTagsContainer').style.display = 'block';
                    artwork.tags.forEach(tag => {
                        const tagElement = document.createElement('span');
                        tagElement.className = 'tag';
                        tagElement.textContent = `#${tag.name}`;
                        tagsContainer.appendChild(tagElement);
                    });
                } else {
                    document.getElementById('popupTagsContainer').style.display = 'none';
                }
                
                // Load thumbnails
                const thumbnailGrid = document.getElementById('thumbnailGrid');
                thumbnailGrid.innerHTML = '';
                
                // Add main image thumbnail
                const mainThumbnail = createThumbnail(artwork.main_image, 0);
                mainThumbnail.classList.add('active');
                thumbnailGrid.appendChild(mainThumbnail);
                
                // Add additional images thumbnails
                if (artwork.additional_images && artwork.additional_images.length > 0) {
                    artwork.additional_images.forEach((image, index) => {
                        const thumbnail = createThumbnail(image.url, index + 1);
                        thumbnailGrid.appendChild(thumbnail);
                    });
                }
                
                // Show popup
                document.getElementById('artworkPopup').classList.add('active');
                document.body.style.overflow = 'hidden';
                
            } catch (error) {
                console.error('Error loading artwork:', error);
                alert('Gagal memuat detail karya');
            }
        }

        function createThumbnail(src, index) {
            const thumbnail = document.createElement('img');
            thumbnail.src = src;
            thumbnail.alt = `Thumbnail ${index}`;
            thumbnail.className = 'thumbnail';
            thumbnail.dataset.index = index;
            
            thumbnail.onclick = function() {
                changeMainImage(this.src);
                
                // Update active state
                document.querySelectorAll('.thumbnail').forEach(thumb => {
                    thumb.classList.remove('active');
                });
                this.classList.add('active');
            };
            
            return thumbnail;
        }

        function changeMainImage(src) {
            const mainImage = document.getElementById('popupMainImage');
            
            // Add fade effect
            mainImage.style.opacity = '0.5';
            
            // Change image source
            mainImage.src = src;
            
            // Restore opacity after image loads
            mainImage.onload = function() {
                mainImage.style.opacity = '1';
            };
            
            // Fallback if image fails to load
            mainImage.onerror = function() {
                mainImage.style.opacity = '1';
                mainImage.src = 'https://via.placeholder.com/600x400?text=Image+Not+Available';
            };
        }

        function closeArtworkPopup() {
            document.getElementById('artworkPopup').classList.remove('active');
            document.body.style.overflow = 'auto';
            currentArtworkId = null;
        }

        // Bookmark Functions
        async function toggleBookmark(event, artworkId) {
            event.stopPropagation();
            
            const button = event.target.closest('.action-btn');
            const icon = button.querySelector('i');
            const isBookmarked = button.classList.contains('bookmarked');
            
            try {
                if (isBookmarked) {
                    // Remove bookmark
                    const response = await fetch(`/bookmarks/${artworkId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.classList.remove('bookmarked');
                        
                        // Update popup bookmark button if open
                        if (currentArtworkId == artworkId) {
                            const popupBookmarkBtn = document.getElementById('popupBookmarkBtn');
                            const popupBookmarkIcon = popupBookmarkBtn.querySelector('i');
                            popupBookmarkIcon.classList.remove('fas');
                            popupBookmarkIcon.classList.add('far');
                            popupBookmarkBtn.classList.remove('bookmarked');
                        }
                    }
                } else {
                    // Add bookmark
                    const response = await fetch('/bookmarks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            artwork_id: artworkId
                        })
                    });
                    
                    if (response.ok) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.classList.add('bookmarked');
                        
                        // Update popup bookmark button if open
                        if (currentArtworkId == artworkId) {
                            const popupBookmarkBtn = document.getElementById('popupBookmarkBtn');
                            const popupBookmarkIcon = popupBookmarkBtn.querySelector('i');
                            popupBookmarkIcon.classList.remove('far');
                            popupBookmarkIcon.classList.add('fas');
                            popupBookmarkBtn.classList.add('bookmarked');
                        }
                    }
                }
            } catch (error) {
                console.error('Error toggling bookmark:', error);
                alert('Terjadi kesalahan saat menyimpan bookmark');
            }
        }

        // Upload Modal Functions
        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('uploadModalContent');
            
            modal.classList.remove('hidden');
            
            // Add animation
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('uploadModalContent');
            
            // Add animation
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                // Reset form
                document.querySelector('#uploadModal form').reset();
                document.getElementById('mainImagePreview').classList.add('hidden');
                document.getElementById('mainImagePlaceholder').classList.remove('hidden');
                document.getElementById('additionalImagesPreview').innerHTML = '';
            }, 300);
        }

        // Edit Profile Modal Functions
        function openEditProfileModal() {
            const modal = document.getElementById('editProfileModal');
            const modalContent = document.getElementById('editProfileModalContent');
            
            modal.classList.remove('hidden');
            
            // Add animation
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeEditProfileModal() {
            const modal = document.getElementById('editProfileModal');
            const modalContent = document.getElementById('editProfileModalContent');
            
            // Add animation
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Comments Modal Functions
        function openCommentsModal(event, artworkId) {
            if (event) event.stopPropagation();
            // Implementation for comments modal
            console.log('Open comments modal for artwork:', artworkId);
        }

        // Profile Menu Functions
        function toggleProfileMenu() {
            document.getElementById('profileMenu').classList.toggle('hidden');
        }

        // Report Modal Functions
        function openReportModal() {
            document.getElementById('reportModal').classList.remove('hidden');
            document.getElementById('profileMenu').classList.add('hidden');
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Upload area functionality
            const mainImageContainer = document.getElementById('mainImageContainer');
            const mainImageInput = document.getElementById('mainImageInput');
            const mainImagePreview = document.getElementById('mainImagePreview');
            const mainImagePlaceholder = document.getElementById('mainImagePlaceholder');
            const additionalImagesInput = document.getElementById('additionalImagesInput');
            const additionalImagesPreview = document.getElementById('additionalImagesPreview');
            
            // Click to upload main image
            mainImageContainer.addEventListener('click', function() {
                mainImageInput.click();
            });
            
            // Preview main image
            mainImageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        mainImagePreview.querySelector('img').src = e.target.result;
                        mainImagePreview.classList.remove('hidden');
                        mainImagePlaceholder.classList.add('hidden');
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Preview additional images
            additionalImagesInput.addEventListener('change', function() {
                additionalImagesPreview.innerHTML = '';
                
                if (this.files) {
                    // Limit to 5 files
                    const files = Array.from(this.files).slice(0, 5);
                    
                    files.forEach((file, index) => {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const imageContainer = document.createElement('div');
                            imageContainer.className = 'relative group';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'w-full h-20 object-cover rounded-lg';
                            
                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity';
                            removeBtn.innerHTML = '<i class="fas fa-times text-xs"></i>';
                            removeBtn.onclick = function() {
                                imageContainer.remove();
                            };
                            
                            imageContainer.appendChild(img);
                            imageContainer.appendChild(removeBtn);
                            additionalImagesPreview.appendChild(imageContainer);
                        }
                        
                        reader.readAsDataURL(file);
                    });
                }
            });
            
            // Drag and drop for main image
            mainImageContainer.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-red-500', 'bg-red-50');
            });
            
            mainImageContainer.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-red-500', 'bg-red-50');
            });
            
            mainImageContainer.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-red-500', 'bg-red-50');
                
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    mainImageInput.files = e.dataTransfer.files;
                    
                    const event = new Event('change', { bubbles: true });
                    mainImageInput.dispatchEvent(event);
                }
            });
            
            // Avatar preview
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');
            
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
            
            // Close modals on outside click
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal-backdrop')) {
                    e.target.classList.add('hidden');
                }
                if (e.target.id === 'artworkPopup') {
                    closeArtworkPopup();
                }
            });
            
            // Close popup on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeArtworkPopup();
                }
            });
        });
    </script>
</body>
</html>