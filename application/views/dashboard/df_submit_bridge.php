<!DOCTYPE html>
<html>
<head>
    <title>Success - Processing PDF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <script type="text/javascript">
        window.onload = function() {
            var pdfUrl = "<?php echo $pdf_url; ?>";
            var dashboardUrl = "<?php echo $redirect_url; ?>";

            // Try auto-open PDF
            var link = document.createElement('a');
            link.href = pdfUrl;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();

            setTimeout(function(){
                window.open(pdfUrl, '_blank');
            }, 500);
        };
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #eef2f7, #f8fafc);
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background: #ffffff;
            padding: 45px 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .icon-box {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #e8f8ef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .icon-box i {
            font-size: 48px;
            color: #28a745;
        }

        h2 {
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        .lead {
            font-size: 16px;
            color: #555;
        }

        .alert {
            font-size: 13px;
            margin-top: 25px;
        }

        .btn-group-custom {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .btn-custom {
            padding: 12px 26px;
            font-size: 15px;
            border-radius: 6px;
            min-width: 180px;
        }

        .btn-dashboard {
            background: #6c757d;
            border-color: #6c757d;
        }

        .btn-dashboard:hover {
            background: #5a6268;
            border-color: #545b62;
        }

        .footer-note {
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>

<body>

<div class="success-card">
    
    <div class="icon-box">
        <i class="glyphicon glyphicon-ok"></i>
    </div>

    <h2>Record Saved Successfully</h2>
    <p class="lead">
        Your data has been saved and DF has been generated.
    </p>

   

    <!-- BUTTONS SIDE BY SIDE -->
    <div class="btn-group-custom">
        <a href="<?php echo $pdf_url; ?>" target="_blank" class="btn btn-primary btn-custom">
            <i class="glyphicon glyphicon-file"></i> View PDF
        </a>

        <a href="<?php echo page_url;?>Dashboard" class="btn btn-dashboard btn-custom" style="color:#fff !important">
            <i class="glyphicon glyphicon-home"></i> Go to Dashboard
        </a>
    </div>

    <div class="footer-note">
        You can safely close this page after viewing the PDF.
    </div>

</div>

</body>
</html>
