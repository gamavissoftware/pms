<!DOCTYPE html>
<!-- application/views/reports/search_view.php -->
<!-- NEW: Added a full-screen loader that displays on form submission. -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Report Search</title>
    
    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .search-container {
            max-width: 550px;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 2.5rem 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            background: -webkit-linear-gradient(45deg, #007bff, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .search-container h1 {
            font-weight: 600;
            color: #1a253c;
            margin-bottom: 0.5rem;
        }
        .search-container p {
            color: #6c757d;
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
        }
        .input-group {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .input-group-text {
            background-color: transparent;
            border-right: 0;
            color: #007bff;
        }
        .form-control { border-left: 0; padding-left: 0; }
        .form-control:focus { box-shadow: none; border-color: #dee2e6; }
        .btn-primary {
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.25);
        }

        /* --- NEW: Loader Styles --- */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            display: none; /* Initially hidden */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #e9ecef; /* Light grey */
            border-top: 5px solid #007bff; /* Blue */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        .loader-overlay p {
            margin-top: 1.5rem;
            font-size: 1.2rem;
            font-weight: 500;
            color: #343a40;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* --- End Loader Styles --- */
    </style>
</head>
<body>

    <!-- NEW: Loader Overlay element -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="spinner"></div>
        <p>Fetching Report...</p>
    </div>

    <div class="search-container">
        <div class="logo">SPM</div> 
        <h1>Production Journey</h1>
        <p>Track any drawing number from start to finish.</p>

        <?php echo form_open(page_url.'report/process_search', ['class' => 'needs-validation', 'id' => 'search-form']); ?>
            <div class="input-group input-group-lg mb-4">
                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                <input type="text" name="drawing_no" id="drawing_no" class="form-control" placeholder="Enter Drawing No." required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Find Report
            </button>
        <?php echo form_close(); ?>

        <?php if(validation_errors()): ?>
            <div class="alert alert-danger d-flex align-items-center mt-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <?php echo validation_errors(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- NEW: JavaScript to control the loader -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('search-form');
            const loaderOverlay = document.getElementById('loader-overlay');

            if (searchForm && loaderOverlay) {
                searchForm.addEventListener('submit', function(event) {
                    // Check if the form is valid before showing the loader
                    if (searchForm.checkValidity()) {
                        loaderOverlay.style.display = 'flex';
                    }
                });
            }
        });
    </script>
</body>
</html>
