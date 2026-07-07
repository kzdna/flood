<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Segmentasi Semantik Banjir — ResNet-50 × U-Net++ × DeepLabV3</title>
<meta name="description" content="Studi komparatif U-Net++ dan DeepLabV3 berbasis backbone ResNet-50 untuk segmentasi semantik banjir pada citra optik RGB.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fortunate-mindfulness-production-d9ef.up.railway.app/css/landing.css">
</head>
<body>

{{-- ============ TOPBAR ============ --}}
<header class="topbar">
  <div class="wrap">
    <div class="brand"><span class="dot"></span> FLOOD-SEG / RESNET-50</div>
    <nav class="topnav">
      <a href="#tantangan">Tantangan</a>
      <a href="#metodologi">Metodologi</a>
      <a href="#arsitektur">Arsitektur</a>
      <a href="#hasil">Hasil</a>
      <a href="#simulasi">Simulasi</a>
      <a href="#kesimpulan">Kesimpulan</a>
    </nav>
  </div>
</header>

{{-- ============ HERO ============ --}}
<section class="hero">
  <div class="contour-field drift" aria-hidden="true">
    <svg viewBox="0 0 1200 600" preserveAspectRatio="none">
      <path d="M-50,120 C200,80 350,180 600,140 S950,60 1250,120" />
      <path d="M-50,200 C220,160 380,260 620,220 S960,140 1250,200" />
      <path d="M-50,280 C240,240 400,330 640,300 S970,220 1250,280" />
      <path d="M-50,360 C260,330 420,400 660,380 S980,310 1250,360" />
      <path d="M-50,440 C280,410 440,470 680,450 S990,400 1250,440" />
    </svg>
  </div>
  <div class="wrap">
    <div class="eyebrow">Computer Vision · Penginderaan Jauh · Mitigasi Bencana</div>
    <div class="hero-grid">
      <div class="reveal in">
        <h1>Luas tertangkap.<br>Tepi <span class="accent">terjaga</span>.</h1>
        <p class="lede">
          Dua arsitektur, satu fondasi <em>backbone</em> ResNet-50. Penelitian ini menguji
          <strong>U-Net++</strong> dan <strong>DeepLabV3</strong> secara head-to-head untuk
          memetakan genangan banjir dari citra optik RGB — mencari titik keseimbangan
          antara akurasi spasial global dan kepekaan deteksi tepi.
        </p>
        <div class="cta-row">
          <a href="#hasil" class="btn btn-primary">Lihat Hasil Perbandingan →</a>
          <a href="#simulasi" class="btn btn-ghost">Coba Simulasi</a>
        </div>
        <div class="hero-meta">
          <span>663 citra resolusi tinggi</span>
          <span>512×512 px</span>
          <span>Loss: BCE + Dice</span>
          <span>{{ $paper['journal'] }}</span>
        </div>
      </div>
      <div class="stat-stack reveal in">
        @foreach($heroStats as $s)
        <div class="stat-card">
          <div class="val">{{ $s['value'] }}</div>
          <div class="lbl">{{ $s['label'] }}</div>
          <div class="who">{{ $s['who'] }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ============ TANTANGAN ============ --}}
<section id="tantangan">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="eyebrow">Latar Belakang</div>
      <h2>Air tidak selalu terlihat seperti air</h2>
      <p>Survei lapangan pasca-bencana lambat dan berisiko. Tapi otomatisasi lewat citra optik RGB
      punya tantangan visualnya sendiri — model harus membedakan genangan dari objek yang
      terlihat hampir serupa.</p>
    </div>
    <div class="problem-grid reveal">
      <div class="problem-card">
        <span class="tag">Kemiripan Visual</span>
        <h3>Jalan basah vs. genangan</h3>
        <p>Aspal basah, bayangan bangunan perkotaan, dan pantulan cahaya matahari kerap memicu kesalahan klasifikasi yang menyerupai air.</p>
      </div>
      <div class="problem-card">
        <span class="tag">Keterbatasan Sensor</span>
        <h3>SAR vs. citra optik</h3>
        <p>Radar (SAR) tahan cuaca buruk tapi membawa speckle noise dan resolusi rendah; RGB optik kaya detail namun lebih sensitif terhadap kompleksitas visual.</p>
      </div>
      <div class="problem-card">
        <span class="tag">Trade-off Arsitektural</span>
        <h3>Detail tepi vs. konteks luas</h3>
        <p>Jaringan saraf standar sering harus memilih: mempertahankan batas tepi presisi, atau menangkap konteks spasial luas — jarang keduanya sekaligus.</p>
      </div>
    </div>
  </div>
