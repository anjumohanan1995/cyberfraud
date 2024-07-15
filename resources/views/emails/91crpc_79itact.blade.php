<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notices[0]['sub'] }}</title>
    <style>
        /* Style your email here */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin: 20px auto;
            max-width: 600px;
        }
        h2 {
            color: #333333;
        }
        p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    {{-- Container with styling --}}
    <div class="container">

        @foreach($notices as $notice)
            <div class="mb-3">
                <p><strong>Sub:</strong> <span style="color: blue;">{{ $notice['sub'] }}</span></p>
            </div>

            <div class="mb-3">
                <p><strong>Salutation:</strong> <span style="color: green;">Team Register name</span></p>
            </div>

            <div class="mb-3">
                <p><strong>Content:</strong></p>
                <p>
                    A complaint in NO: <span style="font-weight: bold;">{{ $notice['number'] }}</span> is reported at National Cyber Crime Reporting Portal (NCRP) for financial fraud in which an Unlawful Website with the URL
                    <a href="{{ $notice['url'] }}" target="_blank" style="color: red;">{{ $notice['url'] }}</a> is involved and it is found that the website is hosted in your registry for propagating cyber fraud. Hence it is directed to provide the details of the below mentioned website by return and also directed to disable the Website within 48 Hrs in order to prevent further Cyber fraud and to ensure the protection of potential victims.
                </p>
                <p>
                    As an Intermediary if you fail to remove or disable the Unlawful website the protection U/s 79 of IT Act will not be applicable and you will be liable for abetment.
                </p>
            </div>

            <div class="mb-3">
                <p><strong>Alleged Website Details:</strong></p>
                <p>
                    <a href="{{ $notice['url'] }}" target="_blank" style="color: red;">{{ $notice['url'] }}</a><br>
                    Domain Name: <span style="color: purple;">{{ $notice['domain_name'] }}</span><br>
                    Registry Domain Details: <span style="color: orange;">{{ $notice['domain_id'] }}</span>
                </p>
            </div>

            <div class="mb-3">
                <p><strong>Details Required:</strong></p>
                <ol>
                    <li>Registration details including:
                        <ul>
                            <li>Email ID</li>
                            <li>Mobile phone numbers</li>
                            <li>IP address with Date and Time</li>
                            <li>Mode of payment details for registration</li>
                        </ul>
                    </li>
                    <li>Any other Sub domains with the above registration email id or mobile number.</li>
                    <li>Registration Details as mentioned in (1) for domain identified under (2)</li>
                </ol>
            </div>

            <p>Urgent action and confirmation is solicited by return.</p>
            <hr> <!-- Optional: Add a separator between notices -->
        @endforeach

    </div>
</body>
</html>
