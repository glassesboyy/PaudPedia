# Pendahuluan

Entity Relationship Diagram (ERD) merupakan sebuah model konseptual yang memetakan struktur logis dari basis data dengan mendefinisikan entitas-entitas yang terlibat beserta hubungan yang mengikatnya. Pada pengembangan platform Paudpedia, penyusunan ERD memegang peranan krusial sebagai cetak biru (*blueprint*) arsitektur data guna memastikan bahwa seluruh kebutuhan fungsional sistem, mulai dari pendaftaran siswa hingga transaksi pembayaran, dapat diakomodasi secara komprehensif dan bebas dari redundansi.

Mengingat skala kompleksitas platform yang mengusung konsep *Software as a Service* (SaaS) *multi-tenant* dan *marketplace*, arsitektur ERD ini dibagi menjadi tiga subsistem utama, yaitu Subsistem SIAKAD, Subsistem Public B2C, dan Subsistem Admin Panel. Pembagian ini dilakukan untuk mempermudah proses analisis, perancangan, implementasi, maupun pemeliharaan sistem di masa mendatang. Pendekatan modular ini memungkinkan pengembang untuk berfokus pada logika bisnis yang spesifik pada setiap modul tanpa kehilangan konteks relasional secara keseluruhan.

Meskipun secara konseptual dan dokumentasi dibagi ke dalam beberapa subsistem yang terpisah, seluruh tabel dan entitas tersebut beroperasi secara fisik di dalam satu basis data tunggal yang saling terintegrasi dinamis. Integrasi lintas subsistem ini dijembatani oleh keberadaan *shared entity* (entitas berbagi) seperti entitas otentikasi pengguna, yang memastikan bahwa aliran data antar modul dapat berjalan secara mulus tanpa mengorbankan integritas konsistensi data secara arsitektural.

<br><br>

# ERD Untuk Subsistem SIAKAD (14 Entitas)

Subsistem Sistem Informasi Akademik (SIAKAD) dirancang khusus untuk memfasilitasi kebutuhan manajemen operasional harian pada berbagai lembaga Pendidikan Anak Usia Dini (PAUD). Subsistem ini mengadopsi arsitektur *multi-tenant*, yang berarti platform mampu melayani banyak sekolah secara independen di dalam satu lingkungan aplikasi. Ruang lingkup subsistem ini mencakup pengelolaan data induk institusi, struktur kepegawaian (guru dan staf), pendaftaran siswa dan profil wali murid, pencatatan presensi harian, hingga pelaporan penilaian perkembangan anak dan manajemen penagihan finansial.

[Diagram ERD Subsistem SIAKAD]

Fungsi utama dari Subsistem SIAKAD adalah menjaga privasi dan batasan akses data antar-sekolah secara mutlak, di mana seluruh entitas transaksional maupun relasional sangat bergantung pada entitas institusi sebagai payung utamanya. Keterkaitan subsistem ini terhadap sistem secara keseluruhan sangatlah masif, sebab ia menjadi penggerak utama (*core engine*) bagi fungsionalitas layanan B2B (*Business-to-Business*) aplikasi. Data yang dikelola dalam SIAKAD ini nantinya akan terhubung secara terpusat dengan modul otentikasi global, memastikan pengawasan yang tersentralisasi bagi para pemangku kepentingan seperti kepala sekolah, guru, dan orang tua.

## Entitas: School

**Atribut**
- id
- name
- npsn
- address
- phone
- email
- logo_url
- subscription_plan
- subscription_started_at
- subscription_ended_at
- is_active

