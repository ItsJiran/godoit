<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\LandingSection;
use App\Enums\Image\ImagePurposeType;
use App\Services\Media\ImageUploadService; // Ensure this is correct 

class LandingSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

      // HOMEPAGE SECTION

      LandingSection::create([
          'index' => 0,
          'type' => 'homepage_description',
          'meta_content' => [
              'title' => 'Selamat Datang di Go Do It',
              'description' => 'Selamat datang di Godoit, platform referral inovatif yang memberdayakan Anda untuk mendapatkan penghasilan dari jaringan dan relasi yang Anda miliki. Kami percaya setiap koneksi berharga. 
              
              Godoit hadir untuk menjembatani Anda dengan peluang bisnis menarik, memungkinkan Anda merekomendasikan produk atau layanan berkualitas, dan mendapatkan komisi yang menguntungkan. 
              
              Baik Anda seorang individu yang ingin menambah pemasukan, atau seorang profesional yang ingin memaksimalkan network Anda, Godoit adalah solusi cerdas untuk mengubah rekomendasi menjadi pendapatan nyata.',
              'hero_image' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
              'button_register' => [
                'href' => 'register'
              ],
          ],
      ]);

      LandingSection::create([
          'index' => 1,
          'type' => 'homepage_product',
          'meta_content' => [
              'title' => 'Tentang Produk Napak Tilas',
              'description' => 'Di Go Do It, kamu dapat memasarkan Produk Napak Tilas Kebangsaan, Napak Tilas Kebangsaan sendiri adalah program outdoor edukatif yang mengajak peserta—mulai dari siswa, guru, hingga keluarga—untuk belajar sejarah secara langsung di titik kejadian.',
              'button_more' => [
                'href' => 'page/napak_tilas'
              ]
          ],
      ]);

      LandingSection::create([
          'index' => 2,
          'type' => 'homepage_testimonials',
          'meta_content' => [
              'title' => 'Testimonials Go Do It',
              "content" => [
                  [
                    "quote" => "Godoit benar-benar membuka mata saya. Dengan modal jaringan pertemanan, saya bisa menghasilkan tambahan uang tanpa perlu jualan langsung. Simpel dan komisinya transparan!",
                    "name" => "Budi Santoso",
                    "role" => "Wiraswasta",
                    'src' => 'https://placehold.co/150x80/000/fff?text=Logo+A'
                  ],
                  [
                    "quote" => "Sebagai freelancer, Godoit jadi sumber pendapatan pasif yang menarik. Saya cuma perlu merekomendasikan layanan IT yang memang dibutuhkan teman-teman bisnis saya, dan Godoit urus sisanya.",
                    "name" => "Sarah Agustina",
                    "role" => "Konsultan Digital",
                    'src' => 'https://placehold.co/150x80/000/fff?text=Logo+A'
                  ],
                  [
                    "quote" => "Awalnya ragu, tapi setelah mencoba, Godoit jauh melampaui ekspektasi. Prosesnya mudah, pilihan produknya banyak, dan tim support-nya responsif. Sangat direkomendasikan!",
                    "name" => "Rina Dewi",
                    "role" => "Ibu Rumah Tangga & Influencer",
                    'src' => 'https://placehold.co/150x80/000/fff?text=Logo+A'
                  ],
                  [
                    "quote" => "Saya sering diminta rekomendasi produk keuangan. Dengan Godoit, saya bisa memberikan solusi terbaik sambil mendapatkan komisi. Win-win solution untuk saya dan teman-teman!",
                    "name" => "Anton Wijaya",
                    "role" => "Financial Planner",
                    'src' => 'https://placehold.co/150x80/000/fff?text=Logo+A'
                  ]
              ],
          ],
      ]);


      LandingSection::create([
          'index' => 3,
          'type' => 'homepage_faq',
          'meta_content' => [
              'title' => 'Faq Go Do It',
              "content"=> [
                  [
                    "question"=> "Apa itu Godoit?",
                    "answer"=> "Godoit adalah platform referral yang memungkinkan Anda mendapatkan komisi dengan merekomendasikan produk atau layanan dari berbagai mitra bisnis kepada jaringan atau relasi Anda."
                  ],
                  [
                    "question"=> "Bagaimana cara kerja Godoit?",
                    "answer"=> "Anda mendaftar, memilih produk/layanan yang ingin direkomendasikan, membagikan link referral unik Anda. Jika ada transaksi sukses melalui link Anda, Anda akan menerima komisi."
                  ],
                  [
                    "question"=> "Siapa saja yang bisa bergabung dengan Godoit?",
                    "answer"=> "Siapa pun yang memiliki jaringan dan ingin menghasilkan uang dari rekomendasi, baik individu, profesional, influencer, atau bahkan bisnis."
                  ],
                  [
                    "question"=> "Apakah ada biaya untuk bergabung dengan Godoit?",
                    "answer"=> "Tidak ada. Bergabung dengan Godoit sepenuhnya gratis. Anda hanya perlu mendaftar dan mulai mereferensikan."
                  ],
                  [
                    "question"=> "Kapan saya akan menerima komisi saya?",
                    "answer"=> "Setiap komisi akan diproses setelah transaksi referral dikonfirmasi dan sesuai dengan ketentuan pembayaran yang berlaku untuk masing-masing produk atau layanan. Detail jadwal pembayaran akan tersedia di dashboard Anda."
                  ],
                  [
                    "question"=> "Bagaimana saya bisa melacak komisi dan performa referral saya?",
                    "answer"=> "Anda akan memiliki dashboard pribadi yang intuitif untuk memantau semua referral, status transaksi, dan jumlah komisi yang telah Anda peroleh."
                  ]
              ]
          ],
      ]);


      LandingSection::create([
          'index' => 4,
          'type' => 'homepage_clients',
          'meta_content' => [
              'title' => 'Client Go Do It',
              'content' => [
                  [
                      'alt' => 'Citra Alam',
                      'src' => 'https://static.wixstatic.com/media/f55b4f_dc2c7a4742ae4721a099beeeaed78ba1~mv2.png/v1/fill/w_101,h_59,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Logo%20Youtube_edited.png'
                  ],
                  [
                      'alt' => 'Citra Clay',
                      'src' => 'https://static.wixstatic.com/media/aa816e_51856e02786a4c69a0c493ebd388a293~mv2.png/v1/fill/w_119,h_46,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/aa816e_51856e02786a4c69a0c493ebd388a293~mv2.png'
                  ],
                  [
                      'alt' => 'Logo Bank X',
                      'src' => 'https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=110,fit=crop,q=95/mv07NykZjWflb5XO/logo-rkp2-1-Aq2oEzx2ZgsnQJZM.png'
                  ],
              ]
          ]
      ]);


      // NAPAK TILAS SECTION

      LandingSection::create([
        'index' => 0,
        'landing_type' => 'napak_tilas',
        'type' => 'napaktilas_hero',
        'meta_content' => [
            'title' => 'Apa itu Napak Tilas',
            'description' => 'Napak Tilas Kebangsaan adalah program outdoor edukatif yang mengajak peserta—mulai dari siswa, guru, hingga keluarga—untuk belajar sejarah secara langsung di titik kejadian.

              Melalui rute yang dirancang khusus, peserta akan :

              ✅ Mengenal siapa dan mau kemana kita sebagai sebuah bangsa.

              ✅ Menyusuri jejak perjuangan pahlawan pendiri bangsa

              ✅ Mengikuti tantangan sejarah interaktif

              ✅ Memperkuat semangat nasionalisme dan kerjasama

              Saat ini sudah lebih  dari 1000 peserta dari Sekolah, Instansi dan Umum telah mengikuti acara ini sekarang sudah memasuki angkatan ke 50.',
            'hero_image' => 'https://static.wixstatic.com/media/f55b4f_810df6c5ce064564bbf5e69e71507acc~mv2.png/v1/fill/w_740,h_188,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/f55b4f_810df6c5ce064564bbf5e69e71507acc~mv2.png',
            'button_latest_product' => true,
        ],
      ]);

      LandingSection::create([
        'index' => 1,
        'landing_type' => 'napak_tilas',
        'type' => 'napaktilas_terms_and_requirement',
        'meta_content' => [
            'title' => 'Syarat Dan Ketentuan',
            'content' => [
                [
                    'title' => 'Persyaratan Usia',
                    'description' => 'Kegiatan bisa diikuti mulai usia 7 tahun.'
                ],
                [
                    'title' => 'Fasilitas Termasuk Biaya Pendaftaran',
                    'description' => 'Biaya pendaftaran sudah termasuk:',
                    'details' => [
                        '2x Snack gratis',
                        '1x Makan',
                        'T-shirt',
                        'Armada (kendaraan yang disediakan)',
                        'Hampers untuk 10 orang pendaftar pertama',
                        'Tiket masuk setiap titik lokasi.'
                    ]
                ],
                [
                    'title' => 'Batas Waktu Pembayaran',
                    'description' => 'Peserta diwajibkan melakukan pembayaran kegiatan paling lambat maksimal H-3 (3 hari sebelum) kegiatan.'
                ],
                [
                    'title' => 'Transportasi',
                    'description' => 'Perjalanan akan dilaksanakan menggunakan kendaraan yang telah disediakan Rumah Kebangsaan Pancasila.'
                ],
                [
                    'title' => 'Titik Temu',
                    'description' => 'Titik temu di Museum Sumpah Pemuda.'
                ],
                [
                    'title' => 'Sesi Zoom Meeting',
                    'description' => 'H-3 (3 hari sebelum) kegiatan akan diadakan Zoom Meeting antara para peserta dengan Panitia Pelaksana.'
                ],
                [
                    'title' => 'Kebijakan Pembatalan/Pengalihan Dana',
                    'description' => 'Apabila peserta berhalangan sampai H-1 (1 hari sebelum) kegiatan, maka dana yang sudah diberikan akan dialokasikan di event berikutnya.'
                ]
            ],
            'button_latest_product' => true,
        ],
      ]);


      LandingSection::create([
        'index' => 2,
        'landing_type' => 'napak_tilas',
        'type' => 'section_gallery',
        'meta_content' => [
            'title' => 'Gallery',
            'content' => [
              'https://static.wixstatic.com/media/f55b4f_8082ee99faa8480dbc55d5489af7e31b~mv2.png/v1/fill/w_243,h_243,fp_0.50_0.50,q_90/f55b4f_8082ee99faa8480dbc55d5489af7e31b~mv2.webp',
              'https://static.wixstatic.com/media/f55b4f_c947e06888304b19b1eeed5f0b1f1f4d~mv2.png/v1/fill/w_244,h_243,fp_0.50_0.50,q_90/f55b4f_c947e06888304b19b1eeed5f0b1f1f4d~mv2.webp',
              'https://static.wixstatic.com/media/f55b4f_3c0d57f8de2e48bfaf99c7663a23e303~mv2.jpg/v1/fill/w_243,h_243,fp_0.50_0.50,q_90/f55b4f_3c0d57f8de2e48bfaf99c7663a23e303~mv2.webp',
              'https://static.wixstatic.com/media/f55b4f_6ab50eee9d2c4505aa2ae79ba25b477b~mv2.jpg/v1/fill/w_243,h_243,fp_0.50_0.50,q_90/f55b4f_6ab50eee9d2c4505aa2ae79ba25b477b~mv2.webp',
              'https://static.wixstatic.com/media/f55b4f_6fe74d96bf8a4a7a8de7816311e084ed~mv2.png/v1/fill/w_244,h_243,fp_0.50_0.50,q_90/f55b4f_6fe74d96bf8a4a7a8de7816311e084ed~mv2.webp',
              'https://static.wixstatic.com/media/f55b4f_5ba0ae7447f440068264bf90f74025c3~mv2.jpg/v1/fill/w_243,h_243,fp_0.50_0.50,q_90/f55b4f_5ba0ae7447f440068264bf90f74025c3~mv2.webp',
            ],
        ],
      ]);

      LandingSection::create([
        'index' => 3,
        'landing_type' => 'napak_tilas',
        'type' => 'section_video',
        'meta_content' => [
            'title' => 'Video Kegiatan',
            'url' => 'https://www.youtube.com/embed/Y_hKK2QjeGY' 
        ],
      ]);

      LandingSection::create([
        'index' => 4,
        'landing_type' => 'napak_tilas',
        'type' => 'section_image',
        'meta_content' => [
            'title' => 'Rundown Kegiatan',
            'description' => 'Rundown ini sebagai gambaran besar terlaksananya kegiatan dalam durasi 1 Hari..',
            'url' => 'https://static.wixstatic.com/media/f55b4f_771c4eb4ab3343cd8e870b06b85525ad~mv2.jpeg/v1/fill/w_740,h_416,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/f55b4f_771c4eb4ab3343cd8e870b06b85525ad~mv2.jpeg' 
        ],
      ]);

      LandingSection::create([
        'index' => 5,
        'landing_type' => 'napak_tilas',
        'type' => 'section_image',
        'meta_content' => [
            'title' => 'Ikuti Kami',
            'description' => 'Ayo cari pengalaman belajar sejarah langsung dari titik sejarah terjadi!',
            'url' => 'https://static.wixstatic.com/media/f55b4f_acec413bed9340bf822933f119749fc4~mv2.png/v1/fill/w_740,h_525,al_c,q_90,usm_0.66_1.00_0.01,enc_avif,quality_auto/f55b4f_acec413bed9340bf822933f119749fc4~mv2.png' 
        ],
      ]);

      LandingSection::create([
        'index' => 6,
        'landing_type' => 'napak_tilas',
        'type' => 'section_contact',
        'meta_content' => [
            'title' => 'Kontak & Info Lebih Lanjut',
            'content' => 'Hubungi Kami: Whatsapp        : 0813-8321-0355
              📞 0813-8321-0355📧 info@citraalam.id📍 IG/FB: @citraalam.id'
        ],
      ]);


    }
}
