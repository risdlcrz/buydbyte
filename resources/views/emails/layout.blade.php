<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BuyDbyte')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }
        
        .email-header .subtitle {
            margin: 10px 0 0 0;
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .email-body h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .email-body p {
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .email-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
        
        .security-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            font-size: 0.9rem;
            color: #856404;
        }
        
        .token-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            text-align: center;
            margin: 20px 0;
            word-break: break-all;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 5px;
            }
            
            .email-header {
                padding: 20px;
            }
            
            .email-header h1 {
                font-size: 2rem;
            }
            
            .email-body {
                padding: 20px;
            }
            
            .btn {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🖥️ BuyDbyte</h1>
            <div class="subtitle">Computer Hardware & Peripherals</div>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>This email was sent from <strong>BuyDbyte</strong> - Your trusted computer hardware store.</p>
            <p>
                <a href="{{ url('/') }}">Visit our website</a> | 
                <a href="mailto:support@buydbyte.com">Contact Support</a>
            </p>
            <p style="font-size: 0.8rem; color: #999;">
                © {{ date('Y') }} BuyDbyte. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>