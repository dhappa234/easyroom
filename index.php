<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Redirect jika sudah login
if (is_logged_in()) {
  if (is_admin()) {
    header('Location: admin/index.php');
  } elseif (is_dosen()) {
    header('Location: dosen/dashboard.php');
  } elseif (is_mahasiswa()) {
    header('Location: mahasiswa/dashboard.php');
  }
  exit;
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>EasyRoom - Sistem Manajemen Ruangan & Jadwal Perkuliahan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- AOS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
      <a class="navbar-brand text-primary" href="#">
        <strong>EasyRoom</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNav" aria-controls="navbarNav"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link" href="#fitur">Fitur</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#tentang">Tentang</a>
          </li>
          <li class="nav-item ms-lg-3">
            <a href="login.php" class="btn btn-primary px-4 btn-anim">
              Masuk Sistem
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero-section">
    <!-- Layer parallax-style -->
    <div class="hero-bg-layer">
      <div class="hero-circle circle-1"></div>
      <div class="hero-circle circle-2"></div>
      <div class="hero-circle circle-3"></div>
    </div>

    <div class="container hero-content">
      <div class="row align-items-center gy-4">
        <div class="col-lg-6" data-aos="fade-right">
          <span class="badge-soft mb-3">
            <i class="bi bi-lightning-charge-fill me-1"></i> Sistem Manajemen Ruangan Fakultas
          </span>
          <h1 class="display-5 mb-3 hero-title">
            Kelola Ruang & Jadwal
            <span class="hero-highlight">Perkuliahan</span><br>
            secara
            <span class="sliding-wrapper m-1">
              <span class="sliding-inner blue-text">
                <span>cepat,</span>
                <span>terstruktur,</span>
                <span>real-time,</span>
                <span>efisien.</span>
              </span>
            </span>
          </h1>
          <p class="lead mb-4 text-muted">
            EasyRoom membantu <strong>admin</strong>, <strong>dosen</strong>, dan <strong>mahasiswa</strong>
            memonitor ketersediaan ruangan, melihat jadwal perkuliahan, dan mengelola peminjaman ruang dalam satu sistem terpusat.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="login.php" class="btn btn-primary btn-lg px-4 btn-anim">
              <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
            </a>
            <a href="#fitur" class="btn btn-outline-primary btn-lg px-4">
              <i class="bi bi-play-circle me-1"></i> Lihat Fitur
            </a>
          </div>
        </div>

        <div class="col-lg-6" data-aos="fade-left">
          <div class="hero-illustration float">
            <div class="card shadow-sm mb-3 hero-card">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div>
                    <h6 class="mb-0 text-primary">Jadwal Hari Ini</h6>
                    <small class="text-muted">Contoh tampilan dashboard</small>
                  </div>
                  <span class="badge bg-success">Online</span>
                </div>
                <div class="table-responsive">
                  <table class="table table-sm mb-0 text-muted align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Mata Kuliah</th>
                        <th>Ruang</th>
                        <th>Waktu</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Rekayasa Perangkat Lunak</td>
                        <td>IsDB FST 4-4</td>
                        <td>09:30 - 11:10</td>
                      </tr>
                      <tr>
                        <td>Falsafah Kesatuan Ilmu</td>
                        <td>Teater FST</td>
                        <td>07:00 - 08:40</td>
                      </tr>
                      <tr>
                        <td>Rapat Dosen</td>
                        <td>Ruang Rapat</td>
                        <td>13:00 - 14:00</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <small class="text-muted d-block mt-2">Data di atas hanya ilustrasi tampilan jadwal.</small>
              </div>
            </div>
            <div class="d-flex justify-content-between text-muted small">
              <span><i class="bi bi-check2-circle me-1"></i> Integrasi jadwal & peminjaman ruang</span>
              <span><i class="bi bi-code-slash me-1"></i> PHP Native & Bootstrap</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FITUR -->
  <section class="py-5 section-light" id="fitur">
    <div class="container">
      <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="section-title mb-2 text-dark">Fitur Utama EasyRoom</h2>
        <p class="text-muted mb-0">
          Mendukung pengelolaan ruangan dan jadwal yang rapi di lingkungan fakultas.
        </p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3" data-aos="zoom-in">
          <div class="card feature-card h-100">
            <div class="card-body">
              <div class="feature-icon">
                <i class="bi bi-calendar-week"></i>
              </div>
              <h5 class="card-title text-dark">Jadwal Perkuliahan</h5>
              <p class="card-text text-muted">
                Dosen & mahasiswa langsung melihat jadwal mengajar dan jadwal kuliah setelah login.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
          <div class="card feature-card h-100">
            <div class="card-body">
              <div class="feature-icon">
                <i class="bi bi-door-open"></i>
              </div>
              <h5 class="card-title text-dark">Ketersediaan Kelas</h5>
              <p class="card-text text-muted">
                Pantau status ruangan (kosong/terpakai), kapasitas, dan fasilitas pendukung secara real-time.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
          <div class="card feature-card h-100">
            <div class="card-body">
              <div class="feature-icon">
                <i class="bi bi-file-earmark-plus"></i>
              </div>
              <h5 class="card-title text-dark">Peminjaman 3 Tahap</h5>
              <p class="card-text text-muted">
                Ajukan peminjaman ruang melalui alur yang jelas: isi data, konfirmasi, dan pantau status pengajuan.
              </p>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="300">
          <div class="card feature-card h-100">
            <div class="card-body">
              <div class="feature-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <h5 class="card-title text-dark">Panel Admin</h5>
              <p class="card-text text-muted">
                Admin mengelola pengguna, data ruang, serta menyetujui atau menolak permohonan peminjaman.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- TENTANG -->
<section class="py-5 section-white" id="tentang">
  <div class="container">
    <div class="row align-items-start gy-4">
      <div class="col-md-6" data-aos="fade-right">
        <h2 class="section-title mb-3 text-dark">Tentang Sistem</h2>
        <p class="text-muted">
          EasyRoom dibangun dengan <strong>PHP Native</strong> dan <strong>Bootstrap</strong>,
          terhubung dengan database yang menyimpan data admin, dosen, mahasiswa, matakuliah,
          ruangan, jadwal, dan peminjaman ruang.
        </p>
        <ul class="text-muted">
          <li>Akun dibuat oleh admin (tanpa registrasi mandiri).</li>
          <li>Peran terpisah: admin, dosen, dan mahasiswa dengan hak akses masing-masing.</li>
          <li>Mendukung pengelolaan jadwal perkuliahan dan peminjaman ruangan secara terintegrasi.</li>
        </ul>
      </div>

      <div class="col-md-6" data-aos="fade-left">
        <div class="card feature-card border-0 h-80">
          <div class="card-body d-flex flex-column">
            <h5 class="mb-3 text-dark">Siap digunakan di Fakultas Anda</h5>
            <p class="text-muted mb-4">
              Struktur tabel sudah disesuaikan dengan kebutuhan manajemen ruang dan jadwal di lingkungan kampus,
              sehingga mudah diintegrasikan dengan proses akademik yang berjalan.
            </p>
            <div class="row g-3 mb-3">
              <!-- Dosen -->
              <div class="col-6">
                <div class="card feature-card h-80">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-0">
                      <div class="feature-icon me-3 fs-4">
                        <i class="bi bi-person"></i>
                      </div>
                      <h5 class="card-title text-dark mb-0">Dosen</h5>
                    </div>
                    <p class="card-text text-muted mb-0">
                      Username: Email <br>Password: NIP (Default)
                    </p>
                  </div>
                </div>
              </div>
              <!--Mahasiswa-->
              <div class="col-6">
                <div class="card feature-card h-100">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-0">
                      <div class="feature-icon me-3 fs-4">
                        <i class="bi bi-mortarboard"></i>
                      </div>
                      <h5 class="card-title text-dark mb-0">Mahasiswa</h5>
                    </div>
                    <p class="card-text text-muted mb-0">
                      Username: NIM <br>Password: NIM (Default)
                    </p>
                  </div>
                </div>
              </div>             
            </div>
            <div class="mt-auto">
              <a href="login.php" class="btn btn-primary btn-anim">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

  <!-- FOOTER -->
  <footer class="py-3">
    <div class="container text-center text-muted small">
      &copy; <?php echo date('Y'); ?> EasyRoom - Sistem Manajemen Ruangan & Jadwal Perkuliahan.
    </div>
  </footer>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 900,
      once: true
    });
  </script>
</body>

</html>