<br>
<br>
<p align="center">
<img src="https://res.cloudinary.com/isuk/image/upload/v1719131563/public-assets/ov0ew2dwgjmlwmnffim9.png" width="100" height="100" alt="Citato CAC"/>
</p>

<div>
    <h1 align="center" style="border:0; margin-bottom:0" >Context Awareness Censorship</h1>
    <p align="center">Censor that need a censor!</p>
</div>

## Background
Suatu hari peliharaan Citato sakit. Degan sigap Citato segera mengontak sebuah klinik hewan melalui chat. Kebetulan Klinik Hewan ini menggunakan sebuah chat provider yang sudah mendukung sensorship terhadap kata-kata kasar.

```
    Citato: Halo Klinik Citata, Sekarang klinik masih buka gak ya? saya mau kesana, emergency
    Klinik: Masih Kak, Kalau boleh tahu hewan apa yang mau diperiksakan?
    Citato: ******
            Lah, Kenapa disensor dah :(
            ****** saya yang sakit kak.
            ****** , malah disensor.
            Tadinya gak marah, sekarang malah jadi sebel saya.
            DASAR CHAT APP ******
    Klinik: Mohon maaf kak, kalau nama hewan yang biasa jadi umpatan memang tersensor. 
            Kita sedang usahakan pindah ke provider chat sebelah supaya ini tidak terjadi lagi
            Sekarang Hewannya dibawa saja langsung kak, supaya kami bisa segera memeriksa.
```
Sekarang bayangkan sendiri, kira-kira customer tersebut mengatakan apa. Di kasus terburuk bisa juga terjadi ketika hewan tersebut dibawa ternyata, hewan tersebut tidak bisa dirawat oleh dokter hewan di klinik. 

--------------------

Tentu saja ini tidak hanya terjadi pada kasus diatas, bisa saja terjadi di kasus konsultasi dokter spesialis penyakit kulit dan kelamin? atau Obgyn, atau yang lainnya. 

Bisa juga pada komentar-komentar yang dilayangkan di social media, yang mungkin anak kita membaca komentar tesebut.

Program berdasarkan Blacklist words tidak akan dapat memahami konteks percakapan. Sehingga hasil sensor hanya didasarkan pada ada tidak nya kata yang bernada umpatan. Padahal pada banyak kasus, kata umpatan tersebut bisa berarti baik, tergantung pada konteks. 

## Solution
Salah satu cara untuk mengatasi kelemahan metode blacklist words tadi adalah dengan cara Context Awareness Censorship. Dengan Context Awareness Censorship (CAC), User bisa memanfaatkan kemampuan AI untuk menyensor kata-kata yang memang tidak layak sesuai dengan konteks percakapan namun tetap dapat menampilkan kata-kata tersebut ketika kata-kata tersebut memang dianggap layak sesuai dengan konteks percakapan.

### Jadi Bagaimana CAC Bekerja? 

CAC memiliki 2 API yang akan digunakan oleh Client untuk mendapatkan censorship yang akurat.
1. Bad Word Detection (`/detect`)
    - Digunakan untuk mendeteksi apakah dalam kalimat terdapat kata yang tidak diperbolehkan.
    - Hal ini diperlukan sebagai salah satu cara untuk menghemat token. Jadi Client API CAC tidak perlu untuk melakukan hit ke API CAC detection di setiap kalimat chat yang diterima.
    - Mengapa menghemat token, karena API ini menerima hanya kalimat chat, sedang CAC Detection menerima history chat yang tentu saja ukuran token lebih besar
    - Jika dengan API ini dideteksi adanya kata yang perlu disensor, maka kita perlu memastikan konteksnya dengan API CAC Detection
2. CAC detection (`/detect/cac`)
    - API menerima history percakapan yang akan digunakan untuk memahami konteks chat terakhir yang dikirim.

Dengan memanfaatkan API tersebut secara bersamaan, diharapkan hasil pendeteksian akan lebih akurat. Untuk lebih jelas mengenai cara kerja dari CAC, Berikut ilustrasi alur kerja dari client yang akan melakukan pengecekan kalimat yang akan disensor.

![Alur kerja CAC](https://res.cloudinary.com/isuk/image/upload/v1719128645/public-assets/sixygwis2slswlmhwoeh.png "Alur Kerja CAC")


Digunakan untuk mendeteksi suatu kalimat mengandung kata yang tidak diperbolehkan berdasarkan context percakapan.
Kita coba provide API yang akan me-return :
1. Perlukah kalimat disensor
2. Kalimat tersebut perlu disensor kata yang mana saja.


### Mengapa Solusi Dalam Bentuk API? 
Kenapa API, karena dengan API, 
1. produk qiscus yang manapun bisa memakai, SDK, OC, bahkan Robolabs. 
2. Selain itu API ini bahkan bisa dipakai oleh pihak lain tanpa kecuali. 
    - Sosmed? 
    - Section Diskusi dari kolom komentar berita
    - Chat Konsultasi Dokter.
    - dll


## Tech
Kami menggunakan Model AI `Llama3-70B-8192` yang di sediakan API nya oleh Groq. Pertimbangan penggunaaan API dari Groq
1. Setelah kami coba untuk mentraining model kecil dengan data training yang ada, bahkan dengan GPU dengan VRAM 8GB, Kami selalu menemui `Out of Memory`, Menggunakan layanan semacam google colabs juga akan menghabiskan biaya yang tidak bisa dibilang sedikit. 
2. Training sendiri untuk saat ini, dengan waktu yang sangat terbatas sangat menghabiskan waktu dan effort. 

Teknologi yang kami pakai dalam pengembangan :
1. Laravel 11 (API). 
2. Nuxt (Demo Chat)
3. Postgress

## Masalah Yang Mungkin Terjadi
Menggunakan CAC bukan berarti tidak akan terjadi masalah. Manusia normal saja, terkadang masih sering keliru memaknai kalimat ambigu ataupun satire. Jadi masalah apa yang mungkin muncul saat memakai CAC
1. Hasil deteksi tidak akurat. Namun dengan fine tuning memanfaatkan data yang baik, akurasi dapat dibenahi perlahan.
2. Bisa saja saat deteksi badword di suatu kalimat, API kami mendeteksi tidak ada kata yang bermakna buruk atau makian, padahal sebenarnya ketika dimasukkan ke dalam konteks, bisa jadi kata tersebut berarti buruk
3. Client bebas menggunakan Bad Word Detection milik sendiri, namun kemungkinan terjadi masalah perbedaan hasil deteksi dengan API kami sangat mungkin terjadi.


## What Next?
Dalam kesempatan kali ini CAC hanya dapat mensensor kalimat atau kata (text), tetapi possibility ke depan sangatlah besar. Namun hal utama yang perlu dilakukan adalah meningkatkan akurasi deteksi.
- Meningkatkan Akurasi deteksi dengan data training yang tepat akan membuat CAC lebih menghasilkan sensor yang berkualitas.
- Deteksi image
    - Dokter, Pengacara, atau siapapun yang menerima konsultasi melalui chat, dapat menfilter image yang diterima, Kasus Sexual Harassment jelas akan berkurang.


## Demo
Demo API kami dapat diakses menggunakan Insomnia dan mengimport [JSON](https://res.cloudinary.com/isuk/raw/upload/v1725875067/citato_o8yech.json) yang tersedia.

Repository Code juga dapat diakses melalui [Github](https://github.com/sunupf/citato)

Dan tak kalah penting adalah demo video yang bisa diakses melalui [Youtube](https://youtu.be/Sj_2t7yWDKw)