**Relasi**
- One to Many dengan Entitas SchoolMember (Satu School memiliki banyak SchoolMember sebagai struktur keanggotaan pengguna di dalamnya.)
- One to Many dengan Entitas ClassRoom (Satu School menyelenggarakan banyak ClassRoom atau rombongan belajar.)
- One to Many dengan Entitas Teacher (Satu School menaungi banyak Teacher sebagai staf pengajarnya.)
- One to Many dengan Entitas ParentProfile (Satu School memiliki banyak ParentProfile sebagai data profil orang tua/wali murid.)
- One to Many dengan Entitas Student (Satu School mendidik banyak Student anak usia dini.)
- One to Many dengan Entitas Attendance (Satu School memiliki banyak catatan rekam Attendance dari berbagai kelas.)
- One to Many dengan Entitas Assessment (Satu School menerbitkan banyak evaluasi Assessment rapor murid.)
- One to Many dengan Entitas DevelopmentProgram (Satu School mengonfigurasi banyak program indikator nilai akademik.)
- One to Many dengan Entitas Finance (Satu School mencatat berbagai riwayat pembayaran tagihan SPP dan tabungan.)
- One to Many dengan Entitas SubscriptionOrder (Satu School dapat membuat banyak riwayat langganan perpanjangan akun Pro.)

---

## Entitas: SchoolMember

**Atribut**
- id
- school_id
- user_id
- role_type
- is_active
- joined_at

**Relasi**
- Many to One dengan Entitas School (Setiap SchoolMember terdaftar eksklusif pada satu instansi School tertentu.)
- Many to One dengan Entitas User (Setiap otoritas SchoolMember diwakili dan dimiliki oleh satu kredensial global User.)

---

## Entitas: ClassRoom

**Atribut**
- id
- school_id
- homeroom_teacher_id
- name
- level
- capacity
- academic_year

**Relasi**
- Many to One dengan Entitas School (Setiap ClassRoom didirikan dan bernaung di bawah satu School.)
- Many to One dengan Entitas Teacher (Setiap ruangan ClassRoom memiliki satu Teacher sebagai wali penanggung jawab kelas (homeroom_teacher).)
- One to Many dengan Entitas Student (Satu ClassRoom ditempati secara fisik oleh banyak Student.)
- One to Many dengan Entitas Attendance (Satu ClassRoom mewadahi banyak kegiatan absen harian Attendance.)
- One to Many dengan Entitas Assessment (Satu ClassRoom menjadi titik temu pelaksanaan ragam Assessment murid.)

---

## Entitas: Teacher

**Atribut**
- id
- user_id
- school_id
- nip
- specialization
- bio -> ini hilangkan

**Relasi**
- Many to One dengan Entitas User (Setiap profil Teacher mengandalkan satu akun otentikasi global User.)
- Many to One dengan Entitas School (Setiap Teacher mengabdi di satu instansi School.)
- One to Many dengan Entitas ClassRoom (Seorang Teacher dapat mengabdi sebagai penanggung jawab wali bagi banyak riwayat ClassRoom di tahun-tahun ajaran berbeda.)
- One to Many dengan Entitas Attendance (Seorang guru Teacher bertugas melaporkan banyak Attendance siswa.) --- terakhir sampe sini
- One to Many dengan Entitas Assessment (Seorang guru Teacher memberikan dan mensahkan banyak catatan nilai Assessment ke muridnya.)

---

## Entitas: ParentProfile

**Atribut**
- id
- school_id
- user_id
- email
- father_name
- mother_name
- phone
- father_occupation
- mother_occupation
- address

**Relasi**
- Many to One dengan Entitas School (Setiap profil ParentProfile dibentuk di dalam lingkungan satu institusi School.)
- Many to One dengan Entitas User (Setiap ParentProfile menempel pada satu akun User yang bertindak sebagai wali login.)
- One to Many dengan Entitas Student (Satu profil keluarga ParentProfile dapat mewali serta mengelola banyak biodata Student (anak) sekaligus di sebuah sekolah.)

---

## Entitas: Student

**Atribut**
- id
- school_id
- class_id
- parent_profile_id
- name
- nisn
- birth_date
- gender
- address
- photo_url
- enrollment_date
- status

