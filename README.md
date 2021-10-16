## APLIKASI PELAPORAN HOAKS

Aplikasi ini merupakan hasil tugas akhir D3 saya. Semoga lulus dan ilmunya bermanfaat. Aamiiin....

Fiturnya belum wah sih, karena mepet cuy buat tugas akhir. Bikinnya dikebut 1 minggu backend sama frontendnya wkwkwkwk 

Fitur backend ini begini nih :
- Hanya API saja, tidak dengan tampilan
- Menambahkan aduan berita oleh masyarakat
- Menambahkan klarifikasi aduan berita oleh admin (pihak yang memiliki kewenangan menilai berita)
- auth (login, register)
- memakai JWT untuk token API nya

Oh ya ini yang mau frontendnya silahkan cek di : https://github.com/MuhammadIhsanAnsory/vue_hoaks_frontend_tugas_akhir

Untuk akun dengan role admin, khusus saya simpan di seeder.

Untuk menjalankan aplikasinya begini :
- copy file .env.example lalu rename jadi .env
- bikin database samakan dengan nama database di .env
- run command 
    1. composer install
    2. php artisan key:generate
    3. php artisan jwt:secret
    4. php artisan migrate
    5. php artisan db:seed     (ini untuk menambahkan akun admin)
    6. php artisan serve

Kalau ada pertanyaan silahkan DM saya di sosial media :
Instagram : <a href="https://instagram.com/muhammadihsanansory_">@muhamadihsanansory_</a>
Facebook : Muhamad Ihsan Ansory

Follow my Github !
Github : <a href="https://github.com/MuhammadIhsanAnsory">https://github.com/MuhammadIhsanAnsory</a>
