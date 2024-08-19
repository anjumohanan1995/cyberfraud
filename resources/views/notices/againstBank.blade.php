<style>
    .container {
           display: flex;
           align-items: center;
           justify-content: space-between;
           text-align: center;
           margin: 20px 0;
           margin-left:20px;
            margin-right:20px;
       }

       .container img {
           height: 200px;
           width: 200px;
       }

       .text {
           flex: 1;
           text-align: center;
       }

       .left,
       .right {
           width: 150px;
       }

       .center {
           flex: 1;
           display: flex;
           flex-direction: column;
           align-items: center;
       }

       .footer {
           display: flex;
           justify-content: flex-end;
           margin-top: 20px;
           text-align: right;
           margin-right:10px;
       }

       .footer p {
           font-size: 20px;
           line-height: 1.5;
           margin: 0;
       }
       .for{
           display: flex;
           justify-content: flex-end;
           text-align: right;
           margin-right:10px;
       }
       body{
           margin-left:20px;
           margin-right:20px;
           margin:10%;

       }
       tbody{
           font-size:18px;
       }
</style>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4;  padding:20px; ">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; margin-left:20px; margin-right:20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-bottom: 20px;">
        <b>
            <h2 style=" margin-top: 0; text-align: center;"><u>
            Notice U/s 94 of Bharatiya Nagarik Suraksha Sanhita, 2023 (BNSS) </u>
        </b>
        <br><br>
        <p style="font-size: 19px; line-height: 1.5;">
                The following complaints are registered at National Cyber Crime Reporting
                Portal (NCRP) for financial fraud in which the accused used your bank’s
                accounts for the alleged crime. For the purposes of investigation, the details
                of the following bank accounts are inevitable. Hence It is requested to
                provide the following details to this Office at the earliest.
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">S No.</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Acknowledgement Number</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Bank/ (Wallet /PG/PA)/
                    Merchant / Insurance </th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Account No./ (Wallet
                    /PG/PA) Id </th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">State</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notice as $index =>   $account)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $index + 1 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $account['acknowledgement_no'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $account['bank'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $account['account_no_2'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">Kerala</td>
                    </tr>
            </tbody>
        </table>

        <p style="font-size: 19px; line-height: 1.5; margin-top: 20px;">
            {{-- <pre> --}}
        Details Required:
        <br>
            1. KYC details, including Adhaar, PAN, primary or alternate mobile contact
            number,email, etc., of the account holder
            <br>
            2. If any other current or savings bank accounts are opened at Your Bank
            furnish those details.
            <br>
            3. Net banking Login access IP details with time stamp from <b>{{ $from_date }} </b>to <b>{{ $to_date }} </b>of the above mentioned bank accounts with date and time
            <br>
            4. IP details with time stamp of NEFT/IMPS transactions of above mentioned
            period.<br>
            5. Any other relevant information about account or account holder.
</pre>
@endforeach

    </p>

        <p style="font-size: 19px; line-height: 2; text-align:center;">
            Urgent action and confirmation is solicited by return.<br>
            <b>
                Contact us on: <a href="mailto:k4cict.pol@kerala.gov.in" style="color: #1a0dab;">k4cict.pol@kerala.gov.in</a>
            </b>
        </p>
        <div id="signature-container" style="margin-top: 20px;"></div>
    </div>
</body>
