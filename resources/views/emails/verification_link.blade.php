<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome to Udex! Verify your email address to get started</title>
<style type="text/css">
table {
    border-collapse: collapse;
    border-color:#ccc;
     font-family:Arial, Helvetica, sans-serif ;
}
</style>
</head>
<body>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
     <!-- <td width="197" align="right" valign="top" style="background-color:#17DA8A"><img src="https://www.wiaipi.com/public/assets/front-end/images/logo.png" width="197" height="61" style="display:block;"></td> -->
      <td align="center" valign="middle" bgcolor="" style="background-color:#17DA8A ; padding:20px; color:#332C41;font-size:28px; ">
      <div style="font-size:24px;">Email Verification</div>
    </td>
  </tr>
</table>
<table width="600" border="1" align="center" cellpadding="0" cellspacing="1" bgcolor="#971800" style="background-color:#fff;">
      <tr>
          <td align="center" valign="top" bgcolor="#ffffff" >
            <table width="90%" border="0" cellspacing="0" cellpadding="10" style="margin-bottom:10px;">
              <tr>
                <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000;"> 
                  <div>
                        <p>Dear {{$content['name']}},</p>
                        <p>Welcome !</p>
                        <p>Thank you for registration. To activate
your account, please click the link below to confirm your email address and
get started with better interview evaluations.
                        </p>
                        <p>
                          <a href="{{ url('api/v1/email_verification?verification_code='.$content['verification_token'].'&email='.$content['email']) }}">
                          Verify Your E-mail Address  
                        </a> 
                      </p>
                      <p> Or copy & paste this link into your browser.<br>
                        <a href="{{ url('api/v1/email_verification?verification_code='.$content['verification_token'].'&email='.$content['email']) }}">
                          {{ url('api/v1/email_verification?verification_code='.$content['verification_token'].'&email='.$content['email']) }}</a> 
                      </p> 
                          <p>Best Regards,</p>
                          <p>Team Syncabi</p>

                  </div>
                </td>
            </tr>
          </table>
        </td>
      </tr>
  </table>
</body>
</html>
