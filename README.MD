# Context Awareness Censorship

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
Sekarang bayangkan sendiri, kira-kira customer tersebut mengatakan apa. Bisa juga terjadi ketika hewan tersebut dibawa ternyata, hewan tersebut tidak bisa dirawat oleh dokter hewan di klinik. 

Tentu saja ini tidak hanya terjadi pada kasus diatas, bisa saja terjadi di kasus konsultasi dokter spesialis penyakit kulit dan kelamin? atau Obgyn, atau yang lainnya. 

Bisa juga pada komentar-komentar yang dilayangkan di social media, yang mungkin anak kita membaca komentar tesebut.


Blacklist words untuk proses censorship memiliki kelemahan akan konteks. Chat App akan selalu berusaha men-sensor kata yang terdaftar dalam blacklist.

```
    Kan blacklistnya bisa di config?.
``` 

Tentu saja, kalau kamu berniat menconfignya setiap menemukan kasus umpatan tak tersensor. atau menghapusnya dari blacklist ketika kamu menemukan kata yang seharusnya tidak tersensor justru tergantikan dengan karakter bintang.

Dengan Context Awareness Censorship, User bisa memanfaatkan kemampuan AI untuk menyensor kata-kata yang memang tidak layak sesuai dengan konteks percakapan namun tetap dapat menampilkan kata-kata tersebut ketika kata-kata tersebut memang dianggap layak sesuai dengan konteks percakapan.

## Solution
Kita coba provide API yang akan me-return :
1. Perlukah kalimat disensor
2. Kalimat tersebut perlu disensor kata yang mana saja.

Kenapa API, karena dengan provide API, produk qiscus yang manapun bisa memakai, SDK, OC, bahkan Robolabs. Selain itu API ini bahkan bisa dipakai oleh pihak lain tanpa kecuali.

API hanya membutuhkan
1. Riwayat percakapan. atau
2. sebuah paragraph utuh (akurasi dipertanyakan, karena konteks akan kabur kalau kalimatnya pendek)

## Tech
Kita memakai model xxxx,

## Flow Chart


## Demo