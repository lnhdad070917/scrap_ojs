<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use App\Models\Link;
use App\Models\Jurnal;

class ScraperController extends Controller
{
    public function index()
    {
        $client = new Client();

        // Ambil semua tautan dari model Link bersama dengan ID-nya
        $links = Link::all();

        foreach ($links as $link) {
            $url = $link->link; // Ambil URL dari model Link
            $linkId = $link->id; // Ambil ID dari model Link



            $website = $client->request('GET', $url);

            $website->filter('table.tocArticle')->each(function ($node) use ($linkId) {
                // Mencari tautan PDF
                $pdfLink = $node->filter('a.file')->attr('href');

                // Mencari tautan "lihat dokumen pada web"
                $webLink = $node->filter('a[href^="https://"]')->attr('href');

                // Mengambil judul artikel
                $title = $node->filter('div.tocTitle a')->text();

                // Mengambil teks dari elemen dengan kelas 'tocDOI' atau 'tocDoi'
                $doi = $node->filter('div.tocDOI a')->count() > 0 ? $node->filter('div.tocDOI a')->text() : '';

                if (empty($doi)) {
                    // Jika tidak ada di kelas 'tocDOI', coba di kelas 'tocDoi'
                    $doi = $node->filter('div.tocDoi a')->count() > 0 ? $node->filter('div.tocDoi a')->text() : '';
                }

                // Mengambil nama-nama penulis
                $authors = $node->filter('div.tocAuthors')->text();

                // Mengambil nomor halaman
                $pages = $node->filter('div.tocPages')->text();
                $existingJurnal = Jurnal::where('doi', $doi)->first();
                if (!$existingJurnal) {
                    $data = [
                        'link_id' => $linkId,
                        'judul' => $title,
                        'author' => $authors,
                        'doi' => $doi,
                        'pdf_link' => $pdfLink,
                        'web_link' => $webLink,
                        'pages' => $pages,
                    ];

                    Jurnal::create($data);
                }
            });
        }
        // $allArticles = array_merge($allArticles, $articles);

    }

    public function getWordFrequencyData()
    {
        // Ambil data dari database (kolom 'judul' dalam tabel 'jurnals')
        $dataFromDatabase = Jurnal::pluck('judul')->all();
        $wordFrequency = [];

        foreach ($dataFromDatabase as $title) {
            $words = str_word_count(strtolower($title), 1);

            foreach ($words as $word) {
                // Skip kata "di," "dan," "ke," "dari," atau kata dengan panjang kurang dari 3 karakter
                if (in_array($word, ["di", "the", "dan", "ke", "dari", "dalam", 'melalui', 'pada', 'sebagai', 'untuk', 'dengan', 'bagi', 'cover', 'isi', 'terhadap', 'tangga', 'ibu']) || strlen($word) < 3) {
                    continue;
                }

                if (isset($wordFrequency[$word])) {
                    $wordFrequency[$word]++;
                } else {
                    $wordFrequency[$word] = 1;
                }
            }
        }

        // Hapus kata-kata dengan total kemunculan kurang dari atau sama dengan 3
        $wordFrequency = array_filter($wordFrequency, function ($count) {
            return $count > 10;
        });

        // Ubah hasil analisis ke format JSON
        $jsonResult = json_encode($wordFrequency);

        return response()->json(['wordFrequency' => $jsonResult])->header('Content-Type', 'application/json');

    }

    // public function loadData(Request $request)
    // {
    //     $skip = $request->input('skip', 0);
    //     $take = 10;
    //     $searchQuery = $request->input('search');

    //     $query = Jurnal::query();

    //     // Jika ada pencarian, tambahkan kondisi where
    //     if ($searchQuery) {
    //         $query->where('judul', 'LIKE', '%' . $searchQuery . '%');
    //         // Anda dapat menambahkan lebih banyak kondisi pencarian di sini
    //     }

    //     $totalData = $query->count(); // Hitung total data yang sesuai dengan pencarian

