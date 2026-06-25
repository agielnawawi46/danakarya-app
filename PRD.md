# PRODUCT REQUIREMENT DOCUMENT (PRD)

## Dana Karya — Sistem Informasi Koperasi Karyawan Multi-Tenant

**Versi:** 1.0.0 (Comprehensive Architecture & System Blueprint)

**Karakteristik:** Multi-Tenant SaaS (Single Database), Payroll Integrated, Double-Entry Accounting

**Teknologi Acuan:** PHP / Laravel Framework, Blade Engine, Spatie Laravel-Permission

---

### 1. PENDAHULUAN & VISI PRODUK

**Dana Karya** adalah platform manajemen Koperasi Simpan Pinjam (KSP) karyawan berbasis web yang mengusung arsitektur *Multi-Tenant*. Aplikasi ini didesain agar satu infrastruktur sistem tunggal dapat menampung banyak entitas koperasi karyawan perusahaan mitra yang terisolasi secara mutlak.

Visi utama dari platform ini adalah mendigitalisasi total tata kelola keuangan koperasi karyawan: mengotomatiskan iuran wajib dan angsuran melalui sinkronisasi berkas payroll, menegakkan kepatuhan pembukuan melalui jurnal akuntansi otomatis (*double-entry*), menghitung skor kelayakan kredit berbasis risiko, serta mendistribusikan bonus tahunan (SHU) secara adil berbasis partisipasi ekonomi anggota.

---

### 2. ARSITEKTUR CORE & ISOLASI DATA (MULTI-TENANCY)

*   **Row-Level Tenant Isolation:** Sistem menggunakan satu basis data tunggal (*Single Database*). Setiap tabel master transaksional (seperti pengguna, simpanan, pengajuan kredit, dan jurnal) wajib memiliki kolom `organization_id`.
*   **Strict Laravel Global Scope:** Backend Laravel menerapkan *Global Scope* bawaan pada model utama. Setiap kueri data otomatis disuntikkan klausa `WHERE organization_id = auth()->user()->organization_id`, sehingga data finansial antar-perusahaan tidak akan pernah tumpang tindih atau bocor.
*   **Spatie Teams Security Context:** Hak akses pengguna diatur menggunakan *Spatie Laravel-Permission* dengan fitur *Teams* diaktifkan. Peran seorang user (misal: `admin` atau `pengurus`) terkunci penuh dalam ID organisasinya dan tidak memiliki hak apa pun pada tenant lain.

---

### 3. MATRIKS PERAN & RUANG LINGKUP OPERASIONAL (5 ROLES)

#### 1. Superadmin (Platform Owner / Creator)
*   **Lingkup Kerja:** Berada di tingkat global (di luar kontainer keuangan operasional tenant). Fokus murni pada kesehatan arsitektur makro aplikasi.
*   **Fungsi Utama:** Memantau utilisasi resource server (database, CPU), mengelola aktivasi atau penonaktifkan (*suspend*) lisensi sewa koperasi mitra, serta memantau log audit trail sistem tingkat tinggi.
*   **Batasan:** Tidak berhak mengintervensi, melihat data personal, atau menginput transaksi keuangan harian di dalam koperasi perusahaan mitra.

#### 2. Admin (Cooperative Initiator / HRD Perusahaan Mitra)
*   **Lingkup Kerja:** Manajer konfigurasi dan administrator tertinggi pada satu kontainer organisasi koperasi perusahaan.
*   **Fungsi Utama:** Mengonfigurasi legalitas dan identitas koperasi resmi, menetapkan nominal *flat* Simpanan Pokok & Wajib, menentukan suku bunga dan opsi tenor pinjaman, mendaftarkan akun tim manajemen (Pengurus/Pengawas), mengimpor data gaji karyawan, serta mengeksekusi ekspor-impor berkas integrasi payroll gajian.
*   **Batasan:** Tidak diizinkan melakukan input transaksi kasir tunai mandiri atau menyetujui (*approval*) pencairan dana pinjaman.

