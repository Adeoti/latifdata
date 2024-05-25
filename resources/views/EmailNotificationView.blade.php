<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$appName}} {{$subject}}</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #dddddd;
        }
        .email-header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 20px 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .email-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #dddddd;
            font-size: 14px;
            color: #777777;
        }
        .email-footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="{{ asset("assets/images/sweetbilllogob.svg") }}" style="height:50px;" alt="{{$appName}} Logo">
            <h1>{{$appName}}</h1>
        </div>
        <div class="email-body">
            @if (!empty($emailRecipient))
                <p>Hi 
                    
                    <b>{{$emailRecipient}},</b>
                
                </p>
            @endif
            <p>{{$emailMessage}}</p>
            <p>Thank you!</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{$appName}}. All rights reserved.</p>
            <p><a href="https://wa.link/sbdozm" style="color: #fe5006; text-decoration: none;">Chat Us on WhatsApp</a></p>
        </div>
    </div>
</body>
</html>