    //     $data = $query->skip($skip)->take($take)->get();

    //     return response()->json(['data' => $data, 'total' => $totalData]);
    // }

    public function loadData(Request $request)
    {
        // Menggunakan parameter page untuk menentukan halaman data yang diinginkan
        $page = $request->input('page', 1);
        $take = 10;
        $searchQuery = $request->input('search');

        $query = Jurnal::query();

        // Jika ada pencarian, tambahkan kondisi where
        if ($searchQuery) {
            $query->where('judul', 'LIKE', '%' . $searchQuery . '%');
            // Anda dapat menambahkan lebih banyak kondisi pencarian di sini
        }

        // Menggunakan metode paginate untuk membatasi dan menghitung data
        $data = $query->paginate($take, ['*'], 'page', $page);

        return response()->json($data);
    }



    public function home()
    {


        $jurnals = Jurnal::all(); // Mengambil semua data dari model Jurnal

        return view('jurnal', compact('jurnals')); // Mengirim data ke Blade 'jurnal.index'

    }

    // public function detectSubdomain(Request $request)
    // {
    //     $domain = "ump.ac.id";
    //     if (empty($domain)) {
    //         return response()->json(['error' => 'Domain harus diisi'], 400);
    //     }

    //     $subdomains = [
    //         'jurnal',
    //         'journal',
    //         'e-journal',
    //         'ejournal',
    //     ];

    //     $activeSubdomains = [];

    //     foreach ($subdomains as $subdomain) {
    //         $url = "http://$subdomain.$domain";

    //         try {
    //             $client = new Client();
    //             $crawler = $client->request('GET', $url);

    //             // $response = $client->get($url, ['http_errors' => false]); // Set http_errors ke false agar tidak membuang exception pada status kode 4xx
    //             // $statusCode = $response->getStatusCode();
    //             $statusCode = $client->getResponse()->getStatusCode();

    //             if ($statusCode >= 200 && $statusCode < 400) {
    //                 $activeSubdomains[] = $subdomain;
    //             }
    //         } catch (\Exception $e) {
    //             // Subdomain tidak aktif
    //         }
    //     }

    //     return response()->json(['active_subdomains' => $activeSubdomains]);
    // }

    // private function checkSubdomainStatus($subdomain)
    // {
    //     // Cek status subdomain dengan cURL atau library HTTP lainnya
    //     // Anda dapat mengganti ini sesuai dengan preferensi Anda
    //     $url = "http://$subdomain";
    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //     curl_exec($ch);
    //     $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);
    //     return $statusCode;
    // }

    // private function getAllSubdomains($domain)
    // {
    //     $subdomains = [];

    //     // Loop untuk mencoba semua kemungkinan subdomain
    //     // Anda dapat menyesuaikan ini sesuai dengan kebutuhan Anda
    //     for ($i = 1; $i <= 10; $i++) {
    //         $subdomain = "subdomain$i.$domain";

    //         // Mencoba mencari rekaman A untuk subdomain
    //         $dnsRecords = dns_get_record($subdomain, DNS_A);

    //         // Jika ada rekaman A untuk subdomain, tambahkan ke daftar subdomain
    //         if (!empty($dnsRecords)) {
    //             $subdomains[] = $subdomain;
    //         }
    //     }

    //     return $subdomains;
    // }

    // private function parseSubdomain($domain)
    // {
    //     // Mencocokkan domain dengan pola subdomain
    //     // Pola ini akan mencocokkan subdomain, jika ada
    //     // Contoh: subdomain.domain.com menjadi ['subdomain', 'domain.com']
    //     preg_match('/([^\.]+)\.(.+)/', $domain, $matches);

    //     // Jika ada subdomain, kembalikan subdomain dan domain utama, jika tidak, kembalikan null untuk subdomain
    //     if (count($matches) == 3) {
    //         return [$matches[1], $matches[2]];
    //     } else {
    //         return [null, $domain];
    //     }
    // }


}