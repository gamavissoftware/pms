<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Barcode</title>
</head>
<body>
    <img src="<?php echo site_url('BarcodeController/generate'); ?>" alt="Barcode Image">
    <form action="<?php echo site_url('BarcodeController/update_record'); ?>" method="POST">
        <label for="barcode">Scan Barcode:</label>
        <input type="text" id="barcode" name="barcode" autofocus>
        <button type="submit">Submit</button>
    </form>
</body>
</html>