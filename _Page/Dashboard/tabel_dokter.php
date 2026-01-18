<?php
    include "../../_Config/Connection.php";

    // Keyword (periode)
    $Keyword = !empty($_POST['Keyword']) ? $_POST['Keyword'] : date('Y');

    // Query utama (1x query saja)
    $sql = "
        SELECT 
            nama_dokter_penerima,
            COUNT(*) AS jumlah
        FROM radiologi
        WHERE datetime_diminta LIKE '%$Keyword%'
        AND nama_dokter_penerima IS NOT NULL
        AND nama_dokter_penerima != ''
        GROUP BY nama_dokter_penerima
        ORDER BY nama_dokter_penerima ASC
    ";

    $query = mysqli_query($Conn, $sql);

    if (mysqli_num_rows($query) == 0) {
        echo '
            <tr>
                <td colspan="2" class="text-center">
                    <small class="text-danger">Tidak Ada Data Yang Ditampilkan</small>
                </td>
            </tr>
        ';
        exit;
    }

    // Hitung total keseluruhan
    $total_semua = 0;
    $data_rows = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $total_semua += $row['jumlah'];
        $data_rows[] = $row;
    }

    // Function format angka
    function formatAngkaSingkat($num) {
        if ($num >= 1000000000) return round($num/1000000000,1).'B';
        if ($num >= 1000000) return round($num/1000000,1).'M';
        if ($num >= 1000) return round($num/1000,1).'K';
        return number_format($num);
    }

    // Output data
    foreach ($data_rows as $data) {

        $jumlah     = (int)$data['jumlah'];
        $persen     = $total_semua > 0 ? round(($jumlah / $total_semua) * 100, 1) : 0;
        $jumlah_fmt = formatAngkaSingkat($jumlah);

        echo '
            <tr>
                <td>
                    <small class="text-grayish">'.$data['nama_dokter_penerima'].'</small>

                    <div class="progress mt-1" style="height:6px;">
                        <div class="progress-bar bg-primary"
                            role="progressbar"
                            style="width: '.$persen.'%"
                            aria-valuenow="'.$persen.'"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>

                    <small class="text-muted d-block mt-1" style="font-size:11px;">
                        '.$persen.'%
                    </small>
                </td>

                <td class="text-end align-middle">
                    <small class="text-grayish">'.$jumlah_fmt.'</small>
                </td>
            </tr>
        ';
    }
?>
<script>
    $('#periode_dokter').html('Periode <?php echo htmlspecialchars($Keyword); ?>');
</script>
