<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP SKM Amanat</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #333333; margin: 0;">SKM Amanat</h2>
        </div>
        
        <p style="color: #555555; font-size: 16px;">Halo,</p>
        
        <p style="color: #555555; font-size: 16px; line-height: 1.5;">
            @if($type === 'reset')
                Anda menerima email ini karena ada permintaan untuk mengatur ulang kata sandi akun Anda di SKM Amanat.
            @else
                Terima kasih telah mendaftar di SKM Amanat. Untuk memverifikasi alamat email Anda, silakan gunakan kode OTP berikut:
            @endif
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <span style="display: inline-block; padding: 15px 30px; background-color: #F53003; color: #ffffff; font-size: 24px; font-weight: bold; border-radius: 5px; letter-spacing: 5px;">
                {{ $otpCode }}
            </span>
        </div>

        <p style="color: #555555; font-size: 16px; line-height: 1.5;">
            Kode OTP ini hanya berlaku selama <strong>10 menit</strong>. Jangan berikan kode ini kepada siapa pun.
        </p>

        @if($type === 'reset')
            <p style="color: #555555; font-size: 14px; margin-top: 30px; border-top: 1px solid #eeeeee; padding-top: 20px;">
                Jika Anda tidak merasa meminta reset kata sandi, abaikan email ini.
            </p>
        @endif

        <p style="color: #999999; font-size: 12px; text-align: center; margin-top: 40px;">
            &copy; {{ date('Y') }} SKM Amanat. Semua Hak Cipta Dilindungi.
        </p>
    </div>
</body>
</html>
