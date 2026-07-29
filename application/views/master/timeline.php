<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Timeline</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .timeline {
            position: relative;
            width: 600px;
            padding: 10px 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #FF9F55;
            left: 50%;
            transform: translateX(-50%);
        }
        .timeline-item {
            position: relative;
            width: 50%;
            padding: 20px 40px;
            box-sizing: border-box;
        }
        .timeline-item.left {
            left: 0;
        }
        .timeline-item.right {
            left: 50%;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: #FF9F55;
            border: 3px solid white;
            border-radius: 50%;
            top: 20px;
            right: -30px;
        }
        .timeline-item.right::before {
            left: -30px;
        }
        .timeline-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .timeline-content h2 {
            margin: 0 0 10px;
            font-size: 1.2em;
            color: #333;
        }
        .timeline-content p {
            margin: 5px 0;
            color: #666;
        }
    </style>
</head>
<body>

<div class="timeline">
    <div class="timeline-item left">
        <div class="timeline-content">
            <h2>Ticket #12345</h2>
            <p>Raised on: 2024-07-01 10:30 AM</p>
            <p>Comments: 5</p>
        </div>
    </div>
    <div class="timeline-item right">
        <div class="timeline-content">
            <h2>Ticket #12346</h2>
            <p>Raised on: 2024-07-01 11:00 AM</p>
            <p>Comments: 3</p>
        </div>
    </div>
    <div class="timeline-item left">
        <div class="timeline-content">
            <h2>Ticket #12347</h2>
            <p>Raised on: 2024-07-01 12:00 PM</p>
            <p>Comments: 8</p>
        </div>
    </div>
    <!-- Add more tickets here -->
</div>

</body>
</html>