</section>

{{-- ============ METODOLOGI ============ --}}
<section id="metodologi">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="eyebrow">Metodologi</div>
      <h2>Lima tahap, satu alur reproduksibel</h2>
      <p>Studi eksperimental kuantitatif yang dirancang berurutan untuk mengisolasi bias —
      dari data mentah hingga metrik evaluasi akhir.</p>
    </div>
    <div class="pipeline reveal">
      @foreach($pipeline as $step)
      <div class="pipe-row">
        <div class="pipe-no">{{ $step['no'] }}</div>
        <div class="pipe-title">{{ $step['title'] }}</div>
        <div class="pipe-desc">{{ $step['desc'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ ARSITEKTUR ============ --}}
<section id="arsitektur">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="eyebrow">Dua Filosofi, Satu Fondasi</div>
      <h2>Bagaimana masing-masing arsitektur "melihat" banjir</h2>
      <p>Keduanya berbagi backbone ResNet-50 yang sama persis — perbedaan performa murni berasal dari strategi decoder.</p>
    </div>

    <div class="arch-grid reveal">
      <div class="arch-card cyan">
        <span class="arch-tagline">{{ $architectures['deeplabv3']['tagline'] }}</span>
        <h3>{{ $architectures['deeplabv3']['name'] }}</h3>
        <div class="arch-mechanism">{{ $architectures['deeplabv3']['mechanism'] }} · dilation rate 1×, 6×, 12×, 18×</div>
        <p>{{ $architectures['deeplabv3']['desc'] }}</p>
        <div class="arch-strength"><b>Kekuatan utama:</b> {{ $architectures['deeplabv3']['strength'] }}</div>
        <div class="arch-ideal">Ideal untuk: {{ $architectures['deeplabv3']['idealFor'] }}</div>
      </div>
      <div class="arch-card coral">
        <span class="arch-tagline">{{ $architectures['unetpp']['tagline'] }}</span>
        <h3>{{ $architectures['unetpp']['name'] }}</h3>
        <div class="arch-mechanism">{{ $architectures['unetpp']['mechanism'] }} · jembatan konvolusi padat</div>
        <p>{{ $architectures['unetpp']['desc'] }}</p>
        <div class="arch-strength"><b>Kekuatan utama:</b> {{ $architectures['unetpp']['strength'] }}</div>
        <div class="arch-ideal">Ideal untuk: {{ $architectures['unetpp']['idealFor'] }}</div>
      </div>
    </div>

    {{-- Signature interactive element --}}
    <div class="compare-shell reveal">
      <div class="compare-label-row">
        <span class="left">◀ U-Net++ — tepi presisi</span>
        <span class="right">DeepLabV3 — cakupan luas ▶</span>
      </div>
      <div class="compare-frame" id="compareFrame">
        <div class="layer left">
          <svg viewBox="0 0 800 400" preserveAspectRatio="xMidYMid slice">
            <rect width="800" height="400" fill="#0d2230"/>
            <g opacity="0.5">
              <rect x="40" y="40" width="70" height="70" fill="#1c3f54"/>
              <rect x="130" y="60" width="55" height="50" fill="#1c3f54"/>
              <rect x="60" y="300" width="80" height="60" fill="#1c3f54"/>
              <rect x="600" y="50" width="90" height="60" fill="#1c3f54"/>
              <rect x="700" y="280" width="70" height="80" fill="#1c3f54"/>
            </g>
            <path d="M0,250 C120,210 180,290 260,260 C340,230 380,310 460,280 C560,245 600,320 700,270 C740,250 780,260 800,250 L800,400 L0,400 Z" fill="#102c3c"/>
            <path d="M0,250 C120,210 180,290 260,260 C340,230 380,310 460,280 C560,245 600,320 700,270 C740,250 780,260 800,250"
                  fill="none" stroke="#ff7a59" stroke-width="2.5"/>
            <path d="M0,250 C120,210 180,290 260,260 C340,230 380,310 460,280 C560,245 600,320 700,270 C740,250 780,260 800,250"
                  fill="none" stroke="#ff7a59" stroke-width="0.6" stroke-dasharray="2 3" opacity="0.8" transform="translate(0,3)"/>
          </svg>
        </div>
        <div class="layer right">
          <svg viewBox="0 0 800 400" preserveAspectRatio="xMidYMid slice">
            <rect width="800" height="400" fill="#0d2230"/>
            <g opacity="0.5">
              <rect x="40" y="40" width="70" height="70" fill="#1c3f54"/>
              <rect x="130" y="60" width="55" height="50" fill="#1c3f54"/>
              <rect x="60" y="300" width="80" height="60" fill="#1c3f54"/>
              <rect x="600" y="50" width="90" height="60" fill="#1c3f54"/>
              <rect x="700" y="280" width="70" height="80" fill="#1c3f54"/>
            </g>
            <path d="M0,235 C140,180 200,300 280,250 C360,210 400,320 480,270 C580,220 620,330 720,260 C760,235 790,250 800,235 L800,400 L0,400 Z" fill="rgba(63,198,217,0.35)"/>
            <path d="M0,235 C140,180 200,300 280,250 C360,210 400,320 480,270 C580,220 620,330 720,260 C760,235 790,250 800,235"
                  fill="none" stroke="#3fc6d9" stroke-width="3"/>
          </svg>
        </div>
        <div class="compare-handle" id="compareHandle"></div>
      </div>
      <p class="compare-note">Ilustrasi konseptual (bukan output model asli) — menggambarkan bagaimana U-Net++ menjaga kerincian tepi, sementara DeepLabV3 melebarkan cakupan area genangan secara menyeluruh. Geser untuk membandingkan.</p>
    </div>
  </div>
</section>

{{-- ============ HASIL ============ --}}
<section id="hasil">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="eyebrow">Hasil Kuantitatif</div>
      <h2>Skor head-to-head pada 63 citra validasi</h2>
      <p>Kedua model melampaui akurasi piksel 97% — perbedaan nyata muncul pada metrik akurasi spasial dan sensitivitas deteksi.</p>
    </div>

    <div class="results-layout reveal">
      <table class="metrics">
        <thead>
          <tr><th>Parameter Evaluasi</th><th style="text-align:right">DeepLabV3</th><th style="text-align:right">U-Net++</th></tr>
        </thead>
        <tbody>
          @foreach($metricsTable as $row)
          <tr>
            <td>{{ $row['metric'] }}</td>
            <td class="num deeplab">{{ number_format($row['deeplabv3'],2,',','.') }}%</td>
            <td class="num unet">{{ number_format($row['unetpp'],2,',','.') }}%</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div class="bars">
        @foreach($metricsTable as $row)
        @php
          $baseline = 90;
          $span = 10;
          $pctDeep = max(0, min(100, ($row['deeplabv3'] - $baseline) / $span * 100));
          $pctUnet = max(0, min(100, ($row['unetpp'] - $baseline) / $span * 100));
        @endphp
        <div class="bar-row bar-pair">
          <div class="bar-label"><span>{{ $row['metric'] }}</span></div>
          <div class="bar-label"><span style="color:var(--cyan)">DeepLabV3</span><span>{{ number_format($row['deeplabv3'],2,',','.') }}%</span></div>
          <div class="bar-track"><div class="bar-fill deeplab" style="width:{{ $pctDeep }}%"></div></div>
          <div class="bar-label"><span style="color:var(--coral)">U-Net++</span><span>{{ number_format($row['unetpp'],2,',','.') }}%</span></div>
          <div class="bar-track"><div class="bar-fill unet" style="width:{{ $pctUnet }}%"></div></div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="section-head reveal" style="margin-top:64px;">
      <div class="eyebrow">Profil Sensitivitas — U-Net++</div>
      <h2>Membaca matriks konfusi</h2>
      <p>Paper melaporkan sebaran confusion matrix secara eksplisit untuk U-Net++, mengonfirmasi keandalannya menekan area luput pantauan.</p>
    </div>
    <div class="profile-grid reveal">
      @foreach($unetppProfile as $p)
      <div class="profile-cell">
        <div class="v">{{ $p['value'] }}</div>
        <div class="l">{{ $p['label'] }}</div>
        <div class="n">{{ $p['note'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ ANALISIS AI ============ --}}
<section id="simulasi">
    <div class="wrap">

        <div class="section-head reveal">
            <div class="eyebrow">Demo AI</div>
            <h2>Analisis Citra Banjir</h2>
            <p>
                Unggah gambar kemudian lakukan analisis untuk mengetahui
                apakah citra tersebut mengandung area banjir atau tidak.
            </p>
        </div>

        <div class="sim-shell reveal">

            <div class="sim-left">

                <input
                    type="file"
                    id="imageInput"
                    accept="image/*"
                    hidden>

                <label
                    for="imageInput"
                    class="btn btn-ghost">

                    📂 Pilih Gambar

                </label>

                <br><br>

                <img
                    id="previewImage"
                    src=""
                    alt="Preview Gambar"
                    style="
                        display:none;
                        width:100%;
                        border-radius:16px;
                        border:1px solid rgba(255,255,255,.1);
                        margin-bottom:20px;
                    ">

                <button
                    class="btn btn-primary"
                    id="predictBtn"
                    type="button">

                    Analisis Gambar

                </button>

                <div
                    class="sim-status"
                    id="predictStatus"
                    style="margin-top:20px;">

                    Belum ada gambar yang dipilih.

                </div>

            </div>

            <div class="sim-readout">

                <div class="sim-num">
                    <div class="v" id="prediction">
                        -
                    </div>

                    <div class="l">
                        Hasil Prediksi
                    </div>
                </div>

                <div class="sim-num">
                    <div class="v" id="confidence">
                        -
                    </div>

                    <div class="l">
                        Confidence
                    </div>
                </div>

                <div class="sim-num">
                    <div class="v" id="floodArea">
                        -
                    </div>

                    <div class="l">
                        Flood Area
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

{{-- ============ KESIMPULAN ============ --}}
<section id="kesimpulan">
  <div class="wrap">
    <div class="section-head reveal">
      <div class="eyebrow">Kesimpulan</div>
      <h2>Tidak ada yang superior mutlak</h2>
      <p>Pilihan arsitektur bergantung pada prioritas operasional di lapangan.</p>
    </div>
    <div class="rec-grid reveal">
      @foreach($recommendations as $r)
      <div class="rec-card">
        <div class="model">{{ $r['model'] }}</div>
        <h3>{{ $r['verdict'] }}</h3>
        <p>{{ $r['reason'] }}</p>
      </div>
      @endforeach
    </div>

    <div class="section-head reveal" style="margin-top:72px;">
      <div class="eyebrow">Saran Pengembangan</div>
      <h2>Tiga arah riset lanjutan</h2>
    </div>
    <div class="future-grid reveal">
      @foreach($futureWork as $f)
      <div class="future-card">
        <h4>{{ $f['title'] }}</h4>
        <p>{{ $f['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ============ FOOTER ============ --}}
<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div class="footer-col">
        <h5>Tentang Penelitian</h5>
        <p style="margin:0;">{{ $paper['title'] }}. Dipublikasikan pada {{ $paper['journal'] }}, {{ $paper['publisher'] }}.</p>
      </div>
      <div class="footer-col">
        <h5>Peneliti</h5>
        <ul>
          @foreach($paper['authors'] as $a)<li>{{ $a }}</li>@endforeach
        </ul>
      </div>
      <div class="footer-col">
        <h5>Pembimbing & Afiliasi</h5>
        <ul>
          @foreach($paper['advisors'] as $a)<li>{{ $a }}</li>@endforeach
        </ul>
        <p style="margin-top:8px;">{{ $paper['affiliation'] }}</p>
      </div>
    </div>
    <div class="footer-bottom">
      Landing page ini dibangun dengan Laravel (tampilan & data) dan layanan simulasi Python (demo metrik ilustratif) berdasarkan isi paper di atas.
    </div>
  </div>
</footer>

<script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