#### 3. Pengurus (Operational Cashier & Bendahara Koperasi)
*   **Lingkup Kerja:** Pelaksana teknis operasional harian koperasi di lapangan.
*   **Fungsi Utama:** Melayani transaksi di loket fisik (input setoran tunai simpanan sukarela dan persetujuan penarikan saldo sukarela), meninjau berkas pengajuan pinjaman digital anggota berdasarkan skor kelayakan otomatis sistem, memproses daftar pencairan dana pasca-approval, serta mengelola penjurnalan akuntansi harian dan Buku Kas.
*   **Batasan:** Tidak memiliki hak akses untuk mengubah parameter aturan keuangan inti (*rate* bunga, nominal simpanan wajib) yang telah ditetapkan oleh Admin.

#### 4. Pengawas (Internal Auditor / Dewan Komisaris)
*   **Lingkup Kerja:** Tim penilai independen untuk transparansi tata kelola koperasi.
*   **Fungsi Utama:** Memiliki hak akses **Strict Read-Only** (Hanya Baca) terhadap Buku Besar, Neraca Saldo, Neraca Akhir, dan Laporan Hasil Usaha (Laba Rugi). Pengawas memantau grafik risiko portofolio kredit (*Non-Performing Loan*) dan menelusuri rincian *Log Audit Transaksi* untuk melacak operator pengurus yang memproses dana tertentu.
*   **Batasan:** Sama sekali tidak memiliki akses memanipulasi, menambah, mengedit, atau menghapus data keuangan di database (tidak memiliki tombol aksi CRUD).

#### 5. Anggota (Karyawan Perusahaan / Anggota Koperasi)
*   **Lingkup Kerja:** Pengguna akhir (*end-user*) dengan hak akses berbasis pelayanan mandiri (*self-service*).
*   **Fungsi Utama:** Memantau akumulasi total tabungan pribadi, melihat riwayat mutasi potong gaji untuk simpanan wajib, mengajukan penarikan simpanan sukarela, melakukan simulasi angsuran via kalkulator digital, memantau sisa sado angsuran berjalan pada *Kartu Piutang Pribadi*, serta melihat rincian bonus SHU tahunannya.
*   **Batasan:** Terisolasi secara ketat (`where user_id = Auth::id()`); tidak akan pernah bisa mencari atau melihat profil dan data finansial milik rekan kerja/anggota lain.

---

### 4. ALUR AUTENTIKASI & SETUP SISTEM (WORKFLOW)

#### A. Registrasi Superadmin (Pembuat Platform)
*   **Aturan Keamanan:** Hak akses Superadmin tingkat global tidak disediakan di antarmuka web publik untuk mencegah eksploitasi peretasan.
*   **Eksekusi:** Pendaftaran akun Superadmin dilakukan langsung dari terminal server saat deployment menggunakan fitur *Laravel Database Seeder* (`DatabaseSeeder.php`) atau *Custom Artisan Command*.

#### B. Registrasi & Siklus Setup Admin (Tenant Baru)
1.  **Pendaftaran Akun:** Calon Admin koperasi perusahaan mitra mendaftar via halaman publik `/register` dengan mengisi data personal (Nama, Email, Password).
2.  **Backend Engine (`DB::transaction`):** Sistem otomatis menerbitkan ID organisasi baru, membuat record user Admin, dan mengikatnya pada entitas organisasi bawaan (*placeholder*) bernama "Koperasi Baru (Belum Dikonfigurasi)".
3.  **Mandatory Setup Pertama:** Saat Admin berhasil login pertama kali di rute `/login`, sistem mendeteksi nama organisasi masih berstatus *placeholder*. Sistem memunculkan *alert notification* interaktif di halaman dashboard. Seluruh fitur operasional dikunci sampai Admin melengkapi formulir **Profil Koperasi** (Nama Koperasi Resmi, Alamat, Kontak, Nomor Badan Hukum, dan Upload Logo).
4.  **Setup Tata Kelola Keuangan:** Admin mengisi parameter keuangan (besaran nominal Simpanan Pokok, Simpanan Wajib Bulanan, limit plafon pinjaman, serta penetapan suku bunga).
5.  **Setup Pengguna & Kepesertaan:** Admin mendaftarkan jajaran akun `Pengurus` dan `Pengawas`. Selanjutnya, Admin mendaftarkan akun `Anggota` secara massal lewat modul impor Excel payroll.

