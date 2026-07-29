<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .funnel_outer {
            width: 420px;
            float: left;
            position: relative;
            padding: 0 10%;
        }

        .funnel_outer * {
            box-sizing: border-box
        }

        .funnel_outer ul {
            margin: 0;
            padding: 0;
        }

        .funnel_outer ul li {
            float: left;
            position: relative;
            margin: 2px 0;
            height: 50px;
            clear: both;
            text-align: center;
            width: 100%;
            list-style: none
        }

        .funnel_outer li span {
            border-top-width: 50px;
            border-top-style: solid;
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            height: 0;
            display: inline-block;
            vertical-align: middle;
        }

        .funnel_step_1 span {
            width: 100%;
            border-top-color: #8080b6;
        }

        .funnel_step_2 span {
            width: calc(100% - 50px);
            border-top-color: #669966
        }

        .funnel_step_3 span {
            width: calc(100% - 100px);
            border-top-color: #a27417
        }

        .funnel_step_4 span {
            width: calc(100% - 150px);
            border-top-color: #ff66cc
        }

        .funnel_step_5 span {
            width: calc(100% - 200px);
            border-top-color: #0099ff
        }

        .funnel_step_6 span {
            width: calc(100% - 250px);
            border-top-color: #027002
        }

        .funnel_step_7 span {
            width: calc(100% - 300px);
            border-top-color: #ff0000;
        }

        .funnel_outer ul li:last-child span {
            border-left: 0;
            border-right: 0;
            border-top-width: 40px;
        }

        .funnel_outer ul li.not_last span {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top-width: 50px;
        }

        .funnel_outer ul li span p {
            margin-top: -30px;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="funnel_outer">
        <ul>
            <li class="funnel_step_1"><span><p>1</p></span></li>
            <li class="funnel_step_2"><span><p>2</p></span> </li>
            <li class="funnel_step_3"><span><p>3</p></span></li>
            <li class="funnel_step_4"><span><p>4</p></span></li> 
            <li class="funnel_step_5"><span><p>5</p></span></li>
            <li class="funnel_step_6"><span><p>6</p></span></li>
            <li class="funnel_step_7"><span><p>7</p></span></li>

        </ul>
    </div>
    <div class="funnel_outer">
        <ul>
            <li class="funnel_step_1"><span><p>1</p></span></li>
            <li class="funnel_step_2"><span><p>2</p></span> </li>
            <li class="funnel_step_3"><span><p>3</p></span></li>
            <li class="funnel_step_4"><span><p>4</p></span></li> 
            <li class="funnel_step_5"><span><p>5</p></span></li>
            <li class="funnel_step_6"><span><p>6</p></span></li>
            <li class="funnel_step_7"><span><p>7</p></span></li>

        </ul>
    </div>
</body>

</html>