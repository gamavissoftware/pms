<!DOCTYPE html>
<html>
<head>
    <title>Respond to Meeting Invitation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 50px auto;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 150px;
        }
        h3 {
            color: #4872b8;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #333333;
        }
        .btn-primary {
            background-color: #4872b8;
            border-color: #4872b8;
            padding: 10px 20px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #365a8c;
            border-color: #365a8c;
        }
        #reasonDiv {
            margin-top: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <img src="https://shubhampack.com/wp-content/uploads/2021/05/Logo.png" alt="Logo" class="logo">
    <h3>Respond to Meeting Invitation</h3><hr>
    <?php echo $this->session->flashdata('message');?>
    
    <form id="responseForm" method="post" action="<?php echo page_url; ?>Form/submitResponse">
        <input type="hidden" name="meeting_id" value="<?php echo $this->uri->segment(3); ?>" />
        <input type="hidden" name="participant_id" value="<?php echo $this->uri->segment(4); ?>" />
        
        <div class="form-group">
            <label for="response">Your Response:</label>
            <select class="form-control" id="response" name="response" required>
                <option value="">Select</option>
                <option value="Available">Available to Join</option>
                <option value="Not Available">Not Available to Join</option>
            </select>
        </div>
        
        <div class="form-group" id="reasonDiv" style="display: none;">
            <label for="reason">Reason for Not Being Available:</label>
            <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Please specify your reason..."></textarea>
        </div>
        
        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary">Submit Response</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        $('#response').on('change', function () {
            if ($(this).val() === 'Not Available') {
                $('#reasonDiv').slideDown();
                $('#reason').attr('required', true);
            } else {
                $('#reasonDiv').slideUp();
                $('#reason').removeAttr('required');
            }
        });
    });
</script>
</body>
</html>
