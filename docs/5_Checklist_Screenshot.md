# 📸 Checklist Screenshot Hasil Pengujian
## Sistem Informasi Koperasi Karyawan — Dana Karya v1.0.0

---

> **Petunjuk:** Ambil screenshot sesuai urutan di bawah. Setiap screenshot harus menunjukkan **hasil aktual** di browser. Beri nama file sesuai kode (misal: `SS-AUTH-01.png`).

---

## A. MODUL AUTENTIKASI (8 Screenshot)

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 1 | SS-AUTH-01 | Halaman Login (`/login`) | Form login lengkap (email, password, ingat saya, tombol masuk) | TC-AUTH-001 |
| 2 | SS-AUTH-02 | Login berhasil → Dashboard Admin | Redirect berhasil, nama user tampil di sidebar | TC-AUTH-001 |
| 3 | SS-AUTH-03 | Login gagal — password salah | Pesan error: *"Email atau password salah"* | TC-AUTH-006 |
| 4 | SS-AUTH-04 | Login gagal — email kosong | Validasi HTML5 muncul di field email | TC-AUTH-008 |
| 5 | SS-AUTH-05 | Halaman Register (`/register`) | Form registrasi lengkap (nama, email, password, konfirmasi) | TC-REG-001 |
| 6 | SS-AUTH-06 | Register berhasil → Halaman Setup | Redirect ke `/admin/organization/setup` | TC-REG-001 |
| 7 | SS-AUTH-07 | Register gagal — email duplikat | Pesan error: *"The email has already been taken"* | TC-REG-002 |
| 8 | SS-AUTH-08 | Logout berhasil | Kembali ke halaman `/login` | TC-AUTH-014 |

---

## B. MODUL ADMIN (14 Screenshot)

> **Login sebagai:** `admin@demo.danakarya.id` / `Demo@123!`

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 9 | SS-ADM-01 | Dashboard Admin (`/admin/dashboard`) | 4 kartu statistik + tabel pinjaman & transaksi terbaru | TC-ADM-001 |
| 10 | SS-ADM-02 | Profil Koperasi (`/admin/organization`) | Form profil terisi lengkap (nama, alamat, logo) | TC-ADM-002 |
| 11 | SS-ADM-03 | Aturan Keuangan (`/admin/rules`) — Form | Semua parameter terisi (simpanan, bunga, alokasi SHU) | TC-ADM-005 |
| 12 | SS-ADM-04 | Aturan Keuangan — Error SHU ≠ 100% | Pesan error: *"Total alokasi SHU harus 100%"* | TC-ADM-006 |
| 13 | SS-ADM-05 | Daftar Anggota (`/admin/members`) | Tabel anggota dengan kolom nama, email, role, status | TC-MBR-012 |
| 14 | SS-ADM-06 | Tambah Anggota (`/admin/members/create`) | Form input lengkap (nama, email, role, gaji, dll) | TC-MBR-001 |
| 15 | SS-ADM-07 | Tambah Anggota — Berhasil | Flash message *"Anggota berhasil ditambahkan"* | TC-MBR-001 |
| 16 | SS-ADM-08 | Tambah Anggota — Email duplikat | Pesan error email sudah ada | TC-MBR-003 |
| 17 | SS-ADM-09 | Edit Anggota (`/admin/members/{id}/edit`) | Form edit terisi data anggota | TC-MBR-006 |
| 18 | SS-ADM-10 | Import CSV — Form upload | Tombol download template + form upload CSV | TC-MBR-009 |
| 19 | SS-ADM-11 | Payroll — Halaman utama (`/admin/payroll`) | 4 kartu ringkasan + tabel tagihan per karyawan | TC-PAY-001 |
| 20 | SS-ADM-12 | Payroll — Export CSV/PDF | Tombol export dan file terunduh | TC-PAY-001 |
| 21 | SS-ADM-13 | Payroll — Form import (tanggal benar) | Form upload CSV aktif | TC-PAY-002 |
| 22 | SS-ADM-14 | Payroll — Import terkunci (tanggal salah) | Alert: *"Import Terkunci: hanya pada tanggal X"* | TC-PAY-003 |

---

## C. MODUL PENGURUS (18 Screenshot)

