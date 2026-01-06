//Ketika download_dicom_router di click
$('.download_dicom_router').click(function(){
    $('#hasil_download_instaler').html('Loading...');
    $.ajax({
        type 	    : 'POST',
        url 	    : '_Page/DicomRouter/DownloadInstaler.php',
        success     : function(data){
            $('#hasil_download_instaler').html(data);
        }
    });
});