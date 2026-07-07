<?php

namespace App\Http\Controllers;

class LandingController extends Controller
{
    /**
     * Render the research landing page.
     * All copy/data here is derived from:
     * "Segmentasi Semantik Banjir Melalui Integrasi Backbone ResNet-50
     *  pada Citra Optik RGB" — JUTIM, Universitas Bina Insan Lubuklinggau.
     */
    public function index()
    {
        $paper = [
            'title' => 'Segmentasi Semantik Banjir Melalui Integrasi Backbone ResNet-50 pada Citra Optik RGB',
            'journal' => 'JUTIM — Jurnal Teknik Informatika Musirawas',
            'publisher' => 'Universitas Bina Insan Lubuklinggau',
            'authors' => [
                'Afzal Asnu Muhammad Ivano',
                'Bintang Rifky Ananta',
                'Fiola Putri Monika',
                'Ilham Pratama',
                'Salsa Dharma Arindina',
            ],
            'advisors' => [
                'Muhammad Naufal, S.Tr.T., M.Kom.',
                'Dr. Ir. Ricardus Anggi Pramunendar, S.Kom., M.CS.',
            ],
            'affiliation' => 'Program Studi Informatika, Universitas Dian Nuswantoro, Semarang',
        ];

        // Headline metrics shown in the hero
        $heroStats = [
            ['value' => '97,8%', 'label' => 'Akurasi piksel tertinggi', 'who' => 'DeepLabV3'],
            ['value' => '95,33%', 'label' => 'Skor IoU tertinggi', 'who' => 'DeepLabV3'],
            ['value' => '97,28%', 'label' => 'Recall tertinggi', 'who' => 'U-Net++'],
        ];

        // The 5-stage research pipeline (a genuine sequence — mirrors Gambar 3 in the paper)
        $pipeline = [
            [
                'no' => '01',
                'title' => 'Dataset Citra Banjir',
                'desc' => 'Flood Semantic Segmentation Dataset (Kaggle): 663 citra resolusi tinggi — 600 untuk pelatihan, 63 untuk validasi, diseragamkan ke 512×512 piksel dan disinkronkan dengan masker biner ground truth.',
            ],
            [
                'no' => '02',
                'title' => 'Pra-pemrosesan & Augmentasi',
                'desc' => 'Normalisasi intensitas piksel 0–255 → 0–1, lalu augmentasi spasial (flip, rotate 90, shift-scale-rotate) dan fotometrik (brightness/contrast, HSV jitter, gaussian noise) khusus pada data latih.',
            ],
            [
                'no' => '03',
                'title' => 'Arsitektur & Backbone',
                'desc' => 'U-Net++ dan DeepLabV3 sama-sama dipasangkan dengan backbone ResNet-50 berbobot pretrained, agar perbandingan kemampuan decoder berlangsung adil sejak inisialisasi.',
            ],
            [
                'no' => '04',
                'title' => 'Training & Optimasi',
                'desc' => 'Fungsi kehilangan kombinasi BCE + Dice (bobot 0,5/0,5) untuk mengatasi class imbalance, dijadwalkan dengan CosineAnnealingLR pada batch size 8.',
            ],
            [
                'no' => '05',
                'title' => 'Evaluasi & Visualisasi',
                'desc' => 'Performa diukur lewat IoU, akurasi piksel, Dice Score, dan Recall, dilengkapi overlay True Positive / False Positive / False Negative untuk audit visual.',
            ],
        ];

        $trainingConfig = [
            'deeplabv3' => [
                'optimizer' => 'AdamW (differential learning rate)',
                'epoch' => '30 epoch penuh',
                'extra' => 'Auxiliary classifier aktif, bobot kontribusi 0,4',
            ],
            'unetpp' => [
                'optimizer' => 'Adam, learning rate awal 1e-6',
                'epoch' => 'Maks. 50 epoch — early stopping di epoch ke-12',
                'extra' => 'Konvergensi loss akhir tercatat sangat rendah: 0,1473',
            ],
        ];

        // Core comparison table (Tabel 1 in the paper)
        $metricsTable = [
            ['metric' => 'Akurasi Piksel', 'deeplabv3' => 97.80, 'unetpp' => 97.10],
            ['metric' => 'Skor IoU', 'deeplabv3' => 95.33, 'unetpp' => 93.90],
            ['metric' => 'Dice Score (F1)', 'deeplabv3' => 97.61, 'unetpp' => 96.86],
        ];

        // U-Net++ confusion-matrix profile (only U-Net++ reports these in the paper)
        $unetppProfile = [
            ['label' => 'Recall', 'value' => '97,28%', 'note' => 'area banjir nyata yang berhasil terdeteksi'],
            ['label' => 'Precision', 'value' => '96,43%', 'note' => 'prediksi banjir yang benar-benar tepat'],
            ['label' => 'True Positive', 'value' => '44,59%', 'note' => 'dari total rasio luas citra yang diproses'],
            ['label' => 'False Positive', 'value' => '1,65%', 'note' => 'daratan kering yang keliru terdeteksi'],
            ['label' => 'False Negative', 'value' => '1,24%', 'note' => 'area banjir yang luput dari deteksi'],
        ];

        $architectures = [
            'unetpp' => [
                'name' => 'U-Net++',
                'tagline' => 'Spesialis batas mikro',
                'mechanism' => 'Nested Skip Pathways',
                'desc' => 'Jalur konvolusi padat menjembatani encoder dan decoder secara bertingkat, mengurangi kesenjangan semantik dan menjaga detail tepi genangan yang tidak beraturan.',
                'strength' => 'Recall 97,28% — nyaris tidak ada wilayah banjir yang terlewat.',
                'idealFor' => 'Delineasi batas sungai, garis pantai, dan genangan kecil yang terfragmentasi.',
            ],
            'deeplabv3' => [
                'name' => 'DeepLabV3',
                'tagline' => 'Spesialis cakupan makro',
                'mechanism' => 'Atrous Spatial Pyramid Pooling (ASPP)',
                'desc' => 'Konvolusi dilasi paralel pada empat laju berbeda (1×, 6×, 12×, 18×) memperluas bidang pandang filter tanpa mengorbankan resolusi spasial.',
                'strength' => 'IoU 95,33% — akurasi spasial global tertinggi dalam pengujian.',
                'idealFor' => 'Pemetaan hamparan banjir berskala masif dari citra UAV/UAS.',
            ],
        ];

        $recommendations = [
            [
                'model' => 'DeepLabV3',
                'verdict' => 'Untuk pemetaan luasan makro',
                'reason' => 'Unggul mutlak pada akurasi spasial global (IoU 95,33%, akurasi piksel 97,80%) berkat ASPP yang menangkap konteks multiskala.',
            ],
            [
                'model' => 'U-Net++',
                'verdict' => 'Untuk delineasi batas mikro',
                'reason' => 'Recall tertinggi (97,28%) menekan False Negative hingga 1,24% — krusial saat prioritas utamanya tidak boleh ada area terdampak yang luput.',
            ],
        ];

        $futureWork = [
            [
                'title' => 'Fusi Sensor',
                'desc' => 'Menggabungkan citra optik RGB dengan data multispektral atau radar (SAR) untuk meredam ambiguitas visual di area kompleks.',
            ],
            [
                'title' => 'Backbone Ringan',
                'desc' => 'Mengganti ResNet-50 dengan MobileNetV3 atau EfficientNet agar inferensi dapat berjalan real-time langsung dari drone.',
            ],
            [
                'title' => 'Pencarian Hiperparameter Otomatis',
                'desc' => 'Menerapkan Bayesian Optimization untuk menemukan kombinasi hiperparameter pelatihan yang paling optimal secara efisien.',
            ],
        ];

        return view('landing', compact(
            'paper',
            'heroStats',
            'pipeline',
            'trainingConfig',
            'metricsTable',
            'unetppProfile',
            'architectures',
            'recommendations',
            'futureWork'
        ));
    }
}
