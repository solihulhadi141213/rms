# Radix  
**Radiology Management System**

Radix adalah aplikasi **Radiology Management System (RMS)** yang dirancang untuk membantu pengelolaan layanan radiologi secara terintegrasi, aman, dan efisien. Aplikasi ini mendukung koneksi dengan berbagai sistem eksternal seperti **SIMRS**, **SATU SEHAT**, dan **PACS**, sehingga memudahkan pertukaran data klinis dan operasional radiologi.

---

## Fitur Utama

- Pengaturan aplikasi  
  (pengaturan tampilan, base URL, favicon, dan konfigurasi umum)
- Pengaturan koneksi **Email Gateway**
- Pengelolaan **Pengguna**
- Koneksi API ke **SIMRS**
- Koneksi API ke **SATU SEHAT**
- Koneksi API ke **PACS**
- Pengelolaan **API Key**  
  (agar aplikasi lain dapat mengakses sumber daya Radix)
- Pengelolaan **Kode Klinis**  
  (mapping kode dari SATU SEHAT)
- Pengelolaan **Kode Pemeriksaan Radiologi**  
  (mapping ke SATU SEHAT)
- Pengelolaan **Order Radiologi**
- **Log aktivitas pengguna**
- Pengelolaan **Profil Pengguna**
- Fitur **Lupa Password**

---

## Teknologi yang Digunakan

Aplikasi Radix dikembangkan menggunakan teknologi berikut:

- **Database**: MySQL 9.1.0  
- **Backend**: PHP 8.0.30  
- **Web Server**: Apache 2.4.62.1  

---

## Dependency / Package

Dependency frontend dikelola menggunakan **npm** dengan daftar package berikut:

```json
{
  "dependencies": {
    "html2canvas": "^1.4.1",
    "jquery": "^3.7.1",
    "jspdf": "^3.0.0",
    "marked": "^15.0.7",
    "mdb-ui-kit": "^8.2.0",
    "signature_pad": "^5.0.4",
    "sweetalert2": "^11.17.2"
  }
}