**Relasi**
- Many to One dengan Entitas School (Setiap anak Student secara mutlak terdaftar di satu School.)
- Many to One dengan Entitas ClassRoom (Setiap Student diplot atau dipetakan pada satu ClassRoom rombel belajar pada satu semester berjalan.)
- Many to One dengan Entitas ParentProfile (Setiap anak Student diasuh mutlak oleh satu entitas keluarga ParentProfile.)
- One to Many dengan Entitas Attendance (Setiap Student memiliki rekam jejak panjang presensi Attendance harian.)
- One to Many dengan Entitas Assessment (Setiap Student memiliki banyak riwayat penilaian rapor kualitatif Assessment.)
- One to Many dengan Entitas Finance (Setiap Student memiliki berbagai kewajiban catatan tagihan SPP dan tabungan Finance.)
- One to Many dengan Entitas StudentReport (Setiap Student dapat menghasilkan banyak cetakan akhir rapor StudentReport.)

---

## Entitas: Attendance

**Atribut**
- id
- student_id
- class_id
- date
- status
- notes
- proof_file_path

**Relasi**
- Many to One dengan Entitas Student (Catatan harian Attendance ini mendeskripsikan kehadiran satu orang Student.)
- Many to One dengan Entitas ClassRoom (Kegiatan presensi Attendance ini diadakan pada ruang lingkup satu ClassRoom tertentu.)

---

## Entitas: Assessment

**Atribut**
- id
- student_id
- indicator_id
- assessment_month
- scale
- semester
- academic_year
- notes
- assessed_at

**Relasi**
- Many to One dengan Entitas Student (Sebuah catatan nilai Assessment diperuntukkan bagi perkembangan satu Student.)
- Many to One dengan Entitas DevelopmentIndicator (Nilai skala evaluasi ini merujuk kepada poin kurikulum DevelopmentIndicator tertentu.)

---

## Entitas: DevelopmentProgram

**Atribut**
- id
- school_id
- name
- order

**Relasi**
- Many to One dengan Entitas School (Kategori kompetensi kurikulum ini diracik khusus untuk satu School.)
- One to Many dengan Entitas DevelopmentIndicator (Satu DevelopmentProgram memayungi secara hierarkis banyak sub-kompetensi penilaian DevelopmentIndicator.)

---

## Entitas: DevelopmentIndicator

**Atribut**
- id
- program_id
- name
- order

**Relasi**
- Many to One dengan Entitas DevelopmentProgram (Setiap instrumen penilai DevelopmentIndicator bernaung dan diorganisir oleh satu DevelopmentProgram induk.)
- One to Many dengan Entitas Assessment (Sebuah DevelopmentIndicator ini akan dipakai berkali-kali sebagai tolak ukur dari puluhan atau ratusan Assessment.)

---

## Entitas: StudentReport

**Atribut**
- id
- school_id
- student_id
- class_id
- teacher_id
- semester
- academic_year
- introduction_notes
- closing_notes
- recommendation

**Relasi**
- Many to One dengan Entitas Student (Buku rekapitulasi StudentReport ini dikeluarkan khusus bagi satu Student.)
- Many to One dengan Entitas Teacher (Dokumen resmi StudentReport ini ditandatangani serta disahkan oleh satu guru Teacher penanggung jawab.)
- One to Many dengan Entitas StudentReportDetail (Satu dokumen utuh StudentReport menyertakan banyak item matriks nilai StudentReportDetail.)

---

## Entitas: StudentReportDetail

**Atribut**
- id
- student_report_id
- program_id
- final_scale
- narrative

**Relasi**
- Many to One dengan Entitas StudentReport (Rincian nilai naratif ini adalah bagian dari kumpulan lembar satu dokumen StudentReport.)
- Many to One dengan Entitas DevelopmentProgram (Hasil agregat matriks evaluatif ini disarikan per-aspek kurikulum dari entitas DevelopmentProgram.)

---

## Entitas: Finance

**Atribut**
- id
- student_id
- type
- amount
- description
- month
- is_paid
- payment_method
- transaction_type
- paid_at
- recorded_by

**Relasi**
- Many to One dengan Entitas Student (Setiap beban kewajiban tagihan SPP maupun rekening celengan ditujukan ke satu siswa Student.)
- Many to One dengan Entitas User (Setiap lembar log penerimaan Finance diverifikasi serta diinput oleh satu pengelola (bendahara/guru) yang terekam pada properti recorded_by dengan rujukan ke tabel global User.)

---

