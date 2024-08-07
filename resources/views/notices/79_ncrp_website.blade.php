<body style="font-family: Arial, sans-serif; color: #000; line-height: 1.6;">
    <div style="font-weight: bold; font-size: 18px; text-align: center; margin-bottom: 20px;">
        Notice U/Sec. 79(3)(b) of IT Act
    </div>
    <div style="font-size: 16px; max-width: 800px; margin: 0 auto; padding: 0 20px;">
        <p style="margin: 20px 0;">
                         We are writing to bring your immediate attention regarding a complaint that has been registered in National Cyber Crime Reporting Portal (Acknowledgement No: <span style="font-weight: bold; color: red;">{{ $notice['ack_no'] }}</span>) against the below mentioned website, which is involved in financial fraud.
        </p>

        <p style="margin: 20px 0;">
                         On detailed investigation, it has been found that this website operates as a scam under the guise of obtaining confidential banking user credentials and engages in online financial fraud, causing illegal financial loss to the public. As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to <span style="font-weight: bold;">REMOVE/DISABLE</span> the below-mentioned website within 24 hours and <span style="font-weight: bold;">PRESERVE</span> the details for further investigation.
        </p>

        <p style="margin: 20px 0;">
                         As an intermediary, if you fail to remove or disable the unlawful content immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable and you will be liable for abetment.
        </p>

        <div style="margin-bottom: 20px;">
            <b>Alleged Website:</b>
            <br>
            <ul style="padding-left: 20px;">
                <li><span style="font-weight: bold; color: red;">Website URL: <a href="{{ $notice['urls'] }}">{{ $notice['urls'] }}</a></span></li>
                <li><span style="font-weight: bold; color: red;">Domain name: {{ $notice['domain_name'] }}</span></li>
                <li><span style="font-weight: bold; color: red;">Registry Domain ID: {{ $notice['domain_id'] }}</span></li>
            </ul>
        </div>

        <div class="contact" style="text-align: center; margin-top: 20px;">
            Urgent action and confirmation is solicited by return
            <br>
            Contact us on: <a href="mailto:cyberops-fsm.pol@kerala.gov.in">cyberops-fsm.pol@kerala.gov.in</a>
        </div>
    </div>
</body>
