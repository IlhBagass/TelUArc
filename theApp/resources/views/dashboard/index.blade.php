<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - TelUArc</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#DC2626',
                        'secondary': '#4B5563',
                    },
                    fontFamily: {
                        'sans': ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .tab-active { @apply bg-red-50 text-red-600 border-r-4 border-red-600; }
        /* Hide all sections by default, show only active */
        .dashboard-section { display: none; }
        .dashboard-section.active { display: block; animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-xl z-20 hidden md:flex flex-col">
            <div class="p-6 border-b flex items-center justify-center">
                 <img src="{{ asset('logo-telu-arc.png') }}" alt="TelUArc Admin" class="h-10 w-auto object-contain">
                 <span class="ml-3 font-bold text-xl text-gray-800">Admin Panel</span>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <button onclick="switchTab('overview')" id="nav-overview" class="w-full flex items-center px-6 py-3 hover:bg-red-50 hover:text-red-600 transition-colors tab-active">
                            <i class="fas fa-th-large w-6"></i>
                            <span class="font-medium">Dashboard</span>
                        </button>
                    </li>
                    <li class="px-6 pt-4 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Management
                    </li>
                    <li>
                        <button onclick="switchTab('artworks')" id="nav-artworks" class="w-full flex items-center px-6 py-3 hover:bg-red-50 hover:text-red-600 transition-colors text-gray-600">
                            <i class="fas fa-paint-brush w-6"></i>
                            <span class="font-medium">Kelola Karya</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="switchTab('reports')" id="nav-reports" class="w-full flex items-center px-6 py-3 hover:bg-red-50 hover:text-red-600 transition-colors text-gray-600">
                            <i class="fas fa-flag w-6"></i>
                            <span class="font-medium">Laporan</span>
                        </button>
                    </li>
                    <li>
                        <button onclick="switchTab('users')" id="nav-users" class="w-full flex items-center px-6 py-3 hover:bg-red-50 hover:text-red-600 transition-colors text-gray-600">
                            <i class="fas fa-users w-6"></i>
                            <span class="font-medium">Kelola Akun</span>
                        </button>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <!-- Mobile Header -->
            <header class="bg-white shadow-sm z-10 md:hidden flex items-center justify-between p-4">
                <div class="flex items-center">
                    <button class="text-gray-600 focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <span class="font-bold text-lg">Admin Panel</span>
                </div>
                <div class="flex items-center">
                     <img src="https://i.pravatar.cc/100" alt="Admin" class="w-8 h-8 rounded-full">
                </div>
            </header>
            
            <!-- Topbar (Desktop) -->
            <header class="bg-white shadow-sm z-10 hidden md:flex items-center justify-between px-8 py-4">
                <h2 id="page-title" class="text-2xl font-bold text-gray-800">
                    Dashboard Overview
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <span class="text-sm font-medium text-gray-600">Admin</span>
                            <img src="https://i.pravatar.cc/100" alt="Admin" class="w-10 h-10 rounded-full border-2 border-white shadow">
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                
                <!-- SECTION: OVERVIEW -->
                <div id="section-overview" class="dashboard-section active">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Users Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Total Pengguna</p>
                                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</h3>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-full text-blue-500">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-green-500 font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i> 12%
                                </span>
                                <span class="text-gray-400 ml-2">dari bulan lalu</span>
                            </div>
                        </div>
                
                        <!-- Total Artworks Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Total Karya</p>
                                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalArtworks }}</h3>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-full text-purple-500">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-green-500 font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i> 5%
                                </span>
                                <span class="text-gray-400 ml-2">dari bulan lalu</span>
                            </div>
                        </div>
                
                        <!-- Reports Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 mb-1">Laporan Baru</p>
                                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalReports }}</h3>
                                </div>
                                <div class="p-3 bg-red-50 rounded-full text-red-500">
                                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-red-500 font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i> 2
                                </span>
                                <span class="text-gray-400 ml-2">perlu tinjauan</span>
                            </div>
                        </div>
                    </div>
                
                    <!-- Recent Activity Section -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Terkini</h3>
                        <div class="space-y-4">
                            <!-- Activity Item -->
                            <div class="flex items-start p-3 hover:bg-gray-50 rounded-lg transition-colors border-b last:border-0 border-gray-100">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Pengguna Baru Mendaftar</p>
                                    <p class="text-sm text-gray-500">John Doe baru saja bergabung dengan TelUArc</p>
                                    <p class="text-xs text-gray-400 mt-1">2 menit yang lalu</p>
                                </div>
                            </div>
                
                            <div class="flex items-start p-3 hover:bg-gray-50 rounded-lg transition-colors border-b last:border-0 border-gray-100">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-500">
                                        <i class="fas fa-upload"></i>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Karya Baru Diupload</p>
                                    <p class="text-sm text-gray-500">"Digital Horizon" oleh Jane Smith</p>
                                    <p class="text-xs text-gray-400 mt-1">15 menit yang lalu</p>
                                </div>
                            </div>
                
                            <div class="flex items-start p-3 hover:bg-gray-50 rounded-lg transition-colors border-b last:border-0 border-gray-100">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                                        <i class="fas fa-flag"></i>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Laporan Konten</p>
                                    <p class="text-sm text-gray-500">Karya #442 dilaporkan sebagai spam</p>
                                    <p class="text-xs text-gray-400 mt-1">1 jam yang lalu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: ARTWORKS -->
                <div id="section-artworks" class="dashboard-section">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Daftar Semua Karya</h3>
                            <div class="flex space-x-2">
                                <div class="relative">
                                    <input type="text" placeholder="Cari karya..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                    <i class="fas fa-filter mr-2"></i> Filter
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                        <th class="p-4 font-semibold border-b">Artwork</th>
                                        <th class="p-4 font-semibold border-b">Seniman</th>
                                        <th class="p-4 font-semibold border-b">Tanggal Upload</th>
                                        <th class="p-4 font-semibold border-b">Status</th>
                                        <th class="p-4 font-semibold border-b text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    @forelse($artworks as $artwork)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4">
                                            <div class="flex items-center space-x-3">
                                                <img src="{{ $artwork->main_image ? asset('storage/' . $artwork->main_image) : 'https://placehold.co/100' }}" alt="Artwork" class="w-12 h-12 rounded-lg object-cover">
                                                <div>
                                                    <p class="font-bold text-gray-800">{{ $artwork->title }}</p>
                                                    <p class="text-gray-500 text-xs">{{ $artwork->category ?? 'Art' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center space-x-2">
                                                <img src="{{ $artwork->user->avatar ? asset('storage/' . $artwork->user->avatar) : 'https://i.pravatar.cc/150' }}" alt="Artist" class="w-6 h-6 rounded-full">
                                                <span class="font-medium text-gray-700">{{ $artwork->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-gray-600">{{ $artwork->created_at->format('d M Y') }}</td>
                                        <td class="p-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                                                Active
                                            </span>
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            <form action="{{ route('dashboard.artworks.destroy', $artwork->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karya ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-gray-500">Belum ada karya.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- SECTION: REPORTS -->
                <div id="section-reports" class="dashboard-section">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Stats Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                             <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                                 <h4 class="text-red-600 font-semibold">Total Laporan</h4>
                                 <p class="text-2xl font-bold">{{ $totalReports }}</p>
                             </div>
                             <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                                 <h4 class="text-yellow-600 font-semibold">Menunggu Tinjauan</h4>
                                 <p class="text-2xl font-bold">{{ $pendingReportsCount }}</p>
                             </div>
                             <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                 <h4 class="text-green-600 font-semibold">Diselesaikan</h4>
                                 <p class="text-2xl font-bold">{{ $totalReports - $pendingReportsCount }}</p>
                             </div>
                        </div>
                
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="p-6 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-800">Daftar Laporan Terbaru</h3>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                            <th class="p-4 font-semibold border-b">Target</th>
                                            <th class="p-4 font-semibold border-b">Pelapor</th>
                                            <th class="p-4 font-semibold border-b">Alasan</th>
                                            <th class="p-4 font-semibold border-b">Tanggal</th>
                                            <th class="p-4 font-semibold border-b">Status</th>
                                            <th class="p-4 font-semibold border-b text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-gray-100">
                                        @forelse($reports as $report)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="p-4">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                    <div>
                                                        <span class="block font-medium text-gray-800">Artwork #{{ $report->artwork_id }}</span>
                                                        <a href="{{ route('artworks.show', $report->artwork_id) }}" target="_blank" class="text-xs text-blue-500 hover:underline">Lihat Konten</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <span class="font-medium text-gray-700">{{ $report->user->name }}</span>
                                            </td>
                                            <td class="p-4">
                                                <p class="text-gray-600 truncate max-w-xs">{{ $report->reason }}</p>
                                            </td>
                                            <td class="p-4 text-gray-600">{{ $report->created_at->diffForHumans() }}</td>
                                            <td class="p-4">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-600">
                                                    Pending
                                                </span>
                                            </td>
                                            <td class="p-4 text-right space-x-2">
                                                <form action="{{ route('dashboard.reports.destroy', $report->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus laporan?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-green-50 text-green-600 rounded hover:bg-green-100 transition-colors text-xs font-medium">Selesai</button>
                                                </form>
                                                <form action="{{ route('dashboard.artworks.destroy', $report->artwork_id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus konten yang dilaporkan?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100 transition-colors text-xs font-medium">Hapus Konten</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="p-4 text-center text-gray-500">Tidak ada laporan.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: USERS -->
                <div id="section-users" class="dashboard-section">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna</h3>
                            <div class="flex space-x-2">
                                <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center">
                                    <i class="fas fa-plus mr-2"></i> Tambah Admin
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                        <th class="p-4 font-semibold border-b">Pengguna</th>
                                        <th class="p-4 font-semibold border-b">Role</th>
                                        <th class="p-4 font-semibold border-b">Bergabung</th>
                                        <th class="p-4 font-semibold border-b">Status</th>
                                        <th class="p-4 font-semibold border-b text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100">
                                    @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 transition-colors {{ $user->role === 'banned' ? 'opacity-75' : '' }}">
                                        <td class="p-4">
                                            <div class="flex items-center space-x-3">
                                                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://i.pravatar.cc/150' }}" alt="User" class="w-10 h-10 rounded-full">
                                                <div>
                                                    <p class="font-bold text-gray-800">{{ $user->name }}</p>
                                                    <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-600' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                                        <td class="p-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'banned' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                                {{ $user->role === 'banned' ? 'Banned' : 'Active' }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-right space-x-2">
                                            @if($user->role !== 'admin')
                                            <form action="{{ route('dashboard.users.toggle-ban', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="{{ $user->role === 'banned' ? 'text-green-500 hover:text-green-700' : 'text-yellow-500 hover:text-yellow-700' }}" title="{{ $user->role === 'banned' ? 'Unban' : 'Ban' }}">
                                                    <i class="fas {{ $user->role === 'banned' ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('dashboard.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-gray-500">Belum ada pengguna.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
    
    <script>
        function switchTab(tabName) {
            // Hide all sections
            document.querySelectorAll('.dashboard-section').forEach(el => el.classList.remove('active'));
            // Remove active style from all nav buttons
            document.querySelectorAll('nav button').forEach(el => {
                el.classList.remove('tab-active');
                el.classList.add('text-gray-600');
            });
            
            // Show selected section
            document.getElementById('section-' + tabName).classList.add('active');
            
            // Update active nav button
            const navBtn = document.getElementById('nav-' + tabName);
            navBtn.classList.add('tab-active');
            navBtn.classList.remove('text-gray-600');
            
            // Update Page Title
            const titles = {
                'overview': 'Dashboard Overview',
                'artworks': 'Kelola Karya',
                'reports': 'Laporan Kenakalan',
                'users': 'Kelola Akun'
            };
            document.getElementById('page-title').innerText = titles[tabName];
        }
    </script>
</body>
</html>