## Entitas: SchoolTransferRequest

**Atribut**
- id
- school_id
- from_user_id
- to_email
- token
- status
- expired_at

**Relasi**
- Many to One dengan Entitas School (Formulir pengajuan pendaftaran dan pindah instansi ditujukan menuju satu School tujuan.)
- Many to One dengan Entitas User (Surat permohonan ini diinisialisasi oleh satu akun User pengaju form (from_user_id).)

<br><br>

# ERD Untuk Subsistem Public B2C (21 Entitas)

Subsistem Public B2C (*Business-to-Consumer*) merupakan wajah publik dari platform Paudpedia yang mewadahi interaksi langsung dengan konsumen secara terbuka, tanpa terikat pada batasan *tenant* institusi sekolah tertentu. Ruang lingkup subsistem ini sangat luas, mencakup operasional *marketplace* untuk katalog produk digital, penyelenggaraan *Learning Management System* (LMS) asinkronus, pendaftaran sesi webinar, hingga portal konten informasi berupa artikel blog dan testimoni. Secara fungsional, subsistem ini menargetkan pengguna mandiri atau masyarakat umum yang ingin mengakses layanan peningkatan kapasitas keilmuan secara langsung.

[Diagram ERD Subsistem Public B2C]

Peran subsistem Public B2C di dalam arsitektur aplikasi adalah sebagai mesin penghasil profit (*revenue generator*) sekunder sekaligus sebagai sarana diseminasi informasi edukasi publik. Dengan memanfaatkan integrasi keranjang belanja, manajemen pesanan, dan perhitungan kode promo yang dinamis, subsistem ini mampu memproses siklus transaksi dari hulu ke hilir secara independen. Lebih lanjut, subsistem ini terintegrasi erat dengan subsistem lain melalui penggunaan entitas otentikasi global sebagai subjek transaksi, sehingga aktivitas pembelajaran mandiri maupun riwayat pembelian pengguna dapat dilacak dan divalidasi oleh administrator pusat.

## Entitas: Category

**Atribut**
- id
- name
- slug
- description
- type

**Relasi**
- One to Many dengan Entitas Course (Satu Category tipe pembelajaran menaungi banyak Course edukasi.)
- One to Many dengan Entitas Product (Satu Category tipe e-commerce dapat mengklasifikasikan ragam Product digital.)
- One to Many dengan Entitas Article (Satu Category publikasi bertugas mengelompokkan ribuan pos Article blog/berita.)

---

## Entitas: Mentor

**Atribut**
- id
- name
- title
- bio
- photo_url
- expertise
- social_media
- is_active

**Relasi**
- One to Many dengan Entitas Webinar (Seorang Mentor dapat menjadi pemateri untuk jadwal tayang banyak sesi Webinar interaktif.)
- One to Many dengan Entitas Course (Seorang pemateri ahli Mentor dapat memproduksi susunan materi banyak Course independen.)

---

## Entitas: Product

**Atribut**
- id
- category_id
- title
- slug
- description
- thumbnail_url
- file_url
- price
- original_price
- is_active

**Relasi**
- Many to One dengan Entitas Category (Setiap arsip Product digital ini dimasukkan ke dalam satu Category tertentu.)
- One to Many (Polymorphic) dengan Entitas OrderItem (Setiap aset Product dapat dipesan ratusan kali ke dalam baris OrderItem milik konsumen.)

---

## Entitas: Webinar

**Atribut**
- id
- mentor_id
- title
- slug
- description
- thumbnail_url
- price
- original_price
- zoom_link
- zoom_meeting_id
- zoom_passcode
- scheduled_at
- duration_minutes
- max_participants
- is_active

**Relasi**
- Many to One dengan Entitas Mentor (Setiap tayangan kelas jarak jauh Webinar diisi dan diampu khusus oleh satu orang Mentor profesional.)
- One to Many (Polymorphic) dengan Entitas OrderItem (Setiap tayangan sesi Webinar dapat ter-checkout masuk ke keranjang dan riwayat OrderItem pembeli di saat bertransaksi.)

---

## Entitas: Course

