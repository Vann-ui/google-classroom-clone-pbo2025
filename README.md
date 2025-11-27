# 📚 Google Classroom Clone

> 💻 **Tugas Besar Pemrograman Berbasis Objek (PPBO)**

Aplikasi Learning Management System (LMS) sederhana yang dikembangkan untuk memenuhi tugas besar mata kuliah PPBO. Sistem ini menghadirkan fitur utama seperti manajemen materi, assignment, submission mahasiswa, diskusi kelas, hingga pemberian nilai dan feedback.

---

## 🎯 Fitur-Fitur Utama Sistem

### 👨‍🏫 Fitur Dosen

- 🔐 **Register & Login** - Pendaftaran dan masuk ke sistem sebagai dosen
- 💬 **Membuat Diskusi** - Dosen dapat membuat diskusi sebagai sarana pengumuman
- 📖 **Mengelola Material** - Create, Read, Update, Delete material (file upload & external link)
- 📝 **Mengelola Assignment** - CRUD lengkap untuk tugas
- 👀 **Melihat Submission Mahasiswa** - Daftar submission tiap assignment
- ⭐ **Memberikan Score** - Pemberian nilai untuk setiap submission mahasiswa
- 💬 **Memberikan Feedback** - Memberikan komentar dan saran kepada mahasiswa
- 🚪 **Logout** - Keluar dari sistem dengan aman

### 🎓 Fitur Mahasiswa

- 🔐 **Register & Login** - Pendaftaran dan masuk ke sistem sebagai mahasiswa
- 🚪 **Join Class** - Bergabung ke dalam kelas yang tersedia
- 📄 **File Submission** - Upload file (PDF/DOCX/PPT) dengan validasi format otomatis
- 🔗 **Link Submission** - Kirim link (Google Drive, GitHub, dll)
- 📚 **Melihat Material** - Akses semua materi yang dibagikan dosen
- 📝 **Melihat Assignment** - Lihat assignment aktif beserta deadline
- 🗑️ **Hapus Assignment** - Menghapus assignment yang telah dikumpulkan
- 🚪 **Logout** - Keluar dari sistem dengan aman

### ⚙️ Fitur Sistem

- ✅ **Verifikasi Format Dokumen** - Validasi otomatis format file yang diupload
- 🕒 **Created At** - Pencatatan waktu pembuatan otomatis
- 🔄 **Updated At** - Pencatatan waktu pembaruan otomatis

---

## 🧩 Konsep OOP yang Diterapkan

| Konsep | Implementasi |
|--------|--------------|
| 🎭 **Abstraction** | `ClassContent` sebagai abstraksi utama untuk konten kelas |
| 🧬 **Inheritance** | `Material` dan `Assignment` mewarisi `ClassContent` |
| 🔄 **Polymorphism** | `Submission` bisa berupa File atau Link |
| 🔐 **Encapsulation** | Setiap class menggunakan atribut private dan getter/setter |

---

## 👥 Tim Pengembang

| Nama | NIM |
|------|-----|
| Evan Mulya Oktarohmat | H1101241066 |
| Marcello Chrisdiantoro | H1101241041 |
| Nabila Nur Anisa | H1101241013 |
| Syafira Aulianisa | H1101241025 |
| Evelyn | H1101241052 |

---

<div align="center">

**✨ Dibuat dengan ❤️ untuk Pembelajaran**

*© 2025 Google Classroom Clone - Proyek PPBO*

</div>