> **Login sebagai:** `pengurus@demo.danakarya.id` / `Demo@123!`

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 23 | SS-PGR-01 | Dashboard Kasir (`/pengurus/dashboard`) | Ringkasan operasional harian | — |
| 24 | SS-PGR-02 | Loket Simpanan (`/pengurus/deposits`) | Tabel setoran dengan filter bulan/tahun | TC-DEP-001 |
| 25 | SS-PGR-03 | Input Setoran (`/pengurus/deposits/create`) | Form: pilih anggota, jenis, jumlah | TC-DEP-001 |
| 26 | SS-PGR-04 | Setoran Berhasil | Flash message *"Setoran berhasil dicatat"* | TC-DEP-001 |
| 27 | SS-PGR-05 | Setoran Pokok Duplikat — Error | Pesan error: *"Anggota sudah memiliki simpanan pokok"* | TC-DEP-003 |
| 28 | SS-PGR-06 | Penarikan Sukarela (`/pengurus/deposits/withdrawals`) | Daftar penarikan + tombol Approve/Reject | TC-DEP-006 |
| 29 | SS-PGR-07 | Approve Penarikan — Berhasil | Flash message sukses approve | TC-DEP-006 |
| 30 | SS-PGR-08 | Daftar Pinjaman (`/pengurus/loans`) | Tabel pinjaman + filter status | TC-LPN-001 |
| 31 | SS-PGR-09 | Detail Pinjaman (`/pengurus/loans/{id}`) | Info pinjaman + credit score + jadwal cicilan | TC-LPN-009 |
| 32 | SS-PGR-10 | Approve Pinjaman — Berhasil | Flash message pinjaman disetujui | TC-LPN-001 |
| 33 | SS-PGR-11 | Reject Pinjaman — Form alasan | Form input alasan penolakan | TC-LPN-005 |
| 34 | SS-PGR-12 | Bayar Angsuran — Berhasil | Flash message angsuran berhasil dibayar | TC-LPN-007 |
| 35 | SS-PGR-13 | Bagan Akun / COA (`/pengurus/accounting/coa`) | Tabel daftar kode akun (1-101, 2-201, dst) | TC-ACC-001 |
| 36 | SS-PGR-14 | Buku Jurnal (`/pengurus/accounting/journals`) | Tabel jurnal umum (referensi, tanggal, debet, kredit) | TC-ACC-003 |
| 37 | SS-PGR-15 | Laporan Buku Kas (`/pengurus/reports/kas`) | Rekap kas harian per bulan | TC-RPT-001 |
| 38 | SS-PGR-16 | Neraca (`/pengurus/reports/neraca`) | Posisi keuangan: Aset = Kewajiban + Modal | TC-RPT-002 |
| 39 | SS-PGR-17 | Laba Rugi (`/pengurus/reports/laba-rugi`) | Pendapatan, Beban, Laba Bersih | TC-RPT-003 |
| 40 | SS-PGR-18 | Distribusi SHU (`/pengurus/reports/shu`) | 3 kartu (Pendapatan, Beban, Laba Bersih) + tabel distribusi | TC-RPT-005 |

---

## D. MODUL PENGAWAS (8 Screenshot)

> **Login sebagai:** `pengawas@demo.danakarya.id` / `Demo@123!`

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 41 | SS-PWS-01 | Dashboard Pengawas (`/pengawas/dashboard`) | Grafik & indikator keuangan | TC-AWA-001 |
| 42 | SS-PWS-02 | Audit Keuangan — Jurnal (`/pengawas/audit-finance`) | Daftar jurnal (read-only, TANPA tombol CRUD) | TC-AWA-002 |
| 43 | SS-PWS-03 | Buku Besar/Ledger (`/pengawas/audit-finance/ledger`) | Detail saldo per akun (read-only) | TC-AWA-003 |
| 44 | SS-PWS-04 | Neraca Saldo (`/pengawas/audit-finance/neraca`) | Neraca tampil (read-only) | TC-AWA-004 |
| 45 | SS-PWS-05 | Laporan SHU (`/pengawas/audit-finance/shu`) | Distribusi SHU per tahun | TC-AWA-005 |
| 46 | SS-PWS-06 | Rekap Simpanan (`/pengawas/audit-finance/simpanan`) | Simpanan per anggota (read-only) | — |
| 47 | SS-PWS-07 | Audit Trail (`/pengawas/audit-trail`) | Log aktivitas: aksi, user, waktu, IP | TC-AWA-008 |
| 48 | SS-PWS-08 | Export PDF — Contoh file PDF | Hasil download PDF laporan (buka di viewer) | TC-AWA-006 |