**Atribut**
- id
- mentor_id
- category_id
- title
- slug
- description
- thumbnail_url
- price
- original_price
- level
- duration_hours
- is_published

**Relasi**
- Many to One dengan Entitas Mentor (Setiap kemasan paket kelas Course dikonstruksi secara berdedikasi oleh satu Mentor.)
- Many to One dengan Entitas Category (Setiap kelas asinkronus Course dikelompokkan pada rumpun keahlian satu Category.)
- One to Many dengan Entitas Module (Satu pokok Course akan dicacah-cacah materi bab utamanya ke dalam puluhan entitas Module.)
- One to Many dengan Entitas CourseEnrollment (Satu akses Course akan dibeli dan dilisensikan kapabilitas hak belajarnya kepada himpunan pendaftar/peserta CourseEnrollment.)
- One to Many (Polymorphic) dengan Entitas OrderItem (Katalog tayang Course dapat ditransaksikan kapan saja pada catatan invoice OrderItem.)

---

## Entitas: Module

**Atribut**
- id
- course_id
- title
- description
- order

**Relasi**
- Many to One dengan Entitas Course (Setiap pecahan unit Module adalah pecahan organik bab silabus turunan dari satu Course induk.)
- One to Many dengan Entitas Lesson (Satu bab Module terdiri atas banyak serpihan file dokumen interaktif berwujud Lesson yang siap dibaca/ditonton.)
- One to Many dengan Entitas Quiz (Satu bab Module dapat memuat penugasan terukur berupa banyak kumpulan Quiz objektif.)

---

## Entitas: Lesson

**Atribut**
- id
- module_id
- title
- content_type
- video_url
- pdf_file
- text_content
- duration_minutes
- order

**Relasi**
- Many to One dengan Entitas Module (Setiap lembar materi asinkronus Lesson terkait dan terurut dalam naungan satu Module.)
- One to Many dengan Entitas LessonProgress (Setiap keping aset media interaktif Lesson akan dipantau riwayat penyelesaian sentuhannya (progress) lewat baris pencatatan log LessonProgress pada ribuan user.)

---

## Entitas: LessonProgress

**Atribut**
- id
- enrollment_id
- lesson_id
- is_completed
- completed_at

**Relasi**
- Many to One dengan Entitas CourseEnrollment (Setiap rekam kemajuan progress LessonProgress berkontribusi menaikkan skor kelulusan keseluruhan untuk satu lisensi CourseEnrollment sang pengguna.)
- Many to One dengan Entitas Lesson (Jejak keberhasilan LessonProgress secara definitif menyatakan tamatnya satu lembar Lesson.)

---

## Entitas: Quiz

**Atribut**
- id
- module_id
- title
- description

**Relasi**
- Many to One dengan Entitas Module (Bentuk penugasan interaktif Quiz selalu bergantung merujuk diletakkan pada penyelesaian bab sebuah Module.)
- One to Many dengan Entitas QuizQuestion (Satu instrumen lembar tugas Quiz menyimpan deretan banyak baris butir soal QuizQuestion.)
- One to Many dengan Entitas QuizAttempt (Satu penugasan Quiz akan dieksekusi simulasi pengerjaannya oleh pengguna melahirkan banyak log QuizAttempt.)

---

## Entitas: QuizQuestion

**Atribut**
- id
- quiz_id
- question

**Relasi**
- Many to One dengan Entitas Quiz (Bentuk butir pertanyaan soal terikat secara ketat bersama satu lembar lembar tugas Quiz.)
- One to Many dengan Entitas QuizAnswer (Satu pertayaan spesifik QuizQuestion mutlak menyimpan setidaknya dua/empat ragam opsi jawaban ganda QuizAnswer yang menemaninya.)

---

## Entitas: QuizAnswer

**Atribut**
- id
- quiz_question_id
- answer
- is_correct

**Relasi**
- Many to One dengan Entitas QuizQuestion (Sebuah butir opsi distraktor QuizAnswer menempel kepada batang soal QuizQuestion.)

---

## Entitas: QuizAttempt

