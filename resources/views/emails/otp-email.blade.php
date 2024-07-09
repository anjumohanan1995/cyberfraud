<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>

<body style="font-family: Arial, sans-serif;">

    <table align="center" width="100%" cellspacing="0" cellpadding="0"
        style="border-collapse: collapse; margin: auto; max-width: 600px; padding: 20px; border: 1px solid #ddd;">
        <tr>
            <td align="center" bgcolor="#f8f9fa" style="padding: 20px;">
                <h2 style="margin: 0; color: #333;">Forgot Your Password?</h2>
            </td>
        </tr>
        <tr>
            <td bgcolor="#ffffff" style="padding: 20px;">
                <p style="margin-bottom: 20px;">Hi there,</p>
                <p>We received a request to reset your password. Your OTP is</p>
                <p style="margin-top: 0; margin-bottom: 20px;">
                    {{-- <a href="{{ route('reset.password', $token) }}"
                        style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px;">Reset
                        Password</a> --}}
                    <span><b>{{ $otp }}</b></span>    
                </p>
               
            </td>
        </tr>
        <tr>
            <td bgcolor="#f8f9fa" style="padding: 20px;">
                <p style="margin: 0; color: #333;">Best Regards,<br>Your Company</p>
            </td>
        </tr>
    </table>

</body>

</html>
