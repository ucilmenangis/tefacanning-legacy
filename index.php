<?php
// Tefa Canning SIP Legacy - Landing Page
require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tefa Canning SIP</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN for development) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#E02424', // Red 600
                        accent: '#F05252',  // Red 500
                        dark: '#9B1C1C',    // Red 800
                        navy: '#111827',    // Gray 900
                    }
                }
            }
        }
    </script>
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="font-sans text-gray-800 antialiased bg-white">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white border-b border-gray-100 z-50">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[70px]">
                <!-- Logo with Text -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img class="h-10 w-auto" src="assets/images/politeknik_logo_red.png" alt="Logo">
                    <div class="flex flex-col justify-center">
                        <span class="text-[17px] font-bold text-[#B91C1C] leading-none mb-1 tracking-wide">TEFA Canning SIP</span>
                        <span class="text-[12px] font-medium text-slate-400 leading-none">Politeknik Negeri Jember</span>
                    </div>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-12">
                    <a href="#katalog" class="text-[11px] font-semibold text-gray-500 hover:text-primary transition-colors tracking-widest uppercase">Produk</a>
                    <a href="#tentang" class="text-[11px] font-semibold text-gray-500 hover:text-primary transition-colors tracking-widest uppercase">Tentang</a>
                    <a href="#batch" class="text-[11px] font-semibold text-gray-500 hover:text-primary transition-colors tracking-widest uppercase">Info Batch</a>
                    <a href="auth/login-customer.php" class="px-5 py-2 text-[12px] font-semibold text-white bg-primary hover:bg-accent rounded-md shadow-sm transition-all">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-[140px] pb-24 overflow-hidden flex flex-col justify-center min-h-[90vh]">
        <!-- Full Hero Background Glow -->
        <div class="absolute inset-0 bg-gradient-to-tr from-rose-50/60 via-red-50/30 to-rose-50/60 blur-[100px] pointer-events-none -z-10 scale-110"></div>
        
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full flex justify-center">
            
            <div class="flex flex-col items-start text-left max-w-[750px] w-full">
                <!-- Badge -->
                <div class="inline-flex items-center justify-center px-3 py-1.5 rounded-full bg-[#FFF5F5] border border-red-100 text-primary mb-6 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary mr-2"></span>
                    <span class="text-[11px] font-bold tracking-wide">Teaching Factory — Polije</span>
                </div>
                
                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl md:text-[60px] font-extrabold tracking-tight text-navy leading-[1.05] mb-6">
                    Canning SIP<br>
                    <span class="text-primary whitespace-nowrap">Sehat, Lezat & Bergizi</span>
                </h1>
                
                <!-- Subhead -->
                <p class="text-[16px] text-gray-500 max-w-[600px] leading-relaxed font-medium mb-10">
                    Sarden kaleng premium dari ikan lemuru segar, diproduksi oleh Teaching Factory Politeknik Negeri Jember dengan standar mutu terjamin.
                </p>
                
                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 items-center">
                    <a href="#katalog" class="inline-flex justify-center items-center px-6 py-3 rounded-lg text-[14px] font-bold text-white bg-[#E02424] hover:bg-dark transition-all shadow-md shadow-red-500/20">
                        Lihat Produk
                        <i class="ph-bold ph-caret-down ml-2"></i>
                    </a>
                    <a href="#batch" class="inline-flex justify-center items-center px-6 py-3 rounded-lg bg-white border border-[#FCA5A5] hover:bg-red-50 transition-all text-primary shadow-[0_2px_10px_rgb(0,0,0,0.02)]">
                        <i class="ph-bold ph-package text-lg mr-2"></i>
                        <span class="text-[14px] font-bold leading-tight whitespace-nowrap">Pre-Order Sekarang</span>
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="mt-14 flex items-center gap-16 sm:gap-24">
                    <div>
                        <div class="text-[26px] font-extrabold text-navy leading-none mb-1.5">3</div>
                        <div class="text-[12px] font-medium text-gray-400 capitalize">Varian Produk</div>
                    </div>
                    <div>
                        <div class="text-[26px] font-extrabold text-navy leading-none mb-1.5">425gr</div>
                        <div class="text-[12px] font-medium text-gray-400 capitalize">Per Kaleng</div>
                    </div>
                    <div>
                        <div class="text-[26px] font-extrabold text-navy leading-none mb-1.5">100%</div>
                        <div class="text-[12px] font-medium text-gray-400 capitalize">Tanpa Pengawet</div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Katalog Section -->
    <div id="katalog" class="py-24 bg-white relative">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-[10px] font-bold text-primary tracking-widest uppercase mb-2">Katalog</h2>
                <h3 class="text-3xl font-bold text-navy mb-4">Produk Kami</h3>
                <p class="text-[14px] text-gray-500 font-normal max-w-md mx-auto">
                    Tiga varian sarden kaleng dengan cita rasa nusantara indonesia, <br>diproduksi tanpa bahan pengawet.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Product 1 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300">
                    <div class="h-[280px] bg-[#FEF2F2] flex items-center justify-center p-6 relative">
                        <!-- Simulated product image rendering like in screenshot (centered, taking some vertical space) -->
                        <img src="assets/images/product.jpeg" alt="Sarden SIP Saus Tomat" class="h-full w-full object-cover rounded-xl shadow-sm">
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-primary text-white">
                                TERLARIS
                            </span>
                            <span class="text-[10px] font-medium text-gray-400 border border-gray-200 px-2 py-0.5 rounded">425gr</span>
                        </div>
                        <h4 class="text-lg font-bold text-navy mb-2">Sarden SIP Saus Tomat</h4>
                        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">
                            Sarden kaleng premium dari perairan nusantara indonesia. Gurih dan sedap dengan balur saus tomat yang lezat.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center text-[12px] text-gray-600 font-medium">
                                <i class="ph-bold ph-check text-green-500 mr-2"></i> Halal
                            </div>
                            <div class="flex items-center text-[12px] text-gray-600 font-medium">
                                <i class="ph-bold ph-check text-green-500 mr-2"></i> Sertifikat Karantina
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300">
                    <div class="h-[280px] bg-[#F0FDF4] flex items-center justify-center p-6 relative">
                        <img src="assets/images/product.jpeg" alt="Sarden SIP Asin" class="h-full w-full object-cover rounded-xl shadow-sm">
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[10px] font-medium text-gray-400 border border-gray-200 px-2 py-0.5 rounded">425gr</span>
                        </div>
                        <h4 class="text-lg font-bold text-navy mb-2">Sarden SIP Asin</h4>
                        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">
                            Varian pertama (awal) — dari lemuru dan bumbu garam. Cocok sebagai teman santapan utama keluarga setiap hari.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center text-[12px] text-gray-600 font-medium">
                                <i class="ph-bold ph-check text-green-500 mr-2"></i> Halal
                            </div>
                            <div class="flex items-center text-[12px] text-gray-600 font-medium">
                                <i class="ph-bold ph-check text-green-500 mr-2"></i> Tanpa Pengawet
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-gray-200/50 transition-all duration-300">
                    <div class="h-[280px] bg-[#FFFBEB] flex items-center justify-center p-6 relative">
                        <img src="assets/images/product.jpeg" alt="Sarden SIP Saus Cabai" class="h-full w-full object-cover rounded-xl shadow-sm">
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[10px] font-medium text-gray-400 border border-gray-200 px-2 py-0.5 rounded">425gr</span>
                        </div>
                        <h4 class="text-lg font-bold text-navy mb-2">Sarden SIP Saus Cabai</h4>
                        <p class="text-[13px] text-gray-500 mb-6 leading-relaxed">
                            Bagi yang suka dengan rasa pedas khas Nusantara. Pedas pas, tidak mengubah cita rasa dasar ikan.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center text-[12px] text-gray-600 font-medium">
                                <i class="ph-bold ph-check text-green-500 mr-2"></i> Halal
                            </div>
                            <div class="flex items-center text-[12px] text-gray-600 font-medium">
                                <i class="ph-bold ph-check text-green-500 mr-2"></i> Ekstra Cabai Segar
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Section -->
    <div id="batch" class="py-24 bg-white relative">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-[11px] font-bold text-primary tracking-widest uppercase mb-2">Info Terbaru</h2>
                <h3 class="text-3xl font-bold text-navy mb-4">Batch Produksi</h3>
                <p class="text-[14px] text-gray-400 font-medium max-w-md mx-auto leading-relaxed">
                    Informasi batch produksi yang sedang dibuka untuk pre-order.
                </p>
            </div>

            <!-- Dynamic grid container -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl border border-red-100 p-6 flex flex-col hover:shadow-lg hover:shadow-red-500/5 transition-all">
                    <div class="flex items-center justify-between mb-5">
                        <div class="w-10 h-10 rounded-xl bg-[#FFF5F5] text-primary flex items-center justify-center">
                            <i class="ph-bold ph-calendar-blank text-lg"></i>
                        </div>
                        <div class="px-2.5 py-1 rounded-full bg-[#ECFDF5] flex items-center text-[10px] font-bold text-[#10B981] tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] mr-1.5"></span>
                            DIBUKA
                        </div>
                    </div>
                    
                    <h4 class="text-lg font-bold text-navy mb-1">Batch 1</h4>
                    <p class="text-[13px] text-primary font-semibold mb-2">Dies Natalies Polije</p>
                    <p class="text-[12px] text-gray-400 mb-6 font-medium">Tidak ada</p>
                    
                    <div class="flex items-center gap-6 mb-8 text-[12px] font-medium text-gray-500">
                        <div class="flex items-center">
                            <i class="ph-bold ph-calendar text-gray-400 text-sm mr-2"></i>
                            15 Feb 2026
                        </div>
                        <div class="flex items-center">
                            <i class="ph-bold ph-shopping-bag text-gray-400 text-sm mr-2"></i>
                            2 pesanan
                        </div>
                    </div>
                    
                    <a href="customer/preorder.php" class="mt-auto w-full flex items-center justify-center py-3.5 rounded-xl text-[13px] font-bold text-primary bg-[#FFF5F5] hover:bg-red-50 transition-colors">
                        Pre-Order Batch Ini <i class="ph-bold ph-arrow-right ml-1.5 text-sm"></i>
                    </a>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Disclaimer -->
    <div class="py-12 bg-[#FFFBEB]">
        <div class="max-w-[900px] mx-auto px-6">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-[14px] bg-[#FDE68A]/60 flex items-center justify-center text-[#D97706]">
                    <i class="ph ph-warning text-[26px]"></i>
                </div>
                <div class="pt-1">
                    <h5 class="text-[14px] font-bold text-[#92400E] mb-2">Disclaimer SNI</h5>
                    <p class="text-[14px] text-[#B45309] leading-[1.8] font-medium max-w-[800px]">
                        Produk TEFA Canning diproduksi dalam lingkungan pembelajaran Teaching Factory. Produk telah melalui proses quality control standar dan sterilisasi komersial, namun mungkin memiliki variasi minor yang tidak memengaruhi kualitas dan keamanan pangan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tentang Kami -->
    <div id="tentang" class="py-24 bg-white">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-[11px] font-bold text-primary tracking-widest uppercase mb-3">Tentang Kami</h2>
                    <h3 class="text-4xl font-extrabold text-navy leading-tight mb-6">
                        Teaching Factory<br>
                        <span class="text-primary">Fish Canning Polije</span>
                    </h3>
                    
                    <p class="text-[15px] text-gray-500 leading-[1.8] mb-8 font-medium">
                        Teaching Factory (TEFA) Canning adalah unit produksi pembelajaran Politeknik Negeri Jember yang memproduksi sarden kaleng berkualitas. Seluruh proses produksi dilakukan oleh mahasiswa di bawah bimbingan dosen ahli.
                    </p>
                    
                    <ul class="space-y-6">
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-md bg-red-50 flex items-center justify-center mr-3 shadow-sm border border-red-50">
                                <i class="ph ph-check text-primary text-[12px]"></i>
                            </div>
                            <span class="text-[14px] text-gray-500 font-medium pt-0.5">Diproduksi dengan proses <strong class="text-gray-600 font-bold">sterilisasi komersial</strong> sesuai standar industri</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-md bg-red-50 flex items-center justify-center mr-3 shadow-sm border border-red-50">
                                <i class="ph ph-check text-primary text-[12px]"></i>
                            </div>
                            <span class="text-[14px] text-gray-500 font-medium pt-0.5"><strong class="text-gray-600 font-bold">Tanpa bahan pengawet</strong> — hanya bahan alami pilihan</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-md bg-red-50 flex items-center justify-center mr-3 shadow-sm border border-red-50">
                                <i class="ph ph-check text-primary text-[12px]"></i>
                            </div>
                            <span class="text-[14px] text-gray-500 font-medium pt-0.5">Menggunakan <strong class="text-gray-600 font-bold">ikan lemuru segar</strong> berkualitas tinggi</span>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 rounded-md bg-red-50 flex items-center justify-center mr-3 shadow-sm border border-red-50">
                                <i class="ph ph-check text-primary text-[12px]"></i>
                            </div>
                            <span class="text-[14px] text-gray-500 font-medium pt-0.5">Sistem <strong class="text-gray-600 font-bold">Pre-Order berbasis Batch</strong> menjamin kesegaran produk</span>
                        </li>
                    </ul>
                </div>
                
                <div class="flex flex-col items-center justify-center gap-4">
                    <!-- Logos Container -->
                    <div class="bg-white border border-gray-100 rounded-[32px] p-10 sm:p-14 shadow-sm w-full max-w-lg flex items-center justify-center">
                        <img src="assets/images/3_logo_in_1.png" alt="Logos" class="w-full h-auto object-contain">
                    </div>
                    <div class="text-[11px] font-medium text-gray-400 mt-2">
                        Teaching Factory · Fish Canning · Politeknik Negeri Jember
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#0F172A] pt-16 pb-8">
        <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-16">
                <!-- Col 1 -->
                <div class="md:col-span-1 border-r border-[#1E293B] pr-8">
                    <img src="assets/images/3_logo_in_1.png" alt="Logos" class="h-8 mb-6 bg-white p-1 rounded">
                    <p class="text-[#94A3B8] text-[12px] leading-relaxed">
                        Teaching Factory Canning adalah sarana pembelajaran industri di kampus yang menghasilkan produk bernilai jual.
                    </p>
                    <a href="https://polije.ac.id" class="text-[11px] text-[#94A3B8] hover:text-white mt-4 inline-block">www.polije.ac.id</a>
                </div>

                <!-- Col 2 -->
                <div>
                    <h5 class="text-white text-[10px] font-bold uppercase tracking-widest mb-6">Lokasi</h5>
                    <p class="text-[#94A3B8] text-[12px] leading-relaxed mb-4">
                        TEFA Canning, Jl. Mastrip, Kotak Pos 164, Krajan Timur, Sumbersari, Kec. Sumbersari, Kab. Jember, Jawa Timur 68121
                    </p>
                    <a href="https://maps.app.goo.gl/354mYQGfX4W81Tf19" target="_blank" class="text-primary text-[11px] hover:text-red-400 flex items-center">
                        <i class="ph-fill ph-map-pin mr-1"></i> Petunjuk Arah
                    </a>
                </div>

                <!-- Col 3 -->
                <div>
                    <h5 class="text-white text-[10px] font-bold uppercase tracking-widest mb-6">Navigasi</h5>
                    <ul class="space-y-3">
                        <li><a href="#katalog" class="text-[#94A3B8] hover:text-white text-[12px]">Produk</a></li>
                        <li><a href="#tentang" class="text-[#94A3B8] hover:text-white text-[12px]">Tentang Kami</a></li>
                        <li><a href="#batch" class="text-[#94A3B8] hover:text-white text-[12px]">Info Batch</a></li>
                        <li><a href="auth/login-customer.php" class="text-[#94A3B8] hover:text-white text-[12px] mt-2 block">Login Pelanggan</a></li>
                        <li><a href="auth/login-admin.php" class="text-[#94A3B8] hover:text-white text-[12px]">Login Admin</a></li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div>
                    <h5 class="text-white text-[10px] font-bold uppercase tracking-widest mb-6">Peta Lokasi</h5>
                    <div class="h-28 bg-[#1E293B] rounded-lg overflow-hidden relative border border-[#334155]">
                        <img src="https://static-maps.yandex.ru/1.x/?lang=en_US&ll=113.7226,-8.1587&z=15&l=map&size=300,150&pt=113.7226,-8.1587,vkgrm" alt="Map" class="w-full h-full object-cover">
                        <a href="https://maps.app.goo.gl/354mYQGfX4W81Tf19" target="_blank" class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 hover:opacity-100 transition-opacity text-white text-[11px] font-bold underline">
                            View API map
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-[#1E293B] pt-6 flex flex-col md:flex-row justify-between items-center text-[#64748B] text-[11px]">
                <p>&copy; <?php echo date('Y'); ?> Teaching Factory Tefa Canning Politeknik Negeri Jember.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white"><i class="ph-fill ph-facebook-logo text-lg"></i></a>
                    <a href="#" class="hover:text-white"><i class="ph-fill ph-instagram-logo text-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