---

## E. MODUL ANGGOTA (10 Screenshot)

> **Login sebagai:** `rina.kusuma@demo.danakarya.id` / `Demo@123!`

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 49 | SS-MBR-01 | Dashboard Anggota (`/member/dashboard`) | 4 kartu saldo + pinjaman aktif + transaksi terbaru | TC-MEM-001 |
| 50 | SS-MBR-02 | Tabungan Saya (`/member/my-deposits`) | Tabel mutasi simpanan (tanggal, jenis, jumlah, status) | TC-MEM-002 |
| 51 | SS-MBR-03 | Ajukan Penarikan (`/member/my-deposits/withdraw`) | Form penarikan + kartu saldo + panel ketentuan | TC-MEM-004 |
| 52 | SS-MBR-04 | Penarikan Berhasil Diajukan | Flash message pengajuan berhasil | TC-MEM-004 |
| 53 | SS-MBR-05 | Fasilitas Kredit (`/member/my-loans`) | Daftar riwayat pinjaman | TC-KRD-004 |
| 54 | SS-MBR-06 | Ajukan Pinjaman (`/member/my-loans/apply`) | Form + kalkulator simulasi (jumlah, tenor, cicilan) | TC-KRD-004 |
| 55 | SS-MBR-07 | Kalkulator — Hasil simulasi | Cicilan, total bunga, credit score tampil | TC-KRD-001 |
| 56 | SS-MBR-08 | Kartu Piutang (`/member/my-loans/{id}/card`) | Detail pinjaman + jadwal cicilan per bulan | TC-KRD-009 |
| 57 | SS-MBR-09 | Bonus SHU (`/member/my-shu`) | Rincian JM + JP per tahun | TC-SHU-001 |
| 58 | SS-MBR-10 | Info Keuangan (`/member/rules`) | Aturan koperasi (simpanan, bunga, plafon, SHU) | — |

---

## F. MODUL SUPERADMIN (3 Screenshot)

> **Login sebagai:** Akun superadmin

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 59 | SS-SUP-01 | Dashboard Superadmin (`/superadmin/dashboard`) | Overview keseluruhan platform | TC-SUP-001 |
| 60 | SS-SUP-02 | Daftar Tenant (`/superadmin/tenants`) | Tabel koperasi (nama, user, status, tombol toggle) | TC-SUP-002 |
| 61 | SS-SUP-03 | Halaman Suspended (`/suspended`) | Pesan koperasi ditangguhkan | TC-SEC-007 |

---

## G. KEAMANAN — AKSES DITOLAK (1 Screenshot)

| No | Kode | Halaman / Kondisi | Yang Harus Terlihat | Test Case |
|----|------|-------------------|---------------------|-----------|
| 62 | SS-SEC-01 | Akses halaman tanpa izin (403) | Halaman 403 Forbidden (misal: anggota akses `/admin`) | TC-SEC-002 |

---

## Ringkasan Total Screenshot

| Modul | Jumlah | Login Yang Digunakan |
|-------|--------|---------------------|
| A. Autentikasi | 8 | Guest / Admin |
| B. Admin | 14 | `admin@demo.danakarya.id` |
| C. Pengurus | 18 | `pengurus@demo.danakarya.id` |
| D. Pengawas | 8 | `pengawas@demo.danakarya.id` |
| E. Anggota | 10 | `rina.kusuma@demo.danakarya.id` |
| F. Superadmin | 3 | Akun superadmin |
| G. Keamanan | 1 | Anggota (akses halaman admin) |
| **TOTAL** | **62** | **5 akun berbeda** |

---

## Tips Pengambilan Screenshot

1. **Gunakan browser Chrome** dalam mode normal (bukan incognito) agar sidebar tampil
2. **Resolusi layar:** Pastikan full-screen atau minimal 1280×720
3. **Urutan login:** Guest → Admin → Pengurus → Pengawas → Anggota → Superadmin
4. **Sertakan URL bar** di screenshot agar terlihat alamat halaman
5. **Flash message** biasanya hilang setelah beberapa detik — screenshot segera setelah aksi
6. **Untuk error testing** (SS-AUTH-03, SS-ADM-04, dll), sengaja input data salah lalu screenshot hasilnya
7. **Format file:** PNG, beri nama sesuai kode (misal `SS-AUTH-01.png`)

> **Password semua akun demo:** `Demo@123!`