**Atribut**
- id
- quiz_id
- user_id
- enrollment_id
- score
- total_questions
- percentage
- is_passed
- started_at
- completed_at

**Relasi**
- Many to One dengan Entitas Quiz (Pengerjaan rekam hasil percobaan interaktif ini dikhususkan pada penyelesaian satu tugas Quiz.)
- Many to One dengan Entitas User (Rekaman log QuizAttempt sepenuhnya diverifikasi atas nama satu orang pelaksana otentik kredensial User.)
- Many to One dengan Entitas CourseEnrollment (Penempatan persentase kelulusan kuis ini menjadi parameter tamat/tidaknya sebuah lisensi kelas tunggal CourseEnrollment.)
- One to Many dengan Entitas QuizAttemptAnswer (Satu siklus utuh usaha pengerjaan (QuizAttempt) beranak-pinak melahirkan log serpihan klik detail dari pengguna untuk merujuk pada banyak rekaman log jawaban mentah (QuizAttemptAnswer).)

---

## Entitas: QuizAttemptAnswer

**Atribut**
- id
- quiz_attempt_id
- quiz_question_id
- selected_answer_id
- is_correct

**Relasi**
- Many to One dengan Entitas QuizAttempt (Catatan *draft* jawaban kuis milik pengguna menyambung secara utuh dan terakumulasi untuk sebuah rekap sesi final di entitas QuizAttempt.)
- Many to One dengan Entitas QuizQuestion (Referensi titik soal mana yang dijawab ditarik menuju sumber QuizQuestion.)
- Many to One dengan Entitas QuizAnswer (Sebuah relasi penunjukan statis di mana opsi yang disubmit pengguna diarahkan menunjuk persis pada satu pilihan *true/false* di kunci jawaban asli milik basis data di tabel QuizAnswer (melalui panah selected_answer_id).)

---

## Entitas: CourseEnrollment

**Atribut**
- id
- course_id
- user_id
- enrolled_at
- progress_percentage
- completed_at
- certificate_url

**Relasi**
- Many to One dengan Entitas Course (Dokumen kepemilikan kursus asinkronus (CourseEnrollment) terbit mewakili lisensi untuk satu ragam layanan produk Course.)
- Many to One dengan Entitas User (Dokumen kepemilikan lisensi dikreditkan berhak melekat tak tergantikan milik satu akun pengguna terdaftar User.)
- One to Many dengan Entitas LessonProgress (Properti dokumen tunggal Enrollment disokong oleh kalkulasi kompilator ratusan log centang ketuntasan bab turunan yakni kumpulan LessonProgress.)
- One to Many dengan Entitas QuizAttempt (Dokumen Enrollment juga diperkaya dengan rangkuman kolektif evaluasi pencapaian skor pada tumpukan lembar tes (QuizAttempt).)

---

## Entitas: Cart

**Atribut**
- id
- user_id

**Relasi**
- One to One (secara logika fungsional) dengan Entitas User (Setiap kantong belanja Cart diciptakan berpasangan eksklusif menjadi dompet penyimpan sementara untuk hanya satu kredensial User. Model `Cart` dan `User` sangatlah personal.)
- One to Many dengan Entitas CartItem (Satu keranjang transaksi Cart akan diisi beragam serpihan aset komoditas atau entitas CartItem.)

---

## Entitas: CartItem

**Atribut**
- id
- cart_id
- item_type
- item_id
- quantity

**Relasi**
- Many to One dengan Entitas Cart (Rekapitulasi rinci keranjang ini berisikan muara satu tempat wadah utama Cart.)
- Relasi Polymorphic (MorphTo) (Relasi referensi fleksibel di mana satu rekaman log sementara ini dapat menyasar rujukan entitas acak antara 'Course', 'Webinar', atau 'Product' melalui kolaborasi kolom fiktif (item_id) dan (item_type).)

---

## Entitas: PromoCode

**Atribut**
- id
- code
- description
- discount_type
- discount_value
- min_purchase_amount
- max_discount_amount
- usage_limit
- usage_count
- start_date
- end_date
- is_active

