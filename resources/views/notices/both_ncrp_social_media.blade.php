<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4;">
    {{-- @foreach ($noticeData as $notice) --}}
        <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
            <h1 style="font-size: 20px; margin-top: 0; text-align: center;"><b>Notice U/Sec. 94 of BNSS & Sec. 79(3)(b) of IT Act 2000</b></h1>
            <p style="margin: 0; padding: 0; font-size: 16px; line-height: 1.5;">
                We are writing to bring your immediate attention to a matter regarding a complaint which has been registered in the National Cyber Crime Reporting Portal (Acknowledgement No: <span style="font-weight: bold; color: red;">{{ $notice['ack_no'] }}</span>) against the below mentioned <span style="font-weight: bold; color: red;">{{ implode(', ', array_unique($notice['domains'])) }}</span> on (<span style="font-weight: bold; color: red;">{{ $notice['evidence_type'] }}</span>) which involves in <span style="font-weight: bold; color: red;">{{ implode(', ', array_unique($notice['categories'])) }}</span>.
                <br><br>
                As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to REMOVE/DISABLE and PRESERVE the aforesaid content posted on your social media platform and, as per Section 94 of Bharatiya Nagarik Suraksha Sanhita (BNSS), also directed to PROVIDE the details associated with the alleged URLs to this office at the earliest.
                <br><br>
                As an intermediary, if you fail to remove or disable the unlawful contents immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable, and you will be liable for abetment.
            </p>
            <br>
            <h2 style="font-size: 18px; margin-top: 0;">Alleged URLs/Accounts:</h2>
            <ul style="list-style-type: none; padding: 0;">
                @foreach ($notice['urls'] as $index => $url)
                    <li>{{ $index + 1 }}. <span style="border-bottom: 1px solid red; padding-bottom: 2px;"><a href="{{ $url }}">{{ $url }}</a></span></li>
                @endforeach
            </ul>
            <br>
            <h2 style="font-size: 18px; margin-top: 0;">Details Required:</h2>
            <ol style="padding-left: 20px;">
                <li>Registration details of the aforementioned account.</li>
                <li>Primary / alternate e-mail IDs, contact numbers associated with the aforementioned account.</li>
                <li>Registration IP address at the time of creation and last login IP address.</li>
            </ol>
            <br>
            <p style="font-size: 16px; line-height: 1.5;">
                Urgent action and confirmation is solicited by return.
                <br><br>
                Contact us on: <a href="mailto:spcyberops.pol@kerala.gov.in" style="color: #1a0dab;">spcyberops.pol@kerala.gov.in</a>
            </p>
        </div>
    {{-- @endforeach --}}
</body>