#### C. Alur Login Satu Pintu (Single Gate Access)
1.  Semua pengguna lintas peran memakai satu form pintu masuk yang sama di URL: `danakarya.com/login`.
2.  Pasca-kredensial cocok, Laravel mengunci variabel sesi global tim `setPermissionsTeamId($user->organization_id)`.
3.  Sistem menyaring data database secara otomatis lewat *Global Scope*, lalu secara dinamis mengalihkan (*redirect*) rute halaman menuju kontrol dashboard role masing-masing.

---

### 5. KEBUTUHAN FUNGSIONAL & ATURAN BISNIS (11 INTI)

1.  **Login Multirole:** Mengisolasi penempatan 5 peran pengguna secara dinamis dan aman di dalam satu tenant.
2.  **Simpanan Pokok (1x Setor):** Tabungan wajib pertama kali mendaftar kepesertaan. Sistem memvalidasi record di tabel `deposits` untuk mengunci duplikasi input. Saldo terkunci penuh dan tidak boleh ditarik di tengah jalan.
3.  **Simpanan Wajib (Bulanan Terjadwal):** Iuran rutin bulanan flat. Sistem memanfaatkan *Laravel Scheduler* (Cron Job) yang berjalan otomatis setiap tanggal 1 awal bulan untuk menerbitkan tagihan massal berstatus *unpaid/pending* bagi semua anggota aktif. Hanya bisa dicairkan saat keluar keanggotaan.
4.  **Simpanan Sukarela (Tabungan Fleksibel):** Tabungan bebas non-kewajiban. Setoran diinput manual oleh Pengurus lewat kasir tunai. Penarikan dana diizinkan kapan saja sepanjang saldo mencukupi dan disetujui Pengurus.
5.  **Multi Organisasi:** Kemampuan platform mengisolasi penuh banyak korporasi mandiri di dalam satu basis data.
6.  **Kalkulator Kelayakan Pinjaman (Credit Scoring System):** Backend membatasi nilai maksimum pengajuan kredit anggota. Batasan angsuran bulanan maksimal diatur sebesar 30% dari nilai gaji bersih karyawan (`salary`) yang tercatat di database.
7.  **Pembangkit (Generate) Jadwal Cicilan:** Mengotomatiskan pembuatan baris tabel jadwal angsuran dari bulan ke-1 hingga selesai kontrak. Komponen angsuran dipecah menjadi nominal Angsuran Pokok dan Angsuran Jasa/Bunga berdasarkan metode bunga pilihan Admin (Flat atau Anuitas).
8.  **Laporan Detail Cicilan (Kartu Piutang):** Buku pantauan detail saldo piutang anggota untuk melacak sisa utang pokok, jangka tenor berjalan, sisa angsuran, serta tracking status keterlambatan jatuh tempo.
9.  **Kalkulasi SHU Tahunan Terotomatisasi:** Menghitung laba bersih koperasi (Total Pendapatan Jasa dikurangi Beban Operasional) pada akhir tahun buku untuk dibagikan secara adil berbasis partisipasi usaha anggota. *(Aturan matematis detail ada di Bab 6)*.
10. **Modul Akuntansi Berpasangan (Double-Entry Bookkeeping):** Setiap kali mutasi simpanan tunai, pelunasan payroll, atau pencairan kredit disetujui, sistem otomatis menjurnal posisi Debet dan Kredit pada tabel `journal_entries` berdasarkan nomor akun Buku Besar (GL) terkait agar Neraca selalu seimbang.
11. **Dashboard Metrik Dinamis:** Visualisasi ringkasan eksekutif berbasis grafik komponen Blade yang disesuaikan per fungsi otorisasi role.

---

### 6. SPESIFIKASI ALGORITMA FINANSIAL DATA