**Relasi**
- One to Many dengan Entitas Order (Satu kode diskon PromoCode bisa dieksploitasi untuk memotong margin harga pada raturan tagihan final cetak invoice log di tabel Order.)

---

## Entitas: Order

**Atribut**
- id
- user_id
- order_number
- total_amount
- discount_amount
- final_amount
- promo_code
- status
- payment_method
- payment_url
- midtrans_order_id
- midtrans_transaction_id
- paid_at

**Relasi**
- Many to One dengan Entitas User (Selembar cetakan transaksi pesanan Order ditagihkan otorisasi pembayarannya berpusat pada satu kredensial pembeli User.)
- One to Many dengan Entitas OrderItem (Selembar kwitansi statis cetak Order merinci sub-tagihan komprehensif atas puluhan unit belanja digital pada model OrderItem yang mengikutinya.)

---

## Entitas: OrderItem

**Atribut**
- id
- order_id
- item_type
- item_id
- item_title
- item_price
- quantity
- subtotal

**Relasi**
- Many to One dengan Entitas Order (Detail rincian belanja *snapshot* ini terlampir definitif ke dalam satu faktur induk Order yang menaunginya.)
- Relasi Polymorphic (MorphTo) (Menautkan koneksi identitas sumber produk aslinya ke bermacam-macam rujukan entitas seperti Course, Webinar, ataupun Product sebagai rujukan riwayat rekam jejak (*tracer*) seandainya harga di masa depan mengalami modifikasi fluktuatif.)

---

## Entitas: Article

**Atribut**
- id
- category_id
- author_id
- title
- slug
- content
- excerpt
- featured_image_url
- tags
- view_count
- reading_time
- is_featured
- is_published
- published_at

**Relasi**
- Many to One dengan Entitas Category (Satu siaran majalah digital / post Article ini diklasifikasikan bernaung rimbun di bawah satu Category tipe artikel.)
- Many to One dengan Entitas User (Seuntaian publikasi karya blog Article dikaitkan hak cipta penerbitannya diwakili *author_id* yang membelok ke basis tabel User (kredensial author/admin).)

---

## Entitas: Testimonial

**Atribut**
- id
- user_id
- name
- title
- content
- rating
- photo_url
- is_featured
- is_approved

**Relasi**
- Many to One dengan Entitas User (Setiap ulasan testimoni platform Testimonial dapat ditautkan *optional* (jika *login*) ke identitas otentik pemberi rating ke entitas User.)

<br><br>

# ERD Untuk Subsistem Admin Panel (5 Entitas)

Subsistem Admin Panel bertindak sebagai pusat kendali utama (*command center*) yang menyediakan pandangan menyeluruh (*helicopter view*) terhadap aktivitas yang terjadi di dalam platform Paudpedia. Ruang lingkup fungsional dari subsistem ini difokuskan pada pengawasan operasional tingkat tinggi, yang meliputi manajemen entitas pengguna global, konfigurasi variabel sistem aplikasi, validasi transaksi B2B untuk perpanjangan paket langganan institusi, serta moderasi keseluruhan aktivitas transaksi dan konten publik. Subsistem ini didesain khusus bagi administrator sistem dan tim pengelola internal aplikasi.

[Diagram ERD Subsistem Admin Panel]

Dalam kaitannya dengan arsitektur sistem secara menyeluruh, Subsistem Admin Panel berfungsi sebagai jembatan *shared entity* yang menghubungkan ekosistem SIAKAD dan ekosistem B2C. Entitas-entitas yang beroperasi pada subsistem ini memegang wewenang hierarkis tertinggi, memungkinkan pengelola untuk melakukan intervensi data secara lintas subsistem, seperti membekukan operasional *tenant* sekolah yang melanggar ketentuan, atau mencabut lisensi kelas dari transaksi yang bermasalah. Dengan demikian, subsistem ini menjamin terciptanya tata kelola platform yang aman, terkontrol, dan tetap patuh terhadap aturan bisnis yang telah ditetapkan.

## Entitas: User (Shared Entity Utama)

