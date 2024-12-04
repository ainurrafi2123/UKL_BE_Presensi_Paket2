# Aplikasi Presensi Online - API

Aplikasi Presensi Online ini adalah solusi untuk mempermudah proses presensi dan pengelolaan data kehadiran bagi pengguna. API ini memungkinkan siswa atau karyawan untuk melakukan presensi, memeriksa riwayat absen, dan melihat analisis kehadiran mereka. Proyek ini dikembangkan menggunakan framework Laravel dengan PHP dan menggunakan JWT (JSON Web Token) untuk autentikasi berbasis token.

## Fitur Utama

1. **Pengelolaan Pengguna**:
   - **Register & Login**: Pengguna dapat melakukan registrasi dan login untuk mendapatkan token autentikasi.
   - **Role-based Access**: Hanya karyawan yang dapat mengakses endpoint tertentu untuk membuat, mengubah, dan menghapus data pengguna.
   - **Get, Update, Delete User**: API memungkinkan admin untuk mendapatkan data pengguna, memperbarui data pengguna, dan menghapus pengguna.

2. **Attendance (Presensi)**:
   - **Absen**: Pengguna dapat melakukan presensi atau absen.
   - **History**: Pengguna dapat melihat riwayat absen mereka.
   - **Summary**: Menyediakan ringkasan kehadiran.
   - **Analisis**: Menampilkan analisis berdasarkan data kehadiran yang ada.

## Prasyarat

- PHP 8.3.x
- Laravel 10.x
- Composer
- MySQL (atau database lainnya)

## Cara Install
 
1. Clone repositori ini:
   ```bash
   git clone https://github.com/ainurrafi2123/UKL_BE_Presensi_Paket2.git

2. Masuk ke direktori proyek:
   ```bash
   cd UKL_BE_Presensi_Paket2

3. Install dependensi menggunakan Composer:
   ```bash
   composer install

4. Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env

5. Generate kunci aplikasi:
   ```bash
   php artisan key:generate

6. Jalankan migrasi database:
   ```bash
   php artisan migrate

7. Jalankan server:
   ```bash
   php artisan serve

## Dokumentasi API

### 1. **Autentikasi**

- **POST** `/api/register` - Untuk registrasi pengguna baru
  - **Body Request**: 
    ```json
    {
      "name": "Nama Pengguna",
      "email": "email@example.com",
      "password": "password123",
      "password_confirmation": "password123",
      "role": "siswa|karyawan"
    }
    ```
  - **Response**: Mengembalikan token autentikasi.
  - **Contoh Response**:
    ```json
    {
    "success": true,
    "user": {
        "name": "jada",
        "username": "amin",
        "role": "siswa",
        "id": 7
    }
    ```

- **POST** `/api/login` - Untuk login dan mendapatkan token
  - **Body Request**:
    ```json
    {
      "username": "Iku",
      "password": "password123"
    }
    ```
  - **Response**: Mengembalikan token autentikasi.
  - **Contoh Response**:
    ```json
    {
        {
        "success": true,
        "user": {
            "id": 3,
            "name": "Yuki",
            "username": "yuks",
            "role": "karyawan"
        },
        "token": "exxxxx"
    }

    ```

- **POST** `/api/logout` - Untuk logout dan menghapus token
  - **Body Request**: Tidak ada.
  - **Response**: Mengembalikan pesan sukses jika logout berhasil.
  - **Contoh Response**:
    ```json
    {
      "message": "Logout successful"
    }
    ```

- **GET** `/api/user` - Untuk mendapatkan data pengguna yang sedang login (dengan autentikasi)
  - **Header**: `Authorization: Bearer {token}`
  - **Response**: Mengembalikan data pengguna.
  - **Contoh Response**:
    ```json
    {
      "id": 1,
      "name": "John Doe",
      "email": "email@example.com",
      "role": "karyawan"
    }
    ```

### 2. **Pengelolaan Pengguna** (Hanya untuk Karyawan)

- **GET** `/api/getuser/{id?}` - Untuk mendapatkan data pengguna berdasarkan ID (optional, jika tidak diberikan akan mengembalikan data pengguna yang login)
  - **Header**: `Authorization: Bearer {token}`
  - **Response**: Data pengguna berdasarkan ID.
  - **Contoh Response**:
    ```json
    {
      "id": 1,
      "name": "John Doe",
      "email": "email@example.com",
      "role": "karyawan"
    }
    ```

- **PUT** `/api/updateuser/{id}` - Untuk mengupdate data pengguna berdasarkan ID
  - **Header**: `Authorization: Bearer {token}`
  - **Body Request**: 
    ```json
    {
      "name": "Updated Name",
      "email": "updated_email@example.com",
      "password": "newpassword123",
      "role": "siswa|karyawan"
    }
    ```
  - **Response**: Mengembalikan data pengguna yang telah diperbarui.
  - **Contoh Response**:
    ```json
    {
      "id": 1,
      "name": "Updated Name",
      "email": "updated_email@example.com",
      "role": "karyawan"
    }
    ```

- **DELETE** `/api/deleteuser/{id}` - Untuk menghapus pengguna berdasarkan ID
  - **Header**: `Authorization: Bearer {token}`
  - **Response**: Status penghapusan pengguna.
  - **Contoh Response**:
    ```json
    {
      "message": "User deleted successfully"
    }
    ```

### 3. **Presensi**

- **POST** `/api/attendance` - Untuk melakukan absen
  - **Header**: `Authorization: Bearer {token}`
  - **Body Request**:
    ```json
    {
      "user_id": 1,
      "date": "2024-12-04",
      "status": "hadir|sakit|izin"
    }
    ```
  - **Response**: Status absen yang berhasil dicatat.
  - **Contoh Response**:
    ```json
    {
      "message": "Attendance recorded successfully"
    }
    ```

- **GET** `/api/attendance/history/{user_id}` - Untuk melihat riwayat absen pengguna berdasarkan ID
  - **Header**: `Authorization: Bearer {token}`
  - **Response**: Daftar riwayat absen pengguna.
  - **Contoh Response**:
    ```json
    [
      {
        "date": "2024-12-01",
        "status": "hadir"
      },
      {
        "date": "2024-12-02",
        "status": "sakit"
      }
    ]
    ```

- **GET** `/api/attendance/summary/{user_id}` - Untuk melihat ringkasan kehadiran berdasarkan ID pengguna
  - **Header**: `Authorization: Bearer {token}`
  - **Response**: Ringkasan data kehadiran.
  - **Contoh Response**:
    ```json
    {
      "total_hadir": 15,
      "total_izin": 3,
      "total_sakit": 2
    }
    ```

- **POST** `/api/attendance/analysis` - Untuk analisis kehadiran berdasarkan data yang ada
  - **Body Request**:
    ```json
    {
      "user_id": 1,
      "month": "12",
      "year": "2024"
    }
    ```
  - **Response**: Analisis kehadiran pengguna.
  - **Contoh Response**:
    ```json
    {
      "attendance_rate": "85%",
      "on_time": 90,
      "late": 5
    }
    ```

---

Dengan dokumentasi ini, setiap endpoint API yang telah kamu buat sudah terjelaskan dengan jelas untuk pengembang atau pengguna yang ingin mengakses dan menggunakan API tersebut. Jika ada tambahan lainnya atau hal yang perlu diperbaiki, beri tahu saja!