#### A. Manajemen Gaji Pokok Anggota (`salary` Tracking)
Sistem tidak membutuhkan integrasi API pihak ketiga yang kompleks untuk mengetahui gaji pokok karyawan. Data dikendalikan oleh Admin melalui dua cara:
*   **Impor Masuk Massal:** Admin mengunduh format template berkas dari sistem, mengisi kolom wajib (Nama, Email, Departemen, dan Gaji Pokok/Salary), lalu mengunggahnya kembali di menu Manajemen Anggota. Sistem mendaftarkan user sekaligus mengunci nominal gaji ke database.
*   **Update Form Profil:** Penyesuaian gaji berkala atau mutasi karyawan baru diinput langsung oleh Admin melalui form dinamis di dashboard.
*   **Rekomendasi Keamanan:** Nilai pada kolom `salary` di tabel `users` disarankan menggunakan fitur enkripsi tingkat kolom Laravel (`encrypted` casting) demi menjaga privasi data karyawan.

#### B. Rumus Perhitungan & Distribusi SHU Tahunan
Total Keuntungan Bersih Koperasi dialokasikan ke pos anggaran sesuai parameter rasio koperasi yang di-setup Admin:
1.  Dana Cadangan Koperasi = 40% (Ditahan untuk modal internal)
2.  Alokasi Bagian Hak Anggota = 40% (Total dana yang dicairkan ke anggota)
3.  Dana Pengurus & Pengawas = 5%
4.  Dana Karyawan / Pengelola = 5%
5.  Dana Pendidikan Koperasi & Sosial = 10%

Porsi **Alokasi Bagian Hak Anggota (40%)** didistribusikan secara adil menggunakan pembobotan indeks partisipasi ekonomi:
*   **Jasa Modal (JM) — Bobot 60%:** Dibagi rata proporsional berdasarkan total tabungan riil anggota di koperasi.
*   **Jasa Usaha (JP) — Bobot 40%:** Dibagi rata proporsional berdasarkan total nilai bunga/jasa pinjaman yang *lunas (status paid)* disetor anggota sepanjang tahun buku berjalan.

**Mesin Backend Kalkulasi Per Anggota:**
```
SHU_Anggota = Jasa_Modal_Anggota (JM) + Jasa_Pinjaman_Anggota (JP)

JM Anggota A = ( Total Simpan Anggota A / Total Simpan Seluruh Anggota ) x Total Alokasi Jasa Modal Koperasi

JP Anggota A = ( Total Bunga Pinjaman Paid Anggota A / Total Bunga Pinjaman Paid Seluruh Anggota ) x Total Alokasi Jasa Pinjaman Koperasi
```
*Catatan Teknis:* Proses perulangan (*looping*) pembagian massal wajib dibungkus dalam `DB::transaction`. Hasil akhir dana SHU otomatis disuntikkan sebagai transaksi Deposit baru berjenis sukarela dengan status `completed`, memicu notifikasi masuk, dan langsung menambah saldo tabungan sukarela anggota secara *real-time*.

---

### 7. SISTEM PELAPORAN & MATRIKS OTORISASI FITUR (ACCESS CONTROL)

| Komponen Fitur & Modul Aplikasi | Superadmin | Admin | Pengurus | Pengawas | Anggota |
| :--- | :--- | :--- | :--- | :--- | :--- |
| Pendaftaran & Suspend Tenant Koperasi | Full CRUD | No Access | No Access | No Access | No Access |
| Konfigurasi Identitas Profil Koperasi | No Access | Full CRUD | No Access | No Access | No Access |
| Aturan Finansial (Bunga, Plafon, Flat Simpanan) | No Access | Full CRUD | No Access | No Access | No Access |
| Pembuatan Akun Manajemen Internal | No Access | Full CRUD | No Access | No Access | No Access |
| Impor Massal & Edit Gaji Anggota (Excel) | No Access | Full CRUD | No Access | No Access | No Access |
| Ekspor Excel Invoice Billing Potong Gaji | No Access | Full CRUD | Read-Only | No Access | No Access |
| Input Loket Tunai Setor/Tarik Simpanan | No Access | No Access | Full CRUD | No Access | No Access |
| Verifikasi Kelayakan & Approval Kredit | No Access | No Access | Full CRUD | No Access | No Access |
| Input Jurnal Akuntansi Buku Besar Manual | No Access | No Access | Full CRUD | No Access | No Access |
| Akses Laporan Keuangan (Neraca, PHU/Laba Rugi) | No Access | No Access | Read-Only | Read-Only | No Access |
| Penelusuran Histori Jurnal Log Audit | No Access | No Access | No Access | Read-Only | No Access |
| Simulasi & Pengajuan Pinjaman Mandiri | No Access | No Access | No Access | No Access | Full CRUD |
| Akses Mutasi & Kartu Piutang Pribadi | No Access | No Access | Read-Only | No Access | Read-Only |

