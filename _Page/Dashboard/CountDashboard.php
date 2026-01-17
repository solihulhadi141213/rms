<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Set header JSON
    header('Content-Type: application/json');

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Siapkan variabel default
    $response = [
        "Diminta"    => "0",
        "Dikerjakan" => "0",
        "Hasil"      => "0",
        "Selesai"    => "0"
    ];

    // Default Tahun Sekarang
    $periode = date('Y');
    $start_date = $periode . '-01-01';
    $end_date = $periode . '-12-31 23:59:59';

    // Fungsi untuk format angka
    function formatNumber($number) {
        if ($number == 0) return "0";
        
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return (string)$number;
    }

    try {
        // Query tunggal yang dioptimalkan dengan conditional aggregation
        $query = "SELECT 
                    SUM(CASE WHEN status_pemeriksaan = 'Diminta' THEN 1 ELSE 0 END) as Diminta,
                    SUM(CASE WHEN status_pemeriksaan = 'Dikerjakan' THEN 1 ELSE 0 END) as Dikerjakan,
                    SUM(CASE WHEN status_pemeriksaan = 'Hasil' THEN 1 ELSE 0 END) as Hasil,
                    SUM(CASE WHEN status_pemeriksaan = 'Selesai' THEN 1 ELSE 0 END) as Selesai
                  FROM radiologi 
                  WHERE datetime_diminta BETWEEN ? AND ?";
        
        // Persiapkan statement
        $stmt = $Conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Error preparing query: " . $Conn->error);
        }
        
        // Bind parameter
        $stmt->bind_param("ss", $start_date, $end_date);
        
        // Eksekusi
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }
        
        // Bind result
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Format angka
            $response = [
                "Diminta"    => formatNumber($row['Diminta'] ?? 0),
                "Dikerjakan" => formatNumber($row['Dikerjakan'] ?? 0),
                "Hasil"      => formatNumber($row['Hasil'] ?? 0),
                "Selesai"    => formatNumber($row['Selesai'] ?? 0)
            ];
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        // Log error jika diperlukan
        error_log("Error in radiologi stats: " . $e->getMessage());
    }

    // Output JSON
    echo json_encode($response);
?>