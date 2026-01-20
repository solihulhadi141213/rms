<?php
    if (!empty($_POST['periode_durasi_pelayanan'])) {

        $periode = $_POST['periode_durasi_pelayanan'];
        $tahun_sekarang = date('Y');
        $tahun_awal = $tahun_sekarang - 5;

        // ====== PERIODE TAHUN ======
        if ($periode === "Tahun") {
            echo '
                <div class="row">
                    <div class="col-12">
                        <label for="tahun"><small>Tahun</small></label>
                        <select class="form-control" name="tahun" id="tahun">
                            <option value="">Pilih Tahun</option>
            ';

            for ($tahun = $tahun_sekarang; $tahun >= $tahun_awal; $tahun--) {
                echo '<option value="'.$tahun.'">'.$tahun.'</option>';
            }

            echo '
                        </select>
                    </div>
                </div>
            ';
        }

        // ====== PERIODE BULAN ======
        if ($periode === "Bulan") {

            $bulan = [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ];

            echo '
                <div class="row">
                    <div class="col-md-6">
                        <label for="tahun"><small>Tahun</small></label>
                        <select class="form-control" name="tahun" id="tahun">
                            <option value="">Pilih Tahun</option>
            ';

            for ($tahun = $tahun_sekarang; $tahun >= $tahun_awal; $tahun--) {
                echo '<option value="'.$tahun.'">'.$tahun.'</option>';
            }

            echo '
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="bulan"><small>Bulan</small></label>
                        <select class="form-control" name="bulan" id="bulan">
                            <option value="">Pilih Bulan</option>
            ';

            foreach ($bulan as $value => $nama) {
                echo '<option value="'.$value.'">'.$nama.'</option>';
            }

            echo '
                        </select>
                    </div>
                </div>
            ';
        }
    }
?>