---

### 8. PETA NAVIGASI SIDEBAR BERDASARKAN RUTE URL LOKAL

#### 1. SIDEBAR SUPERADMIN (Rute URL: `/superadmin/*`)
*   **Dashboard Global (`/superadmin/dashboard`)** Monitor kesehatan makro server, diagram pemakaian database & CPU, statistik total penyewa aktif.
*   **Manajemen Kemitraan (`/superadmin/tenants`)** Daftar Koperasi Perusahaan Mitra, persetujuan inbound registrasi, tombol aktivasi/suspend tenant.
*   **Log Keamanan Global (`/superadmin/system-logs`)** Audit trail pelacakan error sistem tingkat tinggi.

#### 2. SIDEBAR ADMIN (Rute URL: `/admin/*`)
*   **Dashboard Utama Admin (`/admin/dashboard`)** Rekap total anggota, status penyerapan kas masuk bulan berjalan, status sync payroll.
*   **Profil Koperasi (`/admin/profile`)** Pengisian data legalitas koperasi, No Badan Hukum, unggah berkas logo resmi korporasi.
*   **Konfigurasi Aturan (`/admin/rules`)** Form set nominal Simpanan Pokok & Wajib, set suku bunga, jangka tenor pinjaman, dan konfigurasi persentase alokasi SHU.
*   **Manajemen Pengguna (`/admin/members`)** Kelola akun staf internal (Pengurus & Pengawas), modul unduh template dan unggah massal Excel data karyawan.
*   **Modul Sinkronisasi Payroll (`/admin/payroll`)** Menu unduh *Billing Invoice* potongan gaji bulanan (tiap tanggal 25) dan menu upload berkas hasil potongan gaji dari finance untuk rekonsiliasi otomatis pelunasan massal.

#### 3. SIDEBAR PENGURUS (Rute URL: `/pengurus/*`)
*   **Dashboard Kasir (`/pengurus/dashboard`)** Indikator antrean verifikasi pengajuan pinjaman, rekap kas tunai laci loket hari ini, grafik peringatan batas aman kredit macet (NPL).
*   **Loket Simpanan (`/pengurus/deposits`)** Setor tunai langsung uang tabungan sukarela anggota, approval form penarikan saldo sukarela anggota.
*   **Modul Kredit Pinjaman (`/pengurus/loans`)** Daftar antrean verifikasi pinjaman masuk (meninjau skor kelayakan otomatis sistem <30% dari gaji pokok anggota), tabel approval jadwal pencairan dana.
*   **Buku Kas & Buku Besar (`/pengurus/accounting`)** Bagan Kode Akun Akuntansi (COA), form input jurnal umum manual untuk biaya pengeluaran operasional non-anggota (seperti pembelian ATK).
*   **Pusat Laporan Keuangan (`/pengurus/reports`)** Ekspor/Cetak berkas Buku Kas Harian, Rekapitulasi Saldo Anggota, Buku Besar Jurnal, Neraca Lajur, dan Laporan PHU (Laba Rugi).

#### 4. SIDEBAR PENGAWAS (Rute URL: `/pengawas/*`)
*   **Dashboard Pengawasan (`/pengawas/dashboard`)** Grafik analisis rasio likuiditas koperasi, grafik tren pendapatan bunga bersih, rasio NPL macet.
*   **Audit Keuangan (`/pengawas/audit-finance`)** Akses baca (*Strict Read-Only*) Jurnal Umum, Buku Besar, Neraca Saldo, dan Laporan Alokasi SHU.
*   **Log Audit Jejak Digital (`/pengawas/audit-trail`)** Pelacakan historis audit trail internal sistem (memantau pengurus mana yang menyetujui transaksi/pinjaman tertentu, jam berapa, lengkap dengan IP Address).

