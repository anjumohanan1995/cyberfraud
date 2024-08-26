<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
        <h1 style="font-size: 20px; margin-top: 0; text-align: center;"><b>Notice U/Sec 79(3)(b) of IT Act</b></h1>
        <p style="margin: 0; padding: 0; font-size: 16px; line-height: 1.5;">
        We are writing to bring your immediate attention regarding a complaint that has been registered in National Cyber Crime Reporting Portal
        (<span style="font-weight: bold; color: red;">Acknowledgement No: {{ $notice['ack_no'] }}</span>) against the below mentioned website, which is involved in financial fraud.
        <br><br>
        On detailed investigation, it has been found that this website operates as a scam under the guise of obtaining confidential banking user credentials and engages in online financial fraud, causing illegal financial loss to the public. As stipulated by Section 79(3)(b) of the Information Technology Act of India, you are hereby directed to REMOVE/DISABLE the below-mentioned website within 24 hours and PRESERVE the details for further investigation.
        <br><br>
        As an intermediary, if you fail to remove or disable the unlawful content immediately, the protection for intermediaries under Section 79 of the IT Act will not be applicable and you will be liable for abetment.
    </p>
    <br>
    <h2 style="font-size: 18px; margin-top: 0; text-decoration: underline;"><b>Alleged Website:</b></h2>
    <ul style="list-style-type: none; padding: 0;">
        <li><strong>Website URL:</strong> <span style="font-weight: bold; color: red;"><a href="{{ $notice['urls'] }}">{{ $notice['urls'] }}</a></span></li>
        <li><strong>Domain name:</strong> <span style="font-weight: bold; color: red;">{{ $notice['domain_name'] }}</span></li>
        <li><strong>Registry Domain ID:</strong> <span style="font-weight: bold; color: red;">{{ $notice['domain_id'] }}</span></li>
    </ul>
    <br>
    <p style="font-size: 16px; line-height: 1.5;">
        Urgent action and confirmation are solicited by return.
        <br><br>
        Contact us on: <a href="mailto:spcyberops.pol@kerala.gov.in" style="color: #1a0dab;">spcyberops.pol@kerala.gov.in</a>
    </p>
</div>
</body>
