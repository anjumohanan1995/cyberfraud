<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
        <h1 style="font-size: 20px; margin-top: 0; text-align: center;"><b>Notice U/Sec 79(3)(b) of IT Act</b></h1>
        <p style="margin: 0; padding: 0; font-size: 16px; line-height: 1.5;">
            We are writing to bring your immediate attention to a matter of grave concern regarding the below mentioned <span style="font-weight: bold; color: red;">{{ implode(', ', array_unique($notice['domains'])) }}</span> on (<span style="font-weight: bold; color: red;">{{ $notice['evidence_type'] }}</span>) which involves <span style="font-weight: bold; color: red;">{{ implode(', ', array_unique($notice['categories'])) }}</span>.
            <br><br>
            As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to take down the below mentioned contents within 24 hours and preserve the details for further investigation.
            <br><br>
            As an intermediary, if you fail to remove or disable the unlawful content, the protection under Section 79 of the IT Act will not be applicable, and you will be liable for abetment.
        </p>
        <br>
        <h2 style="font-size: 18px; margin-top: 0; text-decoration: underline;"><b>Alleged URLs/Accounts:</b></h2>
        <ul style="list-style-type: none; padding: 0;">
            @foreach ($notice['urls'] as $index => $url)
                <li>{{ $index + 1 }}. <span style="border-bottom: 1px solid red; padding-bottom: 2px;"><a href="{{ $url }}">{{ $url }}</a></span></li>
            @endforeach
        </ul>
        <br>
        <p style="font-size: 16px; line-height: 1.5;">
            Urgent action and confirmation is solicited by return.
            <br><br>
            Contact us on: <a href="mailto:spcyberops.pol@kerala.gov.in" style="color: #1a0dab;">spcyberops.pol@kerala.gov.in</a>
        </p>
    </div>
</body>