#### 5. SIDEBAR ANGGOTA (Rute URL: `/member/*`)
*   **Dashboard Mandiri Karyawan (`/member/dashboard`)** Informasi ringkasan total gabungan tabungan, status sisa pinjaman aktif, besaran potongan cicilan bulan ini.
*   **Tabunganku (`/member/my-deposits`)** Riwayat mutasi mendalam saldo Pokok, Wajib, Sukarela, form digital pengajuan penarikan tabungan sukarela.
*   **Fasilitas Kredit (`/member/my-loans`)** Formulir pengajuan pinjaman online baru dilengkapi kalkulator simulasi angsuran otomatis, menu akses *Kartu Piutang Pribadi* untuk melacak sisa utang pokok dan sisa tenor.
*   **Bonus Keuntungan (`/member/my-shu`)** Transparansi riwayat rincian perolehan poin SHU tahunan dari hasil kalkulasi poin Jasa Modal dan Jasa Usaha pribadi.

---

### 9. KESIMPULAN REKA BENTUK UI/UX & HALA TUJU ESTETIKA VISUAL
Sistem reka bentuk platform Dana Karya secara keseluruhan disatukan di bawah satu kesimpulan standard visual bagi memastikan konsistensi antarmuka pengguna daripada komponen *landing page* sehinggalah ke dalam paparan *dashboard* pengurusan internal.

#### A. Kenyataan Impresi Visual (Visual Impression Statement)
Perpaduan warna dasar Slate, aksen Biru-Indigo yang menyala, serta tipografi **Bold Bersih (Sans-Serif)** berjaya memancarkan aura teknologi kewangan (*FinTech SaaS*) yang moden, selamat, telus, tepercaya, dan sedia untuk digunakan oleh industri (*SaaS Product Ready*). Pendekatan reka bentuk estetika digital yang bersih ini digabungkan secara dinamik untuk meruntuhkan imej kaku "koperasi konvensional kuno" dan menggantikannya dengan ekosistem institusi kewangan korporat yang kukuh, berwibawa, namun tetap inklusif bagi semua golongan pekerja.

#### B. Teras Utama Sistem Desain (Core Design Pillars)
1.  **Profesionalisme & Kredibiliti Kewangan (Corporate FinTech Aura):** Didominasi oleh warna latar belakang neutral yang bersih seperti Slate 50 (`#f8fafc`) dan putih tulen untuk mewujudkan kontras elemen maklumat kewangan yang padat. Penggunaan warna `Blue-600` dan `Indigo-600` sebagai penanda aksen penting secara psikologi visual membina keyakinan dan rasa percaya anggota terhadap kestabilan sistem pengurusan dana.
2.  **Kekuatan Hierarki Tipografi (Bold Typography Hierarchy):** Karakter platform dipertegas secara dramatik melalui penggunaan ketebalan teks maksima (`font-black` atau ketebalan 900) pada bahagian judul utama dan angka-angka metrik bagi memberikan impresi visual yang mantap, stabil, dan futuristik. Sentuhan minimalis premium diterapkan melalui format huruf kapital dengan jarak huruf yang renggang (`uppercase tracking-[0.25em]`) pada menu navigasi untuk meningkatkan tahap kebolehbacaan pantas (*high scannability*).
3.  **Ekosistem Interaktif & Hidup (Micro-Interactions):** Antarmuka pengguna tidak terasa membosankan kerana dihidupkan melalui interaksi *frontend* ringan berasaskan Alpine.js. Ini termasuk penggunaan kesan kaca semi-transparan (*Glassmorphic Effect*) pada bilah menu navigasi semasa skrin digulir, animasi data angka statistik yang bergerak maju daripada sifar secara automatik (*Animated Counter*), serta kesan pembesaran mikro (*Hover Zoom & Glow*) pada kad fitur utama untuk memberikan maklum balas visual yang responsif kepada tindakan pengguna.

#### C. Matlamat Pengalaman Pengguna (UX Goal)
Melalui pembagian hierarki visual yang kontras, sistem ini memastikan data kewangan yang padat (seperti helaian *spreadsheet* laporan, rincian mutasi simpanan, dan tabel simulasi cicilan) tetap kelihatan kemas, tersusun, serta sangat selesa untuk dipindai oleh mata anggota koperasi mahupun pihak pengurusan dalam sekali pandang.
