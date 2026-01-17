<?php
    // Koneksi
    include "../../_Config/Connection.php";

    // Set header JSON
    header('Content-Type: application/json');

    // Zona Waktu
    date_default_timezone_set("Asia/Jakarta");

    // Siapkan variabel default
    $response = [
        "count"           => "--",
        "periode_display" => "None",
        "error"           => false,
        "message"         => ""
    ];

    // Validasi input POST
    if(empty($_POST['Periode']) || empty($_POST['Keyword'])) {
        $response['error'] = true;
        $response['message'] = "Parameter Periode dan Keyword tidak boleh kosong";
        echo json_encode($response);
        exit;
    }

    // Fungsi untuk format angka
    function formatNumber($number) {
        if ($number == 0) return "0";
        
        if ($number >= 1000000000) {
            $value = $number / 1000000000;
            return (round($value, 1) == intval($value) ? intval($value) : round($value, 1)) . 'B';
        } elseif ($number >= 1000000) {
            $value = $number / 1000000;
            return (round($value, 1) == intval($value) ? intval($value) : round($value, 1)) . 'M';
        } elseif ($number >= 1000) {
            $value = $number / 1000;
            return (round($value, 1) == intval($value) ? intval($value) : round($value, 1)) . 'K';
        }
        return (string)$number;
    }

    // Fungsi untuk validasi format tanggal
    function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    try {
        // Tangkap dan sanitasi data
        $periode = trim($_POST['Periode']);
        $keyword = trim($_POST['Keyword']);
        
        // Validasi periode
        if (!in_array($periode, ['Hari', 'Bulan', 'Tahun'])) {
            throw new Exception("Periode tidak valid. Harus 'Hari', 'Bulan', atau 'Tahun'");
        }

        // Proses berdasarkan periode
        $periode_display = "";
        $query_condition = "";
        $params = [];
        
        if($periode == "Tahun") {
            // Validasi format tahun (4 digit)
            if (!preg_match('/^\d{4}$/', $keyword)) {
                throw new Exception("Format tahun tidak valid. Gunakan format 4 digit (contoh: 2024)");
            }
            
            // Format untuk display
            $periode_display = "Tahun $keyword";
            
            // Query condition untuk tahun
            $year = intval($keyword);
            $query_condition = "YEAR(datetime_diminta) = ?";
            $params = [$year];
            
        } else if($periode == "Bulan") {
            // Validasi format bulan (YYYY-MM atau nama bulan)
            $date = strtotime($keyword);
            if (!$date) {
                throw new Exception("Format bulan tidak valid. Gunakan format seperti '2024-01', 'Januari 2024', atau 'Jan 2024'");
            }
            
            // Format untuk display
            $periode_display = "Bulan " . date('F Y', $date);
            
            // Query condition untuk bulan dan tahun
            $month = date('m', $date);
            $year = date('Y', $date);
            $query_condition = "YEAR(datetime_diminta) = ? AND MONTH(datetime_diminta) = ?";
            $params = [$year, $month];
            
        } else if($periode == "Hari") {
            // Coba berbagai format tanggal
            $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d', 'd F Y', 'F d, Y'];
            $date_obj = false;
            
            foreach ($formats as $format) {
                $date_obj = DateTime::createFromFormat($format, $keyword);
                if ($date_obj !== false) {
                    break;
                }
            }
            
            if ($date_obj === false) {
                // Coba dengan strtotime sebagai fallback
                $timestamp = strtotime($keyword);
                if ($timestamp !== false) {
                    $date_obj = new DateTime();
                    $date_obj->setTimestamp($timestamp);
                }
            }
            
            if ($date_obj === false) {
                throw new Exception("Format tanggal tidak valid. Gunakan format seperti '2024-01-15', '15-01-2024', '15 Januari 2024', atau 'Jan 15, 2024'");
            }
            
            // Format untuk display (Indonesia)
            $hari_indonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $bulan_indonesia = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            $hari = $hari_indonesia[$date_obj->format('w')];
            $tanggal = $date_obj->format('j');
            $bulan = $bulan_indonesia[$date_obj->format('n') - 1];
            $tahun = $date_obj->format('Y');
            
            $periode_display = "Hari $hari, $tanggal $bulan $tahun";
            
            // Query condition untuk hari tertentu
            $tanggal_query = $date_obj->format('Y-m-d');
            $query_condition = "DATE(datetime_diminta) = ?";
            $params = [$tanggal_query];
        }

        // Query dengan prepared statement
        $query = "SELECT COUNT(id_radiologi) as total 
                  FROM radiologi 
                  WHERE status_pemeriksaan = 'Diminta' 
                  AND $query_condition";
        
        // Prepare statement
        $stmt = $Conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing query: " . $Conn->error);
        }
        
        // Bind parameters berdasarkan jumlah parameter
        if($periode == "Tahun" || $periode == "Hari") {
            // Tahun: 1 parameter integer, Hari: 1 parameter string
            $param_type = ($periode == "Tahun") ? "i" : "s";
            $stmt->bind_param($param_type, $params[0]);
        } else {
            // Bulan: 2 parameter integer
            $stmt->bind_param("ii", $params[0], $params[1]);
        }
        
        // Execute query
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }
        
        // Get result
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Error getting result: " . $stmt->error);
        }
        
        // Fetch data
        $row = $result->fetch_assoc();
        $jml_data = $row['total'] ?? 0;
        
        // Format angka
        $jml_data_format = formatNumber($jml_data);
        
        // Prepare response
        $response = [
            "count"           => $jml_data_format,
            "periode_display" => $periode_display,
            "error"           => false,
            "message"         => "Success",
            "raw_count"       => $jml_data // Optional: tambahkan count asli untuk keperluan lain
        ];
        
        // Close statement
        $stmt->close();
        
    } catch (Exception $e) {
        // Error handling
        $response = [
            "count"           => "--",
            "periode_display" => "Error",
            "error"           => true,
            "message"         => $e->getMessage()
        ];
    } finally {
        // Close connection if needed
        if (isset($Conn) && $Conn) {
            $Conn->close();
        }
    }

    // Output JSON
    echo json_encode($response);
?>