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
        {{-- <div class="container"> --}}
            {{-- <div class="left text">
                <h3>POLICE</h1>
            </div>
            <div class="center">
                <img src="{{ asset('images/logo.jfif') }}" alt="logo">
                <h3>KERALA</h1>
            </div>
            <div class="right text">
                <h3>DEPARTMENT</h1>
            </div>
        </div>

        <div class="footer">
            <h5>
                <u>No. IP(C4)-12442/2024/Cyb(18)</u><br>
                Cyber Police Headquarters,<br>
                Thiruvananthapuram<br>
                Email: <a href="mailto:sptele.pol@kerala.gov.in" style="color: #1a0dab;">sptele.pol@kerala.gov.in</a><br>
                Phone: 04712448707<br>
                Dated: <strong>{{ $notice[0]['date'] }}</strong><br>
                {{-- <strong>HARISANKAR IPS</strong><br>
                Superintendent of Police (ic)<br> --}}
            {{-- </h5>
        </div> --}}
        <b>
            <h2 style=" margin-top: 0; text-align: center;"><u>
                NOTICE U/s 168 of BHARATIYA NAGARIK SURAKSHA SANHITA (BNSS)-2023</u></h2>


        <h3 style="margin-top: 20px;">Subject: <u> Notice for immediate intervention to prevent cyber fraud</u></h3>
    </b>
        <p style="font-size: 19px; line-height: 1.5;">
            As per the complaint reported in National Cyber Crime Reporting Portal (NCRP) with
            the following acknowledgement numbers and corresponding account numbers of <b>
             {{$notice[0]['bank']}}</b>, accounts are found to be utilized for propagating cyber fraud.
              Disabling the accounts is warranted to prevent cyber abuse and to ensure the protection of potential victims.
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">S No.</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Acknowledgement Number</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Bank/Wallet/Merchant/Insurance Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Account Number</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">State</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2;">Layer</th>
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
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            {{-- {{ $account['Layer'] }} --}}

                            @if ($account['Layer'] == 1)
                            1
                        @elseif ($account['Layer'] > 1 && $account['action_taken_by_bank'] != 'Cash Withdrawal through Cheque' && $account['action_taken_by_bank'] != 'Withdrawal through ATM')
                            {{ $account['Layer'] }}
                        @else
                        {{ $account['action_taken_by_bank'] }}
                        @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="font-size: 19px; line-height: 1.5; margin-top: 20px;">
            Hence, I call upon you to take immediate action to block the said accounts from conducting further transactions, in the interest of criminal justice. A copy of relevant documents and other relevant information is enclosed herewith.
        </p>

        <p style="font-size: 19px; line-height: 2; text-align:center;">
            Urgent action and confirmation is solicited by return.<br>
            <b>
                Contact us on: <a href="mailto:k4cict.pol@kerala.gov.in" style="color: #1a0dab;">k4cict.pol@kerala.gov.in</a>
            </b>
        </p>

        <p style="font-size: 19px; line-height: 1.5; margin-top: 20px;" class="for">
            {{-- <strong>{{ $notice['reference_number'] }}</strong> <span style="float: right;">{{ $notice['unique_code'] }}</span><br> --}}
            For Inspector General of Police (Cyber Operations)
        </p>

        <p style="font-size: 19px; line-height: 1.5;" class="to">
            To: The Nodal Officer,  {{$notice[0]['bank']}}, Kerala.
        </p>
    </div>
</body>