Entitas `User` merupakan jantung autentikasi bagi seluruh aplikasi. Data kredensial pengguna terpusat di sini untuk mencegah duplikasi akun. *Role* atau peran spesifik diurus secara dinamis via dependensi lain (seperti Spatie Role di level sistem atau SchoolMember di level institusi).

**Atribut**
- id
- name
- email
- email_verified_at
- password
- phone
- gender
- date_of_birth
- address
- avatar_url
- is_active

**Relasi Ekstensif Global**
- One to Many dengan Entitas SchoolMember (Seorang akun otentik tunggal User dimungkinkan mendaftar ke beraneka ragam peranan SchoolMember di bermacam-macam tenant institusi SIAKAD.)
- One to One (Fungsional) dengan Entitas Teacher (Satu entitas global User dapat memiliki identitas kepegawaian sebagai Teacher di SIAKAD.)
- One to One (Fungsional) dengan Entitas ParentProfile (Satu entitas global User dapat diwariskan ke entitas kepengasuhan untuk memonitor perkembangan anak lewat ParentProfile SIAKAD.)
- One to Many dengan Entitas CourseEnrollment (Seorang pelanggan User berkesempatan mengikuti dan melisensikan kapabilitas belajarnya pada banyak produk akademi interaktif di ekosistem CourseEnrollment B2C.)
- One to Many dengan Entitas Order (Seorang pelanggan User memiliki deretan *history* panjang kwitansi checkout *gateway* transaksi Order pada sistem Marketplace e-commerce.)
- One to Many dengan Entitas Article (Seorang jurnalis staf kredensial User berwewenang menayangkan ratusan terbitan portofolio siaran Article berita.)
- One to Many dengan Entitas Testimonial (Seorang publik kredensial User meninggalkan rekam jejak tanggapan ulasan murni pada sistem Testimonial CMS.)
- Relasi Polymorphic Many to Many dengan Entitas Role & Permission dari paket Spatie RBAC (Mengatur struktur delegasi matriks sistem otorisasi Administrator, Editor, hingga Guest).

---

## Entitas: SiteSetting

**Atribut**
- id
- key
- value
- type
- description

**Relasi**
- (Entitas Standalone / Tidak memiliki relasi spesifik) Entitas mandiri berbasis *Key-Value Pair* (*Dictionary*) yang bertindak sebagai *environment* tempat Admin Panel menyetel dan menghidup-matikan parameter global (contoh: konfigurasi kontak global, batasan notifikasi aplikasi, tautan jejaring sosial, dan syarat regulasi term & condition platform).

---

## Entitas: SubscriptionOrder

Entitas ini mengelola proses B2B (Business-to-Business) di mana Headmaster sebuah tenant sekolah melakukan perpanjangan paket berlangganan dari level "Free" menjadi tier "Pro". Admin platform mengendalikan perputaran profit pada area entitas pengawasan transaksi berlangganan ini.

**Atribut**
- id
- school_id
- amount
- status
- midtrans_order_id
- midtrans_transaction_id
- snap_token
- payment_method
- duration_months
- paid_at
- expired_at

**Relasi**
- Many to One dengan Entitas School (Setiap tagihan B2B perpanjangan kapasitas *software-as-a-service* ini merepresentasikan identitas tagih yang berafiliasi tunggal menuju rujukan satu institusi bernaung yakni School.)

---

## Entitas: Order (Shared Entity B2C)

*Catatan: Entitas ini sama seperti Order yang ada di Subsistem B2C.*
Di lingkungan Admin Panel, administrator memiliki kemampuan supervisi komprehensif menginspeksi tabel kwitansi global, memvalidasi dan mencabut paksa lisensi dari *checkout* yang menyalahi kaidah regulasi.

---

## Entitas: School (Shared Entity SIAKAD)

*Catatan: Entitas ini adalah kerangka awal yang sama dengan Subsistem SIAKAD.*
Namun, dari kacamata Admin Panel (Superadmin Platform), tabel global `School` (tenant) sepenuhnya berada di telapak tangannya untuk dibekukan status operasionalnya jika terlambat melaksanakan *renewal* tenggat batas waktu perpanjangan atau melanggar terms of service platform. 
